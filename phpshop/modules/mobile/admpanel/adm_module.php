<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.mobile.mobile_system"));

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $action = $PHPShopOrm->update($_POST);
    if ($action)
        header('Location: ?path=modules&id=' . $_GET['id']);
}

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate(number_format($option['version'], 1, '.', false));
    $PHPShopOrm->clean();
    $PHPShopOrm->update(['version_new' => $new_version]);
}

// Выбор шаблона дизайна
function GetSkinList($skin) {
    global $PHPShopGUI;
    $dir = "../templates/";

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

    return $PHPShopGUI->setSelect('skin_new', $value);
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('Сообщение', $PHPShopGUI->setTextarea('message_new', $data['message']), 1, 'Пустое поле не показывает окно запроса на переход');

    // Иконка
    $Tab1 .= $PHPShopGUI->setField('Изображение в шапке', $PHPShopGUI->setInputText(false, "logo_new", $data['logo']));

    // Заголовок
    $returncall_value[] = array(__('телефон'), 1, $data['returncall']);
    $returncall_value[] = array(__('обратный звонок'), 2, $data['returncall']);
    $Tab1 .= $PHPShopGUI->setField("Заголовок", $PHPShopGUI->setSelect('returncall_new', $returncall_value));
    $Tab1 .= $PHPShopGUI->setField("Дизайн", GetSkinList($data['skin']));

    $Tab2 = $PHPShopGUI->setPay();

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("О Модуле", $Tab2));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий 
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>