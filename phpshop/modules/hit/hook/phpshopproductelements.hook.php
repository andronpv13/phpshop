<?php

/**
 * ¬ывод иконки хит в кратком описании товаров.
 */
function hit_product_grid_elements_hook($obj, $dataArray) {

    if (!empty($dataArray['hit'])) {
        $obj->set('hitIcon', PHPShopParser::file($GLOBALS['SysValue']['templates']['hit']['icon'], true, false, true));
    }
    else
        $obj->set('hitIcon', '');
    
}

$addHandler = array('product_grid' => 'hit_product_grid_elements_hook');
?>