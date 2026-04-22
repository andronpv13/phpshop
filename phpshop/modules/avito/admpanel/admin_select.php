<?php

function avitoAddOption() {
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    $PHPShopInterface->_CODE .= '<p class="clearfix"> </p>';
    $PHPShopInterface->_CODE .= __('Avito') . '<br>';
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('price_avito', 1, 'Цена Avito', $memory['catalog.option']['price_avito']);

    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('label_avito', 1, 'Лейбл Avito', $memory['catalog.option']['label_avito']) . '<br>';
}

$addHandler = [
    'actionOption' => 'avitoAddOption'
];
