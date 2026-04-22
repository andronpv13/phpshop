<?php

function product_grid_dolyame_product_hook($obj, $dataArray) {

    $dis = null;

    require_once "./phpshop/modules/dolyame/class/Dolyame.php";
    $Dolyame = new Dolyame();
    
    $price = str_replace(' ', '', $obj->get('productPrice'));

    if ((int) $price <= (int) $Dolyame->max_sum and ! empty($dataArray['dolyame_enabled'])) {

        if (empty($Dolyame->site_id)) {
            $obj->set('dolyame_id', $dataArray['id']);
            $obj->set('dolyame_pay', number_format( ceil($price / 4), 0, '.', ' '));
            $dis = PHPShopParser::file($GLOBALS['SysValue']['templates']['dolyame']['dolyame_product'], true, false, true);
        } else
            $dis = '<span class="dolyame-enabled"></span>';
    }

    $obj->set('dolyame_product', $dis);
}

$addHandler = array
    (
    'product_grid' => 'product_grid_dolyame_product_hook',
);
