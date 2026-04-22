<?php

function vkAddCaptions()
{
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    if(isset($memory['catalog.option']['price_vk'])) {
        $PHPShopInterface->productTableCaption[] = ["VK", "15%", ['view' => (int) $memory['catalog.option']['price_vk']]];
    }
}

$addHandler = [
    'getTableCaption' => 'vkAddCaptions'
];
