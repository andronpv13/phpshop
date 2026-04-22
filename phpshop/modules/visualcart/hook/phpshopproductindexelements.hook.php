<?php

function nowBuy_visualcart_hook($obj, $row, $rout) {

    if ($GLOBALS['AddToTemplateVisualCart']->option['nowbuy'] == 1 and !empty($_COOKIE['usecookie'])) {

        if ($rout == 'START') {
            $obj->limitpos = 10;
            $obj->limitorders = 10;
            $obj->cell = 4;
            $obj->check_index = true;
        }

        if ($rout == 'END') {
            $rand = rand(0, count($row) - 1);
            $obj->set('product_nowBuy_img', $row[$rand]['pic_small']);
            $obj->set('product_nowBuy_id', $row[$rand]['id']);
            $obj->set('product_nowBuy_name', $row[$rand]['name']);
            $obj->set('product_nowBuy_items', $row[$rand]['items']);
            $obj->set('product_nowBuy_price', number_format(PHPShopProductFunction::GetPriceValuta($row[$rand]['id'], array($row[$rand]['price']), $row[$rand]['baseinputvaluta']), $obj->format, '.', ' '));

            $obj->set('visualcart_lib', PHPShopParser::file($GLOBALS['SysValue']['templates']['visualcart']['visualcart_nowbuy'], true, false, true),true);
        }
    }
}

$addHandler = array
    (
    'nowBuy' => 'nowBuy_visualcart_hook',
);
?>