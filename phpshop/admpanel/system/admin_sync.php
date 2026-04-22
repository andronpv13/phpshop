<?php

$TitlePage = __("Обмен данными");
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['system']);

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage, $PHPShopOrm, $hideCatalog;

    PHPShopObj::loadClass('order');

    // Выборка
    $data = $PHPShopOrm->select();
    $option = unserialize($data['1c_option']);
    $data = $PHPShopGUI->valid($data, 'update_name', 'update_descriptio', 'update_content');

    $PHPShopGUI->action_button['Журнал операций'] = array(
        'name' => __('Журнал операций'),
        'action' => 'report.crm',
        'class' => 'btn btn-default btn-sm navbar-btn btn-action-panel',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-calendar'
    );

    // Размер названия поля
    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->addJSFiles('./system/gui/system.gui.js');
    $PHPShopGUI->setActionPanel($TitlePage, false, array('Журнал операций', 'Сохранить'));

    // Доступые статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('Не используется'), 0, $option['1c_load_status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $option['1c_load_status']);


    $PHPShopGUI->_CODE = $PHPShopGUI->setCollapse('Данные для загрузки', $PHPShopGUI->setField("Номенклатура", $PHPShopGUI->setCheckbox('option[update_name]', 1, 'Наименование номенклатуры', $option['update_name']) . '<br>' .
                    $PHPShopGUI->setCheckbox('option[update_description]', 1, 'Краткое описание', $option['update_description']) . '<br>' .
                    $PHPShopGUI->setCheckbox('option[update_content]', 1, 'Подробное описание', $option['update_content']) . '<br>' .
                    $PHPShopGUI->setCheckbox('option[update_category]', 1, 'Родительская категория', $option['update_category']) . '<br>' .
                    $PHPShopGUI->setCheckbox('option[update_sort]', 1, 'Характериcтики и свойства', $option['update_sort']) . '<br>' .
                    $PHPShopGUI->setCheckbox('option[update_option]', 1, 'Подтипы', $option['update_option']) . '<br>' .
                    $PHPShopGUI->setCheckbox('option[update_option_delim]', 1, 'Автоматическое определение вариантов подтипов', $option['update_option_delim']) . '<br>' .
                    $PHPShopGUI->setCheckbox('option[update_price]', 1, 'Цены', $option['update_price']) . '<br>' .
                    $PHPShopGUI->setCheckbox('option[update_item]', 1, 'Склад', $option['update_item']) . '<br>' .
                    $PHPShopGUI->setCheckbox('option[seo_update]', 1, 'SEO ссылка', $option['seo_update'])
    ));

    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Выгрузка заказов', $PHPShopGUI->setField("Статус заказа", $PHPShopGUI->setSelect('option[1c_load_status]', $order_status_value, 300)
                    , 1, 'Заказы выгружаются только при определенном статусе', $hideCatalog));

    /*
      if(empty($hideCatalog))
      $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Обмен с сайтом', $PHPShopGUI->setField("Бухгалтерские документы", $PHPShopGUI->setCheckbox('1c_load_accounts_new', 1, 'Оригинальный счет с печатью и подписями из 1С', $data['1c_load_accounts']) . '<br>' .
      $PHPShopGUI->setCheckbox('1c_load_invoice_new', 1, 'Оригинальная счет-фактура с печатью из 1С', $data['1c_load_invoice']) . '<br>' .
      $PHPShopGUI->setCheckbox('option[1c_load_status_email]', 1, 'E-mail оповещение покупателя о новых загруженных бухгалтерских документах из 1С', $option['1c_load_status_email'])
      , 1, 'Оригинальные документы выгружаются из 1С при синхронизации заказов с помощью PHPShop Exchange.')
      ); */

    // Артикул
    $key_value[] = array(__('Артикул'), 'uid', $option['exchange_key']);
    $key_value[] = array(__('Внешний код'), 'external', $option['exchange_key']);
    $key_value[] = array(__('Код 1С'), 'code', $option['exchange_key']);
    $key_value[] = array(__('Штрихкод'), 'barcode', $option['exchange_key']);
    
    // Название
    $name_value[] = array(__('Наименование номенклатуры'), 'name', $option['exchange_name']);
    $name_value[] = array(__('Наименование для печати'), 'print', $option['exchange_name']);

    // Авторизация
    $auth_value[] = array(__('Логин и пароль'), 0, $option['exchange_auth']);
    $auth_value[] = array(__('Имя файла'), 1, $option['exchange_auth']);

    if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])) {
        $protocol = 'https://';
    } else
        $protocol = 'http://';

    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Настройка CommerceML', $PHPShopGUI->setField("Авторизация", $PHPShopGUI->setSelect('option[exchange_auth]', $auth_value, 300)) .
            $PHPShopGUI->setField($PHPShopGUI->setLink('../../1cManager/' . $option['exchange_auth_path'] . '.php', 'Имя файла', '_blank', false, 'Открыть ссылку', false, false, false), $PHPShopGUI->setInputText($protocol . $_SERVER['SERVER_NAME'] . '/1cManager/', 'option[exchange_auth_path]', $option['exchange_auth_path'], 400, '.php', false, false, 'secret_cml_path')) .
            $PHPShopGUI->setField("Артикул на сайте", $PHPShopGUI->setSelect('option[exchange_key]', $key_value, 300)) . 
                    $PHPShopGUI->setField("Наименование на сайте", $PHPShopGUI->setSelect('option[exchange_name]', $name_value, 300) . '<br>' .
                    $PHPShopGUI->setCheckbox('option[exchange_zip]', 1, 'Сжатие данных ZIP', $option['exchange_zip']) . '<br>' .
                    $PHPShopGUI->setCheckbox('option[exchange_create]', 1, 'Создавать новые товары', $option['exchange_create']) . '<br>' .
                    $PHPShopGUI->setCheckbox('option[exchange_create_category]', 1, 'Создавать новые каталоги', $option['exchange_create_category']) . '<br>' .
                    $PHPShopGUI->setCheckbox('option[exchange_image]', 1, 'Создавать новые изображения', $option['exchange_image']) . '<br>' .
                    $PHPShopGUI->setCheckbox('option[exchange_log]', 1, 'Журнал соединений', $option['exchange_log']) . '<br>' .
                    $PHPShopGUI->setCheckbox('option[exchange_clean]', 1, 'Выключить товары, отсутствующие в файле импорта', $option['exchange_clean']) . '<br>'.
                    $PHPShopGUI->setCheckbox('option[exchange_change]', 1, 'Обработка измененных данных', $option['exchange_change']) . '<br>'
            ) .
            $PHPShopGUI->setField("Цена", $PHPShopGUI->setInputText(false, 'option[exchange_price1]', $option['exchange_price1'], 300, false, false, false, 'Внешний код')) .
            $PHPShopGUI->setField("Цена 2", $PHPShopGUI->setInputText(false, 'option[exchange_price2]', $option['exchange_price2'], 300, false, false, false, 'Внешний код')) .
            $PHPShopGUI->setField("Цена 3", $PHPShopGUI->setInputText(false, 'option[exchange_price3]', $option['exchange_price3'], 300, false, false, false, 'Внешний код')) .
            $PHPShopGUI->setField("Цена 4", $PHPShopGUI->setInputText(false, 'option[exchange_price4]', $option['exchange_price4'], 300, false, false, false, 'Внешний код')) .
            $PHPShopGUI->setField("Цена 5", $PHPShopGUI->setInputText(false, 'option[exchange_price5]', $option['exchange_price5'], 300, false, false, false, 'Внешний код')) .
            $PHPShopGUI->setField("Блокировка характеристик", $PHPShopGUI->setTextarea('option[exchange_sort_ignore]', $option['exchange_sort_ignore'], false, false, false, __('Укажите характеристики через запятую'), __('Примечание'))) .
            $PHPShopGUI->setField("Блокировка обновления товаров", $PHPShopGUI->setTextarea('option[exchange_product_ignore]', $option['exchange_product_ignore'], false, false, false, __('Укажите внешний код товаров через запятую'), __('Внешний код'))) .
            $PHPShopGUI->setField("Размещение изображений", $PHPShopGUI->setInputText($GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/', "option[exchange_image_result_path]", $option['exchange_image_result_path'], 400), 1, 'Путь сохранения изображений')
    );

    if (empty($_SESSION['mod_pro'])) {
        $PHPShopGUI->_CODE = $PHPShopGUI->setAlert('Раздел настройки <b>обмена данными</b> доступен только в версии <a class="btn btn-sm btn-info" href="https://www.phpshop.ru/page/compare.html?from=' . $_SERVER['SERVER_NAME'] . '" target="_blank"><span class="glyphicon glyphicon-info-sign"></span> PHPShop Pro</a>', 'info', true);
    }

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.system.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.system.edit");

    $PHPShopGUI->setFooter($ContentFooter);

    $sidebarleft[] = array('title' => 'Категории', 'content' => $PHPShopGUI->loadLib('tab_menu', false, './system/'));
    $PHPShopGUI->setSidebarLeft($sidebarleft, 2);

    // Футер
    $PHPShopGUI->Compile(2);
    return true;
}

/**
 * Экшен сохранения
 */
function actionSave() {

    // Сохранение данных
    actionUpdate();
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // Выборка
    $data = $PHPShopOrm->select();
    $option = unserialize($data['1c_option']);
    $_POST['option']['exchange_auth_path'] = substr($_POST['option']['exchange_auth_path'], 0, 10);

    if ($_POST['option']['exchange_image'] == 1) {
        $_POST['option']['exchange_zip'] = 1;
    }


    if (is_array($_POST['option']))
        foreach ($_POST['option'] as $key => $val)
            $option[$key] = $val;

    // Создаем папку
    if (!is_dir($_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $option['exchange_image_result_path']))
        @mkdir($_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $option['exchange_image_result_path'], 0777, true);

    // Проверка пути сохранения изображений
    if (stristr($option['exchange_image_result_path'], '..') or ! is_dir($_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $option['exchange_image_result_path']))
        $option['exchange_image_result_path'] = null;

    if (substr($option['exchange_image_result_path'], -1) != '/' and ! empty($option['exchange_image_result_path']))
        $option['exchange_image_result_path'] .= '/';

    // Поиск нулевых значений
    if (is_array($_POST['option']))
        $option_null = array_diff_key($option, $_POST['option']);
    else
        $option_null = $option;

    if (is_array($option_null)) {
        foreach ($option_null as $key => $val)
            $option[$key] = 0;
    }


    $_POST['1c_load_accounts_new'] = $_POST['1c_load_accounts_new'] ? 1 : 0;
    $_POST['1c_load_invoice_new'] = $_POST['1c_load_invoice_new'] ? 1 : 0;
    $_POST['1c_option_new'] = serialize($option);

    // Переименование
    if (!empty($option['exchange_auth']) and ! empty($option['exchange_auth_path']) and ! file_exists($_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/1cManager/' . $option['exchange_auth_path'] . '.php')) {
        copy($_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/1cManager/cml.php', $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/1cManager/' . $option['exchange_auth_path'] . '.php');
    }

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));

    return array("success" => $action);
}

// Обработка событий
$PHPShopGUI->getAction();
?>