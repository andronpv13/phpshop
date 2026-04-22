<?php

/**
 * Цены меняем
 */
function UID_promotions_hook($obj, $row, $rout) {

    if ($rout == "MIDDLE") {
        
        // Получаем информацию о скидках по действующим промоакциям
        $discount_info = promotion_get_discount($row, true); 
        $obj->set('promotionInfo', $discount_info['description']);
    }
}



$addHandler = array
    (
    'UID' => 'UID_promotions_hook',
);
?>