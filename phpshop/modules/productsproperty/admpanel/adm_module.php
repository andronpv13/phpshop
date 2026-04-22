<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.productsproperty.productsproperty_system"));

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

// Функция обновления
function actionUpdate() {
    global $PHPShopModules,$PHPShopOrm;

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);
    $PHPShopOrm->update($_POST);

    header('Location: ?path=modules&id='.$_GET['id']);
}

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    $data = $PHPShopOrm->select();

    $Tab1 = '<p>Модуль позволяет выводить однотипные товары с разными свойствами в виде единой карточки.</p>
        <h4>Настройка товара</h4>
        <p>При редактировании товара во вкладке <kbd>Модули</kbd> - "<b>Свойства</b>" есть возможность настроить свойства и ссылки на товары.</p>
<h4>Настройка шаблона</h4>
    <p><kbd>@productsproperty@</kbd> - переменная отвечает за вывод блока в шаблоне подробного описания товара <code>/phpshop/templates/имя_шаблона/product/main_product_forma_full.tpl</code></p>

    ';

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Инструкция", $PHPShopGUI->setInfo($Tab1)), array("О Модуле", $PHPShopGUI->setPay(false, false, $data['version'], true)));

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
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>