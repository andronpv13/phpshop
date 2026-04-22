<?php

function hitAddLabels($product) {
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    // Постфикс
    if (!empty($_GET['cat']))
        $postfix = '&cat=' . (int) $_GET['cat'];
    else
        $postfix = null;

    if (isset($product['hit']) && (int) $product['hit'] === 1 && (int) $memory['catalog.option']['hit'] === 1)
        $PHPShopInterface->productTableRowLabels[] = '<a class="label label-warning" title="' . __('Хит') . '" href="?path=catalog' . $postfix . '&where[hit]=1">' . __('Х') . '</a> ';
}

$addHandler = [
    'labels' => 'hitAddLabels'
];