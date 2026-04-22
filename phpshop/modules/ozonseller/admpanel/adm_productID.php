<?php

include_once dirname(__FILE__) . '/../class/OzonSeller.php';

function addOzonsellerProductTab($data) {
    global $PHPShopGUI;

    // Размер названия поля
    $PHPShopGUI->field_col = 4;

    $tab = $PHPShopGUI->setField(null, $PHPShopGUI->setCheckbox('export_ozon_new', 1, 'Включить экспорт в OZON', $data['export_ozon']));
    $tab .= $PHPShopGUI->setInput("hidden", "export_ozon_task_id", $data['export_ozon_task_id']);

    $status = ['imported' => '<span class="text-success">' . __('Загружен') . '</span>', 'failed' => '<a class="text-warning" href="?path=modules.dir.ozonseller.product&uid=' . $data['id'] . '" target="_blank">' . __('Ошибка') . '</a>', 'pending' => '<span class="text-muted">' . __('В ожидании') . '</span>'];

    if (!empty($data['export_ozon_task_status']))
        $tab .= $PHPShopGUI->setField('Статус товара', $PHPShopGUI->setText($status[$data['export_ozon_task_status']]));

    // Валюты
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            if ($data['baseinputvaluta'] == $val['id']) {
                $valuta_def_name = $val['code'];
            }
        }

    $tab .= $PHPShopGUI->setField('Цена OZON', $PHPShopGUI->setInputText(null, 'price_ozon_new', $data['price_ozon'], 150, $valuta_def_name), 2);
    $tab .= $PHPShopGUI->setField("Штрихкод", $PHPShopGUI->setInputText(null, 'barcode_ozon_new', $data['barcode_ozon'], 150));
    $tab .= $PHPShopGUI->setField("SKU OZON", $PHPShopGUI->setInputText(null, 'sku_ozon_new', $data['sku_ozon'], 150), 1, 'Используется для ссылки на товар в OZON');


    if (!empty($data['export_ozon']))
        $tab .= $PHPShopGUI->setField('OZON ID', $PHPShopGUI->setInputText(null, 'export_ozon_id_new', $data['export_ozon_id'], 150), 1, 'Используется для обновления товара в OZON');

    $PHPShopGUI->addTab(array("OZON", $tab, true));
}

function OzonsellerUpdate() {

    // Отключение Ozon
    if (!isset($_POST['export_ozon_new']) and ! empty($_POST['name_new'])) {
        $_POST['export_ozon_new'] = 0;
        $_POST['export_ozon_task_id_new'] = 0;
        $_POST['export_ozon_task_status_new'] = '';
    }

    //if (isset($_POST['enabled_new']) and empty($_POST['enabled_new']))
    // $_POST['export_ozon_new'] = 0;
}

function OzonsellerSave() {

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $data = $PHPShopOrm->getOne(['*'], ['id' => '=' . (int) $_POST['rowID']]);

    // Товар для OZON
    if (!empty($data['export_ozon'])) {

        $OzonSeller = new OzonSeller();

        // Товар еще не выгружен
        if (empty($data['export_ozon_id'])) {

            // Выгрузка
            if (empty($data['export_ozon_task_id'])) {
                $result = $OzonSeller->sendProducts($data);
                $task_id = $data['export_ozon_task_id'] = $result['result']['task_id'];
                $error = $result['message'];
            } else
                $task_id = $data['export_ozon_task_id'];

            if (!empty($task_id) and empty($error)) {

                // Проверка статуса выгрузки
                $ProductsInfo = $OzonSeller->sendProductsInfo($data);
                $info = $ProductsInfo['result']['items'][0];

                // Товар выгрузился
                if (!empty($info['product_id'])) {

                    // SKU для ссылки на товар OZON
                    $data['sku_ozon'] = $OzonSeller->getProduct($info['product_id'])['items'][0]['sources'][0]['sku'];

                    // Передача штрихкода
                    if (!empty($data['barcode_ozon']) and ! empty($data['sku_ozon']))
                        $OzonSeller->addBarcode(['barcode_ozon' => $data['barcode_ozon'], 'sku_ozon' => $data['sku_ozon']]);

                    $PHPShopOrm->update(['export_ozon_task_status_new' => $info['status'], 'export_ozon_id_new' => $info['product_id'], 'sku_ozon_new' => $data['sku_ozon']], ['id' => '=' . $data['id']]);
                    $data['export_ozon_id'] = $info['product_id'];
                    $OzonSeller->clean_log($data['id']);
                }
                // Ошибка
                elseif (is_array($info['errors']) and count($info['errors']) > 0) {

                    if (empty($info['status']))
                        $info['status'] = 'error';

                    foreach ($info['errors'] as $k => $er) {

                        // Ссылки
                        $er['description'] = preg_replace("~(http|https|ftp|ftps)://(.*?)(\s|\n|[,.?!](\s|\n)|$)~", '<a href="$1://$2" target="_blank">[ссылка]</a>$3', $er['description']);
                        $error .= ($k + 1) . ' - ' . PHPShopString::utf8_win1251($er['description']) . '<br>';

                        // Лимит
                        if ($er['code'] == 'TOTAL_CREATE_LIMIT_EXCEEDED') {
                            $PHPShopOrm->update(['export_ozon_task_status_new' => null, 'export_ozon_task_id_new' => 0], ['id' => '=' . (int) $data['id']]);
                        }
                    }

                    $PHPShopOrm->update(['export_ozon_task_status_new' => $info['status']], ['id' => '=' . (int) $data['id']]);
                }
                // Неверный task_id
                elseif (!empty($ProductsInfo['code'])) {
                    $error = $ProductsInfo['message'];
                    $PHPShopOrm->update(['export_ozon_task_status_new' => null, 'export_ozon_task_id_new' => 0], ['id' => '=' . (int) $data['id']]);
                }
                // В ожидании
                elseif ($info['status'] == 'pending') {
                    $error = __('Товар поставлен в очередь на запись, сервис OZON временно занят. Требуется повторное отправление данных для завершения выгрузки.');
                    $PHPShopOrm->update(['export_ozon_task_status_new' => $info['status'], 'export_ozon_task_id_new' => $task_id], ['id' => '=' . (int) $data['id']]);
                }

                if (!empty($error))
                    $OzonSeller->export_log($error, $data['id'], $data['name'], $data['pic_small']);
            }
            // Ошибка 
            elseif (!empty($error)) {
                
                
                
                $OzonSeller->export_log($error, $data['id'], $data['name'], $data['pic_small']);
            }
        }
        // Товар выгружен, обновление цен и остатков
        else {

            // SKU для ссылки на товар OZON
            if (empty($data['sku_ozon'])) {
                $data['sku_ozon'] = $OzonSeller->getProduct($data['export_ozon_id'])['items'][0]['sources'][0]['sku'];

                if (!empty($data['sku_ozon']))
                    $PHPShopOrm->update(['sku_ozon_new' => $data['sku_ozon']], ['id' => '=' . $data['id']]);
            }

            // Штрихкод
            if (!empty($data['barcode_ozon']) and ! empty($data['sku_ozon']))
                $OzonSeller->addBarcode(['barcode_ozon' => $data['barcode_ozon'], 'sku_ozon' => $data['sku_ozon']]);

            // Склад
            if (is_array($OzonSeller->warehouse))
                foreach ($OzonSeller->warehouse as $warehouse) {
                    $result = $OzonSeller->setProductStock([$data], $warehouse['id'])['result']['items'][0]['product_id'];
                    //if (!empty($result))
                    //$product_id = $result;
                }

            // Цены
            $OzonSeller->setProductPrice([$data]);
        }
    }
}

$addHandler = array(
    'actionStart' => 'addOzonsellerProductTab',
    'actionDelete' => false,
    'actionUpdate' => 'OzonsellerUpdate',
    'actionSave' => 'OzonsellerSave',
    'actionOptionEdit' => 'addOzonsellerProductTab',
);
?>