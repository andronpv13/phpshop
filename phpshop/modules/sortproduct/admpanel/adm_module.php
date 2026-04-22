<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.sortproduct.sortproduct_system"));

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm;

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&install=check');
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();


    $e_value[] = array('не выводить', 0, $data['enabled']);
    $e_value[] = array('слева', 1, $data['enabled']);
    $e_value[] = array('справа', 2, $data['enabled']);


    $Tab1 = $PHPShopGUI->setField('Заголовок', $PHPShopGUI->setInputText(false, 'title_new', $data['title'],300));
    $Tab1.=$PHPShopGUI->setField('Место вывода', $PHPShopGUI->setSelect('enabled_new', $e_value, 150,true));

    $info = 'Для произвольной вставки элемента следует выбрать парамет вывода "Не выводить" и в ручном режиме вставить переменную
        <kbd>@brandproduct@</kbd> в свой шаблон.
        <p>Для персонализации формы вывода отредактируйте шаблоны <code>phpshop/modules/sortproduct/templates/</code></p>';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay();

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1,true), array("Инструкция", $Tab2), array("О Модуле", $Tab3),array("Элементы", null,'?path=modules.dir.sortproduct'));

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