<?php

$TitlePage = __('Редактирование Склада') . ' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['warehouses']);

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopModules;

    $PHPShopGUI->field_col = 3;

    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));

    // Нет данных
    if (!is_array($data)) {
        header('Location: ?path=' . $_GET['path']);
    }

    $PHPShopGUI->setActionPanel(__("Редактирование Склада") . ": " . $data['name'] . " [ID " . $_GET['id'] . "]", array('Удалить'), array('Сохранить', 'Сохранить и закрыть'));

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
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "Удалить", "right", 70, "", "but", "actionDelete.currency.edit") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.currency.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.currency.edit");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция удаления
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));

    // Удаляем поле хранение склада у товара
    $PHPShopOrm->query('ALTER TABLE `phpshop_products` DROP `items' . $_POST['rowID'] . '`');

    return array("success" => $action);
}

/**
 * Экшен сохранения
 */
function actionSave() {

    // Сохранение данных
    actionUpdate();

    header('Location: ?path=' . $_GET['path']);
}

// Функция обновления
function actionUpdate() {
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

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array("success" => $action);
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>
