<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.errorlog.errorlog_system"));

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    if (empty($_POST['enabled_new']))
        $_POST['enabled_new'] = 0;
    
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

// Функция очистки
function actionClean() {
    global $PHPShopModules;
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.errorlog.errorlog_log")); 
    $action = $PHPShopOrm->delete(array('id' => '>0'));
    header('Location: ?path=modules.dir.errorlog');
    return $action;
}

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $TitlePage, $select_name;

    $PHPShopGUI->action_button['Очистить'] = array(
        'name' => __('Очистить журнал'),
        'action' => 'cleanID',
        'class' => 'btn  btn-default btn-sm navbar-btn',
        'type' => 'submit',
        'icon' => 'glyphicon glyphicon-import'
    );

    $PHPShopGUI->setActionPanel($TitlePage, $select_name, array('Очистить', 'Сохранить и закрыть'));

    // Выборка
    $data = $PHPShopOrm->select();

    switch ($data['enabled']) {
        case 0: $enabled_chek_0 = 'selected';
            break;
        case 1: $enabled_chek_1 = 'selected';
            break;
        default: $enabled_chek_2 = 'selected';
    }

    $option[] = array('Не записывать ошибки', 0, $enabled_chek_0);
    $option[] = array('Записывать только ошибки', 1, $enabled_chek_1);
    $option[] = array('Записывать ошибки и отладки', 2, $enabled_chek_2);
    $Tab1 = $PHPShopGUI->setField('Тип записи', $PHPShopGUI->setSelect('enabled_new', $option,250,true));

    $Info = 'Для внесения пользовательской отладочной информации в общий лог необходимо указать следующий код в месте отладки своей функции:
        <p><code>trigger_error("Текст отладки", E_USER_NOTICE);</code></p>
        Не рекомендуется держать все время включенный модуль, так как он переполняет базу данных отладками.
';
    $Tab2 = $PHPShopGUI->setInfo($Info);

    // Содержание закладки 2
    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], false);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("Инструкция", $Tab2, true), array("О Модуле", $Tab3), array("Журнал событий", 0, '?path=modules.dir.errorlog'));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit") .
            $PHPShopGUI->setInput("submit", "cleanID", "Применить", "right", 80, "", "but", "actionClean.modules.edit");
    ;

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>