<?php

function vkAddOption()
{
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    $PHPShopInterface->_CODE .= '<p class="clearfix"> </p>';
    $PHPShopInterface->_CODE .= __('VK') . '<br>';
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('price_vk', 1, 'Цена VK', $memory['catalog.option']['price_vk']);
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('label_vk', 1, 'Лейбл VK', $memory['catalog.option']['label_vk']) . '<br>';
}

$addHandler = [
    'actionOption' => 'vkAddOption'
];
