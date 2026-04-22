<?php

$TitlePage = __('Создание Валюты');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['currency']);

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $TitlePage, $PHPShopModules;

    $PHPShopGUI->field_col = 2;
    $PHPShopGUI->setActionPanel($TitlePage, false, array('Сохранить и закрыть'));
    
    // Выборка
    $data['name']=__('Новая валюта');
    $data['kurs']=1;
    $data['enabled']=1;
    $data = $PHPShopGUI->valid($data,'code','iso','num');

    $Tab1 = $PHPShopGUI->setField("Название", $PHPShopGUI->setInputText(null, "name_new", $data['name'], 300));
    $Tab1 .= $PHPShopGUI->setField("Обозначение", $PHPShopGUI->setInputText(null, "code_new", $data['code'], 300));
    $Tab1 .= $PHPShopGUI->setField("ISO", $PHPShopGUI->setInputText(null, "iso_new", $data['iso'], 300),1,'Код валюты по стандарту ISO (USD,RUB,UAH). Если вводите RUR или RUB - то рубль заменяется на иконку рубля. Если поле пустое, то валюта выводится из поля Обозначение');
    $Tab1 .= $PHPShopGUI->setField("Курс", $PHPShopGUI->setInputText(null, "kurs_new", $data['kurs'], 300),1,'Обратный курс относительно рубля ($ = 0.015)');
    $Tab1 .= $PHPShopGUI->setField("Приоритет", $PHPShopGUI->setInputText(null, "num_new", $data['num'], 50));
    $Tab1.=$PHPShopGUI->setField("Статус", $PHPShopGUI->setRadio("enabled_new", 1, "Вкл.", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new", 0, "Выкл.", $data['enabled']));
    
    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "ОК", "right", 70, "", "but", "actionInsert.currency.create");

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
