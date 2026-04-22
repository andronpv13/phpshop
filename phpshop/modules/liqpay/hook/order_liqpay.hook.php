<?php

function payment_mod_liqpay_hook($obj,$value) {
    
    // Настройки модуля
    include_once(dirname(__FILE__).'/mod_option.hook.php');
    $option = new PHPShopLiqpayArray();

    $value[]=array($option->getParam('title'),10001,false);
    $obj->set('orderOplata',PHPShopText::select('order_metod',$value,250),true);;
    return true;
}


$addHandler=array
        (
        '#payment'=>'payment_mod_liqpay_hook'
);
?>