<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.partner.partner_users"));
$TitlePage = __('Создание Партнера');


// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm,$PHPShopSystem,$TitlePage;
    
    $PHPShopGUI->setActionPanel($TitlePage, false, array('Сохранить и закрыть'));


    // Знак рубля
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();

    $PHPShopGUI->field_col = 2;
    $Tab1 = $PHPShopGUI->setField('Имя', $PHPShopGUI->setInputText(false, 'name_new', $data['name'], 400));
    $Tab1 .= $PHPShopGUI->setField('Баланс', $PHPShopGUI->setInputText(false, 'money_new', 0, 100,$currency));
    $Tab1 .= $PHPShopGUI->setField('Логин', $PHPShopGUI->setInputText(false, 'login_new', $data['login'], 400));
    $Tab1 .= $PHPShopGUI->setField('Пароль', $PHPShopGUI->setInputText(false, 'password_new', base64_decode($data['password']), 400));
    $Tab1 .= $PHPShopGUI->setField('Статус', $PHPShopGUI->setCheckbox('enabled_new', 1, 'Активирован', 1));
    $Tab1 .= $PHPShopGUI->setField('Реквизиты', $PHPShopGUI->setTextarea('content_new', $data['content'], true, 400, 100));

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "ОК", "right", 70, "", "but", "actionInsert.news.create");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция обновления
function actionInsert() {
    global $PHPShopOrm;
    
    $_POST['date_new'] =  date("d-m-y");

    $action = $PHPShopOrm->insert($_POST);
    header('Location: ?path=' . $_GET['path']);
    return $action;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>