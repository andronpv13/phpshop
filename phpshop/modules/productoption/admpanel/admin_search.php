<?php

function productoptionAddOption() {
    global $PHPShopInterface,$PHPShopModules;

    // SQL
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.productoption.productoption_system"));
    $m_data = $PHPShopOrm->select();
    $vendor = unserialize($m_data['option']);

    // Память заполнения
    parse_str(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY), $query);
    
    if (!empty($vendor['option_1_name']) and ($vendor['option_1_format'] == 'text'))
    $PHPShopInterface->_CODE .= $PHPShopInterface->setField($vendor['option_1_name'], $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'where[option1]', 'placeholder' => '', 'class' => 'pull-left', 'value' => $query['where']['option1'])));
    
    if (!empty($vendor['option_2_name']) and ($vendor['option_2_format'] == 'text'))
    $PHPShopInterface->_CODE .= $PHPShopInterface->setField($vendor['option_1_name'], $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'where[option2]', 'placeholder' => '', 'class' => 'pull-left', 'value' => $query['where']['option2'])));
    
    if (!empty($vendor['option_3_name']) and ($vendor['option_3_format'] == 'text'))
    $PHPShopInterface->_CODE .= $PHPShopInterface->setField($vendor['option_3_name'], $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'where[option3]', 'placeholder' => '', 'class' => 'pull-left', 'value' => $query['where']['option3'])));
    
    if (!empty($vendor['option_4_name']) and ($vendor['option_4_format'] == 'text'))
    $PHPShopInterface->_CODE .= $PHPShopInterface->setField($vendor['option_1_name'], $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'where[option4]', 'placeholder' => '', 'class' => 'pull-left', 'value' => $query['where']['option4'])));
    
    if (!empty($vendor['option_5_name']) and ($vendor['option_5_format'] == 'text'))
    $PHPShopInterface->_CODE .= $PHPShopInterface->setField($vendor['option_5_name'], $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'where[option5]', 'placeholder' => '', 'class' => 'pull-left', 'value' => $query['where']['option5'])));
}

$addHandler = [
    'actionAdvanceSearch' => 'productoptionAddOption'
];
