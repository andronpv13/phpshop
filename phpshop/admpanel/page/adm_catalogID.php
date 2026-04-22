<?php

PHPShopObj::loadClass("page");

$TitlePage = __('Редактирование Категории') . ' #' . $_GET['id'];
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

            if (!empty($_GET['parent_to']) and $k == $_GET['parent_to'])
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
    global $PHPShopGUI, $PHPShopModules, $PHPShopOrm, $PHPShopSystem;

    // Размер названия поля
    $PHPShopGUI->field_col = 2;
    $PHPShopGUI->addJSFiles('./js/jquery.treegrid.js', './js/bootstrap-datetimepicker.min.js', './page/gui/page.gui.js');

    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));

    // Нет данных
    if (!is_array($data)) {
        header('Location: ?path=' . $_GET['path']);
    }

    $PHPShopGUI->action_select['Предпросмотр'] = array(
        'name' => 'Предпросмотр',
        'url' => '../../page/CID_' . $data['id'] . '.html',
        'action' => 'front',
        'target' => '_blank',
        'class' => $GLOBALS['isFrame']
    );

    $PHPShopGUI->setActionPanel(__("Каталог") . ': ' . $data['name'], array('Создать', 'Предпросмотр', '|', 'Удалить'), array('Сохранить', 'Сохранить и закрыть'));

    // Наименование
    $Tab_info = $PHPShopGUI->setField("Название", $PHPShopGUI->setInputText(false, 'name_new', $data['name'], '100%'));

    $PHPShopCategoryArray = new PHPShopPageCategoryArray();
    $CategoryArray = $PHPShopCategoryArray->getArray();

    $CategoryArray[0]['name'] = '- ' . __('Корневой уровень') . ' -';
    $tree_array = array();
    $PHPShopCategoryArrayKey = $PHPShopCategoryArray->getKey('parent_to.id', true);
    if (is_array($PHPShopCategoryArrayKey))
        foreach ($PHPShopCategoryArrayKey as $k => $v) {
            foreach ($v as $cat) {
                $tree_array[$k]['sub'][$cat] = @$CategoryArray[$cat]['name'];
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

            if ($k == $data['parent_to'])
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
    var cat="' . intval($_GET['id']) . '";
    </script>';

    // Выбор каталога
    //$Tab_info .= $PHPShopGUI->setField("Размещение", $tree_select);

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

    if (empty($GLOBALS['isFrame'])) {

        // Левый сайдбар
        $sidebarleft[] = array('title' => 'Категории', 'content' => $tree, 'title-icon' => '<span class="glyphicon glyphicon-plus addNewElement" data-toggle="tooltip" data-placement="top" title="' . __('Добавить каталог') . '"></span>');
        $PHPShopGUI->setSidebarLeft($sidebarleft, 3);
        $PHPShopGUI->sidebarLeftCell = 3;
    }

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "Удалить", "right", 70, "", "but", "actionDelete.page.edit") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.page.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.page.edit");

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
 * Экшен сохранения
 */
function actionSave() {

    // Сохранение данных
    actionUpdate();

    header('Location: ?path=' . $_GET['path']);
}

/**
 * Экшен обновления
 * @return bool 
 */
function actionUpdate() {
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

    // Корректировка пустых значений
    $PHPShopOrm->updateZeroVars('menu_new');

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));

    return array('success' => $action);
}

// Функция удаления
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);
    $action = $PHPShopOrm->delete(array('id' => '=' . intval($_POST['rowID'])));

    return array("success" => $action);
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>