<?php

PHPShopObj::loadClass("array");
PHPShopObj::loadClass("category");

$TitlePage = __('Создание Текстового Блока');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['menu']);

// Заполняем выбор
function setSelectChek($n) {
    $i = 1;
    while ($i <= 10) {
        if ($n == $i)
            $s = "selected";
        else
            $s = "";
        $select[] = array($i, $i, $s);
        $i++;
    }
    return $select;
}

// Построение дерева категорий
function treegenerator($array, $i, $curent, $dop_cat_array) {
    global $tree_array;
    $del = '¦&nbsp;&nbsp;&nbsp;&nbsp;';
    $tree_select = $tree_select_dop = $check = false;

    $del = str_repeat($del, $i);
    if (!empty($array) and is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {

            $check = treegenerator(@$tree_array[$k], $i + 1, $curent, $dop_cat_array);

            if ($k == $curent)
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

            if (empty($check['select'])) {
                $tree_select .= '<option value="' . $k . '" ' . $selected . '>' . $del . $v . '</option>';

                $tree_select_dop .= '<option value="' . $k . '" ' . $selected_dop . '>' . $del . $v . '</option>';

                $i = 1;
            } else {
                $tree_select .= '<option value="' . $k . '" ' . $selected . ' >' . $del . $v . '</option>';
                $tree_select_dop .= '<option value="' . $k . '" ' . $selected_dop . '  >' . $del . $v . '</option>';
            }

            $tree_select .= $check['select'];
            $tree_select_dop .= $check['select_dop'];
        }
    }
    return array('select' => $tree_select, 'select_dop' => $tree_select_dop);
}

function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $TitlePage, $PHPShopModules, $shop_type;

    $PHPShopGUI->field_col = 3;

    // Выборка
    $data['flag'] = 1;
    $data['name'] = __('Новый блок');
    $data = $PHPShopGUI->valid($data, 'content', 'num', 'element', 'dir', 'servers');

    $PHPShopGUI->setActionPanel($TitlePage, false, array('Сохранить и закрыть'));

    // Редактор 1
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('content_new');
    $oFCKeditor->Height = '350';
    $oFCKeditor->Value = $data['content'];

    $Select1 = setSelectChek($data['num']);

    $Select2[] = array("Слева", 0, $data['element']);
    $Select2[] = array("Справа", 1, $data['element']);

    // Содержание закладки 1
    $Tab1 = $PHPShopGUI->setField("Название", $PHPShopGUI->setInput("text", "name_new", $data['name'])) .
            $PHPShopGUI->setField("Статус", $PHPShopGUI->setCheckbox("flag_new", 1, null, $data['flag'])) .
            $PHPShopGUI->setField("Мобильный", $PHPShopGUI->setCheckbox("mobile_new", 1, "Отображать только на мобильных устройствах", $data['mobile']) . $PHPShopGUI->setHelp('По умолчанию, блок выводится только на PC')) .
            $PHPShopGUI->setField("Позиция", $PHPShopGUI->setSelect("num_new", $Select1, 150)) .
            $PHPShopGUI->setField("Место", $PHPShopGUI->setSelect("element_new", $Select2, 150, true)) .
            $PHPShopGUI->setLine() .
            $PHPShopGUI->setField("Таргетинг", $PHPShopGUI->setInput("text", "dir_new", $data['dir']) .
                    $PHPShopGUI->setHelp('* Пример: /page/,/news/. Можно указать несколько адресов через запятую.'));

    $PHPShopCategoryArray = new PHPShopCategoryArray();
    $CategoryArray = $PHPShopCategoryArray->getArray();

    $tree_array = array();

    foreach ($PHPShopCategoryArray->getKey('parent_to.id', true) as $k => $v) {
        foreach ($v as $cat) {
            $tree_array[$k]['sub'][$cat] = $CategoryArray[$cat]['name'];
        }
        $tree_array[$k]['name'] = $CategoryArray[$k]['name'];
        $tree_array[$k]['id'] = $k;
    }


    $GLOBALS['tree_array'] = &$tree_array;

    // Допкаталоги
    $dop_cat_array = preg_split('/#/', $data['dop_cat'], -1, PREG_SPLIT_NO_EMPTY);
    $tree_select_dop = null;

    if (!empty($tree_array[0]['sub']) and is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator(@$tree_array[$k], 1, @$data['category'], $dop_cat_array);

            if ($k == @$data['category'])
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

            if (empty($tree_array[$k]))
                $disabled = null;
            else
                $disabled = ' disabled';


            $tree_select_dop .= '<option value="' . $k . '" ' . $selected_dop . '>' . $v . '</option>';

            $tree_select_dop .= $check['select_dop'];
        }

    $tree_select_dop = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container="body"  data-style="btn btn-default btn-sm" name="dop_cat[]" data-width="100%" multiple><option value="0">' . $CategoryArray[0]['name'] . '</option>' . $tree_select_dop . '</select>';

    // Дополнительные каталоги
    if (empty($shop_type))
        $Tab1 .= $PHPShopGUI->setField('Каталоги', $tree_select_dop . $PHPShopGUI->setHelp('Блок выводится только в заданных каталогах.'));

    $Tab1 .= $PHPShopGUI->setField("Витрины", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/'));

    $Tab1 = $PHPShopGUI->setCollapse('Информация', $Tab1);

    $Tab1 .= $PHPShopGUI->setCollapse("Содержание", $oFCKeditor->AddGUI());

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true, false, true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "ОК", "right", 70, "", "but", "actionInsert.menu.create");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция записи
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules;

    // Мультибаза
    if (is_array($_POST['servers'])) {
        $_POST['servers_new'] = "";
        foreach ($_POST['servers'] as $v)
            if ($v != 'null' and ! strstr($v, ',') and $v != null and ! strstr($_POST['servers_new'], "i" . $v . "i"))
                $_POST['servers_new'] .= "i" . $v . "i";
    }

    if (empty($_POST['flag_new']))
        $_POST['flag_new'] = 0;

    // Доп каталоги
    $_POST['dop_cat_new'] = "";
    if (is_array($_POST['dop_cat']) and $_POST['dop_cat'][0] != 'null') {
        $_POST['dop_cat_new'] = "#";
        foreach ($_POST['dop_cat'] as $v)
            if ($v != 'null' and ! strstr($v, ','))
                $_POST['dop_cat_new'] .= $v . "#";
    }

    if (empty($_POST['mobile_new']))
        $_POST['mobile_new'] = 0;

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);
    $action = $PHPShopOrm->insert($_POST);

    header('Location: ?path=' . $_GET['path']);
}

// Обработка событий 
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>