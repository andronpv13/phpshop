<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.seometanews.seometanews_system"));

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&install=check');
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $TitlePage, $select_name, $PHPShopOrm;

    $PHPShopGUI->setActionPanel($TitlePage, $select_name, array('Сохранить и закрыть'));
    
    // Выборка
    $data = $PHPShopOrm->select();
    
    
    $Tab1 = '<hr>'.$PHPShopGUI->setField("Title раздела:", $PHPShopGUI->setTextArea("title_new", $data['title']),1,'Для '.$_SERVER['SERVER_NAME'].'/news/');
    $Tab1.=$PHPShopGUI->setField("Description раздела:", $PHPShopGUI->setTextArea("description_new", $data['description']),1,'Для '.$_SERVER['SERVER_NAME'].'/news/');
    
        $Tab1.=$PHPShopGUI->setField("Keywords раздела:", $PHPShopGUI->setTextArea("keywords_new", $data['keywords']),1,'Для '.$_SERVER['SERVER_NAME'].'/news/');

    // Форма регистрации
    $Tab2 = $PHPShopGUI->setPay();

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1),array("О Модуле", $Tab2));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", 1) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>