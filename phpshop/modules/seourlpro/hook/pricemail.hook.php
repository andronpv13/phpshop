<?php

/**
 * @param PHPShopPricemail $obj
 * @param PHPShopProduct $product
 * @param string $route
 */
function forma_seo_hook($obj, $product, $route) {
    if($route === 'END') {
        if(isset($product->objRow['prod_seo_name']) && strlen($product->objRow['prod_seo_name']) > 1) {
            $GLOBALS['PHPShopSeoPro']->setMemory($product->objID, $product->objRow['prod_seo_name'], 2, false);
        } else {
            $GLOBALS['PHPShopSeoPro']->setMemory($product->objID, $product->getName(), 2);
        }
    }
}

$addHandler = array ('forma' => 'forma_seo_hook');