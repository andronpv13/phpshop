<?php

function yandexcartAddOption() {
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['yandexcart']['yandexcart_system']);
    $options = $PHPShopOrm->select();

    $PHPShopInterface->_CODE .= '<p class="clearfix"> </p>';
    $PHPShopInterface->_CODE .= __('Яндекс.Маркет') . '<br>';

    if (!empty($options['model']))
        $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('price_yandex', 1, 'Цена Яндекс.Маркет ' . $options['model'], $memory['catalog.option']['price_yandex']);
    if (!empty($options['model_2']))
        $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('price_yandex_2', 1, 'Цена Яндекс.Маркет ' . $options['model_2'], $memory['catalog.option']['price_yandex_2']);

    if (!empty($options['model_3']))
        $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('price_yandex_3', 1, 'Цена Яндекс.Маркет ' . $options['model_3'], $memory['catalog.option']['price_yandex_3']);

    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('label_yandex_market', 1, 'Лейбл Яндекс.Маркет', $memory['catalog.option']['label_yandex_market']) . '<br>';
}

$addHandler = [
    'actionOption' => 'yandexcartAddOption'
];
