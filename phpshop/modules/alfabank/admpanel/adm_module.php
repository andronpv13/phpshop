<?php

PHPShopObj::loadClass('order');

// SQL
$PHPShopOrm = new PHPShopOrm("phpshop_modules_alfabank_system");

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    if (empty($_POST["dev_mode_new"]))
        $_POST["dev_mode_new"] = 0;

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();

    $Tab2 = $PHPShopGUI->setField('Логин API магазина', $PHPShopGUI->setInputText(false, 'login_new', $data['login'], 300));
    $Tab2 .= $PHPShopGUI->setField('Пароль магазина', $PHPShopGUI->setInput("password", 'password_new', $data['password'], false, 300));
    
    $api = array(
        array('pay.alfabank.ru', 'https://pay.alfabank.ru/payment/rest/register.do', $data['api_url']),
        array('payment.alfabank.ru', 'https://payment.alfabank.ru/payment/rest/register.do', $data['api_url']),
        array('ecom.alfabank.ru', 'https://ecom.alfabank.ru/api/rest/register.do', $data['dev_mode']),
    );
    
    $dev = array(
        array(__('Ничего не выбрано'), 0, $data['dev_mode']),
        array('alfa.rbsuat.com', 'https://alfa.rbsuat.com/payment/rest/register.do', $data['dev_mode']),
        array('tws.egopay.ru', 'https://tws.egopay.ru/api/ab/rest/register.do', $data['dev_mode']),
    );
    
    $Tab2.= $PHPShopGUI->setField('URL адрес API', $PHPShopGUI->setSelect('api_url_new', $api, 300));
    $Tab2 .= $PHPShopGUI->setField('Тестовый URL адрес API', $PHPShopGUI->setSelect('dev_mode_new', $dev, 300),1,"Отправка данных на тестовую среду Альфабанка");

    // Система налогообложения
    $tax_system = array(
        array("Общая система налогообложения", 0, $data["taxationSystem"]),
        array("Упрощенная система налогообложения (Доход)", 1, $data["taxationSystem"]),
        array("Упрощенная система налогообложения (Доход минус Расход)", 2, $data["taxationSystem"]),
        array("Единый налог на вмененный доход", 3, $data["taxationSystem"]),
        array("Единый сельскохозяйственный налог", 4, $data["taxationSystem"]),
        array("Патентная система налогообложения", 5, $data["taxationSystem"])
    );
    $Tab2 .= $PHPShopGUI->setField('Cистема налогообложения', $PHPShopGUI->setSelect('taxationSystem_new', $tax_system, 300,true));

    // Доступые статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('Новый заказ'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);

    // Статус заказа
    $Tab2 .= $PHPShopGUI->setField('Оплата при статусе', $PHPShopGUI->setSelect('status_new', $order_status_value, 300));
    $Tab2 .= $PHPShopGUI->setField('Сообщение предварительной проверки', $PHPShopGUI->setTextarea('title_sub_new', $data['title_sub']));

    // Инструкция
    $info = '
        <h4>Настройка модуля</h4>
        <ol>
<li>Предоставить необходимые документы и заключить договор с <a href="https://pay.alfabank.ru/ecommerce/" target="blank">Альфабанком</a>.</li>
<li>На закладке настройки ввести предоставленные Альфанком Логин API магазина (*********-api) и Пароль магазина.</li>
<li>Во время тестирования включить "Режим разработки", данные будут отправляться на тестовую среду Альфабанка.</li>
<li>Указать сотрудникам Альфабанка URL Callback-уведомлений <code>https://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/alfabank/payment/check.php</code></li>
<li>Для перевода модуля в рабочий режим, выключить "Режим разработки".</a></li>
</ol>
<p>После регистрации в Альфабанке вам предоставляются доступы к тестовой среде, в модуле нужно отметить галочку "Режим разработки". После оформления тестового заказа (карты для тестирования можно найти в документации Альфабанка) нужно запросить доступы к рабочей среде Альфабанка, ввести в настройках модуля рабочий логин API и пароль, отключить "Режим разработки". </p>
';

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Настройки", $Tab2, true), array("Инструкция", $PHPShopGUI->setInfo($info)), array("О Модуле", $PHPShopGUI->setPay(false, false, $data['version'],true)));

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