<?php

function success_mod_robokassa_hook($obj, $value) {

    if (isset($_REQUEST['SignatureValue'])) {
        $obj->order_metod = 'modules" and id="10020';
        $obj->message();
        return true;
    }
}

$addHandler = array('index' => 'success_mod_robokassa_hook');
?>