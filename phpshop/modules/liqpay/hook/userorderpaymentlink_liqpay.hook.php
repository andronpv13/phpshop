<?php

function userorderpaymentlink_mod_liqpay_hook($obj, $PHPShopOrderFunction) {

    // Настройки модуля
    include_once(dirname(__FILE__) . '/mod_option.hook.php');
    $PHPShopLiqpayArray = new PHPShopLiqpayArray();
    $option = $PHPShopLiqpayArray->getArray();


    // Контроль оплаты от статуса заказа
    if ($PHPShopOrderFunction->getParam('statusi') == $option['status']) {

        // Номер счета
        $mrh_ouid = explode("-", $PHPShopOrderFunction->objRow['uid']);
        $inv_id = $mrh_ouid[0] . "" . $mrh_ouid[1];

        // Сумма покупки
        $out_summ = $PHPShopOrderFunction->getTotal();

        $xml = '<request>      
      <version>1.2</version>
      <merchant_id>' . $option['merchant_id'] . '</merchant_id>
      <server_url>http://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/liqpay/payment/result.php</server_url>
      <result_url>http://' . $_SERVER['SERVER_NAME'] . '/success/?payment=' . base64_encode('payment=liqpay&inv_id=' . $inv_id) . '</result_url>   
      <order_id>' . $inv_id . '</order_id>
      <amount>' . $out_summ . '</amount>
      <currency>' . $obj->PHPShopSystem->getDefaultValutaIso() . '</currency>
      <description>PHPShopPaymentService</description>
      <default_phone>' . $value['tel_code'] . "-" . $value['tel_name'] . '</default_phone>
      <pay_way>card,liqpay,delayed</pay_way>
      </request>';


        // Подпись
        $sign = base64_encode(sha1($option['merchant_sig'] . $xml . $option['merchant_sig'], 1));

        // Платежная форма
        $payment_forma = PHPShopText::setInput('hidden', 'operation_xml', base64_encode($xml));
        $payment_forma.=PHPShopText::setInput('hidden', 'signature', $sign);
        $payment_forma.=PHPShopText::setInput('submit', 'send', $option['title'], $float = "none", 250);

        $return = PHPShopText::form($payment_forma, 'pay', 'post', 'https://www.liqpay.ua/?do=clickNbuy');
    }
    elseif($PHPShopOrderFunction->getSerilizeParam('orders.Person.order_metod') == 10001)
        $return = ', Заказ обрабатывается менеджером';

    return $return;
}

$addHandler = array
    (
    'userorderpaymentlink' => 'userorderpaymentlink_mod_liqpay_hook'
);
?>