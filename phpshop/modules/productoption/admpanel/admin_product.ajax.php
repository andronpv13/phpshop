<?php

function productoptionAddOption($row) {
    global $PHPShopInterface, $PHPShopModules;

    $memory = $PHPShopInterface->getProductTableFields();

    // SQL
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.productoption.productoption_system"));
    $m_data = $PHPShopOrm->select();
    $vendor = unserialize($m_data['option']);

    $radio = array('<span class="glyphicon glyphicon-remove text-muted"></span>', '<span class="glyphicon glyphicon-ok"></span>');

    if (!empty($vendor['option_1_name'])) {

        if ($vendor['option_1_format'] == 'text')
            $PHPShopInterface->productTableRow[] = [
                'name' => $row['option1'],
                'sort' => 'option1',
                'editable' => 'option1_new',
                'view' => (int) $memory['catalog.option']['option1']
            ];
        else if ($vendor['option_1_format'] == 'radio') {

            $PHPShopInterface->productTableRow[] = [
                'name' => $radio[$row['option1']],
                'sort' => 'option1',
                'view' => (int) $memory['catalog.option']['option1']
            ];
        }
    }

    if (!empty($vendor['option_2_name'])) {

        if ($vendor['option_2_format'] == 'text')
            $PHPShopInterface->productTableRow[] = [
                'name' => $row['option2'],
                'sort' => 'option2',
                'editable' => 'option2_new',
                'view' => (int) $memory['catalog.option']['option2']
            ];
        else if ($vendor['option_2_format'] == 'radio') {

            $PHPShopInterface->productTableRow[] = [
                'name' => $radio[$row['option2']],
                'sort' => 'option2',
                'view' => (int) $memory['catalog.option']['option2']
            ];
        }
    }
    
    if (!empty($vendor['option_3_name'])) {

        if ($vendor['option_3_format'] == 'text')
            $PHPShopInterface->productTableRow[] = [
                'name' => $row['option3'],
                'sort' => 'option3',
                'editable' => 'option3_new',
                'view' => (int) $memory['catalog.option']['option3']
            ];
        else if ($vendor['option_3_format'] == 'radio') {

            $PHPShopInterface->productTableRow[] = [
                'name' => $radio[$row['option3']],
                'sort' => 'option3',
                'view' => (int) $memory['catalog.option']['option3']
            ];
        }
    }
    
    if (!empty($vendor['option_4_name'])) {

        if ($vendor['option_4_format'] == 'text')
            $PHPShopInterface->productTableRow[] = [
                'name' => $row['option4'],
                'sort' => 'option4',
                'editable' => 'option4_new',
                'view' => (int) $memory['catalog.option']['option4']
            ];
        else if ($vendor['option_4_format'] == 'radio') {

            $PHPShopInterface->productTableRow[] = [
                'name' => $radio[$row['option4']],
                'sort' => 'option4',
                'view' => (int) $memory['catalog.option']['option4']
            ];
        }
    }
    
     if (!empty($vendor['option_5_name'])) {

        if ($vendor['option_5_format'] == 'text')
            $PHPShopInterface->productTableRow[] = [
                'name' => $row['option5'],
                'sort' => 'option5',
                'editable' => 'option45_new',
                'view' => (int) $memory['catalog.option']['option5']
            ];
        else if ($vendor['option_5_format'] == 'radio') {

            $PHPShopInterface->productTableRow[] = [
                'name' => $radio[$row['option5']],
                'sort' => 'option5',
                'view' => (int) $memory['catalog.option']['option5']
            ];
        }
    }
    
}

$addHandler = [
    'grid' => 'productoptionAddOption',
];
