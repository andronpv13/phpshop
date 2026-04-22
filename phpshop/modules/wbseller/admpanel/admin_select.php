<?php

function wbAddOption()
{
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    $PHPShopInterface->_CODE .= '<p class="clearfix"> </p>';
    $PHPShopInterface->_CODE .= __('WB') . '<br>';
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('price_wb', 1, 'Цена WB', $memory['catalog.option']['price_wb']);
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('label_wb', 1, 'Лейбл WB', $memory['catalog.option']['label_wb']) . '<br>';
}

$addHandler = [
    'actionOption' => 'wbAddOption'
];
