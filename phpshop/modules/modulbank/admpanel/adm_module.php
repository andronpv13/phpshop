<?php
PHPShopObj::loadClass('order');

// SQL
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['modulbank']['modulbank_system']);


// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));
    header('Location: ?path=modules&id='.$_GET['id']);
    return $action;
}


// Функция обновления
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;
    
    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    if (empty($_POST["dev_mode_new"]))
        $_POST["dev_mode_new"] = 0;

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setInfo('<p>Модуль интеграции интернет-магазина с платежным шлюзом МодульБанка, позволяет проводить оплату заказа картой через МодульБанк.
 Перед началом работы, необходимо произвести необходимые настройки на соответствующей вкладке. Режим разработки позволяет отправлять запросы на тестовую среду МодульБанка</p>');

    $Tab2 = $PHPShopGUI->setField('Идентификатор магазина:', $PHPShopGUI->setInputText(false, 'merchant_new', $data['merchant'], 300));
    $Tab2 .= $PHPShopGUI->setField('Секретный ключ:', $PHPShopGUI->setInput("password", 'key_new', $data['key'], false, 300));

    // Система налогообложения
    $tax_system = array (
        array("Общая система налогообложения", 'osn', $data["taxationSystem"]),
        array("Упрощенная система налогообложения (Доход)", 'usn_income', $data["taxationSystem"]),
        array("Упрощенная система налогообложения (Доход минус Расход)", 'usn_income_outcome', $data["taxationSystem"]),
        array("Единый налог на вмененный доход", 'envd', $data["taxationSystem"]),
        array("Единый сельскохозяйственный налог", 'esn', $data["taxationSystem"]),
        array("Патентная система налогообложения", 'patent', $data["taxationSystem"])
    );
    $Tab2 .= $PHPShopGUI->setField('Cистема налогообложения:', $PHPShopGUI->setSelect('taxationSystem_new', $tax_system, 300,true));

    // Доступые статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('Новый заказ'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);

    // Статус заказа
    $Tab2 .= $PHPShopGUI->setField('Оплата при статусе:', $PHPShopGUI->setSelect('status_new', $order_status_value, 300));

    $Tab2 .= $PHPShopGUI->setField('Режим разработки:', $PHPShopGUI->setCheckbox("dev_mode_new", 1, "Отправка данных на тестовую среду", $data["dev_mode"]));

    $Tab2 .= $PHPShopGUI->setField('Сообщение предварительной проверки:', $PHPShopGUI->setTextarea('title_sub_new', $data['title_sub']));

    $Tab2 .= $PHPShopGUI->setField('Описание оплаты:', $PHPShopGUI->setTextarea('title_payment_new', $data['title_payment']));

    // Инструкция
    $info = '
        <h4>Настройка модуля</h4>
        <ol>
<li>Предоставить необходимые документы и заключить договор с <a href="https://modulbank.ru/ekvayring/internet" target="_blank">МодульБанком</a></li>
<li>На закладке настройки ввести "Идентификатор магазина", полученный от МодульБанка.</li>
<li>На закладке настройки ввести "Секретный ключ", полученный от МодульБанка.</li>
<li>Во время тестирования включить "Режим разработки", ввести тестовый "Секретный ключ", данные будут отправляться на тестовую среду МодульБанка</li>
<li>Для перевода модуля в рабочий режим, выключить "Режим разработки", ввести рабочий "Секретный ключ".</a></li>
</ol>
';
    
    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], true);

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