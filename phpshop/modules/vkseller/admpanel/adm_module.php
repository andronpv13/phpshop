<?php

include_once dirname(__FILE__) . '/../class/VkSeller.php';

PHPShopObj::loadClass("order");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("category");
PHPShopObj::loadClass("delivery");

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.vkseller.vkseller_system"));
$VkSeller = new VkSeller();

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

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    // Корректировка пустых значений
    $PHPShopOrm->updateZeroVars('link_new');

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.vkseller.vkseller_system"));
    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);


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

            if ($_POST['enabled_all'] == 1)
                $PHPShopOrmProducts->update(array('export_vk_new' => (int) $_POST['enabled_all']), $where);
            else
                $PHPShopOrmProducts->update(array('export_vk_new' => (int) $_POST['enabled_all'], 'export_vk_task_status_new' => 0, 'export_vk_id_new' => 0), $where);
        }
    }

    header('Location: ?path=modules&id=' . $_GET['id']);

    return $action;
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

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $TitlePage, $select_name;

    $PHPShopGUI->field_col = 4;
    $PHPShopGUI->addJSFiles('../modules/vkseller/admpanel/gui/vkseller.gui.js');

    // Выборка
    $data = $PHPShopOrm->select();

    if ($data['model'] === 'API') {
        $PHPShopGUI->action_button['Экспортировать данные'] = [
            'name' => __('Экспортировать данные'),
            'class' => 'btn btn-default btn-sm navbar-btn vk-export',
            'type' => 'button',
            'icon' => 'glyphicon glyphicon-export'
        ];
        $PHPShopGUI->setActionPanel($TitlePage, $select_name, ['Экспортировать данные', 'Сохранить и закрыть']);
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


    $tree_select = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container="body" data-style="btn btn-default btn-sm" name="categories[]"  data-width="100%" multiple>' . $tree_select . '</select>';

    $models = [
        ['Oбновление данных через YML', 'YML', $data['model']],
        ['Обновление данных через API и YML', 'API', $data['model']]
    ];

    if (empty($_SESSION['mod_pro']))
        $models = [
            ['Обновление данных через API и YML (доступна в версии Pro)', 'YML', $data['model']],
            ['Oбновление данных через YML', 'YML', $data['model']]
        ];

    $Tab1 = $PHPShopGUI->setField('Модель работы', $PHPShopGUI->setSelect('model_new', $models, '100%', true));

    // Статусы автоматической загрузки
    $status_import_array = ['Новый заказ', 'Согласовывается', 'Собирается', 'Доставляется', 'Выполнен', 'Отменен', 'Возврат'];
    foreach ($status_import_array as $k => $status_val) {
        $order_status_import_value[] = array(__($status_val), $k, $data['status_import']);
    }

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


    if ($data['model'] === 'API') {
        $Tab1 .= $PHPShopGUI->setField('API key', $PHPShopGUI->setTextarea('token_new', $data['token'], false, '100%', '100') . $PHPShopGUI->setHelp('Получить <a href="../modules/vkseller/token.php?client_id=" id="client_token" target="_blank">Персональный ключ</a>'));
        $Tab1 .= $PHPShopGUI->setField('Токен обновления', $PHPShopGUI->setTextarea('refresh_token_new', $data['refresh_token'], false, '100%', '100'));
        $Tab1 .= $PHPShopGUI->setField("ID устройства", $PHPShopGUI->setInputText(null, "device_id_new", $data['device_id'], '100%'));
        $Tab1 .= $PHPShopGUI->setField("ID приложения", $PHPShopGUI->setInputText(null, "client_id_new", $data['client_id'], '100%'));
        $Tab1 .= $PHPShopGUI->setField("Защищенный ключ приложения", $PHPShopGUI->setInputText(null, "client_secret_new", $data['client_secret'], '100%'));
        $Tab1 .= $PHPShopGUI->setField("ID сообщества", $PHPShopGUI->setInputText('public', "owner_id_new", $data['owner_id'], '100%'));
        $Tab1 .= $PHPShopGUI->setField('Статус нового заказа', $PHPShopGUI->setSelect('status_new', $order_status_value, '100%'));
        $Tab1 .= $PHPShopGUI->setField('Статус заказа в VK для автоматической загрузки', $PHPShopGUI->setSelect('status_import_new', $order_status_import_value, '100%'));
        $Tab1 .= $PHPShopGUI->setField('Доставка', $PHPShopGUI->setSelect('delivery_new', $delivery_value, '100%'));
        $Tab1 .= $PHPShopGUI->setField('Ссылка на товар', $PHPShopGUI->setCheckbox('link_new', 1, 'Показать ссылку на товар в ВК', $data['link']));
    }

    $catOption = $PHPShopGUI->setField("Размещение", $tree_select . $PHPShopGUI->setCheckbox("categories_all", 1, "Выбрать все категории?", 0), 1, 'Пакетное редактирование. Настройка не сохраняется.');
    $catOption .= $PHPShopGUI->setField("Вывод в VK", $PHPShopGUI->setRadio("enabled_all", 1, "Вкл.", 1) . $PHPShopGUI->setRadio("enabled_all", 0, "Выкл.", 1));

    $catOption .= $PHPShopGUI->setField('Пароль YML-файла', $PHPShopGUI->setInputText(false, 'password_new', $data['password'], '100%', $PHPShopGUI->setLink('http://' . $_SERVER['SERVER_NAME'] . '/yml/?marketplace=vk&pas=' . $data['password'], '<span class=\'glyphicon glyphicon-eye-open\'></span>', '_blank', false, __('Открыть'))));
    $catOption .= $PHPShopGUI->setField('Ключ обновления', $PHPShopGUI->setRadio("type_new", 1, "ID товара", $data['type']) . $PHPShopGUI->setRadio("type_new", 2, "Артикул товара", $data['type']));

    $Tab1 = $PHPShopGUI->setCollapse('Настройки', $Tab1);
    $Tab1 .= $PHPShopGUI->setCollapse('Товары', $catOption);

    if ($data['fee_type'] == 1) {
        $status_pre = '-';
    } else {
        $status_pre = '+';
    }

    $Tab3 = $PHPShopGUI->setCollapse('Цены', $PHPShopGUI->setField('Колонка цен VK', $PHPShopGUI->setSelect('price_new', $PHPShopGUI->setSelectValue($data['price'], 5), 100)) .
            $PHPShopGUI->setField('Наценка', $PHPShopGUI->setInputText($status_pre, 'fee_new', $data['fee'], 100, '%')) .
            $PHPShopGUI->setField('Действие', $PHPShopGUI->setRadio("fee_type_new", 1, "Понижение", $data['fee_type']) . $PHPShopGUI->setRadio("fee_type_new", 2, "Повышение", $data['fee_type']))
    );

    // Инструкция
    $Tab2 = $PHPShopGUI->loadLib('tab_info', $data, '../modules/' . $_GET['id'] . '/admpanel/');

    // Форма регистрации
    $Tab4 = $PHPShopGUI->setPay(false, false, $data['version'], true);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1 . $Tab3, true, false, true), array("Инструкция", $Tab2), array("О Модуле", $Tab4));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("hidden", "locale_vk_start_export", __('Вы действительно хотите запустить экспорт в ВКонтакте?')) .
            $PHPShopGUI->setInput("hidden", "locale_vk_stop_export", __('Вы действительно хотите прервать экспорт данных?')) .
            $PHPShopGUI->setInput("hidden", "locale_vk_export", __('Экспорт товаров в ВКонтакте')) .
            $PHPShopGUI->setInput("hidden", "locale_vk_export_done", __('Экспорт в ВКонтакте выполнен, выгружено % товаров')) .
            $PHPShopGUI->setInput("hidden", "stop", 0) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
