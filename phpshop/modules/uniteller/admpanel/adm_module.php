<?php
PHPShopObj::loadClass('order');

// SQL
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['uniteller']['uniteller_system']);
// Функция обновления
function actionUpdate() {
    global $PHPShopOrm;
    $PHPShopOrm->debug = false;

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();

    $Tab2  = $PHPShopGUI->setField('Uniteller Point ID:', $PHPShopGUI->setInputText(false, 'shop_idp_new', $data['shop_idp'], 300));
    $Tab2 .= $PHPShopGUI->setField('Пароль:', $PHPShopGUI->setInput("password", 'password_new', $data['password'], false, 300));

    // Система налогообложения
    $tax_system = array (
        array("Общая система налогообложения", 0, $data["taxationSystem"]),
        array("Упрощенная система налогообложения (Доход)", 1, $data["taxationSystem"]),
        array("Упрощенная система налогообложения (Доход минус Расход)", 2, $data["taxationSystem"]),
        array("Единый налог на вмененный доход", 3, $data["taxationSystem"]),
        array("Единый сельскохозяйственный налог", 4, $data["taxationSystem"]),
        array("Патентная система налогообложения", 5, $data["taxationSystem"])
    );
    $Tab2 .= $PHPShopGUI->setField('Система налогообложения:', $PHPShopGUI->setSelect('taxationSystem_new', $tax_system, 300,true));

    // Доступые статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('Новый заказ'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);

    // Статус заказа
    $Tab2 .= $PHPShopGUI->setField('Оплата при статусе:', $PHPShopGUI->setSelect('status_new', $order_status_value, 300));

    $Tab2 .= $PHPShopGUI->setField('Сообщение предварительной проверки:', $PHPShopGUI->setTextarea('title_sub_new', $data['title_sub']));

    $Tab2 .= $PHPShopGUI->setField('Описание оплаты:', $PHPShopGUI->setTextarea('title_payment_new', $data['title_payment']));

    // Инструкция
    $info = '
        <h4>Настройка модуля</h4>
        <ol>
<li>Предоставить необходимые документы и заключить договор с <a href="https://www.uniteller.ru" target="_blank">Uniteller</a></li>
<li>На закладке настройки ввести "Uniteller Point ID", который можно найти в Личном кабинете Uniteller, в меню "Точки продаж".</li>
<li>На закладке настройки ввести "Пароль", который можно найти в Личном кабинете Uniteller, в меню "Параметры Авторизации".</li>
<li>В личном кабинете <a href="https://www.uniteller.ru" target="_blank">Uniteller</a> указать Check URL: <code>https://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/uniteller/payment/check.php</code> <br></li>
</ol>
';
    
    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay(null, false, $data['version'], false);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Настройки", $Tab2, true), array("Инструкция", $PHPShopGUI->setInfo($info)), array("О Модуле", $Tab3));

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