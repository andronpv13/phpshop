<?php

function productoptionAddCaptions() {
    global $PHPShopInterface, $PHPShopModules;

    $memory = $PHPShopInterface->getProductTableFields();

    // SQL
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.productoption.productoption_system"));
    $m_data = $PHPShopOrm->select();
    $vendor = unserialize($m_data['option']);

    if (isset($memory['catalog.option']['option1'])) {

        if ($vendor['option_1_format'] == 'text')
            $size = '10%';
        else
            $size = '7%';

        $PHPShopInterface->productTableCaption[] = [$vendor['option_1_name'], $size, ['view' => (int) $memory['catalog.option']['option1']]];
    }

    if (isset($memory['catalog.option']['option2'])) {

        if ($vendor['option_2_format'] == 'text')
            $size = '10%';
        else
            $size = '7%';

        $PHPShopInterface->productTableCaption[] = [$vendor['option_2_name'], $size, ['view' => (int) $memory['catalog.option']['option2']]];
    }

    if (isset($memory['catalog.option']['option3'])) {

        if ($vendor['option_3_format'] == 'text')
            $size = '10%';
        else
            $size = '7%';

        $PHPShopInterface->productTableCaption[] = [$vendor['option_3_name'], $size, ['view' => (int) $memory['catalog.option']['option3']]];
    }

    if (isset($memory['catalog.option']['option4'])) {

        if ($vendor['option_4_format'] == 'text')
            $size = '10%';
        else
            $size = '7%';

        $PHPShopInterface->productTableCaption[] = [$vendor['option_4_name'], $size, ['view' => (int) $memory['catalog.option']['option4']]];
    }

    if (isset($memory['catalog.option']['option5'])) {

        if ($vendor['option_4_format'] == 'text')
            $size = '10%';
        else
            $size = '7%';

        $PHPShopInterface->productTableCaption[] = [$vendor['option_5_name'], $size, ['view' => (int) $memory['catalog.option']['option5']]];
    }
}

$addHandler = [
    'getTableCaption' => 'productoptionAddCaptions'
];
