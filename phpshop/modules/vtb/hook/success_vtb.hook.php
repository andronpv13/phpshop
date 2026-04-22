<?php

function success_mod_vtb_hook($obj, $value) {

    if ($value["payment"] == "vtb") {
        $obj->order_metod = 'modules" and id="10019';

        $mrh_ouid = explode("-", $_REQUEST['uid']);
        $obj->inv_id = $mrh_ouid[0] . $mrh_ouid[1];

        $obj->message();

        return true;
    }
}

$addHandler = array('index' => 'success_mod_vtb_hook');
?>