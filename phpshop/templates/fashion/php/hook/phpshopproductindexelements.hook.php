<?php

function specMain_hook($obj) {
    $obj->check_index = true;
}

function nowBuy_hook($obj, $row, $rout) {

    if ($rout == 'START') {
        $obj->limitpos = 8;
        $obj->limitorders = 10;
        $obj->cell = 4;
        $obj->check_index = true;
    }
    
}

$addHandler = array
    (
    'specMain' => 'specMain_hook',
    'nowBuy' => 'nowBuy_hook',
);
?>
