<?php

function payment_mod_cloudpayment_hook($obj) {
    
    // Настройки модуля
    include_once(dirname(__FILE__).'/mod_option.hook.php');
    $option = new PHPShopcloudpaymentArray();
    $obj->value[10014] =array($option->getParam('title'),10014,false);
}


$addHandler=array
        (
        'payment'=>'payment_mod_cloudpayment_hook'
);
?>