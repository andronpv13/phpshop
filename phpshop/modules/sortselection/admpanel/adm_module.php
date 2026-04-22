<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.sortselection.sortselection_system"));

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
    global $PHPShopOrm, $PHPShopModules;

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $_POST['sort_new'] = serialize($_POST['sort_new']);

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

/**
 * Выбор характеристики
 */
function getSortValue($category, $sort) {
    global $PHPShopGUI;
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
    $PHPShopOrm->debug = false;

    $sort = unserialize($sort);

    $data = $PHPShopOrm->select(array('*'), array('category' => "='" . $category . "'"), array('order' => 'num'), array('limit' => 100));
    if (is_array($data))
        foreach ($data as $row) {

            if (@in_array($row['id'], $sort))
                $sel = 'selected';
            else
                $sel = false;

            $value[] = array($row['name'], $row['id'], $sel);
        }

    return $PHPShopGUI->setSelect('sort_new[]', $value, 300, false, false, true, false, 1, true);
}

/**
 * Выбор группы характеристики
 */
function getSort($n) {
    global $PHPShopGUI;
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), array('category' => "='0'"), array('order' => 'num'), array('limit' => 100));
    if (is_array($data))
        foreach ($data as $row) {

            if ($n == $row['id'])
                $sel = 'selected';
            else
                $sel = false;

            $value[] = array($row['name'], $row['id'], $sel);
        }

    return $PHPShopGUI->setSelect('sort_categories_new', $value, 300);
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();

    $value[] = array('всплывающее окно', 1, $data['enabled']);
    $value[] = array('блок', 2, $data['enabled']);
    
    $f_value[] = array('только на главной странице', 1, $data['flag']);
    $f_value[] = array('везде', 2, $data['flag']);


    $Tab1 = $PHPShopGUI->setField('Заголовок', $PHPShopGUI->setInputText(false, 'title_new', $data['title'], '100%'));
    $Tab1 .= $PHPShopGUI->setField('Группа характеристик', getSort($data['sort_categories']));
    
    if(!empty($data['sort_categories']))
    $Tab1 .= $PHPShopGUI->setField('Характеристики', getSortValue($data['sort_categories'], $data['sort']));
    
    $Tab1 .= $PHPShopGUI->setField('Шаблон вывода', $PHPShopGUI->setSelect('enabled_new', $value, 300, true));
    $Tab1.=$PHPShopGUI->setField('Место вывода', $PHPShopGUI->setSelect('flag_new', $f_value, 300,true));

    $info = 'Для вставки элемента следует в ручном режиме вставить переменную
        <kbd>@sortselection@</kbd> в файл главной страницы <code>phpshop/templates/имя_шаблона/main/index.tpl</code> своего шаблона.
        <p>Для персонализации формы вывода отредактируйте шаблоны <code>phpshop/modules/sortselection/templates/</code></p>';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay($data['serial'], false, $data['version'], true);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("Инструкция", $Tab2), array("О Модуле", $Tab3));

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