<?php

function price_mod_paypal_hook($obj, $price, $currency_kurs) {
    return $obj->PHPShopOrder->returnSumma($price * $currency_kurs, $obj->discount);
}

function send_to_order_mod_paypal_hook($obj, $value, $rout) {
    global $PHPShopValutaArray;

    if ($rout == 'MIDDLE' and $value['order_metod'] == 10003) {

        $_POST['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE'] = $_POST['country_new'];
        $_POST['PAYMENTREQUEST_0_SHIPTOCITY'] = $_POST['city_new'];
        $_POST['PAYMENTREQUEST_0_SHIPTOZIP'] = $_POST['index_new'];
        $_POST['adr_name'] = $_POST['street_new'];
        $_POST['PAYMENTREQUEST_0_SHIPTOSTREET2'] = $_POST['house_new'];
        $_POST['PAYMENTREQUEST_0_SHIPTOSTREET3'] = $_POST['flat_new'];

        // Настройки модуля
        include_once(dirname(__FILE__) . '/mod_option.hook.php');
        $Array = new PHPShopPaypalArray();
        $option = $Array->getArray();

        // Сохраняем корзину
        $obj->cart_clean_enabled = false;

        // Библиотека
        include_once($GLOBALS['SysValue']['class']['paypal']);

        // Контроль оплаты от статуса заказа
        if (empty($option['status'])) {

            // Номер счета
            $mrh_ouid = explode("-", $value['ouid']);
            $inv_id = $mrh_ouid[0] . $mrh_ouid[1];

            // Сумма покупки
            $out_summ = $obj->get('total');

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
                'CANCELURL' => 'http://' . $_SERVER['SERVER_NAME'] . '/order/?from=paypal&order_id=' . $inv_id,
                'ADDROVERRIDE' => 1,
                'LOCALECODE' => 'RU',
                'EMAIL' => $value['mail'],
                'LANDINGPAGE' => 'Login',
                'BRANDNAME'=> PHPShopString::win_utf8($obj->PHPShopSystem->getName())
            );

            
            // Адрес доставки для магазина
            $adr_name='Страна '.$_POST['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE'].', г. '.$_POST['PAYMENTREQUEST_0_SHIPTOCITY'].', '.$_POST['PAYMENTREQUEST_0_SHIPTOZIP'];
            $delivery=' ул. '.$_POST['adr_name'].', д.'.$_POST['PAYMENTREQUEST_0_SHIPTOSTREET2'].', кв. '.$_POST['PAYMENTREQUEST_0_SHIPTOSTREET3'];
            $_POST['adr_name']=$adr_name.$delivery;
            
            // Адрес доставки для Paypal
            if(strlen($delivery) > 100){
                $delivery_1=  substr($delivery, 0, 100);
                $delivery_2=  substr($delivery, 100, 200);
                $requestParams['PAYMENTREQUEST_0_SHIPTOSTREET']=PHPShopString::win_utf8($delivery_1);
                $requestParams['PAYMENTREQUEST_0_SHIPTOSTREET2']=PHPShopString::win_utf8($delivery_2);
            }
            else $requestParams['PAYMENTREQUEST_0_SHIPTOSTREET']=PHPShopString::win_utf8($delivery);
            
            $requestParams['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE']=$_POST['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE'];
            $requestParams['PAYMENTREQUEST_0_SHIPTOZIP']=$_POST['PAYMENTREQUEST_0_SHIPTOZIP'];
            $requestParams['PAYMENTREQUEST_0_SHIPTOCITY']=PHPShopString::win_utf8($_POST['PAYMENTREQUEST_0_SHIPTOCITY']);
            

            $orderParams = array(
                'PAYMENTREQUEST_0_AMT' => number_format($out_summ * $currency_kurs, 2, '.', ''),
                'PAYMENTREQUEST_0_SHIPPINGAMT' => number_format($obj->delivery * $currency_kurs, 2, '.', ''),
                'PAYMENTREQUEST_0_SHIPTOPHONENUM'=>$value['tel_code'].$value['tel_name'],
                'PAYMENTREQUEST_0_SHIPTONAME'=>PHPShopString::win_utf8($value['name_person']),
                'PAYMENTREQUEST_0_CURRENCYCODE' => $currency_iso,
                'PAYMENTREQUEST_0_ITEMAMT' => number_format($out_summ * $currency_kurs - $obj->delivery * $currency_kurs, 2, '.', ''),
                'PAYMENTREQUEST_0_DESC' => PHPShopString::win_utf8($_SERVER['SERVER_NAME'] . ': Оплата заказа №' . $obj->ouid),
                'PAYMENTREQUEST_0_INVNUM' => $inv_id,
                'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale'
            );

            $i = 0;
            if (is_array($obj->PHPShopCart->_CART))
                foreach ($obj->PHPShopCart->_CART as $val) {
                    $item['L_PAYMENTREQUEST_0_NAME' . $i] = PHPShopString::win_utf8($val['name']);
                    //$item['L_PAYMENTREQUEST_0_DESC' . $i] = PHPShopString::win_utf8($val['name']);
                    $item['L_PAYMENTREQUEST_0_AMT' . $i] = price_mod_paypal_hook($obj, $val['price'], $currency_kurs);
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

            // Запрос был успешно принят
            if (is_array($response) && $response['ACK'] == 'Success') {
                $token = $response['TOKEN'];
                $location.=urlencode($token);

                // Очищаем корзину
                $obj->cart_clean_enabled = false;

                //header('Location: '.$location);
                // Платежная форма
                $payment_forma = PHPShopText::button($option['link'], "window.location.replace('" . $location . "')", 'paybutton');
            }
            // Ошибка
            else {

                // Описание ошибки
                $error_mesage = 'Код ошибки: ' . $response['L_ERRORCODE0'];
                $error_mesage.=' Описание ошибки: ' . $response['L_SHORTMESSAGE0'] . $response['L_LONGMESSAGE0'];
                $payment_forma = PHPShopText::b($error_mesage);
                $content = $error_mesage;
                $content.='Заказ: ' . $value['ouid'] . '. Сумма: ' . $out_summ . ' ' . $currency_iso;

                // Сообщение администратору
                new PHPShopMail($obj->PHPShopSystem->getParam('adminmail2'), $obj->PHPShopSystem->getParam('adminmail2'), 'PayPal Error ' . PHPShopDate::get(), $content);
            }



            $obj->set('payment_forma', $payment_forma);
            $obj->set('payment_info', $option['title_end']);
            $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['paypal']['paypal_payment_forma'], true);
        } else {

            $obj->set('mesageText', $option['title_end']);
            $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['order_forma_mesage']);
        }

        $obj->set('orderMesage', $forma);
    }
}

$addHandler = array
    (
    'send_to_order' => 'send_to_order_mod_paypal_hook'
);
?>