<?php


// Функция обновления
function actionUpdate() {
    global $PHPShopOrm;

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&install=check');
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $TitlePage, $select_name;
    
    $PHPShopGUI->setActionPanel($TitlePage, $select_name, array('Закрыть'));

    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay(false, true);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("О Модуле", $Tab3));

    return true;
}

// Обработка событий
$PHPShopGUI->getAction();


// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>