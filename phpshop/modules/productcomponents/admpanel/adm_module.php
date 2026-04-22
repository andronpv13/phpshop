<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.productcomponents.productcomponents_system"));

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

// Функция обновления
function actionUpdate() {
    global $PHPShopModules, $PHPShopOrm;

    if (empty($_POST['product_search_new']))
        $_POST['product_search_new'] = 0;

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm->update($_POST);

    header('Location: ?path=modules&id=' . $_GET['id']);
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('Помощь в подборе товара', $PHPShopGUI->setCheckbox('product_search_new', 1, null, $data['product_search']));
    
        $export_value[]=['Цены и склад', 0, $data['logic']];
    $export_value[]=['Цены', 1, $data['logic']];
    $export_value[]=['Склад', 2, $data['logic']];
    $Tab1 .= $PHPShopGUI->setField('Обновление данных', $PHPShopGUI->setSelect('logic_new', $export_value, false,true));

    $Info = '<p>Модуль позволяет рассчитывать цену и количество сборного товара на основе его комплектующих.</p>
        <h4>Настройка товара</h4>
        <p>При редактирование товара во вкладке <kbd>Модули</kbd> - <kbd>Комплектующие</kbd> есть возможность настроить список комплектующих товаров и скидку.</p>
    <p>Для автоматического расчета по расписанию следует добавить новую задачу в модуль <a href="https://docs.phpshop.ru/moduli/razrabotchikam/cron" target="_blank">Задачи</a> с адресом запускаемого файла <code>phpshop/modules/productcomponents/cron/products.php</code>. Остатки и цены рассчитываются так же при редактировании карточки товара в магазине.';

    // Содержание закладки 2
    $Tab3 = $PHPShopGUI->setPay($serial = false, false, $data['version'], true);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("Инструкция", $Info), array("О Модуле", $Tab3));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>