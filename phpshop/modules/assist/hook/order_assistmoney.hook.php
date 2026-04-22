<?php

function payment_mod_Assistmoney_hook($obj) {
    
    // Настройки модуля
    include_once(dirname(__FILE__).'/mod_option.hook.php');
    $option = new PHPShopAssistmoneyArray();
    $obj->value[10010] =array($option->getParam('title'),10010,false);
}


$addHandler=array
        (
        'payment'=>'payment_mod_Assistmoney_hook'
);
?>