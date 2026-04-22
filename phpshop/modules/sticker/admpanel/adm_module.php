<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.sticker.sticker_system"));
$PHPShopOrm->debug = false;

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    if (empty($_POST['editor_new']))
        $_POST['editor_new'] = 0;


    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));
}

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('Визуальный редактор', $PHPShopGUI->setCheckbox('editor_new', 1, null, $data['editor']));

    $Info = '<p>Для вывода стикера в шаблоне используйте переменную <kbd>@sticker_маркер@</kbd>. 
        Маркер указывается в одноименном поле карточки редактирования стикера. 
        Имя маркера обязательно должно быть на латинском языке.
        </p> 
         <p>
         Для интеграции стикера в ручном режиме включите следующий код в содержание страницы или текстового блока:
        <p>
        <pre>@php
$PHPShopStickerElement = new PHPShopStickerElement();
echo $PHPShopStickerElement->forma("маркер стикера");
php@</pre>
         </p>';

    $Tab2 = $PHPShopGUI->setInfo($Info);


    // Содержание закладки 2
    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], true);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("Инструкция", $Tab2), array("О Модуле", $Tab3), array("Стикеры", null, '?path=modules.dir.sticker'));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>