<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.pricerequest.pricerequest_system"));

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;
    
    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('E-mail для запросов', $PHPShopGUI->setInputText(false, 'mail_new', $data['mail'],300));
    $Tab1.=$PHPShopGUI->setField('Текст кнопки', $PHPShopGUI->setInputText(false, 'name_new', $data['name'], 300));
    $Tab1 .= $PHPShopGUI->setField('Сообщение', $PHPShopGUI->setTextarea('message_new', $data['message']));
    
    // Место вывода
    $d_value[] = array('подробное описание', 0, $data['display']);
    $d_value[] = array('подробное и краткое описание', 1, $data['display']);
    $Tab1 .= $PHPShopGUI->setField('Вывод', $PHPShopGUI->setSelect('display_new', $d_value, 250,true));

    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay($data['serial'], false, $data['version'], true);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true),  array("О Модуле", $Tab3), array("Обзор заявок", 0, '?path=modules.dir.pricerequest'));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>