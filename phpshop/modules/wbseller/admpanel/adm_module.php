<?php

include_once dirname(__FILE__) . '/../class/WbSeller.php';

PHPShopObj::loadClass("order");
PHPShopObj::loadClass("delivery");

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.wbseller.wbseller_system"));
$WbSeller = new WbSeller();

// Выгрузка всех товаров
function actionImportProducts() {

    // Безопасность
    $cron_secure = md5($GLOBALS['SysValue']['connect']['host'] . $GLOBALS['SysValue']['connect']['dbase'] . $GLOBALS['SysValue']['connect']['user_db'] . $GLOBALS['SysValue']['connect']['pass_db']);

    $protocol = 'http://';
    if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])) {
        $protocol = 'https://';
    }

    $true_path = $protocol . $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir'] . "/phpshop/modules/ozonseller/admpanel/ajax/import.ajax.php?s=" . $cron_secure;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $true_path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_exec($ch);
    curl_close($ch);

    header('Location: ./csv/product.wb.csv');
}

// Обновление цен
function actionUpdatePrice() {

    // Безопасность
    $cron_secure = md5($GLOBALS['SysValue']['connect']['host'] . $GLOBALS['SysValue']['connect']['dbase'] . $GLOBALS['SysValue']['connect']['user_db'] . $GLOBALS['SysValue']['connect']['pass_db']);

    $protocol = 'http://';
    if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])) {
        $protocol = 'https://';
    }

    $true_path = $protocol . $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir'] . "/phpshop/modules/wbseller/cron/products.php?s=" . $cron_secure;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $true_path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_exec($ch);
    curl_close($ch);
}

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

// Функция обновления
function actionUpdate() {
    global $PHPShopModules, $PHPShopOrm;

    // Корректировка пустых значений
    $PHPShopOrm->updateZeroVars('link_new', 'create_products_new', 'log_new', 'discount_new');

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.wbseller.wbseller_system"));
    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);


    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $WbSeller, $TitlePage, $select_name;

    $PHPShopGUI->field_col = 4;

    // Выборка
    $data = $PHPShopOrm->select();

    if ($data['token'] !== '') {
        $PHPShopGUI->action_button['Выгрузить цены'] = [
            'name' => __('Выгрузить цены'),
            'class' => 'btn btn-default btn-sm navbar-btn ',
            'type' => 'submit',
            'action' => 'exportID',
            'icon' => 'glyphicon glyphicon-export'
        ];

        $PHPShopGUI->action_button['Загрузить товары'] = [
            'name' => __('Загрузить товары в CSV'),
            'class' => 'btn btn-default btn-sm navbar-btn ',
            'type' => 'submit',
            'action' => 'importID',
            'icon' => 'glyphicon glyphicon-cloud-download'
        ];

        $PHPShopGUI->setActionPanel($TitlePage, $select_name, ['Загрузить товары', 'Выгрузить цены', 'Сохранить и закрыть']);
    }

    // Статус
    $status[] = [__('Новый заказ'), 0, $data['status']];
    $statusArray = (new PHPShopOrm('phpshop_order_status'))->getList(['id', 'name']);
    foreach ($statusArray as $statusParam) {
        $status[] = [$statusParam['name'], $statusParam['id'], $data['status']];
    }

    // Доступые статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('Новый заказ'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);


    $Tab1 = $PHPShopGUI->setField('API key', $PHPShopGUI->setTextarea('token_new', $data['token'], false, '100%', '100'));
    $Tab1 .= $PHPShopGUI->setField('Статус нового заказа', $PHPShopGUI->setSelect('status_new', $order_status_value, '100%'));


    // Статусы автоматической загрузки
    $order_status_import_value[] = array(__('Ничего не выбрано'), 0, $data['status_import']);
    foreach ($WbSeller->status_list as $k => $status_val) {
        $order_status_import_value[] = array(__($status_val), $k, $data['status_import']);
    }
    $Tab1 .= $PHPShopGUI->setField('Статус заказа в WB для автоматической загрузки', $PHPShopGUI->setSelect('status_import_new', $order_status_import_value, '100%'));

    // Доставка
    $PHPShopDeliveryArray = new PHPShopDeliveryArray();

    $DeliveryArray = $PHPShopDeliveryArray->getArray();
    if (is_array($DeliveryArray))
        foreach ($DeliveryArray as $delivery) {

            // Длинные наименования
            if (strpos($delivery['city'], '.')) {
                $name = explode(".", $delivery['city']);
                $delivery['city'] = $name[0];
            }

            $delivery_value[] = array($delivery['city'], $delivery['id'], $data['delivery'], 'data-subtext="' . $delivery['price'] . '"');
        }

    $Tab1 .= $PHPShopGUI->setField('Доставка', $PHPShopGUI->setSelect('delivery_new', $delivery_value, '100%'));


    $Tab1 .= $PHPShopGUI->setField('Ключ обновления', $PHPShopGUI->setRadio("type_new", 1, "ID товара", $data['type']) . $PHPShopGUI->setRadio("type_new", 2, "Артикул товара", $data['type']));
    $Tab1 .= $PHPShopGUI->setField('Ссылка на товар', $PHPShopGUI->setCheckbox('link_new', 1, 'Показать ссылку на товар в Wildberries', $data['link']));
    $Tab1 .= $PHPShopGUI->setField('Создавать товар', $PHPShopGUI->setCheckbox('create_products_new', 1, 'Создавать автоматически товар из заказа', $data['create_products']));
    $Tab1 .= $PHPShopGUI->setField('Журнал операций', $PHPShopGUI->setCheckbox('log_new', 1, null, $data['log']));

    $Tab1 = $PHPShopGUI->setCollapse('Настройки', $Tab1);


    if ($data['fee_type'] == 1) {
        $status_pre = '-';
    } else {
        $status_pre = '+';
    }

    $getWarehouse = $WbSeller->getWarehouse();

    if ($getWarehouse['title'] != "unauthorized") {
        
        foreach ($getWarehouse as $warehouse)
            if (is_array($warehouse))
                $warehouse_value[] = array(PHPShopString::utf8_win1251($warehouse['name']), $warehouse['id'], $data['warehouse_id']);

        $Tab3 = $PHPShopGUI->setCollapse('Цены', $PHPShopGUI->setField('Колонка цен WB', $PHPShopGUI->setSelect('price_new', $PHPShopGUI->setSelectValue($data['price'], 5), 100)) .
                $PHPShopGUI->setField('Наценка', $PHPShopGUI->setInputText($status_pre, 'fee_new', $data['fee'], 100, '%')) .
                $PHPShopGUI->setField('Действие', $PHPShopGUI->setRadio("fee_type_new", 1, "Понижение", $data['fee_type']) . $PHPShopGUI->setRadio("fee_type_new", 2, "Повышение", $data['fee_type'])) .
                $PHPShopGUI->setField("Склад WB", $PHPShopGUI->setSelect('warehouse_id_new', $warehouse_value, '100%')) .
                $PHPShopGUI->setField('Скидки WB', $PHPShopGUI->setCheckbox('discount_new', 1, 'Снятие всех скидок в WB', $data['discount']))
        );
    }
    elseif (!empty($data['token'])) {
        $Tab3 = $PHPShopGUI->setAlert('Ошибка проверки API key, недействительный ключ', 'danger', true);
    }

    // Инструкция
    $Tab2 = $PHPShopGUI->loadLib('tab_info', $data, '../modules/' . $_GET['id'] . '/admpanel/');

    // Форма регистрации
    $Tab4 = $PHPShopGUI->setPay(false, false, $data['version'], true);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1 . $Tab3, true, false, true), array("Инструкция", $Tab2), array("О Модуле", $Tab4));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit") .
            $PHPShopGUI->setInput("submit", "importID", "Применить", "right", 80, "", "but", "actionImportProducts.modules.edit") .
            $PHPShopGUI->setInput("submit", "exportID", "Применить", "right", 80, "", "but", "actionUpdatePrice.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

/**
 * Подбор категорий
 */
function actionCategorySearch() {
    global $WbSeller;

    $data = $WbSeller->getTree(PHPShopString::win_utf8($_POST['words']))['data'];

    if (is_array($data)) {
        foreach ($data as $row) {

            $result .= '<a href=\'#\' class=\'select-search-wb\'  data-id=\'' . (int) $row['subjectID'] . '\' data-name=\'' . PHPShopString::utf8_win1251($row['subjectName']) . '\'>' . PHPShopString::utf8_win1251($row['parentName']) . ' &rarr; ' . PHPShopString::utf8_win1251($row['subjectName']) . '</a><br>';
        }

        if (!empty($result))
            $result .= '<button type="button" class="close pull-right" aria-label="Close"><span aria-hidden="true">&times;</span></button>';

        exit($result);
    } else
        exit();
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
