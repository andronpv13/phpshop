<?php

function success_mod_dolyame_hook($obj, $value) {

    if ($value["payment"] == "dolyame") {
        $obj->order_metod = 'modules" and id="10025';

        $mrh_ouid = explode("-", $_REQUEST['uid']);
        $obj->inv_id = $mrh_ouid[0] . $mrh_ouid[1];

        $obj->message();

        return true;
    }
}

$addHandler = array('index' => 'success_mod_dolyame_hook');
?>