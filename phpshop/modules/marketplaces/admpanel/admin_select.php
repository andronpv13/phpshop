<?php

function marketplacesAddOption()
{
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    $PHPShopInterface->_CODE .= '<p class="clearfix"> </p>';
    $PHPShopInterface->_CODE .= __('Маркетплейсы') . '<br>';
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('price_google', 1, 'Цена Google Merchant', $memory['catalog.option']['price_google']);
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('price_cdek', 1, 'Цена Яндекс.Маркет', $memory['catalog.option']['price_cdek']);
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('price_aliexpress', 1, 'Цена AliExpress', $memory['catalog.option']['price_aliexpress']). '<br>' ;
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('price_sbermarket', 1, 'Цена Мегамаркет', $memory['catalog.option']['price_sbermarket']);
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('label_google_merchant', 1, 'Лейбл Google Merchant', $memory['catalog.option']['label_google_merchant']);
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('label_cdek', 1, 'Лейбл Яндекс.Маркет', $memory['catalog.option']['label_cdek']) . '<br>';
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('label_aliexpress', 1, 'Лейбл AliExpress', $memory['catalog.option']['label_aliexpress']);
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('label_sbermarket', 1, 'Лейбл Мегамаркет', $memory['catalog.option']['label_sbermarket']);
}

$addHandler = [
    'actionOption' => 'marketplacesAddOption'
];
