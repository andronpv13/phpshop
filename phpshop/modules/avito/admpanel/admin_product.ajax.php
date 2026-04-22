<?php

function avitoAddOption($row)
{
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    $PHPShopInterface->productTableRow[] = [
        'name'     => $row['price_avito'],
        'sort'     => 'price_avito',
        'editable' => 'price_avito_new',
        'view'     => (int) $memory['catalog.option']['price_avito']
    ];
    
}

function avitoAddLabels($product) {
    global $PHPShopInterface;

    $memory = $PHPShopInterface->getProductTableFields();

    // Постфикс
    if (!empty($_GET['cat']))
        $postfix = '&cat=' . (int) $_GET['cat'];
    else
        $postfix = null;
    
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['avito']['avito_system']);
    $options = $PHPShopOrm->select();

    if ((int) $product['yml'] === 1 && (int) $memory['catalog.option']['label_avito'] === 1)
        $PHPShopInterface->productTableRowLabels[] = '<a class="label label-success" title="' . __('Avito') . '" href="?path=catalog' . $postfix . '&where[export_avito]=1">' . __('A') . '</a> ';
}

$addHandler = [
    'grid'   => 'avitoAddOption',
    'labels' => 'avitoAddLabels'
];