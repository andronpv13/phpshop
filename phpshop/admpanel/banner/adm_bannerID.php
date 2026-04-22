<?php

PHPShopObj::loadClass("array");
PHPShopObj::loadClass("category");

$TitlePage = __('Редактирование Баннера') . ' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['banner']);

// Выбор шаблона дизайна
function GetSkinList($skin) {
    global $PHPShopGUI;
    $dir = "../templates/";

    $value[] = array('Не выбрано', '', '');

    if (is_dir($dir)) {
        if (@$dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if (file_exists($dir . '/' . $file . "/main/index.tpl")) {

                    if ($skin == $file)
                        $sel = "selected";
                    else
                        $sel = "";

                    if ($file != "." and $file != ".." and ! strpos($file, '.'))
                        $value[] = array($file, $file, $sel);
                }
            }
            closedir($dh);
        }
    }

    return $PHPShopGUI->setSelect('skin_new', $value, 300);
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

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopOrm, $PHPShopModules, $shop_type;

    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . $_GET['id']));
    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->addJSFiles('./js/bootstrap-colorpicker.min.js');
    $PHPShopGUI->addCSSFiles('./css/bootstrap-colorpicker.min.css');

    $PHPShopGUI->setActionPanel(__("Редактирование Баннера") . ": " . $data['name'], array('Удалить'), array('Сохранить', 'Сохранить и закрыть'));

    if (empty($data['color']))
        $data['color'] = '#000000';

    $Tab1 = $PHPShopGUI->setField("Название", $PHPShopGUI->setInput("text", "name_new", $data['name'])) .
            $PHPShopGUI->setField("Статус", $PHPShopGUI->setRadio("flag_new", 1, "Включить", $data['flag']) .
                    $PHPShopGUI->setRadio("flag_new", 0, "Выключить", $data['flag'])) .
            $PHPShopGUI->setField("Мобильный", $PHPShopGUI->setCheckbox("mobile_new", 1, "Отображать только на мобильных устройствах", $data['mobile']) . $PHPShopGUI->setHelp('По умолчанию, баннер выводится только на PC')) .
            $PHPShopGUI->setField("Тип вывода", $PHPShopGUI->setRadio("type_new", 0, "В колонке", $data['type']) .
                    $PHPShopGUI->setRadio("type_new", 2, "Горизонтальный", $data['type']) .
                    $PHPShopGUI->setRadio("type_new", 1, "Всплывающее окно", $data['type']) . '<br>' .
                    $PHPShopGUI->setRadio("type_new", 3, "В меню каталога", $data['type'])
    );

    $Tab2 = $PHPShopGUI->setField("Таргетинг:", $PHPShopGUI->setInput("text", "dir_new", $data['dir']) . $PHPShopGUI->setHelp('/ - главная, /page/page.html - страница. Пример ввода: /, /page/dostavka.html'));

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
        $Tab2 .= $PHPShopGUI->setField('Каталоги', $tree_select_dop . $PHPShopGUI->setHelp('Баннер выводится только в заданных каталогах.'));


    // Описание 
    $Tab1 .= $PHPShopGUI->setField("Описание", $PHPShopGUI->setTextarea('description_new', $data['description']));

    // Цель
    $Tab1 .= $PHPShopGUI->setField("Цель", $PHPShopGUI->setInput("text", "link_new", $data['link']) . $PHPShopGUI->setHelp("Пример: /pages/info.html или https://google.com"));

    // Цвет
    $Tab1 .= $PHPShopGUI->setField("Инверсия цвета", $PHPShopGUI->setInputText(null, "color_new", (int) $data['color'], 100, '%'));

    // Иконка
    $Tab1 .= $PHPShopGUI->setField("Изображение для фона", $PHPShopGUI->setIcon($data['image'], "image_new", false));

    $Tab1 = $PHPShopGUI->setCollapse('Информация', $Tab1);

    // Редактор 
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('content_new');
    $oFCKeditor->Height = '400';
    $oFCKeditor->Value = $data['content'];


    $Tab_tip1 = $PHPShopGUI->setField("Условия показа", $PHPShopGUI->setRadio("display_new", 0, "Показывать всегда", $data['display']) .
            $PHPShopGUI->setRadio("display_new", 1, "Первый заход на сайт", $data['display'])
    );

    $size_value[] = array('Маленькое', 0, $data['size']);
    $size_value[] = array('Среднее', 1, $data['size']);
    $size_value[] = array('Большое', 2, $data['size']);

    $Tab_tip1 .= $PHPShopGUI->setField("Размер окна", $PHPShopGUI->setSelect('size_new', $size_value, 150, true));

    // Витрина
    $Tab2 .= $PHPShopGUI->setField("Витрины", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/'));

    $Tab2_help = $PHPShopGUI->setHelp('Оставьте поля пустыми, если нужен вывод на всех страницах/каталогах, или укажите точную страницу/каталог:');
    $Tab1 .= $PHPShopGUI->setCollapse("Точечный вывод", $Tab2_help . $Tab2);

    // Содержание 
    $Tab1 .= $PHPShopGUI->setCollapse("Содержание", '<div>' . $oFCKeditor->AddGUI() . '</div>');

    $Tab1 .= $PHPShopGUI->setCollapse('Всплывающее окно', $Tab_tip1);

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true, false, true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $_GET['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "Удалить", "right", 70, "", "btn-danger", "actionDelete.banner.edit") .
            $PHPShopGUI->setInput("submit", "editID", "ОК", "right", 70, "", "btn-success", "actionUpdate.banner.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.banner.edit");

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
    return array('success' => $action);
}

// Добавление изображения 
function iconAdd($name = 'icon_new') {
    global $PHPShopSystem;

    // Папка сохранения
    $path = '/UserFiles/Image/' . $PHPShopSystem->getSerilizeParam('admoption.image_result_path');

    // Копируем от пользователя
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        $_FILES['file']['name'] = PHPShopString::toLatin(str_replace('.' . $_FILES['file']['ext'], '', PHPShopString::utf8_win1251($_FILES['file']['name']))) . '.' . $_FILES['file']['ext'];
        if (in_array($_FILES['file']['ext'], array('gif', 'png', 'jpg', 'svg'))) {
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

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    if (empty($_POST['ajax'])) {

        // Мультибаза
        if (is_array($_POST['servers'])) {
            $_POST['servers_new'] = "";
            foreach ($_POST['servers'] as $v)
                if ($v != 'null' and ! strstr($v, ',') and $v != null and ! strstr($_POST['servers_new'], "i" . $v . "i"))
                    $_POST['servers_new'] .= "i" . $v . "i";
        }

        if (empty($_POST['mobile_new']))
            $_POST['mobile_new'] = 0;

        // Доп каталоги
        $_POST['dop_cat_new'] = "";
        if (is_array($_POST['dop_cat']) and $_POST['dop_cat'][0] != 'null') {
            $_POST['dop_cat_new'] = "#";
            foreach ($_POST['dop_cat'] as $v)
                if ($v != 'null' and ! strstr($v, ','))
                    $_POST['dop_cat_new'] .= $v . "#";
        }

        // Изображение
        $_POST['image_new'] = iconAdd('image_new');
    }

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array('success' => $action);
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>
