<?php

$TitlePage = __('Создание Склада');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['warehouses']);

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $TitlePage, $PHPShopModules;

    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->setActionPanel($TitlePage, false, array('Сохранить и закрыть'));

    // Выборка
    $data['name'] = __('Новый склад');
    $data['enabled'] = 1;
    $data['num'] = 1;
    $data = $PHPShopGUI->valid($data, 'uid', 'description', 'servers');

    $Tab1 = $PHPShopGUI->setField("Название", $PHPShopGUI->setInputText(null, "name_new", $data['name']));
    $Tab1 .= $PHPShopGUI->setField("Внешний код", $PHPShopGUI->setInputText(null, "uid_new", $data['uid']), 2, 'Код склада в 1С');
    $Tab1 .= $PHPShopGUI->setField("Описание на сайте", $PHPShopGUI->setInputText(null, "description_new", $data['description']));
    $Tab1 .= $PHPShopGUI->setField("Статус", $PHPShopGUI->setRadio("enabled_new", 1, "Вкл.", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new", 0, "Выкл.", $data['enabled']));
    $Tab1 .= $PHPShopGUI->setField("Сортировка", $PHPShopGUI->setInputText('№', "num_new", $data['num'], 100));

    // Витрина
    $Tab1 .= $PHPShopGUI->setField("Витрины", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/'));

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true, false, true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "ОК", "right", 70, "", "but", "actionInsert.servers.create");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция обновления
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules;

    // Мультибаза
    if (is_array($_POST['servers'])) {
        $_POST['servers_new'] = "";
        foreach ($_POST['servers'] as $v)
            if ($v != 'null' and ! strstr($v, ',') and $v != null and ! strstr($_POST['servers_new'], "i" . $v . "i"))
                $_POST['servers_new'] .= "i" . $v . "i";
    }

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);
    $action = $PHPShopOrm->insert($_POST);

    // Создаем поле хранение склада
    $PHPShopOrm->query('ALTER TABLE `phpshop_products` ADD `items' . $action . '` int(11) default "0"');

    header('Location: ?path=' . $_GET['path']);
    return $action;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>
