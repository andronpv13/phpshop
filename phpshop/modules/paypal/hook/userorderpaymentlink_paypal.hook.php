<?php

function price_mod_paypal_hook($PHPShopOrderFunction, $price, $currency_kurs) {
    return $PHPShopOrderFunction->returnSumma($price * $currency_kurs, $PHPShopOrderFunction->getDiscount());
}

function userorderpaymentlink_mod_paypal_hook($obj, $PHPShopOrderFunction) {
    global $PHPShopSystem, $PHPShopValutaArray;

    // Настройки модуля
    include_once(dirname(__FILE__) . '/mod_option.hook.php');
    $PHPShopArray = new PHPShopPaypalArray();
    $option = $PHPShopArray->getArray();

    // Тип оплаты
    $order_metod = $PHPShopOrderFunction->getSerilizeParam('orders.Person.order_metod');

    // Контроль оплаты от статуса заказа
    if ($order_metod == 10003)
        if (intval($PHPShopOrderFunction->getParam('statusi')) == $option['status']) {

            // Библиотека
            include_once($GLOBALS['SysValue']['class']['paypal']);

            // Номер счета
            $mrh_ouid = explode("-", $PHPShopOrderFunction->objRow['uid']);
            $inv_id = $mrh_ouid[0] . "" . $mrh_ouid[1];

            // Сумма покупки
            $out_summ = $PHPShopOrderFunction->getTotal();

            // Валюта расчета с PayPal

            if ($obj->PHPShopSystem->getDefaultValutaId != $option['currency_id']) {
                $currency_kurs = $PHPShopValutaArray->getParam(intval($option['currency_id']) . '.kurs');
                $currency_iso = $PHPShopValutaArray->getParam(intval($option['currency_id']) . '.iso');
            } else {
                $currency_kurs = 1;
                $currency_iso = $obj->PHPShopSystem->getDefaultValutaIso();
            }

            // Параметры запроса
            $requestParams = array(
                'RETURNURL' => 'http://' . $_SERVER['SERVER_NAME'] . '/success/?payment=modules&order_method=paypal',
                'CANCELURL' => 'http://' . $_SERVER['SERVER_NAME'] . '/fail/?from=paypal&order_id=' . $inv_id,
                'ADDROVERRIDE' => 1,
                'LOCALECODE' => 'RU',
                'EMAIL' => $PHPShopOrderFunction->getSerilizeParam('Person.mail'),
                'LANDINGPAGE' => 'Login',
                'BRANDNAME' => PHPShopString::win_utf8($obj->PHPShopSystem->getName())
            );


            /*
            // Адрес доставки
            if (strlen($value['adr_name']) > 100) {
                $delivery_1 = substr($PHPShopOrderFunction->getSerilizeParam('Person.adr_name'), 0, 100);
                $delivery_2 = substr($PHPShopOrderFunction->getSerilizeParam('Person.adr_name'), 100, 200);
                $requestParams['PAYMENTREQUEST_0_SHIPTOSTREET'] = PHPShopString::win_utf8($delivery_1);
                $requestParams['PAYMENTREQUEST_0_SHIPTOSTREET2'] = PHPShopString::win_utf8($delivery_2);
            }
            else
                $requestParams['PAYMENTREQUEST_0_SHIPTOSTREET'] = PHPShopString::win_utf8($PHPShopOrderFunction->getSerilizeParam('Person.adr_name'));
            */
            
            $orderParams = array(
                'PAYMENTREQUEST_0_AMT' => number_format($out_summ * $currency_kurs, 2, '.', ''),
                'PAYMENTREQUEST_0_SHIPPINGAMT' => number_format($PHPShopOrderFunction->getDeliverySumma(), 2, '.', ''),
                'PAYMENTREQUEST_0_SHIPTOPHONENUM'=>$PHPShopOrderFunction->getSerilizeParam('Person.tel_code').$PHPShopOrderFunction->getSerilizeParam('Person.tel_name'),
                'PAYMENTREQUEST_0_SHIPTONAME'=>PHPShopString::win_utf8($PHPShopOrderFunction->getSerilizeParam('Person.name_person')),
                'PAYMENTREQUEST_0_CURRENCYCODE' => $currency_iso,
                'PAYMENTREQUEST_0_ITEMAMT' => number_format($out_summ * $currency_kurs - $PHPShopOrderFunction->getDeliverySumma() * $currency_kurs, 2, '.', ''),
                'PAYMENTREQUEST_0_DESC' => PHPShopString::win_utf8($_SERVER['SERVER_NAME'] . ': Оплата заказа №' . $PHPShopOrderFunction->objRow['uid']),
                'PAYMENTREQUEST_0_INVNUM' => $inv_id,
                'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale'
            );

            $order = $PHPShopOrderFunction->unserializeParam('orders');

            $i = 0;
            if (is_array($order['Cart']['cart']))
                foreach ($order['Cart']['cart'] as $val) {
                    $item['L_PAYMENTREQUEST_0_NAME' . $i] = PHPShopString::win_utf8($val['name']);
                    //$item['L_PAYMENTREQUEST_0_DESC' . $i] = PHPShopString::win_utf8($val['name']);
                    $item['L_PAYMENTREQUEST_0_AMT' . $i] = price_mod_paypal_hook($PHPShopOrderFunction, $val['price'], $currency_kurs);
                    $item['L_PAYMENTREQUEST_0_QTY' . $i] = $val['num'];
                    $i++;
                }

            $paypal = new Paypal();
            $paypal->_credentials['USER'] = $option['merchant_id'];
            $paypal->_credentials['PWD'] = $option['merchant_pwd'];
            $paypal->_credentials['SIGNATURE'] = $option['merchant_sig'];

            // Режим песочницы
            if ($option['sandbox'] == 2) {
                $paypal->_endPoint = 'https://api-3t.paypal.com/nvp';
                $location = 'https://www.paypal.com/webscr?cmd=_express-checkout&token=';
            }
            else
                $location = 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=';

            $response = $paypal->request('SetExpressCheckout', $requestParams + $orderParams + $item);

            // Лог
            $paypal->log(array('Запрос' => $requestParams + $orderParams + $item, 'Ответ' => $response), $inv_id, $response['ACK'], 'SetExpressCheckout');

            if (is_array($response) && $response['ACK'] == 'Success') { // Запрос был успешно принят
                $token = $response['TOKEN'];
                $location.=urlencode($token);
                //header('Location: '.$location);
            }


            // Платежная форма
            $payment_forma = PHPShopText::button($option['link'], "window.location.replace('" . $location . "')", 'paybutton');

            $return = ' ' . $payment_forma;
        } elseif ($PHPShopOrderFunction->getParam('statusi') != 101)
            $return = ', ' . $option['title_end'];

    return $return;
}

$addHandler = array
    (
    'userorderpaymentlink' => 'userorderpaymentlink_mod_paypal_hook'
);
?>