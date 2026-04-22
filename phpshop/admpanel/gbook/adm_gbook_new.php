<?php

$TitlePage = __('Создание отзыва');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['gbook']);

function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopModules, $TitlePage;

    $PHPShopGUI->field_col = 3;

    // Выборка
    $data['datas'] = PHPShopDate::get();
    $data['tema'] = __('Отзыв от ') . $data['datas'];
    $data['name'] = __('Администратор');
    $data = $PHPShopGUI->valid($data, 'otvet', 'mail', 'otsiv', 'flag', 'servers');

    $PHPShopGUI->setActionPanel($TitlePage, false, array('Сохранить и закрыть'));

    // datetimepicker
    $PHPShopGUI->addJSFiles('./js/bootstrap-datetimepicker.min.js', './news/gui/news.gui.js');
    $PHPShopGUI->addCSSFiles('./css/bootstrap-datetimepicker.min.css');


    // Редактор 1
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('otvet_new');
    $oFCKeditor->Height = '400';
    $oFCKeditor->Value = $data['otvet'];

    // Содержание закладки 1
    $Tab1 = $PHPShopGUI->setField("Дата", $PHPShopGUI->setInputDate("datas_new", PHPShopDate::get(time())));

    $Tab1 .= $PHPShopGUI->setField("Имя", $PHPShopGUI->setInput("text", "name_new", $data['name']));

    $Tab1 .= $PHPShopGUI->setField("E-mail", $PHPShopGUI->setInput("text", "mail_new", $data['mail']));

    $Tab1 .= $PHPShopGUI->setField("Тема", $PHPShopGUI->setTextarea("tema_new", $data['tema'])) .
            $PHPShopGUI->setField("Отзыв", $PHPShopGUI->setTextarea("otsiv_new", $data['otsiv'], "", '100%', '200') . $PHPShopGUI->setAIHelpButton('otsiv_new', 100, 'gbook_review'));

    $Tab1 .= $PHPShopGUI->setField("Статус", $PHPShopGUI->setCheckbox("flag_new", 1, null, $data['flag']));

    $Tab1 .= $PHPShopGUI->setField("Витрины", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/'));

    $Tab1 = $PHPShopGUI->setCollapse('Отзыв', $Tab1);

    // Содержание закладки 2
    $Tab1 .= $PHPShopGUI->setCollapse('Ответ', $oFCKeditor->AddGUI() . $PHPShopGUI->setAIHelpButton('otvet_new', 100, 'gbook_answer', 'otsiv_new'));

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, null);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true, false, true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "ОК", "right", 70, "", "but", "actionInsert.gbook.create");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция обновления
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules;

    $_POST['datas_new'] = time();

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
    header('Location: ?path=' . $_GET['path']);
    return $action;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>