<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.productday.productday_system"));

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
    global $PHPShopModules,$PHPShopOrm;
    
    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);
    
    if($_POST['time_new']>24 or empty($_POST['time_new']))
        $_POST['time_new'] = 24;

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id='.$_GET['id']);
    return $action;
}


function actionStart() {
    global $PHPShopGUI,$PHPShopOrm;
    
     //Выборка
    $data = $PHPShopOrm->select();
    
    $action_value[] = array('Убирать товар из блока после окончания акции', 1, $data['status']);
    $action_value[] = array('Оставлять товар в блоке после окончания акции', 2, $data['status']);
    $action_value[] = array('Выводить товар из спецпредложений по дате обновления', 3, $data['status']);
    
    
    $Tab1 =$PHPShopGUI->setField("Вывод в блоке", $PHPShopGUI->setSelect('status_new', $action_value, 400,true));
    $Tab1 .= $PHPShopGUI->setField('Час окончания акции', $PHPShopGUI->setInputText(false, 'time_new', $data['time'],50),2,'Час в формате 1-24');
    
    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1,true),array("О Модуле", $PHPShopGUI->setPay(false, false, $data['version'], true)));

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
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>