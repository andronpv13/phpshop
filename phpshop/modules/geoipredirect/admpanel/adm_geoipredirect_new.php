<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.geoipredirect.geoipredirect_city"));


// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm,$TitlePage;
    
    $TitlePage=__("Создание адреса перенаправления");

    $PHPShopGUI->field_col = 2;
    $PHPShopGUI->setActionPanel(__("Создание адреса перенаправления"), false, array('Сохранить и закрыть'));
$data['enabled']=1;

    $Tab1= $PHPShopGUI->setField('Город: ', $PHPShopGUI->setInputText(null, 'name_new', null,400,null,null,null,'Москва'));
    $Tab1.= $PHPShopGUI->setField('Адрес перенаправления: ', $PHPShopGUI->setInputText('http://', 'host_new', null,400,null,null,null,'show'.$_SERVER['SERVER_NAME']));
     $Tab1.=$PHPShopGUI->setField("Статус", $PHPShopGUI->setRadio("enabled_new", 1, "Вкл.", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new", 0, "Выкл.", $data['enabled']));
   
    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1,true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "ОК", "right", 70, "", "but", "actionInsert.servers.create");
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция обновления
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules;

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);
    $action = $PHPShopOrm->insert($_POST);

    header('Location: ?path=' . $_GET['path']);
    return $action;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>