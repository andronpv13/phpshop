<?php

function getCartProductPrice_wholesale_hook($obj, $data) {

    $product = $data['product'];
    $column = $data['column'];

    include_once $GLOBALS['SysValue']['class']['wholesale'];
    $PHPShopWholesale = new PHPShopWholesale();
    $opt = $PHPShopWholesale->getOpt($product->objRow);

    if (is_array($opt)) {

        // Проверка кол-ва товара
        if ($obj->_CART[$product->objID]['num'] >= $product->getParam('wholesale_check')) {

            // Колонка
            if ($opt['tip'] == 1) {

                $column_opt = $product->getParam('wholesale_price');

                if ($column_opt > 1) {

                    if ($column === 'price') {
                        $price = PHPShopProductFunction::GetPriceValuta($product->objID, $product->getParam('price' . (int) $column_opt), $product->getParam("baseinputvaluta"), true, true);
                    }

                    if ($column === 'price_n') {
                        $price = PHPShopProductFunction::GetPriceValuta($product->objID, $product->getParam('price'), $product->getParam("baseinputvaluta"), true, false);
                    }
                }
            }
            // Скидка
            else {

                $discount = $product->getParam('wholesale_discount');

                if (!empty($discount)) {

                    if ($column === 'price') {
                        $price = PHPShopProductFunction::GetPriceValuta($product->objID, $product->getParam('price'), $product->getParam("baseinputvaluta"), true, true);
                        $price = $price - ($price * intval($discount) / 100);
                    }

                    if ($column === 'price_n') {
                        $price = PHPShopProductFunction::GetPriceValuta($product->objID, $product->getParam('price'), $product->getParam("baseinputvaluta"), true, false);
                    }
                }
            }

            if (!empty($price))
                return array('result' => $price);
        }
    }
}

$addHandler = array('getCartProductPrice' => 'getCartProductPrice_wholesale_hook');
?>