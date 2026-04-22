<?php

function success_mod_acquiropay_hook($obj, $value)
{
    if (!empty($_REQUEST['cf'])) {
        $obj->order_metod = 'modules" and id="10018';
        $obj->message();
        return true;
    }
}

$addHandler = array('index' => 'success_mod_acquiropay_hook');
