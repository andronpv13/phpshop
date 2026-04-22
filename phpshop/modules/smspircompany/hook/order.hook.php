<?php

function order_hook($obj,$row,$rout) {
    if($rout == 'END') {
        
        $cartContent = $obj->get('orderContentCart') 
        . PHPShopParser::file(dirname(__DIR__) . '/lib/templates/order/js_custom_input_tel_new.tpl', true);
        
        $obj->set('orderContentCart', $cartContent);
     
    }
}

$addHandler = array
(
  'order'=>'order_hook',
);

?>