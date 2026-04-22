<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.smsfly.smsfly_system"));

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

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


    $Tab1 = $PHPShopGUI->setField('№ Телефона для SMS', $PHPShopGUI->setInputArg(array('type' => 'text.required', 'value' => $data['phone'], 'name' => 'phone_new', 'placeholder' => '380631234567', 'size' => 300)));
    $Tab1 .= $PHPShopGUI->setField('Пользователь', $PHPShopGUI->setInputText(false, 'merchant_user_new', $data['merchant_user'], 300));
    $Tab1 .= $PHPShopGUI->setField('Пароль', $PHPShopGUI->setInput('password', 'merchant_pwd_new', $data['merchant_pwd'], false, 300));
    $Tab1 .= $PHPShopGUI->setField('Отправитель (Alfaname)', $PHPShopGUI->setInputText(false, 'alfaname_new', $data['alfaname'], 300));

    // Sandbox
    $sandbox_value[] = array('Включен', 1, $data['sandbox']);
    $sandbox_value[] = array('Выключен', 2, $data['sandbox']);
    $Tab1 .= $PHPShopGUI->setField('Тестовый режим', $PHPShopGUI->setSelect('sandbox_new', $sandbox_value, 300, true));


    $Tab2 = $PHPShopGUI->setPay();


    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Авторизация", $Tab1, true), array("О Модуле", $Tab2));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>