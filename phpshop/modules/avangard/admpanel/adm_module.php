<?php
PHPShopObj::loadClass('order');

// SQL
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['avangard']['avangard_system']);

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate(number_format($option['version'], 1, '.', false));
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm;
    $PHPShopOrm->debug = false;

    if (empty($_POST["dev_mode_new"]))
        $_POST["dev_mode_new"] = 0;

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    include_once '../modules/avangard/class/Avangard.php';

    // Выборка
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setInfo('<p>Модуль интеграции интернет-магазина с платежным шлюзом банка Авангард, позволяет проводить оплату заказа банковской картой.
 Перед началом работы, необходимо произвести необходимые настройки на соответствующей вкладке.</p>');

    $Tab2 = $PHPShopGUI->setField('ID магазина:', '<input class="form-control input-sm" type="number" step="1" min="0" value="' . $data['shop_id'] . '" name="shop_id_new" style="width:300px; ">');
    $Tab2 .= $PHPShopGUI->setField('Пароль магазина:', $PHPShopGUI->setInput('password', 'password_new', $data['password'], false, 300));
    $Tab2 .= $PHPShopGUI->setField('Подпись магазина:', $PHPShopGUI->setInput('text', 'shop_sign_new', $data['shop_sign'], false, 300));
    $Tab2 .= $PHPShopGUI->setField('Подпись системы эквайринга:', $PHPShopGUI->setInput('text', 'av_sign_new', $data['av_sign'], false, 300));
    
    $qr_value = array(
        array('Off', 0, $data['qr']),
        array('On', 1, $data['qr'])
    );
    $Tab2 .= $PHPShopGUI->setField('Оплата по QR:', $PHPShopGUI->setSelect('qr_new', $qr_value, 0));

    // Доступые статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('Новый заказ'), 0, $data['status_id']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status_id']);

    // Статус заказа
    $Tab2 .= $PHPShopGUI->setField('Оплата при статусе:', $PHPShopGUI->setSelect('status_id_new', $order_status_value, 300));
    $Tab2 .= $PHPShopGUI->setField('Сообщение предварительной проверки:', $PHPShopGUI->setTextarea('title_sub_new', $data['title_sub'], false, 300));
    $Tab2 .= $PHPShopGUI->setField('Описание оплаты:', $PHPShopGUI->setTextarea('title_payment_new', $data['title_payment'], false, 300));

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page']);
    $page = $PHPShopOrm->select(array('*'), false, array('order' => 'name asc'));

    $value = array();
    $value[] = array(__('Не использовать'), 0, $data['page_id']);
    if (is_array($page))
        foreach ($page as $val) {
            $value[] = array($val['name'], $val['id'], $data['page_id']);
        }

    $Tab2.=$PHPShopGUI->setField('Страница Договора Оферты:', $PHPShopGUI->setSelect('page_id_new', $value, 300));

    // Инструкция
    $info = '
        <h4>Настройка модуля</h4>
        <ol>
<li>Предоставить необходимые документы и заключить договор с банком <a href="https://www.avangard.ru/rus/" target="_blank">Авангард</a></li>
<li>На закладке настройки ввести "ID магазина", полученный от Банка.</li>
<li>На закладке настройки ввести "Пароль магазина", полученный от Банка.</li>
<li>На закладке настройки ввести "Подпись магазина", полученную от Банка.</li>
<li>На закладке настройки ввести "Подпись системы эквайринга", полученную от Банка.</li>
<li>В личном кабинете <a href="https://www.avangard.ru/rus/" target="_blank">Авангард</a> указать URL уведомления об успешном платеже: <code>' . Avangard::getProtocol() . $_SERVER['SERVER_NAME'] . '/phpshop/modules/avangard/payment/check.php</code> <br></li>
</ol>
';
	$Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], true);
	
	$contacts = 'По вопросам работы модуля свяжитесь с нами e-com@avangard.ru';

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Настройки", $Tab2, true), array("Инструкция", $PHPShopGUI->setInfo($info), true), array("О Модуле", $Tab3), array("Поддержка", $PHPShopGUI->setInfo($contacts)));

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