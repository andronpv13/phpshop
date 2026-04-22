<?php

function yandexcartAddCaptions() {
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['yandexcart']['yandexcart_system']);
    $options = $PHPShopOrm->select();

    if (isset($memory['catalog.option']['price_yandex']) and !empty($options['model'])) {
        $PHPShopInterface->productTableCaption[] = ["ß.Ì ".$options['model'], "10%", ['view' => (int) $memory['catalog.option']['price_yandex']]];
    }
    
    if (isset($memory['catalog.option']['price_yandex_2']) and !empty($options['model_2'])) {
        $PHPShopInterface->productTableCaption[] = ["ß.Ì ".$options['model_2'], "10%", ['view' => (int) $memory['catalog.option']['price_yandex_2']]];
    }
    
    if (isset($memory['catalog.option']['price_yandex_3']) and !empty($options['model_3'])) {
        $PHPShopInterface->productTableCaption[] = ["ß.Ì ".$options['model_3'], "10%", ['view' => (int) $memory['catalog.option']['price_yandex_3']]];
    }
}

$addHandler = [
    'getTableCaption' => 'yandexcartAddCaptions'
];