<?php

$TitlePage = __("Создание бота");
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['bot']);

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI;

   // Выборка
    $data['enabled'] = 0;
    $data['name'] = __('New Bot');
    $data = $PHPShopGUI->valid($data, 'description');

    $PHPShopGUI->field_col = 4;
    $PHPShopGUI->setActionPanel(__("Создание бота") . ": " . $data['name'], false, array('Сохранить и закрыть'));


    $Tab1 = $PHPShopGUI->setField("Название", $PHPShopGUI->setInput("text", "name_new", $data['name'])) .
            $PHPShopGUI->setField("Доступ на сайт", $PHPShopGUI->setCheckbox('enabled_new', 1, false, $data['enabled'])) .
            $PHPShopGUI->setField("Описание", $PHPShopGUI->setTextarea("description_new", $data['description'], false, false, 100)
    );

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Информация", $Tab1, true, false, true));
    
    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "ОК", "right", 70, "", "but", "actionInsert.system.edit");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);

    return true;
}

// Функция записи
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
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>