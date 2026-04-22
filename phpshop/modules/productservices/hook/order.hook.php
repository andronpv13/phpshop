<?php

function getCartProductPrice_ProductServices_hook($obj, $data) {

    $product = $data['product'];
    if ($data['column'] === 'price') {

        $PHPShopProduct = new PHPShopProduct($product->objID);
        if ($PHPShopProduct->getParam('productservices_discount') > 0) {

            $price = $PHPShopProduct->getPrice();
            $price = $price - $price * $PHPShopProduct->getParam('productservices_discount') / 100;
            return array('result' => $price);
        }
    }

    if ($data['column'] === 'price_n') {


        $PHPShopProduct = new PHPShopProduct($product->objID);
        if ($PHPShopProduct->getParam('productservices_discount') > 0) {
            return array('result' => $PHPShopProduct->getPrice());
        }
    }
}

$addHandler = array('getCartProductPrice' => 'getCartProductPrice_ProductServices_hook');
