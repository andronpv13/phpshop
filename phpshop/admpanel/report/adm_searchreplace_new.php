<?php

PHPShopObj::loadClass('category');

$TitlePage = __('Создание Переадресации Поиска');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['search_base']);

// Построение дерева категорий
function treegenerator($array, $i, $parent) {
    global $tree_array;
    $del = '&brvbar;&nbsp;&nbsp;&nbsp;&nbsp;';
    $tree = $tree_select = $check = false;
    $del = str_repeat($del, $i);
    if (!empty($array) and is_array($array['sub'])) {
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
                //$i++;
            }

            $tree .= '<tr class="treegrid-' . $k . ' treegrid-parent-' . $parent . ' data-tree">
		<td><a href="?path=catalog&id=' . $k . '">' . $v . '</a></td>
                    </tr>';

            $tree_select .= $check['select'];
            $tree .= $check['tree'];
        }
    }
    return array('select' => $tree_select, 'tree' => $tree);
}

// Выбор каталога
function viewCatalog() {

    $PHPShopCategoryArray = new PHPShopCategoryArray();
    $CategoryArray = $PHPShopCategoryArray->getArray();

    $CategoryArray[0]['name'] = '- ' . __('Выбрать каталог') . ' -';
    $tree_array = array();

    foreach ($PHPShopCategoryArray->getKey('parent_to.id', true) as $k => $v) {
        foreach ($v as $cat) {
            $tree_array[$k]['sub'][$cat] = $CategoryArray[$cat]['name'];
        }
        $tree_array[$k]['name'] = $CategoryArray[$k]['name'];
        $tree_array[$k]['id'] = $k;
    }


    $GLOBALS['tree_array'] = &$tree_array;

    $tree_select = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container="" data-width="100%" data-style="btn btn-default btn-sm" name="category_new"><option value="0">' . $CategoryArray[0]['name'] . '</option>';

    if (!empty($tree_array) and is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator(@$tree_array[$k], 1, @$data['category']);

            if (!empty($data['category']) and $k == $data['category'])
                $selected = 'selected';
            else
                $selected = null;

            if (empty($tree_array[$k]))
                $disabled = null;
            else
                $disabled = ' disabled';

            $tree_select .= '<option value="' . $k . '" ' . $selected . $disabled . '>' . $v . '</option>';

            $tree_select .= $check['select'];
        }
    $tree_select .= '</select>';

    return $tree_select;
}

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage;


    // Размер названия поля
    $PHPShopGUI->field_col = 2;
    $PHPShopGUI->setActionPanel($TitlePage, false, array('Сохранить и закрыть'));
    $PHPShopGUI->addJSFiles('./js/jquery.tagsinput.min.js', './report/gui/report.gui.js');
    $PHPShopGUI->addCSSFiles('./css/jquery.tagsinput.css');

    // Передача данных
    if (!empty($_GET['data']))
        $data = $_GET['data'];

    $data['enabled'] = 1;
    $data = $PHPShopGUI->valid($data, 'name', 'uid', 'category');

    // Содержание закладки 1
    $Tab1 = $PHPShopGUI->setField("Запрос", $PHPShopGUI->setInputText(false, "name_new", str_replace(array('i', 'ii'), array('', ','), $data['name'])) . $PHPShopGUI->setRadio("enabled_new", 1, "Показывать", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new", 0, "Скрыть", $data['enabled']));

    // Товары
    $Tab1 .= $PHPShopGUI->setField('Товары', $PHPShopGUI->setTextarea('uid_new', $data['uid'], false, false, false, __('Укажите ID товаров или воспользуйтесь') . ' <a href="#" data-target="#uid_new"  class="btn btn-sm btn-default tag-search"><span class="glyphicon glyphicon-search"></span> ' . __('поиском товаров') . '</a>'));

    // Каталог
    $Tab1 .= $PHPShopGUI->setField('Каталог', viewCatalog($data['category']), false, 'Переадресация на страницу списка товаров в выбранном каталоге');

    // URL
    $Tab1 .= $PHPShopGUI->setField("URL", $PHPShopGUI->setInputText('http://', "url_new", $data['url']));

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "ОК", "right", 70, "", "but", "actionInsert.order.edit");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция записи
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules;

    if (strpos($_POST['name_new'], ',')) {
        $name_new = null;
        $name = explode(",", $_POST['name_new']);
        foreach ($name as $v)
            $name_new .= "i" . $v . "i";

        $_POST['name_new'] = $name_new;
    } else
        $_POST['name_new'] = "i" . $_POST['name_new'] . "i";

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->insert($_POST);

    header('Location: ?path=' . $_GET['path']);

    return $action;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>