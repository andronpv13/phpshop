<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.sortproduct.sortproduct_forms"));

function checkName($name) {
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);
    $data = $PHPShopOrm->select(array('*'), array('name' => '="' . $name . '"'), false, array('limit' => 1));
    if (!empty($data['id']))
        return $data['id'];
}

function checkId($id) {
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . $id), false, array('limit' => 1));
    if (!empty($data['name']))
        return $data['name'];
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm;

    // Проверка значния характеристики
    if (is_numeric($_POST['value_name_new'])) {
        $_POST['value_id_new'] = $_POST['value_name_new'];
        $_POST['value_name_new'] = checkId($_POST['value_name_new']);
    } else {
        $_POST['value_id_new'] = checkName($_POST['value_name_new']);
    }


    if (empty($_POST['enabled_new']))
        $_POST['enabled_new'] = 0;

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array('success'=>$action);
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

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));


    $Tab1 = $PHPShopGUI->setField('Порядок:', $PHPShopGUI->setInputText(null, 'num_new', $data['num'], '100'));
    $Tab1.=$PHPShopGUI->setField('Количество ссылок:', $PHPShopGUI->setInputText(null, 'items_new', $data['items'], '100'));
    $Tab1.=$PHPShopGUI->setField('Статус:', $PHPShopGUI->setCheckbox('enabled_new', 1, 'Включить', $data['enabled']));
    $Tab1.=$PHPShopGUI->setField('Характеристика', getSortValue($data['sort']));
    $Tab1.=$PHPShopGUI->setField('Значение', $PHPShopGUI->setInputText(false, 'value_name_new', $data['value_name'], 300) . $PHPShopGUI->setHelp(__('Введите значение или ID выбранной').' <a href="?path=sort"><span class="glyphicon glyphicon-share-alt"></span>'.__('Характеристики').'</a>',false,false));
    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "Удалить", "right", 70, "", "but", "actionDelete.modules.edit") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.modules.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.modules.edit");

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

// Функция удаления
function actionDelete() {
    global $PHPShopOrm;
    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array("success" => $action);
}

// Обработка событий
$PHPShopGUI->getAction();


// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>