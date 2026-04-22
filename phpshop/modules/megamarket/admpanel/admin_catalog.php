<?php

function megamarketAddCaptions()
{
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    if(isset($memory['catalog.option']['price_megamarket'])) {
        $PHPShopInterface->productTableCaption[] = ["Мега", "10%", ['view' => (int) $memory['catalog.option']['price_megamarket']]];
    }
}

$addHandler = [
    'getTableCaption' => 'megamarketAddCaptions'
];
