<?php

function success_mod_idram_hook($obj, $value) {
    if (isset($_REQUEST['EDP_BILL_NO'])) {

        include_once 'phpshop/modules/idram/class/Idram.php';

        if($_REQUEST['status'] === 'success'){
            $obj->order_metod = 'modules" and id="' . Idram::IDRAM_PAYMENT_ID;

            $mrh_ouid = explode("-", $_REQUEST['EDP_BILL_NO']);
            $obj->inv_id = $mrh_ouid[0] . $mrh_ouid[1];

            $obj->message();

            return true;
        } else
            $obj->error();
    }
}
$addHandler = array('index' => 'success_mod_idram_hook');
?>