<?php

function yandexcartAddOption($row)
{
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    $PHPShopInterface->productTableRow[] = [
        'name'     => $row['price_yandex'],
        'sort'     => 'price_yandex',
        'editable' => 'price_yandex_new',
        'view'     => (int) $memory['catalog.option']['price_yandex']
    ];
    
    $PHPShopInterface->productTableRow[] = [
        'name'     => $row['price_yandex_2'],
        'sort'     => 'price_yandex_2',
        'editable' => 'price_yandex_2_new',
        'view'     => (int) $memory['catalog.option']['price_yandex_2']
    ];
    
    $PHPShopInterface->productTableRow[] = [
        'name'     => $row['price_yandex_3'],
        'sort'     => 'price_yandex_3',
        'editable' => 'price_yandex_3_new',
        'view'     => (int) $memory['catalog.option']['price_yandex_3']
    ];
}

function yandexAddLabels($product) {
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    // Постфикс
    if (!empty($_GET['cat']))
        $postfix = '&cat=' . (int) $_GET['cat'];
    else
        $postfix = null;
    
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['yandexcart']['yandexcart_system']);
    $options = $PHPShopOrm->select();

    if ((int) $product['yml'] === 1 && (int) $memory['catalog.option']['label_yandex_market'] === 1)
        $PHPShopInterface->productTableRowLabels[] = '<a class="label label-success" title="' . __('Яндекс.Маркет '.$options['model']) . '" href="?path=catalog' . $postfix . '&where[yml]=1">' . __('Я '.$options['model']) . '</a> ';
    
    if ((int) $product['yml_2'] === 1 && (int) $memory['catalog.option']['label_yandex_market'] === 1)
        $PHPShopInterface->productTableRowLabels[] = '<a class="label label-warning" title="' . __('Яндекс.Маркет '.$options['model_2']) . '" href="?path=catalog' . $postfix . '&where[yml_2]=1">' . __('Я '.$options['model_2']) . '</a> ';
    
    if ((int) $product['yml_3'] === 1 && (int) $memory['catalog.option']['label_yandex_market'] === 1)
        $PHPShopInterface->productTableRowLabels[] = '<a class="label label-info" title="' . __('Яндекс.Маркет '.$options['model_3']) . '" href="?path=catalog' . $postfix . '&where[yml_3]=1">' . __('Я '.$options['model_3']) . '</a> ';
}

$addHandler = [
    'grid'   => 'yandexcartAddOption',
    'labels' => 'yandexAddLabels'
];