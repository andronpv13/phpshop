<?php

function payment_mod_deltakey_hook($obj, $value) {
    // Настройки модуля
    include_once(dirname(__FILE__) . '/mod_option.hook.php');
    $option = new PHPShopDeltaKeyArray();
    $value[10011] = array($option->getParam('title'), 10011, false);
    //$obj->set('orderOplata', PHPShopText::select('order_metod', $value, 250), true);
    
    //return true;
}

$addHandler = array('payment' => 'payment_mod_deltakey_hook');
?>