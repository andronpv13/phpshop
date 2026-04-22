<?php

function success_mod_liqpay_hook($obj, $value) {
  
    // Преобразуем строку в переменные
    parse_str(base64_decode($value['payment']), $value);
	
    if ($value['payment'] == 'liqpay') {

        include_once(dirname(__FILE__) . '/mod_option.hook.php');
        include_once(dirname(__DIR__) . '/class/LiqPay.php');
        $PHPShopLiqpayArray = new PHPShopLiqpayArray();
        $option = $PHPShopLiqpayArray->getArray();

        $liqpay = new LiqPay($option['merchant_id'], $option['merchant_sig']);
        $res = $liqpay->api("request", [
            'action'   => 'status',
            'version'  => '3',
            'order_id' => $value['inv_id']
        ]);

        if(isset($res->status) && ($res->status === 'success' || $res->status === 'wait_accept')) {
            $obj->order_metod = 'modules" and id="10001';
            $obj->inv_id = $value['inv_id'];

            $obj->message();

            return true;
        }

        $obj->error();
    }
}

$addHandler = ['index' => 'success_mod_liqpay_hook'];
?>