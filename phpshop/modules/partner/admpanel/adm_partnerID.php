<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.partner.partner_users"));
$TitlePage = __('Редактирование Партнера') . ' #' . $_GET['id'];

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm;

    if (empty($_POST['enabled_new']))
        $_POST['enabled_new'] = 0;

    $_POST['password_new'] = base64_encode($_POST['password_new']);
    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array('success' => $action);
}

/**
 * Экшен сохранения
 */
function actionSave() {
    global $PHPShopGUI;


    // Сохранение данных
    actionUpdate();

    header('Location: ?path=' . $_GET['path']);
}

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm,$PHPShopSystem;

    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));
    
    $PHPShopGUI->action_select['Все заказы'] = array(
        'name' => 'Все заказы партнера',
        'url' => '?path=modules.dir.partner.log&where[partner_id]=' . $data['id'],
    );
    
   $PHPShopGUI->setActionPanel(__("Редактирование Партнера"), array('Все заказы', '|', 'Удалить'), array('Сохранить и закрыть'));

    // Знак рубля
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();

    $PHPShopGUI->field_col = 2;
    $Tab1 = $PHPShopGUI->setField('Имя', $PHPShopGUI->setInputText(false, 'name_new', $data['name'], 400));
    $Tab1 .= $PHPShopGUI->setField('Баланс', $PHPShopGUI->setInputText(false, 'money_new', (int) $data['money'], 100,$currency));
    $Tab1 .= $PHPShopGUI->setField('Логин', $PHPShopGUI->setInputText(false, 'login_new', $data['login'], 400));
    $Tab1 .= $PHPShopGUI->setField('Пароль', $PHPShopGUI->setInputText(false, 'password_new', base64_decode($data['password']), 400));
    $Tab1 .= $PHPShopGUI->setField('Статус', $PHPShopGUI->setCheckbox('enabled_new', 1, 'Активирован', $data['enabled']));
    $Tab1 .= $PHPShopGUI->setField('Реквизиты', $PHPShopGUI->setTextarea('content_new', $data['content'], true, 400, 100));

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "Удалить", "right", 70, "", "but", "actionDelete.modules.edit") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.modules.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
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