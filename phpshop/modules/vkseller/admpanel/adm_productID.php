<?php

include_once dirname(__FILE__) . '/../class/VkSeller.php';

function addVksellerProductTab($data) {
    global $PHPShopGUI;

    // Размер названия поля
    $PHPShopGUI->field_col = 4;
    $VkSeller = new VkSeller();

    $tab = $PHPShopGUI->setField(null, $PHPShopGUI->setCheckbox('export_vk_new', 1, 'Включить экспорт в VK', $data['export_vk']));

    if (!empty($data['export_vk_task_status']))
        $tab .= $PHPShopGUI->setField(null, $PHPShopGUI->setText('<span class="text-success">' . __('Загружен') . ' ' . PHPShopDate::get($data['export_vk_task_status'], true) . '</span>'));

    // Валюты
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            if ($data['baseinputvaluta'] == $val['id']) {
                $valuta_def_name = $val['code'];
            }
        }
    $tab .= $PHPShopGUI->setField('Цена VK', $PHPShopGUI->setInputText(null, 'price_vk_new', $data['price_vk'], 150, $valuta_def_name), 2);

    if (!empty($data['export_vk_id']))
        $tab .= $PHPShopGUI->setField('VK ID', $PHPShopGUI->setInputText(null, 'export_vk_id_new', $data['export_vk_id'], 150, $PHPShopGUI->setLink('https://vk.ru/market-' . $VkSeller->owner_id . '?screen=cart&w=product-' . $VkSeller->owner_id . '_' . $data['export_vk_id'] . '%2Fquery', '<span class=\'glyphicon glyphicon-eye-open\'></span>', '_blank', false, __('Перейте на сайт ВКонтакте'))));

    $PHPShopGUI->addTab(array("ВКонтакте", $tab, true));
}

function VksellerUpdate() {

    // Отключение VK
    if (!isset($_POST['export_vk_new']) and ! empty($_POST['name_new'])) {
        $_POST['export_vk_new'] = 0;
        // $_POST['export_vk_task_status_new'] = '';
        //$_POST['export_vk_id_new'] = '';
    }
}

function VksellerSave() {

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $data = $PHPShopOrm->getOne(['*'], ['id' => '=' . (int) $_POST['rowID']]);

    if ($data['items'] > 0)
        $prod['deleted'] = 0;
    else
        $prod['deleted'] = 1;


    if (!empty($data['export_vk'])) {

        $VkSeller = new VkSeller();

        if ($VkSeller->model == 'API') {

            // Создание
            if (empty($data['export_vk_task_status'])) {

                // Фото главное
                $data['main_photo_id'] = $VkSeller->sendImages($data['id'], $data['pic_big'])['response'][0]['id'];

                // Фото дополнительные
                $images = $VkSeller->getImages($data['id'], $data['pic_big']);
                if (is_array($images))
                    foreach ($images as $image) {

                        $photo_result = $VkSeller->sendImages($data['id'], $image)['response'][0]['id'];

                        if (!empty($photo_result))
                            $photo_ids[] = $photo_result;
                    }

                if (is_array($photo_ids))
                    $data['photo_ids'] = implode(",", $photo_ids);

                if (!empty($data['main_photo_id'])) {
                    $export_vk = $VkSeller->sendProduct($data);
                    $export_vk_id = $export_vk['response']['market_item_id'];
                }

                // Товар выгрузился
                if (!empty($export_vk_id)) {
                    $PHPShopOrm->update(['export_vk_task_status_new' => time(), 'export_vk_id_new' => $export_vk_id], ['id' => '=' . (int) $data['id']]);
                    $VkSeller->clean_log($data['id']);
                    //unset($_POST['export_vk_id_new']);
                }
                // Ошибка
                elseif (!empty($export_vk['error']['error_msg'])) {
                    $VkSeller->export_log($export_vk['error']['error_msg'], $data['id'], $data['name']);
                }
            }
            // обновление
            else {
                $VkSeller->updateProduct($data);
            }
        }
    }
}

$addHandler = array(
    'actionStart' => 'addVksellerProductTab',
    'actionDelete' => false,
    'actionUpdate' => 'VksellerUpdate',
    'actionSave' => 'VksellerSave',
);
?>