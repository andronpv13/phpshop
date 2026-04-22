<?php

include_once dirname(__FILE__) . '/../class/Megamarket.php';

PHPShopObj::loadClass("order");
PHPShopObj::loadClass("delivery");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("category");

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.megamarket.megamarket_system"));
$Megamarket = new Megamarket();

// Обновление цен
function actionUpdatePrice() {

    // Безопасность
    $cron_secure = md5($GLOBALS['SysValue']['connect']['host'] . $GLOBALS['SysValue']['connect']['dbase'] . $GLOBALS['SysValue']['connect']['user_db'] . $GLOBALS['SysValue']['connect']['pass_db']);

    $protocol = 'http://';
    if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])) {
        $protocol = 'https://';
    }

    $true_path = $protocol . $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir'] . "/phpshop/modules/megamarket/cron/products.php?s=" . $cron_secure;

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

// Построение дерева категорий
function treegenerator($array, $i, $curent, $dop_cat_array) {
    global $tree_array;
    $del = '&brvbar;&nbsp;&nbsp;&nbsp;&nbsp;';
    $tree_select = $tree_select_dop = $check = false;

    $del = str_repeat($del, $i);
    if (is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {

            $check = treegenerator($tree_array[$k], $i + 1, $k, $dop_cat_array);

            $selected = null;
            $disabled = null;

            if (is_array($dop_cat_array))
                foreach ($dop_cat_array as $vs) {
                    if ($k == $vs)
                        $selected = "selected";
                }

            if (empty($check['select'])) {
                $tree_select .= '<option value="' . $k . '" ' . $selected . $disabled . '>' . $del . $v . '</option>';

                $i = 1;
            } else {
                $tree_select .= '<option value="' . $k . '" ' . $selected . ' disabled>' . $del . $v . '</option>';
            }

            $tree_select .= $check['select'];
        }
    }
    return array('select' => $tree_select);
}

// Функция обновления
function actionUpdate() {
    global $PHPShopModules, $PHPShopOrm;

    // Корректировка пустых значений
    $PHPShopOrm->updateZeroVars('log_new');
    $PHPShopOrm->debug = false;

    // Категории
    if (is_array($_POST['categories']) and $_POST['categories'][0] != 'null') {

        $cat_array = array();
        foreach ($_POST['categories'] as $v)
            if (!empty($v) and ! strstr($v, ','))
                $cat_array[] = $v;

        if (is_array($cat_array)) {
            $where = array('category' => ' IN ("' . implode('","', $cat_array) . '")');
            $PHPShopOrmProducts = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
            $PHPShopOrmProducts->debug = false;
            $PHPShopOrmProducts->update(array('export_megamarket_new' => intval($_POST['enabled_all'])), $where);
        }
    }

    $action = $PHPShopOrm->update($_POST);


    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $Megamarket, $TitlePage, $select_name;

    $PHPShopGUI->field_col = 4;
    $PHPShopGUI->addJSFiles('../modules/megamarket/admpanel/gui/megamarket.gui.js');

    // Выборка
    $data = $PHPShopOrm->select();

    if ($data['token'] !== '') {
        
        switch($data['export']){
            case 0: 
                $export_name = __('Выгрузить цены и склад');
                break;
            case 1: 
                $export_name = __('Выгрузить цены');
                break;
            case 2: 
                $export_name = __('Выгрузить склад');
                break;
            
        }

        $PHPShopGUI->action_button['Выгрузить цены'] = [
            'name' => $export_name,
            'class' => 'btn btn-default btn-sm navbar-btn ',
            'type' => 'submit',
            'action' => 'exportID',
            'icon' => 'glyphicon glyphicon-export'
        ];
        $PHPShopGUI->setActionPanel($TitlePage, $select_name, ['Выгрузить цены', 'Сохранить и закрыть']);
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


    $Tab1 = $PHPShopGUI->setField('Авторизационный токен', $PHPShopGUI->setInputText(false, 'token_new', $data['token'], '100%'));
    $Tab1 .= $PHPShopGUI->setField('Статус нового заказа', $PHPShopGUI->setSelect('status_new', $order_status_value, '100%'));

   
    $export_value[]=['Цены и склад', 0, $data['export']];
    $export_value[]=['Цены', 1, $data['export']];
    $export_value[]=['Склад', 2, $data['export']];
    $Tab1 .= $PHPShopGUI->setField('Обновление данных', $PHPShopGUI->setSelect('export_new', $export_value, '100%',true));

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
    $Tab1 .= $PHPShopGUI->setField('Журнал операций', $PHPShopGUI->setCheckbox('log_new', 1, null, $data['log']));

    $Tab1 = $PHPShopGUI->setCollapse('Настройки', $Tab1);


    if ($data['fee_type'] == 1) {
        $status_pre = '-';
    } else {
        $status_pre = '+';
    }




    $PHPShopCategoryArray = new PHPShopCategoryArray($where);
    $CategoryArray = $PHPShopCategoryArray->getArray();
    $GLOBALS['count'] = count($CategoryArray);

    $tree_array = array();

    foreach ($PHPShopCategoryArray->getKey('parent_to.id', true) as $k => $v) {
        foreach ($v as $cat) {
            $tree_array[$k]['sub'][$cat] = $CategoryArray[$cat]['name'];
        }
        $tree_array[$k]['name'] = $CategoryArray[$k]['name'];
        $tree_array[$k]['id'] = $k;
        if ($k == $data['parent_to'])
            $tree_array[$k]['selected'] = true;
    }

    $GLOBALS['tree_array'] = &$tree_array;

    // Допкаталоги
    $dop_cat_array = preg_split('/,/', $data['categories'], -1, PREG_SPLIT_NO_EMPTY);

    if (is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator($tree_array[$k], 1, $k, $dop_cat_array);

            // Допкаталоги
            $selected = null;
            if (is_array($dop_cat_array))
                foreach ($dop_cat_array as $vs) {
                    if ($k == $vs)
                        $selected = "selected";
                }


            if (empty($tree_array[$k]))
                $disabled = null;
            else
                $disabled = ' disabled';

            $tree_select .= '<option value="' . $k . '"  ' . $selected . $disabled . '>' . $v . '</option>';

            $tree_select .= $check['select'];
        }

    $tree_select_sm = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container="body" data-style="btn btn-default btn-sm" name="categories[]"  data-width="100%" multiple>' . $tree_select . '</select>';

    $Tab2 = $PHPShopGUI->setCollapse('Цены', $PHPShopGUI->setField('Колонка цен Мегамаркет', $PHPShopGUI->setSelect('price_new', $PHPShopGUI->setSelectValue($data['price'], 5), 100)) .
            $PHPShopGUI->setField('Наценка', $PHPShopGUI->setInputText($status_pre, 'fee_new', $data['fee'], 100, '%')) .
            $PHPShopGUI->setField('Действие', $PHPShopGUI->setRadio("fee_type_new", 1, "Понижение", $data['fee_type']) . $PHPShopGUI->setRadio("fee_type_new", 2, "Повышение", $data['fee_type']))
    );
    
    $catOption = $PHPShopGUI->setField('Пароль YML-файла', $PHPShopGUI->setInputText(false, 'password_new', $data['password'], '100%', $PHPShopGUI->setLink('http://' . $_SERVER['SERVER_NAME'] . '/yml/?marketplace=megamarket&pas=' . $data['password'], '<span class=\'glyphicon glyphicon-eye-open\'></span>', '_blank', false, __('Открыть'))));

    
    $catOption .= $PHPShopGUI->setField("Размещение", $tree_select_sm . $PHPShopGUI->setCheckbox("categories_all", 1, "Выбрать все категории?", 0), 1, 'Пакетное редактирование. Настройка не сохраняется.');
    $catOption .= $PHPShopGUI->setField("Вывод в Мегамаркет", $PHPShopGUI->setRadio("enabled_all", 1, "Вкл.", 1) . $PHPShopGUI->setRadio("enabled_all", 0, "Выкл.", 1));

    $Tab3 .= $PHPShopGUI->setCollapse('Товары', $catOption);

    // Инструкция
    $Tab4 = $PHPShopGUI->loadLib('tab_info', $data, '../modules/' . $_GET['id'] . '/admpanel/');

    // Форма регистрации
    $Tab5 = $PHPShopGUI->setPay(false, false, $data['version'], true);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1 . $Tab3. $Tab2, true, false, true), array("Инструкция", $Tab4), array("О Модуле", $Tab5));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit") .
            $PHPShopGUI->setInput("submit", "exportID", "Применить", "right", 80, "", "but", "actionUpdatePrice.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
