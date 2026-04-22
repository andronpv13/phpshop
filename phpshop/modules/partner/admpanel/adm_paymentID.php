<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.partner.partner_payment"));

/**
 * Экшен сохранения
 */
function actionSave() {
    global $PHPShopGUI;


    // Сохранение данных
    actionUpdate();

    header('Location: ?path=' . $_GET['path']);
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules, $PHPShopSystem;

    // Списываем баланс партнера
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.partner.partner_users"));
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('id,money'), array('login' => "='" . $_POST['partnerLogin'] . "'"), false, array('limit' => 1));
    if (is_array($data))
        if ($data['money'] >= $_POST['sum']) {
            $money = $data['money'];
            $total = $money - $_POST['sum'];
            $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.partner.partner_users"));
            $PHPShopOrm->debug = false;
            $action = $PHPShopOrm->update(array('money_new' => $total), array('id' => '=' . $data['id']));
        }

    if ($action == true) {

        if (empty($_POST['enabled_new']))
            $_POST['enabled_new'] = 0;
        $_POST['date_done_new'] = time();

        // Сообщение пользователю
        if (!empty($_POST['sendmail'])) {
            PHPShopObj::loadClass("mail");
            new PHPShopMail($_POST['login'], $PHPShopSystem->getValue('adminmail2'), 'Выплаты партнерам - ' . $PHPShopSystem->getValue('name'), $_POST['content']);
        }

        $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.partner.partner_payment"));
        $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    }


    return array('success' => $action);
}

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopOrm, $PHPShopModules;


    // Выборка
    $PHPShopOrm->sql = 'SELECT a.*, b.login, b.money, b.content FROM ' . $PHPShopModules->getParam("base.partner.partner_payment") . ' AS a JOIN ' . $PHPShopModules->getParam("base.partner.partner_users") . ' AS b ON a.partner_id = b.id where a.id=' . $_GET['id'] . ' limit 1';
    $dataArray = $PHPShopOrm->select();
    $data = $dataArray[0];

    $Tab1 = $PHPShopGUI->setField('Логин', $PHPShopGUI->setInputText(null, 'login', $data['login']));
    $Tab1.=$PHPShopGUI->setField('Статус', $PHPShopGUI->setCheckbox('enabled_new', 1, 'Заявка выполнена', $data['enabled']));
    $Tab1.=$PHPShopGUI->setField('Баланс', $PHPShopGUI->setInputText(null, '', $data['money'], 150, $PHPShopSystem->getDefaultValutaCode()));
    $Tab1.=$PHPShopGUI->setField('Заявка', $PHPShopGUI->setInputText(null, 'sum', $data['sum'], 150, $PHPShopSystem->getDefaultValutaCode()));
    $Tab1.=$PHPShopGUI->setField('Реквизиты', $PHPShopGUI->setTextarea('content', $data['content'],true,400,100));

    $message = 'Уважаемый, ' . $data['login'] . '.
' . $GLOBALS['SysValue']['lang']['partner_money_ready'] . '
Выплачено ' . $data['sum'] . ' ' . $PHPShopSystem->getDefaultValutaCode();

    $Tab1.=$PHPShopGUI->setField('Сообщение', $PHPShopGUI->setTextarea('content', $message, false, false, 200) . $PHPShopGUI->setCheckbox('sendmail', 1, 'Отправить сообщение пользователю', $data['enabled']));



    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, 350));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "partnerLogin", $data['login'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
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