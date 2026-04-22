<?php

function wbAddOption($row)
{
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    $PHPShopInterface->productTableRow[] = [
        'name'     => $row['price_wb'],
        'sort'     => 'price_wb',
        'editable' => 'price_wb_new',
        'view'     => (int) $memory['catalog.option']['price_wb']
    ];
    
}

function wbAddLabels($product) {
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    // Постфикс
    if (!empty($_GET['cat']))
        $postfix = '&cat=' . (int) $_GET['cat'];
    else
        $postfix = null;

    if (isset($product['export_wb']) && (int) $product['export_wb'] === 1 && (int) $memory['catalog.option']['label_wb'] === 1)
        $PHPShopInterface->productTableRowLabels[] = '<a class="label label-warning" title="' . __('Вывод в WB') . '" href="?path=catalog' . $postfix . '&where[export_wb]=1">' . __('WB') . '</a> ';
}

$addHandler = [
    'grid' => 'wbAddOption',
    'labels' => 'wbAddLabels'
];