<?php

include_once dirname(__DIR__) . '/class/NovaPay.php';

function userorderpaymentlink_novapay_hook($obj, $PHPShopOrderFunction) {

    if (NovaPay::isNovaPayPaymentMethod($PHPShopOrderFunction->order_metod_id)) {
        $NovaPay = new NovaPay();

        if ($PHPShopOrderFunction->getParam('statusi') == $NovaPay->options['status'] or empty($NovaPay->options['status'])) {
            if((int) $_GET['novapay_now'] == 1) {
                try {
                    $url = $NovaPay->createPayment($PHPShopOrderFunction->objRow['uid']);
                    header('Location: ' . $url);
                } catch (\Exception $exception) {
                    $return = 'Ошибка регистрации платежа, обратитесь к администратору.';
                }
            } else {
                $return = PHPShopText::a('?order_info=' . $PHPShopOrderFunction->objRow['uid'] . '&novapay_now=1#Order', $NovaPay->options['title'], false, false, 14, false, 'btn btn-primary');
            }

        } elseif (NovaPay::isNovaPayPaymentMethod($PHPShopOrderFunction->getSerilizeParam('orders.Person.order_metod'))) {
            $return = ' Заказ обрабатывается менеджером';
        }

        return $return;
    }
}

$addHandler = array('userorderpaymentlink' => 'userorderpaymentlink_novapay_hook');
?>