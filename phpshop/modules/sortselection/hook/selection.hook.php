<?php

/**
 * Добавление кнопки быстрого заказа
 */
function sortselection_hook($obj, $row, $rout) {

    if ($rout == 'START') {
        $obj->num_row = 99;
    } else if ($rout == 'END') {

        // Название
        $name = __('Подбор по параметрам');
        $obj->set('sortName', $name);
        $obj->set('sortDes','');
        $obj->title = $name . ' - ' . $obj->PHPShopSystem->getParam('title');
        $obj->description = $name . ', ' . $obj->PHPShopSystem->getParam('descrip');
        
    }
}

function sortselection_setPaginator($obj, $row, $rout) {
    $obj->set('productPageNav', '');
}

$addHandler = array
    (
    'v' => 'sortselection_hook',
    'setPaginator' => 'sortselection_setPaginator'
);
?>