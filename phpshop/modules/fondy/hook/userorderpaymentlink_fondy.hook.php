<?php

function userorderpaymentlink_mod_fondy_hook($obj, $PHPShopOrderFunction)
{
    global $PHPShopSystem;
    include_once 'phpshop/modules/fondy/class/Fondy.php';
    $fondy = new Fondy();

    $currencyISO = $PHPShopSystem->getDefaultValutaIso();

    // Контроль оплаты от статуса заказа
    if ($PHPShopOrderFunction->order_metod_id == 10034)
        if ($PHPShopOrderFunction->getParam('statusi') == $fondy->option['status_checkout'] or empty($fondy->option['status_checkout'])) {

            $order = $PHPShopOrderFunction->unserializeParam('orders');

            $fondy->option['currency'] = $currencyISO;
            $fondy->option['order_id'] = 'order_' . $order['Person']['ouid'];
            $fondy->option['order_desc'] = 'Order ' . $order['Person']['ouid'];
            $fondy->option['amount'] = ($PHPShopOrderFunction->getTotal() * 100);

            if (!$linkPayment = $fondy->isLinkPayment()) {
                $linkData = $fondy->getPaymentLink();
                if ($linkData['response']['response_status'] == 'success') {
                    $linkPayment = $linkData['response']['checkout_url'];
                    $fondy->log($fondy->option, $fondy->option['order_id'], 'Форма подготовлена для отправки', 'Регистрация заказа');
                    //$fondy->log("Link payment", $fondy->option['order_id'], $linkPayment, 'link');
                    $hash = md5($fondy->option['order_id'] . $fondy->option['amount'] . $fondy->option['merchant_id']);
                    $_SESSION[$hash] = $linkPayment;
                    $obj->set('payment_forma', PHPShopText::a($linkPayment, 'Оплатить', 'Оплатить с помощью FONDY', false, false, false, "btn btn-primary"));
                } else {
                    $obj->set('payment_forma', PHPShopText::message($linkData['response']['error_message']));
                }
            } else {
                $obj->set('payment_forma', PHPShopText::a($linkPayment, 'Оплатить', 'Оплатить с помощью FONDY', false, false, false, "btn btn-primary"));
            }

            $return = ParseTemplateReturn($GLOBALS['SysValue']['templates']['fondy']['fondy_payment_forma'], true);
        } elseif ($PHPShopOrderFunction->getSerilizeParam('orders.Person.order_metod') == 10034)
            $return = 'Заказ обрабатывается менеджером';
    return $return;
}

$addHandler = array('userorderpaymentlink' => 'userorderpaymentlink_mod_fondy_hook');
