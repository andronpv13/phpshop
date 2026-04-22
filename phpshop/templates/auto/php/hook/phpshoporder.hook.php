<?php

function template_order_error_hook($obj) {

    $obj->set('mesageTextIcon', 'images/empty-cart.svg');
    
}

$addHandler = array
    (
    'error' => 'template_order_error_hook',
);
?>