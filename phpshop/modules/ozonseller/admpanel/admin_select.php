<?php

function ozonAddOption()
{
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    $PHPShopInterface->_CODE .= '<p class="clearfix"> </p>';
    $PHPShopInterface->_CODE .= __('OZON') . '<br>';
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('price_ozon', 1, 'Цена OZON', $memory['catalog.option']['price_ozon']);
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('label_ozon', 1, 'Лейбл OZON', $memory['catalog.option']['label_ozon']) . '<br>';
}

$addHandler = [
    'actionOption' => 'ozonAddOption'
];
