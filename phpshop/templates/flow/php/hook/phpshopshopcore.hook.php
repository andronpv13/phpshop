<?php

/**
 * Вывод иконок распродажи и спецпредложений в кратком описании товаров.
 */
function phpshopshopcore_product_grid_nt_hook($obj, $dataArray) {
    global $PHPShopCart;

    // Спецпредложения
    if (!empty($dataArray['spec']))
        $obj->set('specIcon', ParseTemplateReturn('product/specIcon.tpl'));
    else
        $obj->set('specIcon', '');

    // Новинки
    if (!empty($dataArray['newtip']))
        $obj->set('newtipIcon', ParseTemplateReturn('product/newtipIcon.tpl'));
    else
        $obj->set('newtipIcon', '');

    // В корзине
    if (empty($PHPShopCart)){
        $PHPShopCart = new PHPShopCart();
    }

    if ($PHPShopCart->isItemInCart($dataArray['id'])) {
        $obj->set('flowProductSale', $obj->lang('productSaleReady'));
    } else {
        $obj->set('flowProductSale', $obj->lang('product_sale'));
    }
}

$addHandler = array
    (
    'product_grid' => 'phpshopshopcore_product_grid_nt_hook',
);
?>