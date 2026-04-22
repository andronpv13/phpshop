<?php

$TitlePage = __('Редактирование варианта подтипа').' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['parent_name']);

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopModules;

    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));

    $PHPShopGUI->field_col = 4;
    
    $PHPShopGUI->setActionPanel(__("Варианты подтипов") . '<span class="hidden-xs"> / ' . $data['name'] . '</span>', array('Удалить'), array('Сохранить', 'Сохранить и закрыть'));

    $Tab1 = $PHPShopGUI->setField("Наименование подтипа", $PHPShopGUI->setInputArg(array('type' => 'text.required','locale'=>false, 'name' => "name_new", 'value' => $data['name'], 'placeholder' => 'Размер'))) .
            $PHPShopGUI->setField("Наименование цвета", $PHPShopGUI->setInputArg(array('type' => 'text', 'name' => "color_new", 'value' => $data['color'], 'placeholder' => 'Цвет'))) .
            $PHPShopGUI->setField("Статус", $PHPShopGUI->setCheckbox("enabled_new", 1, null, $data['enabled']));


    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1,true,false,'block-grid'));

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "Удалить", "right", 70, "", "but", "actionDelete.sort.edit") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.sort.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.sort.edit");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
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

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);
    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    
    return array("success" =>  $action);
}

// Функция удаления
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);


    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array("success" =>  $action);
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>