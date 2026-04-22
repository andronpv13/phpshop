<?php

$TitlePage = __('Создание Новости');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['news']);

function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $PHPShopSystem, $TitlePage, $hideSite;

    // Размер названия поля
    $PHPShopGUI->field_col = 3;

    // Выборка
    $data['datas'] = PHPShopDate::get();
    $data['zag'] = __('Новость за ') . $data['datas'] . ' ' . __('число');
    $data = $PHPShopGUI->valid($data, 'kratko', 'podrob', 'icon', 'odnotip', 'servers');

    // datetimepicker
    $PHPShopGUI->addJSFiles('./js/jquery.tagsinput.min.js', './js/bootstrap-datetimepicker.min.js', './js/jquery.waypoints.min.js', './news/gui/news.gui.js');
    $PHPShopGUI->addCSSFiles('./css/jquery.tagsinput.css', './css/bootstrap-datetimepicker.min.css');

    $PHPShopGUI->setActionPanel($TitlePage, false, array('Сохранить и закрыть'));

    // Редактор 1
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('kratko_new');
    $oFCKeditor->Height = '300';
    $oFCKeditor->Value = $data['kratko'];

    $Tab1 = $PHPShopGUI->setField("Дата", $PHPShopGUI->setInputDate("datas_new", $data['datas'])) .
            $PHPShopGUI->setField("Заголовок", $PHPShopGUI->setInput("text", "zag_new", $data['zag']));

    if (empty($data['date_start']))
        $data['date_start'] = $data['datas'];

    $Tab1 .= $PHPShopGUI->setField("Начало показа", $PHPShopGUI->setInputDate("datau_new", PHPShopDate::get($data['datau'])));

    // Иконка
    $Tab2 .= $PHPShopGUI->setField("Изображение", $PHPShopGUI->setIcon($data['icon'], "icon_new", false));
    $Tab2 .= $PHPShopGUI->setField("Витрины", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/'));

    // Рекомендуемые товары
    if (empty($hideSite))
        $Tab1 .= $PHPShopGUI->setField('Рекомендуемые товары', $PHPShopGUI->setTextarea('odnotip_new', $data['odnotip'], false, false, 00, __('Укажите ID товаров или воспользуйтесь') . ' <a href="#" data-target="#odnotip_new"  class="btn btn-sm btn-default tag-search"><span class="glyphicon glyphicon-search"></span> ' . __('поиском товаров') . '</a>'));

    $Tab1 = $PHPShopGUI->setCollapse('Информация', $Tab1);
    $Tab1 .= $PHPShopGUI->setCollapse("Анонс", $oFCKeditor->AddGUI() . $PHPShopGUI->setAIHelpButton('kratko_new', 300, 'news_description'));

    // Редактор 2
    $oFCKeditor2 = new Editor('podrob_new');
    $oFCKeditor2->Height = '470';
    $oFCKeditor2->Value = $data['podrob'];

    $Tab1 .= $PHPShopGUI->setCollapse('Дополнительно', $Tab2);
    $Tab1 .= $PHPShopGUI->setCollapse("Подробно", '<div>' . $oFCKeditor2->AddGUI() . $PHPShopGUI->setAIHelpButton('podrob_new', 300, 'news_content') . '</div>');

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true, false, true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "ОК", "right", 70, "", "but", "actionInsert.news.create");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция обновления
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules;

    if (!empty($_POST['datau_new']))
        $_POST['datau_new'] = PHPShopDate::GetUnixTime($_POST['datau_new']);
    else
        $_POST['datau_new'] = PHPShopDate::GetUnixTime($_POST['datas_new']);

    // Мультибаза
    if (is_array($_POST['servers'])) {
        $_POST['servers_new'] = "";
        foreach ($_POST['servers'] as $v)
            if ($v != 'null' and ! strstr($v, ',') and $v != null and ! strstr($_POST['servers_new'], "i" . $v . "i"))
                $_POST['servers_new'] .= "i" . $v . "i";
    }

    $_POST['icon_new'] = iconAdd();

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->insert($_POST);
    header('Location: ?path=' . $_GET['path']);
    return $action;
}

// Добавление изображения 
function iconAdd() {
    global $PHPShopSystem;

    // Папка сохранения
    $path = $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $PHPShopSystem->getSerilizeParam('admoption.image_result_path');

    // Копируем от пользователя
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
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

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>
