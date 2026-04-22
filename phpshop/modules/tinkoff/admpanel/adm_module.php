<?php

$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.tinkoff.tinkoff_system"));

/**
 * Обновление версии модуля
 * @return mixed
 */
function actionBaseUpdate(){
    global $PHPShopModules, $PHPShopOrm;

    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

/**
 * Обновление настроек
 * @return mixed
 */
function actionUpdate(){
    global $PHPShopOrm,$PHPShopModules;
    
    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    if (empty($_POST["force_payment_new"]))
        $_POST["force_payment_new"] = 0;

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);

    return $action;
}

/**
 * Отображение настроек модуля
 * @return bool
 */
function actionStart()
{
    global $PHPShopGUI, $PHPShopOrm;

    PHPShopObj::loadClass('order');

    $PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['tinkoff']['tinkoff_system'];
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('Наименование типа оплаты', $PHPShopGUI->setInputText(false, 'title_new', $data['title']));
    $Tab1 .= $PHPShopGUI->setField('Шлюз', $PHPShopGUI->setInputText(false, 'gateway_new', $data['gateway'], 300));
    $Tab1 .= $PHPShopGUI->setField('Терминал', $PHPShopGUI->setInputText(false, 'terminal_new', $data['terminal'], 300));
    $Tab1 .= $PHPShopGUI->setField('Секретный ключ', $PHPShopGUI->setInputText(false, 'secret_key_new', $data['secret_key'], 300));
    $Tab1 .= $PHPShopGUI->setField('Переходить к оплате без подтверждения', $PHPShopGUI->setCheckbox("force_payment_new", 1, "После оформления заказа открывать страницу оплаты", $data["force_payment"]));

    $onclick = "function toggleTaxation() { document.getElementsByClassName('tinkoff-taxation')[0].classList.toggle('hidden'); }     
        toggleTaxation();";

    $Tab1 .= $PHPShopGUI->setField("Передавать данные для формирования чека", $PHPShopGUI->setRadio("enabled_taxation_new", 1, "Да", $data['enabled_taxation'], $onclick)
        . $PHPShopGUI->setRadio("enabled_taxation_new", 0, "Нет", $data['enabled_taxation'], $onclick));
    
    
    // Инструкция
    $info = '
        <h4>Настройка модуля</h4>
        <ol>
<li>Предоставить необходимые документы и <a href="https://www.tbank.ru/kassa/form/partner/phpshop/" target="blank">заключить договор с Т-Банк</a>.</li>
<li>На закладке настройки ввести предоставленные банком Т-Банк адрес "Шлюза", код "Терминала" и "Секрентый ключ".</li>
<li>Выбрать режим налогообложения товаров при включенном флаге передаче данных данных для формирования чека.</a></li>
<li>Выбрать режим налогообложения доставки в карточке редактирования доставки.</a></li>
<li>В личном кабинете Т-Банк в разделе "Магазины" указать адрес для уведомлений о кассовых чеках <code>http://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/tinkoff/payment/notification.php</code></li>
<li>В личном кабинете Т-Банк в разделе "Магазины" указать URL страницы успешного платежа <code>http://' . $_SERVER['SERVER_NAME'] . '/success/?payment=tinkoff</code></li>
<li>В личном кабинете Т-Банк в разделе "Магазины" указать URL страницы неуспешного платежа <code>http://' . $_SERVER['SERVER_NAME'] . '/fail/</code></li>
</ol>';

    $taxation = array(
        array('Общая СН', 'osn', $data['taxation']),
        array('Упрощенная СН (доходы)', 'usn_income', $data['taxation']),
        array('Упрощенная СН (доходы минус расходы) ', 'usn_income_outcome', $data['taxation']),
        array('Единый налог на вмененный доход', 'envd', $data['taxation']),
        array('Единый сельскохозяйственный налог', 'esn', $data['taxation']),
        array('Патентная СН', 'patent', $data['taxation']),
    );
    $taxationSelect = $PHPShopGUI->setSelect('taxation_new', $taxation, 300,true);
    $Tab1 .= $PHPShopGUI->setField('Система налогообложения', $taxationSelect, 1, null, 'tinkoff-taxation' . ($data['enabled_taxation'] ? '' : ' hidden'));

    // Доступые статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('Новый заказ'), 0, $data['status']);
    $order_status_receipt_value[] = array(__('Не выбрано'), 0, $data['status_receipt']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status){
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);
            $order_status_confirmed_value[] = array($order_status['name'], $order_status['id'], $data['status_confirmed']);
            $order_status_receipt_value[] = array($order_status['name'], $order_status['id'], $data['status_receipt']);
        }

    // Статус заказа
    $Tab1.= $PHPShopGUI->setField('Оплата при статусе', $PHPShopGUI->setSelect('status_new', $order_status_value, 300));
    $Tab1.= $PHPShopGUI->setField('Статус после подверждении заказа', $PHPShopGUI->setSelect('status_confirmed_new', $order_status_confirmed_value, 300));
    
    $payment_mode_value[] = array('Полная предоплата', 1, $data['payment_mode']);
    $payment_mode_value[] = array('Полный расчет', 2, $data['payment_mode']);
    
    $Tab1 .= $PHPShopGUI->setField('Способ расчета', $PHPShopGUI->setSelect('payment_mode_new', $payment_mode_value , 300, true));
    $Tab1 .= $PHPShopGUI->setField('Второй чек при статусе', $PHPShopGUI->setSelect('status_receipt_new', $order_status_receipt_value , 300));

    $Tab1.=$PHPShopGUI->setField('Описание оплаты', $PHPShopGUI->setTextarea('title_end_new', $data['title_end']));

    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay(null, false, $data['version'], true);
    
    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1,true),array("Инструкция", $PHPShopGUI->setInfo($info)),array("О Модуле", $Tab3));

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
$PHPShopGUI->setAction($_GET['id'], 'actionStart');
