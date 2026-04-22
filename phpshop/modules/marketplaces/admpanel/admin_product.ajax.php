<?php

function marketplacesAddOption($row)
{
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    $PHPShopInterface->productTableRow[] = [
        'name'     => $row['price_google'],
        'sort'     => 'price_google',
        'editable' => 'price_google_new',
        'view'     => (int) $memory['catalog.option']['price_google']
    ];

    $PHPShopInterface->productTableRow[] = [
        'name'     => $row['price_cdek'],
        'sort'     => 'price_cdek',
        'editable' => 'price_cdek_new',
        'view'     => (int) $memory['catalog.option']['price_cdek']
    ];

    $PHPShopInterface->productTableRow[] = [
        'name'     => $row['price_aliexpress'],
        'sort'     => 'price_aliexpress',
        'editable' => 'price_aliexpress_new',
        'view'     => (int) $memory['catalog.option']['price_aliexpress']
    ];

    $PHPShopInterface->productTableRow[] = [
        'name'     => $row['price_sbermarket'],
        'sort'     => 'price_sbermarket',
        'editable' => 'price_sbermarket_new',
        'view'     => (int) $memory['catalog.option']['price_sbermarket']
    ];
}

function marketplacesAddLabels($product) {
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    // Постфикс
    if (!empty($_GET['cat']))
        $postfix = '&cat=' . (int) $_GET['cat'];
    else
        $postfix = null;

    if (isset($product['sbermarket']) && (int) $product['sbermarket'] === 1 && (int) $memory['catalog.option']['label_sbermarket'] === 1)
        $PHPShopInterface->productTableRowLabels[] = '<a class="label label-success" title="' . __('Вывод в Мегамаркет') . '" href="?path=catalog' . $postfix . '&where[sbermarket]=1">' . __('ММ') . '</a> ';

    if (isset($product['cdek']) && (int) $product['cdek'] === 1 && (int) $memory['catalog.option']['label_cdek'] === 1)
        $PHPShopInterface->productTableRowLabels[] = '<a class="label label-success" title="' . __('Вывод в Яндекс.Маркет') . '" href="?path=catalog' . $postfix . '&where[cdek]=1">' . __('ЯМ') . '</a> ';

    if (isset($product['aliexpress']) && (int) $product['aliexpress'] === 1 && (int) $memory['catalog.option']['label_aliexpress'] === 1)
        $PHPShopInterface->productTableRowLabels[] = '<a class="label label-success" title="' . __('Вывод в AliExpress') . '" href="?path=catalog' . $postfix . '&where[aliexpress]=1">' . __('Ali') . '</a> ';

    if (isset($product['google_merchant']) && (int) $product['google_merchant'] === 1 && (int) $memory['catalog.option']['label_google_merchant'] === 1)
        $PHPShopInterface->productTableRowLabels[] = '<a class="label label-success" title="' . __('Вывод в Google Merchant') . '" href="?path=catalog' . $postfix . '&where[google_merchant]=1">' . __('GM') . '</a> ';
}

$addHandler = [
    'grid' => 'marketplacesAddOption',
    'labels' => 'marketplacesAddLabels'
];