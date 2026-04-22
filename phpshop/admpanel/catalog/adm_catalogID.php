<?php

PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("page");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("category");

$TitlePage = __('Редактирование Категории') . ' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);

// Построение дерева категорий
function treegenerator($array, $i, $curent, $dop_cat_array) {
    global $tree_array;
    $del = '&brvbar;&nbsp;&nbsp;&nbsp;&nbsp;';
    $tree_select = $tree_select_dop = $check = false;

    $del = str_repeat($del, $i);
    if (!empty($array) and is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {

            $check = treegenerator(@$tree_array[$k], $i + 1, $k, $dop_cat_array);

            if ($k == $_GET['parent_to'])
                $selected = 'selected';
            else
                $selected = null;

            // Проверка зацикливания
            if ($k == $_GET['id'])
                $disabled = ' disabled ';
            else
                $disabled = null;

            // Допкаталоги
            $selected_dop = null;
            if (is_array($dop_cat_array))
                foreach ($dop_cat_array as $vs) {
                    if ($k == $vs)
                        $selected_dop = "selected";
                }

            if (empty($check['select'])) {
                $tree_select .= '<option value="' . $k . '" ' . $selected . $disabled . '>' . $del . $v . '</option>';

                //if ($k < 1000000)
                $tree_select_dop .= '<option value="' . $k . '" ' . $selected_dop . $disabled . '>' . $del . $v . '</option>';

                $i = 1;
            } else {
                $tree_select .= '<option value="' . $k . '" ' . $selected . $disabled . ' >' . $del . $v . '</option>';
                // if ($k < 1000000)
                $tree_select_dop .= '<option value="' . $k . '" ' . $selected_dop . $disabled . '>' . $del . $v . '</option>';
            }

            $tree_select .= $check['select'];
            $tree_select_dop .= $check['select_dop'];
        }
    }
    return array('select' => $tree_select, 'select_dop' => $tree_select_dop);
}

/**
 * Экшен загрузки форм редактирования
 */
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $PHPShopOrm, $PHPShopSystem, $PHPShopBase, $isFrame;

    // Размер названия поля
    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->addJSFiles('./js/jquery.treegrid.js', './catalog/gui/catalog.gui.js', './js/bootstrap-treeview.min.js');
    $PHPShopGUI->addCSSFiles('./css/bootstrap-treeview.min.css');

    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_REQUEST['id'])));

    // Нет данных
    if (!is_array($data)) {
        header('Location: ?path=' . $_GET['path']);
    }

    $PHPShopGUI->action_select['Предпросмотр'] = array(
        'name' => 'Предпросмотр',
        'url' => '../../shop/CID_' . $data['id'] . '.html',
        'action' => 'front' . $GLOBALS['isFrame'],
        'target' => '_blank'
    );

    $PHPShopGUI->action_select['Товары'] = array(
        'name' => 'Товары в каталоге',
        'url' => '?path=' . $_GET['path'] . '&cat=' . intval($_GET['id']),
        'class' => $GLOBALS['isFrame']
    );

    $PHPShopGUI->action_select['Удалить каталог'] = array(
        'name' => 'Удалить <span class="glyphicon glyphicon-trash"></span>',
        'locale' => true,
        'action' => 'delete-category',
        'url' => '#'
    );


    // Наименование
    $Tab_info = $PHPShopGUI->setField("Название", $PHPShopGUI->setInputText(false, 'name_new', $data['name'], '100%'));

    // Права менеджеров
    if ($PHPShopSystem->ifSerilizeParam('admoption.rule_enabled', 1) and ! $PHPShopBase->Rule->CheckedRules('catalog', 'remove')) {
        $where = array('secure_groups' => " REGEXP 'i" . $_SESSION['idPHPSHOP'] . "i' or secure_groups = ''");
        $secure_groups = true;
    } else
        $where = $secure_groups = false;

    $PHPShopCategoryArray = new PHPShopCategoryArray($where);
    $CategoryArray = $PHPShopCategoryArray->getArray();
    $GLOBALS['count'] = count($CategoryArray);

    $CategoryArray[0]['name'] = '- ' . __('Корневой уровень') . ' -';
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
    $_GET['parent_to'] = $data['parent_to'];

    // Блокировка ссылки на товары
    if (!empty($GLOBALS['tree_array'][$data['id']]['sub'])) {
        $PHPShopGUI->action_select['Товары'] = array(
            'name' => 'Товары в каталоге',
            'url' => '?path=' . $_GET['path'] . '&cat=' . intval($_GET['id']),
            'class' => 'viewproduct disabled',
        );
    }

    $PHPShopGUI->setActionPanel('<span class="' . $isFrame . '">' . __("Каталог") . ': </span>' . $data['name'] . ' [ID ' . $data['id'] . ']', array('Товары', 'Создать', 'Предпросмотр', '|', 'Удалить каталог'), array('Сохранить', 'Сохранить и закрыть'));

    // Допкаталоги
    $dop_cat_array = preg_split('/#/', $data['dop_cat'], -1, PREG_SPLIT_NO_EMPTY);

    $tree_select = $tree_select_dop = null;

    if ($k == $data['parent_to'])
        $selected = 'selected';
    if (!empty($tree_array) and is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator(@$tree_array[$k], 1, $k, $dop_cat_array);


            if ($k == $data['parent_to'])
                $selected = 'selected';
            else
                $selected = null;

            // Допкаталоги
            $selected_dop = null;
            if (is_array($dop_cat_array))
                foreach ($dop_cat_array as $vs) {
                    if ($k == $vs)
                        $selected_dop = "selected";
                }


            // Проверка зацикливания
            if ($k == $_GET['id'])
                $disabled = ' disabled ';
            else
                $disabled = null;

            $tree_select .= '<option value="' . $k . '"  ' . $selected . $disabled . '>' . $v . '</option>';

            $tree_select_dop .= '<option value="' . $k . '" ' . $selected_dop . $disabled . '>' . $v . '</option>';

            $tree_select .= $check['select'];
            $tree_select_dop .= $check['select_dop'];
        }

    $tree_select_dop = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container=""  data-style="btn btn-default btn-sm" name="dop_cat[]" data-width="100%" multiple><option value="0">' . $CategoryArray[0]['name'] . '</option>' . $tree_select_dop . '</select>';

    $tree_select = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container=""  data-style="btn btn-default btn-sm" name="parent_to_new"  data-width="100%"><option value="0">' . $CategoryArray[0]['name'] . '</option>' . $tree_select . '</select>';

    // Выбор каталога
    $Tab_info .= $PHPShopGUI->setField("Размещение", $tree_select);

    // Сетка
    $num_row_adm_value[] = array('1', 1, $data['num_row']);
    $num_row_adm_value[] = array('2', 2, $data['num_row']);
    $num_row_adm_value[] = array('3', 3, $data['num_row']);
    $num_row_adm_value[] = array('4', 4, $data['num_row']);
    $num_row_adm_value[] = array('5', 5, $data['num_row']);
    $num_row_adm_value[] = array('6', 6, $data['num_row']);


    $Tab_info .= $PHPShopGUI->setField("Товарная сетка в каталоге", $PHPShopGUI->setSelect('num_row_new', $num_row_adm_value, 50), 1, 'Товаров в длину 
	  для каталогов по умолчанию. Сетки 5 и 6 поддерживаются не всеми шаблонами');

    $vid = $PHPShopGUI->setCheckbox('vid_new', 1, 'Не выводить внутренние подкаталоги в меню', $data['vid']) . '<br>';
    $vid .= $PHPShopGUI->setCheckbox('podcatalog_view_new', 1, 'Не выводить внутренние подкаталоги в товарах', $data['podcatalog_view']) . '<br>';
    $vid .= $PHPShopGUI->setCheckbox('skin_enabled_new', 1, 'Скрыть каталог', $data['skin_enabled']) . '<br>';
    $vid .= $PHPShopGUI->setCheckbox('menu_new', 1, 'Главное меню', $data['menu']) . '<br>';
    $vid .= $PHPShopGUI->setCheckbox('tile_new', 1, 'Плитка на главной', $data['tile']) . '<br>';
    $Tab_info .= $PHPShopGUI->setField("Опции вывода", $vid);

    // Товаров на странице
    $Tab_info .= $PHPShopGUI->setLine() . $PHPShopGUI->setField("Товаров на странице", $PHPShopGUI->setInputText(false, 'num_cow_new', $data['num_cow'], '100', __('шт.')));

    // Тип сортировки
    $order_by_value[] = array(__('по имени'), 1, $data['order_by']);
    $order_by_value[] = array(__('по цене'), 2, $data['order_by']);
    $order_by_value[] = array(__('по складу'), 3, $data['order_by']);
    $order_to_value[] = array(__('возрастанию'), 1, $data['order_to']);
    $order_to_value[] = array(__('убыванию'), 2, $data['order_to']);

    $Tab_info .= $PHPShopGUI->setField("Сортировка", $PHPShopGUI->setInputText(null, "num_new", $data['num'], 100, false, 'left') . '&nbsp' .
            $PHPShopGUI->setSelect('order_by_new', $order_by_value, 120) . $PHPShopGUI->setSelect('order_to_new', $order_to_value, 120) . '<br>' . $PHPShopGUI->setCheckbox('order_set', 1, 'Применить сейчас ко всем каталогам', 0));

    // Дополнительные каталоги
    $Tab_info .= $PHPShopGUI->setField('Дополнительные каталоги', $tree_select_dop, 1, 'Подкаталоги одновременно выводятся в нескольких каталогах.');

    $Tab1 = $PHPShopGUI->setCollapse('Информация', $Tab_info);

    // Цвет
    $Tab_icon = $PHPShopGUI->setField("Инверсия цвета текста", $PHPShopGUI->setInputText(null, "color_new", (int) $data['color'], 100, '%'));

    // Иконка
    $Tab_icon .= $PHPShopGUI->setField("Изображение", $PHPShopGUI->setIcon($data['icon'], "icon_new", false, ['load' => true, 'server' => true, 'url' => true, 'multi' => false, 'search' => true]));

    $Tab1 .= $PHPShopGUI->setCollapse('Иконка', $Tab_icon);

    // Редактор
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $editor = new Editor('content_new');
    $editor->Height = '450';
    $editor->Config['EditorAreaCSS'] = chr(47) . "phpshop" . chr(47) . "templates" . chr(47) . $PHPShopSystem->getValue('skin') . chr(47) . $PHPShopBase->getParam('css.default');
    $editor->ToolbarSet = 'Normal';
    $editor->Value = $data['content'];
    $Tab2 = $editor->AddGUI();

    // AI
    $Tab2 .= $PHPShopGUI->setAIHelpButton('content_new', 300, 'catalog_content');

    // Заголовки
    $Tab7 = $PHPShopGUI->loadLib('tab_headers', $data);

    // Права
    if ($PHPShopSystem->ifSerilizeParam('admoption.rule_enabled', 1))
        $Tab9 = $PHPShopGUI->setCollapse('Каталог могут редактировать', $PHPShopGUI->loadLib('tab_secure', $data), 'in', false);

    // Добавление закладки характеристики если нет подкаталогов
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
    $subcategory_data = $PHPShopOrm->select(array('id'), array('parent_to' => '=' . intval($data['id'])), false, array('limit' => 1));

    // Кэш фильтра
    if ($PHPShopSystem->getSerilizeParam("admoption.filter_cache_enabled") == 1) {
        $cache = $PHPShopGUI->setCheckbox('reset_cache', 1, __('Очистить кэш характеристик фильтра отбора по параметрам'), false);
        $Tab8 = $PHPShopGUI->setCollapse(__('Кэширование характеристик'), $cache, 'in', false);
    }

    $help_sort = $PHPShopGUI->setHelp('Не забудьте выбрать значение этих характеристик в товарах во вкладке Характеристики. Можно это сделать пакетно <a href="https://wiki.phpshop.ru/rabota-s-bazoi/import-i-eksport#csv" target="_blank" title="Перейти">через csv файл</a>');
    $Tab8 = $PHPShopGUI->setCollapse('Характеристики', $PHPShopGUI->loadLib('tab_sorts', $data) . $help_sort, 'in', false);

    if (!is_array($subcategory_data))
        $Tab8 .= $PHPShopGUI->setCollapse('Варианты подтипов', tab_parent($data) . $PHPShopGUI->setHelp('Управление вариантами подтипов товаров находится в разделе <a href="?path=sort.parent" title="Перейти">Варианты подтипов</a>'), 'in', true);
    else
        $Tab8 .= $PHPShopGUI->setCollapse('Варианты подтипов', $PHPShopGUI->setHelp('Варианты подтипов доступны только в подкаталогах с товарами.'), 'in', true);


    // Мультибаза
    $Tab9 .= $PHPShopGUI->setCollapse('Показывать на витринах', $PHPShopGUI->loadLib('tab_multibase', $data));

    // Склад
    if (empty($data['ed_izm']))
        $ed_izm = __('шт.');
    else
        $ed_izm = $data['ed_izm'];

    // Вес
    $Tab_info_size = $PHPShopGUI->setField('Вес', $PHPShopGUI->setInputText(false, 'weight_new', $data['weight'], 100, __('г&nbsp;&nbsp;&nbsp;&nbsp;')), 'left');

    // Габариты
    $Tab_info_size .= $PHPShopGUI->setField('Длина', $PHPShopGUI->setInputText(false, 'length_new', $data['length'], 100, __('см&nbsp;')), 'left');
    $Tab_info_size .= $PHPShopGUI->setField('Ширина', $PHPShopGUI->setInputText(false, 'width_new', $data['width'], 100, __('см&nbsp;')), 'left');
    $Tab_info_size .= $PHPShopGUI->setField('Высота', $PHPShopGUI->setInputText(false, 'height_new', $data['height'], 100, __('см&nbsp;')), 'left');
    $Tab_info_size .= $PHPShopGUI->setField('Единица измерения', $PHPShopGUI->setInputText(false, 'ed_izm_new', $ed_izm, 100));

    $Tab9 .= $PHPShopGUI->setCollapse('Габариты по умолчанию', $Tab_info_size);

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1), array("Описание", $Tab2), array("Заголовки", $Tab7), array("Характеристики", $Tab8, true), array("Дополнительно", $Tab9, true));

    // Прогрессбар
    if ($GLOBALS['count'] > 500)
        $treebar = '<div class="progress">
  <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 45%">
    <span class="sr-only">' . __("Загрузка") . '..</span>
  </div>
</div>';
    else
        $treebar = null;

    // Поиск категорий
    $search = '<div class="none" id="category-search" style="padding-bottom:5px;"><div class="input-group input-sm">
                <input type="input" class="form-control input-sm" type="search" id="input-category-search" placeholder="' . __('Искать в категориях...') . '" value="">
                 <span class="input-group-btn">
                  <a class="btn btn-default btn-sm" id="btn-search" type="submit"><span class="glyphicon glyphicon-search"></span></a>
                 </span>
            </div></div>';

    if (empty($GLOBALS['isFrame'])) {

        // Левый сайдбар
        $sidebarleft[] = array('title' => 'Категории', 'content' => $search . '<div id="tree">' . $treebar . '</div>', 'title-icon' => '<span class="glyphicon glyphicon-plus addNewElement" data-toggle="tooltip" data-placement="top" title="' . __('Добавить каталог') . '"></span>&nbsp;<span class="glyphicon glyphicon-chevron-down" data-toggle="tooltip" data-placement="top" title="' . __('Развернуть') . '"></span>&nbsp;<span class="glyphicon glyphicon-chevron-up" data-toggle="tooltip" data-placement="top" title="' . __('Свернуть') . '"></span>&nbsp;<span class="glyphicon glyphicon-search" id="show-category-search" data-toggle="tooltip" data-placement="top" title="' . __('Поиск') . '"></span>');

        $PHPShopGUI->setSidebarLeft($sidebarleft, 3);
        $PHPShopGUI->sidebarLeftCell = 3;
    }


    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "Удалить", "right", 70, "", "but", "actionDelete.catalog.edit") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.catalog.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.catalog.edit");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

/**
 * Экшен сохранения
 */
function actionSave() {

    // Сохранение данных
    actionUpdate();

    header('Location: ?path=catalog.list');
}

/**
 * Экшен обновления
 * @return bool
 */
function actionUpdate() {
    global $PHPShopModules, $PHPShopBase;

    // Характеристики
    $_POST['sort_new'] = serialize($_POST['sort_new']);

    // Проверка прав редактирования
    if ($PHPShopBase->Rule->CheckedRules('catalog', 'rule')) {

        $secure = null;
        if (is_array($_POST['secure_groups_new']))
            foreach ($_POST['secure_groups_new'] as $crid => $value) {
                $secure .= 'i' . $crid . 'i';
                if (!empty($_POST['secure_groups_new']['all'])) {
                    $secure = '';
                    break;
                }
            }

        $_POST['secure_groups_new'] = $secure;
    } else
        unset($_POST['secure_groups_new']);

    // Мультибаза
    if (is_array($_POST['servers'])) {
        $_POST['servers_new'] = "";
        foreach ($_POST['servers'] as $v)
            if ($v != 'null' and ! strstr($v, ',') and $v != null and ! strstr($_POST['servers_new'], "i" . $v . "i"))
                $_POST['servers_new'] .= "i" . $v . "i";
    }

    // Доп каталоги
    $_POST['dop_cat_new'] = "";
    if (is_array($_POST['dop_cat']) and $_POST['dop_cat'][0] != 'null') {
        $_POST['dop_cat_new'] = "#";
        foreach ($_POST['dop_cat'] as $v)
            if ($v != 'null' and ! strstr($v, ','))
                $_POST['dop_cat_new'] .= $v . "#";
    }

    $PHPShopCategory = new PHPShopCategory($_POST['rowID']);
    if ($PHPShopCategory->getParam('icon') != $_POST['icon_new'])
        $_POST['icon_new'] = iconAdd();

    // Очистка кеш характеристик
    if (!empty($_POST['reset_cache'])) {
        $_POST['sort_cache_new'] = '';
        $_POST['sort_cache_created_at_new'] = 0;
    }

    // Смена сортровки у всех
    if (!empty($_POST['order_set'])) {
        $PHPShopOrmCat = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
        $PHPShopOrmCat->update(array('order_by_new' => $_POST['order_by_new'], 'order_to_new' => $_POST['order_to_new']));
    }

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);

    // Корректировка пустых значений
    $PHPShopOrm->updateZeroVars('vid_new', 'skin_enabled_new', 'menu_new', 'tile_new', 'podcatalog_view_new');
    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    $PHPShopOrm->clean();

    // Проверка товаров родителя и перенос товаров в новый каталог
    $PHPShopOrm->clean();
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $check = $PHPShopOrm->select(array('id'), array("category" => "=" . $_POST['parent_to_new']), false, array('limit' => '1'));

    if (is_array($check))
        $PHPShopOrm->update(array("category" => $_POST['rowID']), array("category" => "=" . $_POST['parent_to_new']), false);

    return array('success' => $action);
}

// Добавление изображения
function iconAdd() {
    global $PHPShopSystem;

    // Папка сохранения
    $path = $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $PHPShopSystem->getSerilizeParam('admoption.image_result_path');

    // Сохранять в папки каталогов
    if ($PHPShopSystem->ifSerilizeParam('admoption.image_save_catalog')) {

        $PHPShopCategory = new PHPShopCategory($_POST['rowID']);
        $parent_to = $PHPShopCategory->getParam('parent_to');
        $pathName = ucfirst(PHPShopString::toLatin($PHPShopCategory->getName()));

        if (!empty($parent_to)) {
            $PHPShopCategory = new PHPShopCategory($parent_to);
            $pathName = ucfirst(PHPShopString::toLatin($PHPShopCategory->getName())) . '/' . $pathName;
            $parent_to = $PHPShopCategory->getParam('parent_to');
        }

        if (!empty($parent_to)) {
            $PHPShopCategory = new PHPShopCategory($parent_to);
            $pathName = '/' . ucfirst(PHPShopString::toLatin($PHPShopCategory->getName())) . '/' . $pathName;
        }

        $path .= $pathName . '/';

        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . $path))
            @mkdir($_SERVER['DOCUMENT_ROOT'] . $path, 0777, true);
    }

    // Корекция
    $path = str_replace('//', '/', $path);

    // Копируем от пользователя
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        $_FILES['file']['name'] = PHPShopString::toLatin(str_replace('.' . $_FILES['file']['ext'], '', PHPShopString::utf8_win1251($_FILES['file']['name']))) . '.' . $_FILES['file']['ext'];
        if (in_array($_FILES['file']['ext'], array('gif', 'png', 'jpg', 'jpeg', 'svg', 'webp'))) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'])) {
                $file = $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'];
            }
        }
    }

    // Копируем файл из URL
    elseif (!empty($_POST['furl'])) {
        $file = $_POST['icon_new'];
        $path_parts = pathinfo($file);
        $file_name = $path_parts['basename'];
        $file_ext = PHPShopSecurity::getExt($file_name);
        $file_name = PHPShopString::toLatin(str_replace('.' . $file_ext, '', PHPShopString::utf8_win1251($file_name))) . '.' . $file_ext;

        if (in_array($file_ext, array('gif', 'png', 'jpg', 'jpeg', 'svg', 'webp'))) {
            if (copy($file, $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $path . $file_name)) {
                $file = $GLOBALS['dir']['dir'] . $path . $file_name;
            }
        }
    }

    // Читаем файл из файлового менеджера
    elseif (!empty($_POST['icon_new'])) {
        $file = $_POST['icon_new'];
    }

    if (empty($file))
        $file = '';

    // Нарезка
    if ($PHPShopSystem->ifSerilizeParam('admoption.image_cat') and ! empty($file)) {
        require_once $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/thumb/phpthumb.php';

        // Параметры ресайзинга
        $img_tw = $PHPShopSystem->getSerilizeParam('admoption.img_tw_c');
        $img_th = $PHPShopSystem->getSerilizeParam('admoption.img_th_c');
        $img_tw = empty($img_tw) ? 410 : $img_tw;
        $img_th = empty($img_th) ? 200 : $img_th;
        $img_adaptive = $PHPShopSystem->getSerilizeParam('admoption.image_cat_adaptive');

        // Маленькое изображение (тумбнейл)
        $thumb = new PHPThumb($_SERVER['DOCUMENT_ROOT'] . $file);
        $thumb->setOptions(array('jpegQuality' => $PHPShopSystem->getSerilizeParam('admoption.width_kratko')));

        // Адаптивность
        if (!empty($img_adaptive))
            $thumb->adaptiveResize($img_tw, $img_th);
        else
            $thumb->resize($img_tw, $img_th);

        // Сохранение в webp
        if ($PHPShopSystem->ifSerilizeParam('admoption.image_webp_save')) {
            $thumb->setFormat('WEBP');
            $file = str_replace(['.jpg', '.JPG', '.png', '.PNG', '.gif', '.GIF'], '.webp', $file);
        }

        $thumb->save($_SERVER['DOCUMENT_ROOT'] . $file);
    }

    return $file;
}

// Функция удаления
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $category = new PHPShopCategory((int) $_POST['rowID']);
    $categories = array_column($category->getChildrenCategories(1000, ['id'], false), 'id');
    $categories[] = (int) $_POST['rowID'];

    $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $count = $orm->select(["COUNT('id') as count"], ['category' => sprintf(' IN (%s)', implode(',', $categories))]);
    if ((int) $count['count'] > 0) {
        if ($_POST['products_operation'] === 'delete') {
            $orm->delete(['category' => sprintf(' IN (%s)', implode(',', $categories))]);
        } else {
            $orm->update(['category_new' => 1000004, 'datas_new' => time()], ['category' => sprintf(' IN (%s)', implode(',', $categories))]);
        }
    }

    $action = $PHPShopOrm->delete(['id' => sprintf(' IN (%s)', implode(',', $categories))]);

    return array("success" => $action, 'count' => $count['count']);
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>