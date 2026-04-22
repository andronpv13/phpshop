<?php

function ozonAddOption($row)
{
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    $PHPShopInterface->productTableRow[] = [
        'name'     => $row['price_ozon'],
        'sort'     => 'price_ozon',
        'editable' => 'price_ozon_new',
        'view'     => (int) $memory['catalog.option']['price_ozon']
    ];
    
}

function ozonAddLabels($product) {
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    // Постфикс
    if (!empty($_GET['cat']))
        $postfix = '&cat=' . (int) $_GET['cat'];
    else
        $postfix = null;

    if (isset($product['export_ozon']) && (int) $product['export_ozon'] === 1 && (int) $memory['catalog.option']['label_ozon'] === 1)
        $PHPShopInterface->productTableRowLabels[] = '<a class="label label-warning" title="' . __('Вывод в OZON') . '" href="?path=catalog' . $postfix . '&where[export_ozon]=1">' . __('OZ') . '</a> ';
}

$addHandler = [
    'grid' => 'ozonAddOption',
    'labels' => 'ozonAddLabels'
];