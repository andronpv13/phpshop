<?php

function send_fondy_hook($obj, $value, $rout)
{
    global $PHPShopSystem;

    if ($rout === 'MIDDLE' and $value['order_metod'] == 10034) {

        $currencyISO = $PHPShopSystem->getDefaultValutaIso();

        include_once 'phpshop/modules/fondy/class/Fondy.php';
        $fondy = new Fondy();

        if (empty($fondy->option['status']) && $fondy->option['payment_type'] == 'redirect') {
            $fondy->option['currency'] = $currencyISO;
            $fondy->option['order_id'] = 'order_' . $value['ouid'];
            $fondy->option['order_desc'] = 'Order ' . $value['ouid'];
            $fondy->option['amount'] = $obj->get('total');


//            $payment_form = $fondy->getForm();
//            $fondy->log($fondy->option, $fondy->option['merchant_id'], 'Форма подготовлена для отправки', 'Регистрация заказа');
//            $obj->set('payment_forma', PHPShopText::form($payment_form, 'fondypay', 'post', $fondy::$FORM_ACTION, '_blank'));
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
            $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['fondy']['fondy_payment_forma'], true);

        } else {
            $clean_cart = "
            <script>
                if(window.document.getElementById('num')){
                    window.document.getElementById('num').innerHTML='0';
                    window.document.getElementById('sum').innerHTML='0';
                }
            </script>";
            $obj->set('mesageText', $fondy->option['title_sub'] . $clean_cart);
            $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['order_forma_mesage']);

            unset($_SESSION['cart']);
        }
        $obj->set('orderMesage', $forma);
    }
}

$addHandler = array('send_to_order' => 'send_fondy_hook');