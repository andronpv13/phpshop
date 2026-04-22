<?php

$TitlePage = __('Редактирование Валюты').' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['currency']);

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopModules;

    $PHPShopGUI->field_col = 2;

    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));

    // Нет данных
    if (!is_array($data)) {
        header('Location: ?path=' . $_GET['path']);
    }

    $PHPShopGUI->setActionPanel(__("Редактирование Валюты").": " . $data['name'], array('Удалить'), array('Сохранить', 'Сохранить и закрыть'));

    $Tab1 = $PHPShopGUI->setField("Название", $PHPShopGUI->setInputText(null, "name_new", $data['name'], 300));
    $Tab1 .= $PHPShopGUI->setField("Обозначение", $PHPShopGUI->setInputText(null, "code_new", $data['code'], 300));
    $Tab1 .= $PHPShopGUI->setField("ISO", $PHPShopGUI->setInputText(null, "iso_new", $data['iso'], 300),1,'Код валюты по стандарту ISO (USD,RUB,UAH). Если вводите RUR или RUB - то рубль заменяется на иконку рубля. Если поле пустое, то валюта выводится из поля Обозначение');
    $Tab1 .= $PHPShopGUI->setField("Курс", $PHPShopGUI->setInputText(null, "kurs_new", $data['kurs'], 300),1,'Обратный курс относительно рубля ($ = 0.015)');
    $Tab1 .= $PHPShopGUI->setField("Приоритет", $PHPShopGUI->setInputText(null, "num_new", $data['num'], 50));
    $Tab1.=$PHPShopGUI->setField("Статус", $PHPShopGUI->setRadio("enabled_new", 1, "Вкл.", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new", 0, "Выкл.", $data['enabled']));
    
    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1,true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "Удалить", "right", 70, "", "but", "actionDelete.currency.edit") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.currency.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.currency.edit");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    
    $sidebarright[] = array('title' => 'Курсы валют онлайн', 'content' => $PHPShopGUI->loadLib('tab_currency', $data, './system/'));
    $PHPShopGUI->setSidebarRight($sidebarright, 2);
    
    return true;
}

// Функция удаления
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);


    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array("success" =>  $action);
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

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array("success" =>  $action);
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>
