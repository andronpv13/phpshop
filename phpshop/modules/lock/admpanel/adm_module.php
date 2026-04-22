<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.lock.lock_system"));

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

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate(number_format($option['version'], 1, '.', false));
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
    $PHPShopOrm->clean();
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();


    $e_value[] = array('Выкл', 1, $data['flag']);
    $e_value[] = array('Вкл', 2, $data['flag']);

    $e_adm_value[] = array('Выкл', 1, $data['flag_admin']);
    $e_adm_value[] = array('Вкл', 2, $data['flag_admin']);

    $Tab1 = $PHPShopGUI->setField('Авторизация на сайте', $PHPShopGUI->setSelect('flag_new', $e_value, 200, true));
    $Tab1 .= $PHPShopGUI->setField('Авторизация в админке', $PHPShopGUI->setSelect('flag_admin_new', $e_adm_value, 200, true));
    $Tab1 .= $PHPShopGUI->setField('Пользоваль', $PHPShopGUI->setInput('text.required', "login_new", $data['login'], false, 200));
    $Tab1 .= $PHPShopGUI->setField("Пароль", $PHPShopGUI->setInput("password.required", "password_new", $data['password'], null,200, false, false, false, false, '<a href="#" class="password-view"  data-toggle="tooltip" data-placement="top" title="' . __('Показать пароль') . '"><span class="glyphicon glyphicon-eye-open"></span></a>'));
    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], true);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("О Модуле", $Tab3,));

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