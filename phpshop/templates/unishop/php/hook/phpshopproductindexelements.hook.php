<?php

function nowBuy_hook($obj, $data, $rout) {

    if ($rout == 'START' and $obj->PHPShopNav->notPath('order'))
        $obj->check_index = true;

    if ($rout == 'MIDDLE') {
        $obj->limitpos = 5;
        if ($obj->enabled == 1)
            $obj->enabled = 2;
    }
}

$addHandler = array
    (
    'nowBuy' => 'nowBuy_hook',
);
?>