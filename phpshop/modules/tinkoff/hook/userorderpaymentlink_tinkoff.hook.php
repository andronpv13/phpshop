<?php

function userorderpaymentlink_tinkoff_hook($obj, $PHPShopOrderFunction) {
    global $PHPShopSystem;

    // Настройки модуля
    include_once $GLOBALS['SysValue']['class']['tinkoff'];
    $tinkoff = new Tinkoff();

    // Контроль оплаты от статуса заказа
    if ($PHPShopOrderFunction->order_metod_id == 10032)
        if ($PHPShopOrderFunction->getParam('statusi') == $tinkoff->settings['status'] or empty($tinkoff->settings['status'])) {
            $email['mail'] = $PHPShopOrderFunction->getMail();

            $obj->ouid = $PHPShopOrderFunction->objRow['uid'];

            $obj->tinkoff_total = floatval(number_format($PHPShopOrderFunction->getTotal(), 2, '.', '')) * 100;
            $order = $PHPShopOrderFunction->unserializeParam('orders');
            $obj->tinkoff_cart = $order['Cart']['cart'];
            $obj->discount = $order['Person']['discount'];

            // Доставка
            if (!empty($order['Cart']['dostavka'])) {

                PHPShopObj::loadClass('delivery');
                $PHPShopDelivery = new PHPShopDelivery($order['Person']['dostavka_metod']);

                if($ofd_nds = $PHPShopDelivery->getParam('ofd_nds'))
                    $tax = $PHPShopDelivery->getParam('ofd_nds');
                else
                    $tax = $PHPShopSystem->getParam('nds');

                $obj->tinkoff_delivery_nds = $tax;

                $obj->delivery = floatval(number_format($order['Cart']['dostavka'], 2, '.', ''));
            }

            $request = $tinkoff->getPaymentUrl($obj, $email);

            $return = PHPShopText::setInput('button', 'send', __("Оплатить заказ"), $float = "none", 250, "window.location.replace('" . $request['url'] . "')");

        } elseif ($PHPShopOrderFunction->getSerilizeParam('orders.Person.order_metod') == 10032)
            $return = ', Заказ обрабатывается менеджером';

    return $return;
}

$addHandler = array
    (
    'userorderpaymentlink' => 'userorderpaymentlink_tinkoff_hook'
);
?>