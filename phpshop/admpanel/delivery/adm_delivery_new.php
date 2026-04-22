<?php

PHPShopObj::loadClass(array('delivery', 'payment', 'category'));


$TitlePage = __('Создание Доставки');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['delivery']);

// Построение дерева категорий
function treegenerator($array, $i, $curent, $dop_cat_array) {
    global $tree_array;
    $del = '&brvbar;&nbsp;&nbsp;&nbsp;&nbsp;';
    $tree_select = $tree_select_dop = $check = false;

    $del = str_repeat($del, $i);
    if (!empty($array) and is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {

            $check = treegenerator(@$tree_array[$k], $i + 1, $k, $dop_cat_array);

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

/**
 * Экшен загрузки форм редактирования
 */
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $PHPShopSystem;

    $PHPShopDelivery = new PHPShopDelivery();

    // Размер названия поля
    $PHPShopGUI->field_col = 5;
    $PHPShopGUI->addJSFiles('./js/jquery.treegrid.js', './delivery/gui/delivery.gui.js');

    if (@$_GET['target'] == 'cat') {
        $catalog = true;
        $data['is_folder'] = 1;
    } else {
        $catalog = false;
        $data['is_folder'] = 0;
    }
    $data['is_mod'] = 1;

    // Начальные данные
    if ($catalog)
        $data['city'] = __('Новая категория доставки');
    else
        $data['city'] = __('Новая доставка');

    $data['enabled'] = 1;
    $data['PID'] = $_GET['cat'];

    $data = $PHPShopGUI->valid($data, 'flag', 'price', 'price_null', 'price_null_enabled', 'taxa', 'ofd_nds', 'num', 'city_select', 'icon', 'payment', 'data_fields', 'comment', 'sum_max', 'sum_min', 'weight_max', 'weight_min', 'servers', 'warehouse');

    $PHPShopGUI->setActionPanel(__("Доставка") . ' &rarr; ' . $data['city'], false, array('Создать и редактировать', 'Сохранить и закрыть'));

    // Наименование
    $Tab_info = $PHPShopGUI->setField("Название", $PHPShopGUI->setInputText(false, 'city_new', $data['city'], '100%') . $PHPShopGUI->setInput('hidden', 'is_folder_new', $data['is_folder']));

    $PHPShopDeliveryArray = new PHPShopDeliveryArray(array('is_folder' => "='1'"));
    $CategoryDeliveryArray = $PHPShopDeliveryArray->getArray();

    $CategoryDeliveryArray[0]['city'] = '- ' . __('Корневой уровень') . ' -';
    $CategoryDeliveryArray[0]['id'] = 0;

    foreach ($CategoryDeliveryArray as $val) {
        $city_value[] = array($val['city'], $val['id'], $data['PID']);
    }

    $_GET['parent_to'] = $data['PID'];

    $PHPShopCategoryArray = new PHPShopCategoryArray();
    $CategoryArray = $PHPShopCategoryArray->getArray();

    if (is_array($CategoryArray))
        $GLOBALS['count'] = count($CategoryArray);

    $CategoryArray[0]['name'] = '- ' . __('Корневой уровень') . ' -';
    $tree_array = array();

    foreach ($PHPShopCategoryArray->getKey('parent_to.id', true) as $k => $v) {
        foreach ($v as $cat) {
            $tree_array[$k]['sub'][$cat] = $CategoryArray[$cat]['name'];
        }
        $tree_array[$k]['name'] = $CategoryArray[$k]['name'];
        $tree_array[$k]['id'] = $k;
        if (!empty($data['parent_to']) and $k == $data['parent_to'])
            $tree_array[$k]['selected'] = true;
    }

    $GLOBALS['tree_array'] = &$tree_array;

    // Допкаталоги
    $dop_cat_array = preg_split('/,/', $data['categories'], -1, PREG_SPLIT_NO_EMPTY);
    $tree_select = $tree_select = null;

    if (!empty($tree_array[0]['sub']) and is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator(@$tree_array[$k], 1, $k, $dop_cat_array);


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


    $tree_select = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container="body"  data-style="btn btn-default btn-sm" name="categories[]"  data-width="100%" multiple>' . $tree_select . '</select>';


    // Выбор каталога
    if (!$catalog)
        $Tab_info .= $PHPShopGUI->setField("Каталог", $PHPShopGUI->setSelect('PID_new', $city_value, '100%'));

    // Вывод
    $Tab_info .= $PHPShopGUI->setField("Статус", $PHPShopGUI->setCheckbox('enabled_new', 1, null, $data['enabled']));
    $Tab_info .= $PHPShopGUI->setField("Доставка по умолчанию", $PHPShopGUI->setCheckbox('flag_new', 1, null, $data['flag']));

    // Цены
    $Tab_price = $PHPShopGUI->setField("Стоимость", $PHPShopGUI->setInputText(false, 'price_new', $data['price'], '150', $PHPShopSystem->getDefaultValutaCode()));

    $Tab_price .= $PHPShopGUI->setField("Бесплатная доставка свыше", $PHPShopGUI->setInputText(false, 'price_null_new', $data['price_null'], '150', $PHPShopSystem->getDefaultValutaCode()) . $PHPShopGUI->setCheckbox('price_null_enabled_new', 1, "Учитывать", $data['price_null_enabled']));

    // Категори товаров
    $Tab_price .= $PHPShopGUI->setField('Категории', $PHPShopGUI->setHelp('Выберите категории товаров для бесплатной доставки.') .
            $PHPShopGUI->setCheckbox("categories_check_new", 1, "Учитывать категории товара", $data['categories_check']) . '<br>' .
            $PHPShopGUI->setCheckbox("categories_all", 1, "Выбрать все категории?", 0) . '<br>' . $tree_select);

    // Такса
    $Tab_price .= $PHPShopGUI->setField(sprintf("Такса за каждые %s г веса", $PHPShopDelivery->fee), $PHPShopGUI->setInputText(false, 'taxa_new', $data['taxa'], '150', $PHPShopSystem->getDefaultValutaCode()) .
            $PHPShopGUI->setHelp(sprintf('Используется для задания дополнительной тарификации (например, для "Почта России").<br>Каждые дополнительные %s грамм свыше базовых %s грамм будут стоить указанную сумму.', $PHPShopDelivery->fee, $PHPShopDelivery->fee)));

    if ($data['ofd_nds'] == '')
        $data['ofd_nds'] = $PHPShopSystem->getParam('nds');

    $Tab_price .= $PHPShopGUI->setField("Значение НДС", $PHPShopGUI->setInputText(null, 'ofd_nds_new', $data['ofd_nds'], 100, '%'));

    // Тип сортировки
    $Tab_info .= $PHPShopGUI->setField("Приоритет", $PHPShopGUI->setInputText('№', "num_new", $data['num'], 150));

    // Настройка выбора городов из БД
    $city_select_value[] = array('Не использовать', 0, $data['city_select']);
    $city_select_value[] = array('Только Регионы и города РФ', 1, $data['city_select']);
    $city_select_value[] = array('Все страны мира', 2, $data['city_select']);

    if (!$catalog)
        $Tab_info .= $PHPShopGUI->setField("Помощь подбора", $PHPShopGUI->setSelect('city_select_new', $city_select_value, null, true));

    $Tab1 = $PHPShopGUI->setCollapse('Информация', $Tab_info);

    $Tab1 .= $PHPShopGUI->setCollapse('Внешний вид', $PHPShopGUI->setField("Изображение", $PHPShopGUI->setIcon($data['icon'], "icon_new", false)) .
            $PHPShopGUI->setField("Комментарий", $PHPShopGUI->setTextarea('comment_new', $data['comment'], false)));

    $PHPShopPaymentArray = new PHPShopPaymentArray(array('enabled' => "='1'"));
    if (strstr($data['payment'], ","))
        $payment_array = explode(",", $data['payment']);
    else
        $payment_array[] = $data['payment'];

    $PaymentArray = $PHPShopPaymentArray->getArray();
    if (is_array($PaymentArray))
        foreach ($PaymentArray as $payment) {

            if (in_array($payment['id'], $payment_array))
                $payment_check = $payment['id'];
            else
                $payment_check = null;
            $payment_value[] = array($payment['name'], $payment['id'], $payment_check);
        }

    // Оплаты
    if (@$_GET['target'] != 'cat') {
        $Tab2 = $PHPShopGUI->setField("Блокировка оплат", $PHPShopGUI->setSelect('payment_new[]', $payment_value, '100%', null, false, $search = false, false, 1, true));


        $Tab2 .= $PHPShopGUI->setField('Не изменять стоимость', $PHPShopGUI->setRadio('is_mod_new', 1, 'Выключить', $data['is_mod'], true, 'text-warning') . $PHPShopGUI->setRadio('is_mod_new', 2, 'Включить', $data['is_mod']));
    }


    // Склады
    $PHPShopOrmWarehouse = new PHPShopOrm($GLOBALS['SysValue']['base']['warehouses']);
    $dataWarehouse = $PHPShopOrmWarehouse->select(array('*'), array('enabled' => "='1'"), array('order' => 'num DESC'), array('limit' => 100));
    $warehouse_value[] = array(__('Общий склад'), 0, $data['warehouse']);
    if (is_array($dataWarehouse)) {
        foreach ($dataWarehouse as $val) {
            $warehouse_value[] = array($val['name'], $val['id'], $data['warehouse']);
        }
    }

    $Tab1 .= $PHPShopGUI->setCollapse('Дополнительно', $PHPShopGUI->setField("Витрины", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/')) .
            $PHPShopGUI->setField("Склад для списания", $PHPShopGUI->setSelect('warehouse_new', $warehouse_value, 300)));

    // Внешний код
    $Tab1 .= $PHPShopGUI->setCollapse('Интеграция', $PHPShopGUI->setField('Внешний код', $PHPShopGUI->setInputText(null, 'external_code_new', $data['external_code'], '100%')));

    // Блокировки
    if (empty($_GET['target']) or $_GET['target'] != 'cat') {
        $Tab2 .= $PHPShopGUI->setField("Блокировка при стоимости более", $PHPShopGUI->setInputText(null, "sum_max_new", $data['sum_max'], 150, $PHPShopSystem->getDefaultValutaCode()));
        $Tab2 .= $PHPShopGUI->setField("Блокировка при стоимости менее", $PHPShopGUI->setInputText(null, "sum_min_new", $data['sum_min'], 150, $PHPShopSystem->getDefaultValutaCode()));
        $Tab2 .= $PHPShopGUI->setField("Блокировка при весе более", $PHPShopGUI->setInputText(null, "weight_max_new", $data['weight_max'], 150, __('грамм')));
        $Tab2 .= $PHPShopGUI->setField("Блокировка при весе менее", $PHPShopGUI->setInputText(null, "weight_min_new", $data['weight_min'], 150, __('грамм')));

        // Объемы
        $Tab2 .= $PHPShopGUI->setField("Блокировка при длине более", $PHPShopGUI->setInputText(null, "length_max_new", $data['length_max'], 150, __('см')));
        $Tab2 .= $PHPShopGUI->setField("Блокировка при высоте более", $PHPShopGUI->setInputText(null, "height_max_new", $data['height_max'], 150, __('см')));
        $Tab2 .= $PHPShopGUI->setField("Блокировка при ширине более", $PHPShopGUI->setInputText(null, "width_max_new", $data['width_max'], 150, __('см')));
        $Tab2 .= $PHPShopGUI->setField("Блокировка при сумме сторон более", $PHPShopGUI->setInputText(null, "sum_side_max_new", $data['sum_side_max'], 150, __('см')));
    }

    if (!empty($Tab2))
        $Tab1 .= $PHPShopGUI->setCollapse('Блокировка', $Tab2);

    // Цены
    if (!$catalog)
        $Tab1 .= $PHPShopGUI->setCollapse('Цены', $Tab_price);


    // Дополнительные поля
    if (!$catalog)
        $Tab2 = $PHPShopGUI->loadLib('tab_option', $data);

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод формы закладки
    if (!$catalog)
        $PHPShopGUI->setTab(array("Основное", $Tab1, true, false, true), array("Адреса пользователя", $Tab2));
    else
        $PHPShopGUI->setTab(array("Основное", $Tab1, true, false, true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "ОК", "right", 70, "", "but", "actionInsert.delivery.create");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция записи
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules;

    $PHPShopOrm->updateZeroVars('flag_new', 'enabled_new', 'price_null_enabled_new', 'categories_check_new','paid_new');

    $_POST['icon_new'] = iconAdd('icon_new');

    // Оплаты
    if (isset($_POST['payment_new'])) {
        if (is_array($_POST['payment_new']))
            $_POST['payment_new'] = @implode(',', $_POST['payment_new']);
    }

    // Категории товаров
    $_POST['categories_new'] = "";
    if (is_array($_POST['categories']) and $_POST['categories'][0] != 'null') {

        $_POST['categories_check_new'] = 1;

        foreach ($_POST['categories'] as $v)
            if (!empty($v) and ! strstr($v, ','))
                $_POST['categories_new'] .= $v . ",";
    } else
        $_POST['categories_check_new'] = 0;

    // Мультибаза
    if (is_array($_POST['servers'])) {
        $_POST['servers_new'] = "";
        foreach ($_POST['servers'] as $v)
            if ($v != 'null' and ! strstr($v, ',') and $v != null and ! strstr($_POST['servers_new'], "i" . $v . "i"))
                $_POST['servers_new'] .= "i" . $v . "i";
    }

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->insert($_POST);

    if ($_POST['saveID'] == 'Создать и редактировать')
        header('Location: ?path=' . $_GET['path'] . '&id=' . $action);
    else
        header('Location: ?path=' . $_GET['path']);

    return $action;
}

// Добавление изображения 
function iconAdd($name = 'icon_new') {

    // Папка сохранения
    $path = '/UserFiles/Image/';

    // Копируем от пользователя
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        if (in_array($_FILES['file']['ext'], array('gif', 'png', 'jpg'))) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'])) {
                $file = $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'];
            }
        }
    }

    // Читаем файл из URL
    elseif (!empty($_POST['furl'])) {
        $file = $_POST[$name];
    }

    // Читаем файл из файлового менеджера
    elseif (!empty($_POST[$name])) {
        $file = $_POST[$name];
    }

    if (!empty($file)) {
        return $file;
    }
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$_POST = $PHPShopGUI->valid($_POST, 'saveID');
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>