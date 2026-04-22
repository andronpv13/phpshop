<?php

function vkAddOption($row)
{
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    $PHPShopInterface->productTableRow[] = [
        'name'     => $row['price_vk'],
        'sort'     => 'price_vk',
        'editable' => 'price_vk_new',
        'view'     => (int) $memory['catalog.option']['price_vk']
    ];
    
}

function vkAddLabels($product) {
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    // Постфикс
    if (!empty($_GET['cat']))
        $postfix = '&cat=' . (int) $_GET['cat'];
    else
        $postfix = null;

    if (isset($product['export_vk']) && (int) $product['export_vk'] === 1 && (int) $memory['catalog.option']['label_vk'] === 1)
        $PHPShopInterface->productTableRowLabels[] = '<a class="label label-warning" title="' . __('Вывод в VK') . '" href="?path=catalog' . $postfix . '&where[export_vk]=1">' . __('VK') . '</a> ';
}

$addHandler = [
    'grid' => 'vkAddOption',
    'labels' => 'vkAddLabels'
];