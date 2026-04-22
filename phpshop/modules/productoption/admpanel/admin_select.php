<?php

function productoptionAddOption() {
    global $PHPShopInterface,$PHPShopModules;

    $memory = $PHPShopInterface->getProductTableFields();

    // SQL
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.productoption.productoption_system"));
    $m_data = $PHPShopOrm->select();
    $vendor = unserialize($m_data['option']);

    $PHPShopInterface->_CODE .= '<p class="clearfix"> </p>';
    $PHPShopInterface->_CODE .= __('Дополнительные поля') . '<br>';
    
    if (!empty($vendor['option_1_name']) and ($vendor['option_1_format'] == 'text' or $vendor['option_1_format'] == 'radio'))
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('option1', 1, $vendor['option_1_name'], $memory['catalog.option']['option1']);
    
    if (!empty($vendor['option_2_name']) and ($vendor['option_2_format'] == 'text' or $vendor['option_2_format'] == 'radio'))
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('option2', 1, $vendor['option_2_name'], $memory['catalog.option']['option2']);
    
    if (!empty($vendor['option_3_name']) and ($vendor['option_3_format'] == 'text' or $vendor['option_3_format'] == 'radio'))
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('option3', 1, $vendor['option_3_name'], $memory['catalog.option']['option3']);
    
    if (!empty($vendor['option_4_name']) and ($vendor['option_4_format'] == 'text' or $vendor['option_4_format'] == 'radio'))
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('option24', 1, $vendor['option_4_name'], $memory['catalog.option']['option42']);
    
    if (!empty($vendor['option_5_name']) and ($vendor['option_5_format'] == 'text' or $vendor['option_5_format'] == 'radio'))
    $PHPShopInterface->_CODE .= $PHPShopInterface->setCheckbox('option5', 1, $vendor['option_5_name'], $memory['catalog.option']['option5']);

}

$addHandler = [
    'actionOption' => 'productoptionAddOption'
];
