<?php

function getCartProductPrice_gift_hook($obj, $data) {
    $product = $data['product'];
    if($data['column'] === 'price') {
        if(isset($obj->_CART[$product->objID]['gift_price'])) {
            return array('result' => $obj->_CART[$product->objID]['gift_price']);
        }
    }

    if($data['column'] === 'price_n') {
        if(isset($obj->_CART[$product->objID]['gift_price_n'])) {
            return array('result' => $obj->_CART[$product->objID]['gift_price_n']);
        }
    }
}

$addHandler = array ('getCartProductPrice' => 'getCartProductPrice_gift_hook');
?>