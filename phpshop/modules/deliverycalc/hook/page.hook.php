<?php

/**
 * Добавление jss
 */
function index_deliverycalc_hook($obj, $row, $rout) {
    
    $PHPShopDeliverycalcElement = new PHPShopDeliverycalcElement();

    if ($rout == 'START') {
        $PHPShopDeliverycalcElement->deliverycalc_start();
    }

    if ($rout == 'END') {

        $PHPShopDeliverycalcElement->deliverycalc_end();
    }
}

$addHandler = array
    (
    'index' => 'index_deliverycalc_hook'
);
?>