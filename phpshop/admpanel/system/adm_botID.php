<?php

$TitlePage = __("Редактирование бота");
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['bot']);

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;


    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));
    $PHPShopGUI->field_col = 4;
    $PHPShopGUI->setActionPanel(__("Редактирование бота") . ": " . $data['name'], array('Удалить'), array('Сохранить', 'Сохранить и закрыть'));


    $Tab1 = $PHPShopGUI->setField("Название", $PHPShopGUI->setInput("text", "name_new", $data['name'])) .
            $PHPShopGUI->setField("Дата добавления в журнал", $PHPShopGUI->setText(PHPShopDate::get($data['date'], true))) .
            $PHPShopGUI->setField("Дата блокировки", $PHPShopGUI->setText(PHPShopDate::get($data['date_block'], true))) .
            $PHPShopGUI->setField("Доступ на сайт", $PHPShopGUI->setCheckbox('enabled_new', 1, false, $data['enabled'])) .
            $PHPShopGUI->setField("Описание", $PHPShopGUI->setTextarea("description_new", $data['description'], false, false, 100)
    );


    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Информация", $Tab1, true, false, true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $_GET['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "Удалить", "right", 70, "", "btn-danger", "actionDelete.system.edit") .
            $PHPShopGUI->setInput("submit", "editID", "ОК", "right", 70, "", "btn-success", "actionUpdate.system.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.system.edit");

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
    return array('success' => $action);
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

    // Корректировка пустых значений
    $PHPShopOrm->updateZeroVars('enabled_new');
   
    $_POST['date_block_new']=time();

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array('success' => $action);
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>