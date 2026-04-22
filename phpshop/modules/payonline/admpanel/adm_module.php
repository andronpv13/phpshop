<?php
PHPShopObj::loadClass('order');

// SQL
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['payonline']['payonline_system']);

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm;
    $PHPShopOrm->debug = false;

    if (empty($_POST["fiskalization_new"]))
        $_POST["fiskalization_new"] = 0;

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();

    $Tab2  = $PHPShopGUI->setField('Merchant ID:', $PHPShopGUI->setInputText(false, 'merchant_id_new', $data['merchant_id'], 300));
    $Tab2 .= $PHPShopGUI->setField('Секретный ключ:', $PHPShopGUI->setInput("password", 'key_new', $data['key'], false, 300));

    // Доступые статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('Новый заказ'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);

    // Статус заказа
    $Tab2 .= $PHPShopGUI->setField('Оплата при статусе:', $PHPShopGUI->setSelect('status_new', $order_status_value, 300));
    $Tab2 .= $PHPShopGUI->setField('Сообщение предварительной проверки:', $PHPShopGUI->setTextarea('title_sub_new', $data['title_sub'],true,300));
    $Tab2 .= $PHPShopGUI->setField('Описание оплаты:', $PHPShopGUI->setTextarea('title_payment_new', $data['title_payment'],true,300));

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page']);
    $page = $PHPShopOrm->select(array('*'), false, array('order' => 'name asc'));

    $value = array();
    $value[] = array(__('Не использовать'), 0, $data['page_id']);
    if (is_array($page))
        foreach ($page as $val) {
            $value[] = array($val['name'], $val['id'], $data['page_id']);
        }

    $Tab2.=$PHPShopGUI->setField('Страница Договора Оферты:', $PHPShopGUI->setSelect('page_id_new', $value, 300));
    $Tab2 .= $PHPShopGUI->setField('Фискализация', $PHPShopGUI->setCheckbox("fiskalization_new", 1, "Включить фискализацию платежей", $data["fiskalization"]));

    // Инструкция
    $info = '
        <h4>Настройка модуля</h4>
        <ol>
<li>Предоставить необходимые документы и заключить договор с <a href="http://www.payonline.ru/" target="_blank">PayOnline</a></li>
<li>На закладке настройки ввести "Merchant ID".</li>
<li>На закладке настройки ввести "Секретный ключ".</li>
<li>В личном кабинете <a href="http://www.payonline.ru/" target="_blank">PayOnline</a> указать Check URL: <code>https://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/payonline/payment/check.php</code> <br></li>
</ol>';
    
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