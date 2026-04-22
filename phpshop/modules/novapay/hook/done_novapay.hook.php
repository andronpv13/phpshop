<?php

include_once dirname(__DIR__) . '/class/NovaPay.php';

function send_to_order_novapay_hook($obj, $value, $rout) {
    if ($rout === 'END' && NovaPay::isNovaPayPaymentMethod($value['order_metod'])) {

        $NovaPay = new NovaPay();

        if (empty($NovaPay->options['status'])) {
            try {
                $url = $NovaPay->createPayment($value['ouid']);
                $payment_forma = PHPShopText::a($url, $NovaPay->options['title'], false, false, 14, false, 'btn btn-primary');
            } catch (\Exception $e) {
                $payment_forma = '<p>Ошибка регистрации платежа, обратитесь к администратору.</p>';
            }

            $obj->set('payment_forma', $payment_forma);
            $obj->set('payment_info', $NovaPay->options['title_end']);
            $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['novapay']['novapay_payment_forma'], true);

        } else {
            $obj->set('mesageText', $NovaPay->options['title_end']);
            $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['order_forma_mesage']);
        }

        $obj->set('orderMesage', $forma);
    }
}

$addHandler = array('send_to_order' => 'send_to_order_novapay_hook');
?>