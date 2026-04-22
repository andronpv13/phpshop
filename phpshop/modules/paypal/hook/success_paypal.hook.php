<?php

function success_mod_paypal_hook($obj, $value) {
    global $mod_paypal_option;

    if ($value['payment'] == 'modules' and $value['order_method'] == 'paypal') {

        // Токен присутствует
        if (isset($_GET['token']) && !empty($_GET['token'])) {

            // Настройки модуля
            include_once(dirname(__FILE__) . '/mod_option.hook.php');
            $Array = new PHPShopPaypalArray();
            $option = $Array->getArray();
            $mod_paypal_option = $option;

            // Библиотека
            include_once($GLOBALS['SysValue']['class']['paypal']);

            $paypal = new Paypal();
            $paypal->_credentials['USER'] = $option['merchant_id'];
            $paypal->_credentials['PWD'] = $option['merchant_pwd'];
            $paypal->_credentials['SIGNATURE'] = $option['merchant_sig'];


            // Режим песочницы
            if ($option['sandbox'] == 2) {
                $paypal->_endPoint = 'https://api-3t.paypal.com/nvp';
            }

            $checkoutDetails = $paypal->request('GetExpressCheckoutDetails', array('TOKEN' => $_GET['token']));

            // Лог
            $paypal->log($checkoutDetails, $checkoutDetails['PAYMENTREQUEST_0_INVNUM'], null, 'GetExpressCheckoutDetails');

            // Завершаем транзакцию
            $requestParams = array(
                'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
                'PAYERID' => $_GET['PayerID'],
                'PAYMENTREQUEST_0_AMT' => $checkoutDetails['PAYMENTREQUEST_0_AMT'],
                'PAYMENTREQUEST_0_SHIPPINGAMT' => $checkoutDetails['PAYMENTREQUEST_0_SHIPPINGAMT'],
                'PAYMENTREQUEST_0_SHIPTOPHONENUM' => $checkoutDetails['PAYMENTREQUEST_0_SHIPTOPHONENUM'],
                'PAYMENTREQUEST_0_SHIPTONAME' => $checkoutDetails['PAYMENTREQUEST_0_SHIPTONAME'],
                'PAYMENTREQUEST_0_SHIPTOSTREET' => $checkoutDetails['PAYMENTREQUEST_0_SHIPTOSTREET'],
                'PAYMENTREQUEST_0_SHIPTOSTREET2' => $checkoutDetails['PAYMENTREQUEST_0_SHIPTOSTREET2'],
                'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE' => $checkoutDetails['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE'],
                'PAYMENTREQUEST_0_SHIPTOZIP' => $checkoutDetails['PAYMENTREQUEST_0_SHIPTOZIP'],
                'PAYMENTREQUEST_0_SHIPTOCITY' => $checkoutDetails['PAYMENTREQUEST_0_SHIPTOCITY'],
                'PAYMENTREQUEST_0_CURRENCYCODE' => $checkoutDetails['PAYMENTREQUEST_0_CURRENCYCODE'],
                'PAYMENTREQUEST_0_ITEMAMT' => $checkoutDetails['PAYMENTREQUEST_0_ITEMAMT'],
                'PAYMENTREQUEST_0_DESC' => $checkoutDetails['PAYMENTREQUEST_0_DESC'],
                'PAYMENTREQUEST_0_INVNUM' => $checkoutDetails['PAYMENTREQUEST_0_INVNUM'],
                'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
                'TOKEN' => $checkoutDetails['TOKEN'],
                'ADDROVERRIDE' => 1,
                'LOCALECODE' => 'RU',
                'EMAIL' => $value['mail'],
                'LANDINGPAGE' => 'Login',
                'BRANDNAME' => PHPShopString::win_utf8($obj->PHPShopSystem->getName())
            );


            // Корзина
            $i = 0;
            while ($i < 10) {
                if (!empty($checkoutDetails['L_PAYMENTREQUEST_0_NAME' . $i])) {
                    $requestParams['L_PAYMENTREQUEST_0_NAME' . $i] = $checkoutDetails['L_PAYMENTREQUEST_0_NAME' . $i];
                    $requestParams['L_PAYMENTREQUEST_0_AMT' . $i] = $checkoutDetails['L_PAYMENTREQUEST_0_AMT' . $i];
                    $requestParams['L_PAYMENTREQUEST_0_QTY' . $i] = $checkoutDetails['L_PAYMENTREQUEST_0_QTY' . $i];
                }
                $i++;
            }

            $response = $paypal->request('DoExpressCheckoutPayment', $requestParams);

            // Лог
            $paypal->log(array('Запрос' => $requestParams, 'Ответ' => $response), $checkoutDetails['PAYMENTREQUEST_0_INVNUM'], $response['PAYMENTINFO_0_PAYMENTSTATUS'], 'DoExpressCheckoutPayment');

            if (is_array($response) && $response['PAYMENTINFO_0_PAYMENTSTATUS'] == 'Completed') { // Оплата успешно проведена
                $return = array();

                // Тип обработчика
                $return['order_metod'] = 'modules';

                // Имя для лога оплат
                $return['order_metod_name'] = 'PayPal';

                // Включаем функцию обновления статуса заказа
                $return['success_function'] = true;
                $return['crc'] = 1;
                $return['my_crc'] = 1;
                $return['inv_id'] = $checkoutDetails['PAYMENTREQUEST_0_INVNUM'];
                $return['out_summ'] = $checkoutDetails['PAYMENTREQUEST_0_AMT'];

                return $return;
            }
            // Платеж на ручной обработке
            elseif (is_array($response) && $response['PAYMENTINFO_0_PAYMENTSTATUS'] == 'Pending' && $response['PAYMENTINFO_0_PENDINGREASON'] == 'paymentreview') {
                $mod_paypal_option['message_header'] = "Оплата заказа";
                $mod_paypal_option['message'] = "PayPal перевел проверку платежа в ручной режим. Это займет некоторое время.";
            }
        }
    }
}

function message_mod_paypal_hook($obj) {
    global $mod_paypal_option;

    $cart_clean = "
<script>
if(window.document.getElementById('num')){
window.document.getElementById('num').innerHTML='0';
window.document.getElementById('sum').innerHTML='0';
}
</script>";

    // Сообщение пользователю об успешном платеже
    $text = PHPShopText::notice($mod_paypal_option['message_header'] . PHPShopText::br(), $icon = false, '14px') . $mod_paypal_option['message'] . $cart_clean;
    $obj->set('mesageText', $text);
    $obj->set('orderMesage', ParseTemplateReturn($obj->getValue('templates.order_forma_mesage')));
}

$addHandler = array
    (
    'index' => 'success_mod_paypal_hook',
    'message' => 'message_mod_paypal_hook'
);
?>