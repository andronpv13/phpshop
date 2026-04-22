<?php

$TitlePage = __('Редактирование Отзыва') . ' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['gbook']);

function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopOrm, $PHPShopModules;

    $PHPShopGUI->field_col = 3;

    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));

    // datetimepicker
    $PHPShopGUI->addJSFiles('./js/bootstrap-datetimepicker.min.js', './news/gui/news.gui.js');
    $PHPShopGUI->addCSSFiles('./css/bootstrap-datetimepicker.min.css');

    $PHPShopGUI->action_select['Предпросмотр'] = array(
        'name' => 'Предпросмотр',
        'url' => '../../gbook/ID_' . $data['id'] . '.html',
        'action' => 'front',
        'target' => '_blank'
    );

    $PHPShopGUI->setActionPanel(__("Редактирование Отзыва от") . " " . $data['name'], array('Предпросмотр', '|', 'Удалить'), array('Сохранить', 'Сохранить и закрыть'));

    // Редактор 1
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('otvet_new');
    $oFCKeditor->Height = '400';
    $oFCKeditor->Value = $data['otvet'];

    // Содержание закладки 1
    $Tab1 = $PHPShopGUI->setField("Дата", $PHPShopGUI->setInputDate("datas_new", PHPShopDate::get($data['datas'])));

    $Tab1 .= $PHPShopGUI->setField("Имя", $PHPShopGUI->setInput("text", "name_new", $data['name']));

    $Tab1 .= $PHPShopGUI->setField("E-mail", $PHPShopGUI->setInput("text", "mail_new", $data['mail']));

    $Tab1 .= $PHPShopGUI->setField("Тема", $PHPShopGUI->setTextarea("tema_new", $data['tema'])) .
            $PHPShopGUI->setField("Отзыв", $PHPShopGUI->setTextarea("otsiv_new", $data['otsiv'], "", '100%', '200') . $PHPShopGUI->setAIHelpButton('otsiv_new', 100, 'gbook_review'));
    $Tab1 .= $PHPShopGUI->setField("Статус", $PHPShopGUI->setCheckbox("flag_new", 1, null, $data['flag']));

    $Tab1 .= $PHPShopGUI->setField("Витрины", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/'));

    $Tab1 = $PHPShopGUI->setCollapse('Отзыв', $Tab1);

    // Содержание закладки 2
    $Tab1 .= $PHPShopGUI->setCollapse('Ответ', $oFCKeditor->AddGUI() . $PHPShopGUI->setAIHelpButton('otvet_new', 200, 'gbook_answer', 'otsiv_new'));


    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true, false, true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "Удалить", "right", 70, "", "but", "actionDelete.gbook.edit") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.gbook.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.gbook.edit");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция отправки почты
function sendMail($name, $mail) {
    global $PHPShopSystem, $PHPShopBase;

    // Подключаем библиотеку отправки почты
    PHPShopObj::loadClass("mail");

    $zag = __("Ваш отзыв добавлен на сайт") . " " . $PHPShopSystem->getValue('name');
    $message = __("Уважаемый") . " " . $name . ",

" . __("Ваш отзыв добавлен на сайт по адресу") . ": http://" . $_SERVER['SERVER_NAME'] . $PHPShopBase->getParam('dir.dir') . "/gbook/

" . __("Спасибо за проявленный интерес.");
    new PHPShopMail($mail, $PHPShopSystem->getEmail(), $zag, $message);
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

    // Корректировка пустых значений
    $PHPShopOrm->updateZeroVars('flag_new');

    if (empty($_POST['ajax'])) {
        $_POST['datas_new'] = PHPShopDate::GetUnixTime($_POST['datas_new']);
    }
    if (empty($_POST['flag_new']))
        $_POST['flag_new'] = 0;
    else if (!empty($_POST['mail_new']))
        sendMail($_POST['name_new'], $_POST['mail_new']);

    // Мультибаза
    if (is_array($_POST['servers'])) {
        $_POST['servers_new'] = "";
        foreach ($_POST['servers'] as $v)
            if ($v != 'null' and ! strstr($v, ',') and $v != null and ! strstr($_POST['servers_new'], "i" . $v . "i"))
                $_POST['servers_new'] .= "i" . $v . "i";
    }

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array("success" => $action);
}

// Функция удаления
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array("success" => $action);
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>