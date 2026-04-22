<?php

function userorderpaymentlink_mod_robokassa_hook($obj, $PHPShopOrderFunction) {
    global $PHPShopSystem;

    // Настройки модуля
    include_once(dirname(__FILE__) . '/mod_option.hook.php');

    $PHPShopRobokassaArray = new PHPShopRobokassaArray();
    $option = $PHPShopRobokassaArray->getArray();

    // Контроль оплаты от статуса заказа
    if ($PHPShopOrderFunction->order_metod_id == 10020)
        if ($PHPShopOrderFunction->getParam('statusi') == $option['status'] or empty($option['status'])) {

            // Номер счета
            $mrh_ouid = explode("-", $PHPShopOrderFunction->objRow['uid']);
            $inv_id = $mrh_ouid[0] . "" . $mrh_ouid[1];

            // Сумма покупки
            $out_summ = $PHPShopOrderFunction->getTotal();

            // Платежная форма
            $payment_forma .= PHPShopText::setInput('hidden', 'MrchLogin', trim($option['merchant_login']), false, 10);
            $payment_forma .= PHPShopText::setInput('hidden', 'OutSum', $out_summ, false, 10);
            $payment_forma .= PHPShopText::setInput('hidden', 'InvId', $inv_id, false, 10);
            $payment_forma .= PHPShopText::setInput('hidden', 'Desc', $PHPShopOrderFunction->objRow['uid'], false, 10);

            // ОФД
            $order = $PHPShopOrderFunction->unserializeParam('orders');

            // НДС
            if ($PHPShopSystem->getParam('nds_enabled') == '') {
                $tax = $tax_delivery = 'none';
            } else {
                $tax = 'vat' . $PHPShopSystem->objRow['nds'];

                // НДС Доставки
                if (!empty($order['Cart']['dostavka'])) {
                    PHPShopObj::loadClass('delivery');
                    $PHPShopDelivery = new PHPShopDelivery($order['Person']['dostavka_metod']);

                    $tax_delivery = $PHPShopDelivery->getParam('ofd_nds');
                    if ($tax_delivery == '')
                        $tax_delivery = $tax;
                    else
                        $tax_delivery = 'vat' . $PHPShopDelivery->objRow['ofd_nds'];
                }
            }

            // Корзина
            if (is_array($order['Cart']['cart'])) {

                foreach ($order['Cart']['cart'] as $product) {
                    if ((float) $order['Person']['discount'] > 0 && empty($product['promo_price']))
                        $price = $product['price'] - ($product['price'] * (float) $order['Person']['discount'] / 100);
                    else
                        $price = $product['price'];

                    $ym_merchant_receipt['items'][] = array(
                        'name' => $product['name'],
                        'quantity' => floatval(number_format($product['num'], 3, '.', '')),
                        'sum' => floatval(number_format($price, 2, '.', '')) * floatval(number_format($product['num'], 3, '.', '')),
                        'tax' => $tax,
                        'payment_method' => 'full_prepayment',
                        'payment_object' => 'commodity'
                    );
                }
            }

            // Доставка
            if (!empty($order['Cart']['dostavka'])) {

                $ym_merchant_receipt['items'][] = array(
                    'name' => 'Доставка',
                    'quantity' => 1,
                    'sum' => floatval(number_format($order['Cart']['dostavka'], 2, '.', '')),
                    'tax' => $tax_delivery,
                    'payment_method' => 'full_prepayment',
                    'payment_object' => 'service'
                );
            }


            $Receipt = urlencode(PHPShopString::json_safe_encode($ym_merchant_receipt));

            // Подпись
            $crc = md5(trim($option['merchant_login']) . ':' . $out_summ . ':' . $inv_id . ':' . $Receipt . ':' . trim($option['merchant_key']));

            $payment_forma .= PHPShopText::setInput('hidden', 'Receipt', $Receipt, false, 10);
            $payment_forma .= PHPShopText::setInput('hidden', 'SignatureValue', $crc, false, 10);
            $payment_forma .= PHPShopText::setInput('hidden', 'Encoding', 'utf-8', false, 10);

            if ($option['dev_mode'] == 1)
                $payment_forma .= PHPShopText::setInput('hidden', 'IsTest', '1', false, 10);

            $payment_forma .= PHPShopText::setInput('submit', 'send', $option['title'], false, 250);

            // Данные в лог
            $PHPShopRobokassaArray->log(array('action' => 'user', 'MrchLogin' => trim($option['merchant_login']), 'sum' => $out_summ, 'Email' => $_POST['mail'], 'orderNumber' => $inv_id, 'Receipt' => $Receipt), $inv_id, 'форма готова к отправке', 'данные формы для отправки на оплату');

            if ($option['merchant_country'] == 'Россия')
                $return = PHPShopText::form($payment_forma, 'pay', 'post', 'https://auth.robokassa.ru/Merchant/Index.aspx');
            else
                $return = PHPShopText::form($payment_forma, 'pay', 'post', 'https://auth.robokassa.kz/Merchant/Index.aspx');
        } elseif ($PHPShopOrderFunction->getSerilizeParam('orders.Person.order_metod') == 10020)
            $return = $option['title_sub'];

    return $return;
}

$addHandler = array('userorderpaymentlink' => 'userorderpaymentlink_mod_robokassa_hook');
?>