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
    
    /*
    if ($rout == 'END') {
       $rand=rand(0,count($row)-1);
       $obj->set('product_nowBuy_img',$row[$rand]['pic_small']);
       $obj->set('product_nowBuy_id',$row[$rand]['id']);
       $obj->set('product_nowBuy_name',$row[$rand]['name']);
       $obj->set('product_nowBuy_items',$row[$rand]['items']);
       $obj->set('product_nowBuy_price',number_format(PHPShopProductFunction::GetPriceValuta($row[$rand]['id'],array($row[$rand]['price'])), $obj->format, '.', ' '));
       $obj->set('nowBuyProduct',ParseTemplateReturn('product/product_nowbuy.tpl'));
    }*/
}

$addHandler = array
    (
    'specMain' => 'specMain_hook',
    'nowBuy' => 'nowBuy_hook',
);
?>
