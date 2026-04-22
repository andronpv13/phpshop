<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.adanalyzer.adanalyzer_system"));

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;
    
    // Корректировка пустых значений
    $PHPShopOrm->updateZeroVars('status_new', 'enabled_new');
    
     // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}


// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();

    $Tab1=$PHPShopGUI->setField('Источник', $PHPShopGUI->setCheckbox('enabled_new', 1, 'Добавлять название рекламной кампании в комментарий менеджеру', $data['enabled']));
    $Tab1.=$PHPShopGUI->setField('История переходов', $PHPShopGUI->setCheckbox('status_new', 1, 'Удалить UTM-метку после заказа у пользователя', $data['status']));
    $Tab2 = $PHPShopGUI->setPay(false, false, $data['version'], false);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1,true),array("Рекламные кампании", null,'?path=modules.dir.adanalyzer'),array("Отчеты", null,'?path=modules.dir.adanalyzer.stat'), array("О Модуле", $Tab2));

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