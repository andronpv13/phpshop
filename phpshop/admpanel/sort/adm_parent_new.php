<?php

$TitlePage = __('Создание варианта подтипа');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['parent_name']);

function actionStart() {
    global $PHPShopGUI, $PHPShopModules,$TitlePage;

    // Выборка
    $data['start_date'] = time();
    $data['end_date'] = time() + 10000000;
    $data['enabled'] = 1;
    $data['day_num'] = 1;
    $data['news_num'] = 3;
    
    $data = $PHPShopGUI->valid($data,'name','color');

    $PHPShopGUI->field_col = 4;
    $PHPShopGUI->setActionPanel($TitlePage, false, array('Сохранить и закрыть'));

    $Tab1 = $PHPShopGUI->setField("Наименование подтипа", $PHPShopGUI->setInputArg(array('type' => 'text.required', 'name' => "name_new", 'value' => $data['name'], 'placeholder' => 'Размер'))) .
            $PHPShopGUI->setField("Наименование цвета", $PHPShopGUI->setInputArg(array('type' => 'text', 'name' => "color_new", 'value' => $data['color'], 'placeholder' => 'Цвет'))) .
            $PHPShopGUI->setField("Статус", $PHPShopGUI->setCheckbox("enabled_new", 1, null, $data['enabled']));


    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1,true,false,'block-grid'));

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "ОК", "right", 70, "", "but", "actionInsert.sort.create");

    // Футер
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
