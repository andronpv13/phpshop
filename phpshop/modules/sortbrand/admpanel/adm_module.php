<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.sortbrand.sortbrand_system"));

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm;

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id='.$_GET['id']);
    return $action;
}

/**
 * Выбор характеристики
 */
function getSortValue($n) {
    global $PHPShopGUI;
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), array('filtr' => "='1'", 'goodoption' => "!='1'"), array('order' => 'num'), array('limit' => 100));
    if (is_array($data))
        foreach ($data as $row) {

            if ($n == $row['id'])
                $sel = 'selected';
            else
                $sel = false;

            $value[] = array($row['name'], $row['id'], $sel);
        }

    return $PHPShopGUI->setSelect('sort_new', $value, 300);
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();

    $e_value[] = array('не выводить', 0, $data['enabled']);
    $e_value[] = array('слева', 1, $data['enabled']);
    $e_value[] = array('справа', 2, $data['enabled']);

    $f_value[] = array('выпадающий список', 1, $data['flag']);
    $f_value[] = array('ссылки', 2, $data['flag']);


    $Tab1 = $PHPShopGUI->setField('Заголовок', $PHPShopGUI->setInputText(false, 'title_new', $data['title'],300));
    $Tab1.=$PHPShopGUI->setField('Характеристика', getSortValue($data['sort']));
    $Tab1.=$PHPShopGUI->setField('Место вывода', $PHPShopGUI->setSelect('enabled_new', $e_value, 300,true));
    $Tab1.=$PHPShopGUI->setField('Шаблон вывода', $PHPShopGUI->setSelect('flag_new', $f_value, 300,true));

    $info = 'Для произвольной вставки элемента следует выбрать параметр вывода "Не выводить" и в ручном режиме вставить переменную
        <kbd>@brand@</kbd> в свой шаблон.
        <p>Для персонализации формы вывода отредактируйте шаблоны <code>phpshop/modules/sortbrand/templates/</code></p>';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay();

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("Инструкция", $Tab2), array("О Модуле", $Tab3));

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