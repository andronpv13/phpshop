<?php

PHPShopObj::loadClass("page");

$TitlePage = __('Новая категория страниц');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page_categories']);

// Построение дерева категорий
function treegenerator($array, $i, $parent) {
    global $tree_array;
    $del = '&brvbar;&nbsp;&nbsp;&nbsp;&nbsp;';
    $tree = $tree_select = $check = false;
    $del = str_repeat($del, $i);
    if (!empty($array['sub']) and is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {

            $check = treegenerator(@$tree_array[$k], $i + 1, $k);

            if (!empty($_GET['cat']) and $k == $_GET['cat'])
                $selected = 'selected';
            else
                $selected = null;

            if (empty($check['select'])) {
                $tree_select .= '<option value="' . $k . '" ' . $selected . '>' . $del . $v . '</option>';
                $i = 1;
            } else {
                $tree_select .= '<option value="' . $k . '" ' . $selected . '>' . $del . $v . '</option>';
            }

            $tree .= '<tr class="treegrid-' . $k . ' treegrid-parent-' . $parent . ' data-tree">
		<td><a href="?path=page.catalog&id=' . $k . '">' . $v . '</a></td>
                    </tr>';

            $tree_select .= $check['select'];
            $tree .= $check['tree'];
        }
    }

    return array('select' => $tree_select, 'tree' => $tree);
}

/**
 * Экшен загрузки форм редактирования
 */
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage, $PHPShopSystem;

    // Размер названия поля
    $PHPShopGUI->field_col = 2;
    $PHPShopGUI->addJSFiles('./js/jquery.treegrid.js', './js/bootstrap-datetimepicker.min.js', './page/gui/page.gui.js');

    // Начальные данные
    $data = array();
    $data['num'] = 1;
    $data = $PHPShopGUI->valid($data, 'name', 'servers', 'menu', 'icon', 'title', 'description', 'keywords', 'content', 'parent_to');

    $PHPShopGUI->setActionPanel($TitlePage, false, array('Создать и редактировать', 'Сохранить и закрыть'));

    // Наименование
    $Tab_info = $PHPShopGUI->setField("Название", $PHPShopGUI->setInputArg(array('name' => 'name_new', 'type' => 'text.requared', 'value' => $data['name'])));


    $PHPShopCategoryArray = new PHPShopPageCategoryArray();
    $CategoryArray = $PHPShopCategoryArray->getArray();

    $CategoryArray[0]['name'] = '- ' . __('Корневой уровень') . ' -';
    $tree_array = array();

    $PHPShopCategoryArrayKey = $PHPShopCategoryArray->getKey('parent_to.id', true);
    if (is_array($PHPShopCategoryArrayKey))
        foreach ($PHPShopCategoryArrayKey as $k => $v) {
            foreach ($v as $cat) {
                $tree_array[$k]['sub'][$cat] = $CategoryArray[$cat]['name'];
            }
            $tree_array[$k]['name'] = @$CategoryArray[$k]['name'];
            $tree_array[$k]['id'] = $k;
            if (!empty($data['parent_to']) and $k == $data['parent_to'])
                $tree_array[$k]['selected'] = true;
        }

    $GLOBALS['tree_array'] = &$tree_array;
    $_GET['parent_to'] = $data['parent_to'];

    $tree_select = '<select class="selectpicker show-menu-arrow hidden-edit" data-container="body"  data-style="btn btn-default btn-sm" name="parent_to_new" data-width="100%"><option value="0">' . $CategoryArray[0]['name'] . '</option>';
    $tree = '<table class="table table-hover">';
    if ($k == $data['parent_to'])
        $selected = 'selected';
    if (is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator(@$tree_array[$k], 1, $k);

            $tree .= '<tr class="treegrid-' . $k . ' data-tree">
		<td><a href="?path=page.catalog&id=' . $k . '">' . $v . '</a></td>
                    </tr>';

            if ($k == $_GET['cat'])
                $selected = 'selected';
            else
                $selected = null;

            $tree_select .= '<option value="' . $k . '"  ' . $selected . '>' . $v . '</option>';

            $tree_select .= $check['select'];
            $tree .= $check['tree'];
        }
    $tree_select .= '</select>';
    $tree .= '</table>
        <script>
    var cat="' . intval($_GET['cat']) . '";
    </script>';

    // Выбор каталога
    //$Tab_info.= $PHPShopGUI->setField("Размещение", $tree_select);

    $Tab_info .= $PHPShopGUI->setField("Приоритет", $PHPShopGUI->setInputText(false, 'num_new', $data['num'], '100'));

    $Tab_info .= $PHPShopGUI->setField("Витрины", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/'));
    $Tab_info .= $PHPShopGUI->setField("Опции вывода", $PHPShopGUI->setCheckbox('menu_new', 1, 'Главное меню', $data['menu']));

    $Tab1 = $PHPShopGUI->setCollapse('Информация', $Tab_info);

    // Иконка
    $Tab_icon = $PHPShopGUI->setField("Изображение", $PHPShopGUI->setIcon($data['icon'], "icon_new", false));
    $Tab1 .= $PHPShopGUI->setCollapse('Иконка', $Tab_icon);

    // SEO
    $Tab_seo = $PHPShopGUI->setField("Title", $PHPShopGUI->setTextarea("title_new", $data['title']));
    $Tab_seo .= $PHPShopGUI->setField("Description", $PHPShopGUI->setTextarea("description_new", $data['description']));
    $Tab_seo .= $PHPShopGUI->setField("Keywords", $PHPShopGUI->setTextarea("keywords_new", $data['keywords']));
    $Tab1 .= $PHPShopGUI->setCollapse('SEO / Мета-данные', $Tab_seo);

    // Редактор
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $editor = new Editor('content_new');
    $editor->Height = '550';
    $editor->ToolbarSet = 'Normal';
    $editor->Value = $data['content'];
    $Tab2 = $editor->AddGUI();

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1), array("Описание", $Tab2));

    // Левый сайдбар
    $sidebarleft[] = array('title' => 'Категории', 'content' => $tree, 'title-icon' => '<span class="glyphicon glyphicon-plus addNewElement" data-toggle="tooltip" data-placement="top" title="' . __('Добавить каталог') . '"></span>');
    $PHPShopGUI->setSidebarLeft($sidebarleft, 3);
    $PHPShopGUI->sidebarLeftCell = 3;

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "ОК", "right", 70, "", "but", "actionInsert.page.create");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Добавление изображения 
function iconAdd() {
    global $PHPShopSystem;

    // Папка сохранения
    $path = $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $PHPShopSystem->getSerilizeParam('admoption.image_result_path');

    // Копируем от пользователя
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        $_FILES['file']['name'] = PHPShopString::toLatin(str_replace('.' . $_FILES['file']['ext'], '', PHPShopString::utf8_win1251($_FILES['file']['name']))) . '.' . $_FILES['file']['ext'];
        if (in_array($_FILES['file']['ext'], array('gif', 'png', 'jpg', 'jpeg', 'svg'))) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'])) {
                $file = $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'];
            }
        }
    }

    // Читаем файл из URL
    elseif (!empty($_POST['furl'])) {
        $file = $_POST['icon_new'];
    }

    // Читаем файл из файлового менеджера
    elseif (!empty($_POST['icon_new'])) {
        $file = $_POST['icon_new'];
    }

    if (empty($file))
        $file = '';

    return $file;
}

/**
 * Экшен записи
 * @return bool 
 */
function actionInsert() {
    global $PHPShopModules, $PHPShopOrm;

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    // Мультибаза
    if (is_array($_POST['servers'])) {
        $_POST['servers_new'] = "";
        foreach ($_POST['servers'] as $v)
            if ($v != 'null' and ! strstr($v, ',') and $v != null and ! strstr($_POST['servers_new'], "i" . $v . "i"))
                $_POST['servers_new'] .= "i" . $v . "i";
    }

    $_POST['icon_new'] = iconAdd();

    $action = $PHPShopOrm->insert($_POST);

    if ($_POST['saveID'] == 'Создать и редактировать')
        header('Location: ?path=page.catalog&id=' . $action);
    else
        header('Location: ?path=page.catalog&cat=' . $_POST['category_new']);

    return $action;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>