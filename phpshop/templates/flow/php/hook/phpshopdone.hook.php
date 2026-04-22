<?php

function template_send_to_order_hook($obj, $data, $rout) {
    if ($rout == 'END') {
        $obj->set('mesageTextIcon', '/assets/svg/icons/icon-21.svg');
    }
}

function template_index_hook($obj, $data, $rout) {
    if ($rout == 'END') {
        $obj->set('mesageTextIcon', '/assets/svg/illustrations/empty-cart.svg');
    }
}

$addHandler = array
    (
    'send_to_order' => 'template_send_to_order_hook',
    'index' => 'template_index_hook'
);
?>