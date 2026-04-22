<?php
PHPShopObj::loadClass('category');

$TitlePage = __('Редактирование переадресации поиска').' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['search_base']);

// Построение дерева категорий
function treegenerator($array, $i, $parent) {
    global $tree_array;
    $del = '&brvbar;&nbsp;&nbsp;&nbsp;&nbsp;';
    $tree = $tree_select = $check = false;
    $del = str_repeat($del, $i);
    if (!empty($array['sub']) and is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {

            $check = treegenerator(@$tree_array[$k], $i + 1, $k);

            if ($k == $_GET['parent_to'])
                $selected = 'selected';
            else
                $selected = null;

            if (empty($check['select'])) {
                $tree_select.='<option value="' . $k . '" ' . $selected . '>' . $del . $v . '</option>';
                $i = 1;
            } else {
                $tree_select.='<option value="' . $k . '" ' . $selected . '>' . $del . $v . '</option>';
                //$i++;
            }

            $tree.='<tr class="treegrid-' . $k . ' treegrid-parent-' . $parent . ' data-tree">
		<td><a href="?path=catalog&id=' . $k . '">' . $v . '</a></td>
                    </tr>';

            $tree_select.=$check['select'];
            $tree.=$check['tree'];
        }
    }
    return array('select' => $tree_select, 'tree' => $tree);
}

// Выбор каталога
function viewCatalog($category) {
    
    $_GET['parent_to'] = $category;

    $PHPShopCategoryArray = new PHPShopCategoryArray();
    $CategoryArray = $PHPShopCategoryArray->getArray();

    $CategoryArray[0]['name'] = '- '.__('Выбрать каталог').' -';
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

    if (is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator(@$tree_array[$k], 1, $category);

            if ($k == $category)
                $selected = 'selected';
            else
                $selected = null;

            if (empty($tree_array[$k]))
                $disabled = null;
            else
                $disabled = ' disabled';

            $tree_select.='<option value="' . $k . '" ' . $selected . $disabled . '>' . $v . '</option>';

            $tree_select.=$check['select'];
        }
    $tree_select.='</select>';

    return $tree_select;
}

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopModules;

    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_REQUEST['id'])));
        $PHPShopGUI->addJSFiles('./js/jquery.tagsinput.min.js', './report/gui/report.gui.js');
    $PHPShopGUI->addCSSFiles('./css/jquery.tagsinput.css');

    // Нет данных
    if (!is_array($data)) {
        header('Location: ?path=' . $_GET['path']);
    }

    // Размер названия поля
    $PHPShopGUI->field_col = 2;
    $data['name'] = str_replace('ii', ',', $data['name']);
    $PHPShopGUI->setActionPanel(__("Переадресация поиска") . ' / ' . str_replace('i', '', $data['name']),array('Удалить'), array('Сохранить', 'Сохранить и закрыть'),false);

    // Содержание закладки 1
    $Tab1 = $PHPShopGUI->setField("Запрос", $PHPShopGUI->setInputText(false, "name_new", str_replace(array('i', 'ii'), array('', ','), $data['name'])) . $PHPShopGUI->setRadio("enabled_new", 1, "Показывать", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new", 0, "Скрыть", $data['enabled']));
    
    // Товары
    $Tab1.=$PHPShopGUI->setField('Товары', $PHPShopGUI->setTextarea('uid_new', $data['uid'], false, false, false, __('Укажите ID товаров или воспользуйтесь <a href="#" data-target="#uid_new"  class="btn btn-sm btn-default tag-search"><span class="glyphicon glyphicon-search"></span> поиском товаров</a>')));

    // Каталог
    $Tab1.=$PHPShopGUI->setField('Каталог', viewCatalog($data['category']),false,'Переадресация на страницу списка товаров в выбранном каталоге');
    
    // URL
    $Tab1 .= $PHPShopGUI->setField("URL", $PHPShopGUI->setInputText('http://', "url_new",$data['url']));

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "Удалить", "right", 70, "", "but", "actionDelete.report.edit") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.report.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.report.edit");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция удаления
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);


    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array("success" => $action);
}

/**
 * Экшен сохранения
 */
function actionSave() {
    
    // Сохранение данных
    $result=actionUpdate();

    if (isset($_REQUEST['ajax'])) {
        exit(json_encode(array("success" => $result)));
    }
    else header('Location: ?path=' . $_GET['path']);
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    if (strpos($_POST['name_new'], ',')) {
        $name_new=null;
        $name = explode(",", $_POST['name_new']);
        foreach ($name as $v)
            $name_new.="i" . $v . "i";

        $_POST['name_new'] = $name_new;
    }
    else  $_POST['name_new'] = "i".$_POST['name_new']."i";

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array("success" => $action);
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>