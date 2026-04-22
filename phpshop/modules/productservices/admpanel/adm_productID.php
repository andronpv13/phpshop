<?php

function addProductIDProductservices($data) {
    global $PHPShopGUI;

    $Tab = $PHPShopGUI->setField('Скидка', $PHPShopGUI->setInputText(null, 'productservices_discount_new', $data['productservices_discount'], 100,'%'));
    $Tab .= $PHPShopGUI->setTextarea('productservices_products_new', $data['productservices_products'], false, false, false,
        __('Укажите ID товаров или воспользуйтесь') .
        ' <a href="#" data-target="#productservices_products_new"  class="btn btn-sm btn-default tag-search"><span class="glyphicon glyphicon-search"></span> ' . __('поиском товаров') . '</a>');

    $PHPShopGUI->addJSFiles('../modules/productservices/admpanel/gui/productservices.gui.js');

    $PHPShopGUI->addTab(array("Услуги", $Tab, true));
}

$addHandler = array(
    'actionStart' => 'addProductIDProductservices',
    'actionDelete' => false,
    'actionUpdate' => false
);
?>