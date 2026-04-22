<?php

function avitoAddCaptions() {
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    if (isset($memory['catalog.option']['price_avito'])) {
        $PHPShopInterface->productTableCaption[] = ["Avito", "10%", ['view' => (int) $memory['catalog.option']['price_avito']]];
    }
}

$addHandler = [
    'getTableCaption' => 'avitoAddCaptions'
];