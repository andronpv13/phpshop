<?php

/**
 * Запись заказа в базу партнера
 */
function send_to_order_partner_hook($obj, $row, $rout) {

    if ($rout == 'END') {

        if (PHPShopSecurity::true_param($_SESSION['partner_id'])) {

            require_once "./phpshop/modules/partner/class/partner.class.php";
            $PHPShopPartnerOrder = new PHPShopPartnerOrder();

            // Модуль включен
            if ($PHPShopPartnerOrder->option['enabled'] == 1) {
                $PHPShopPartnerOrder->writeLog($obj->orderId, $obj->total);
            }
        }
    }
}

$addHandler = array
    (
    'send_to_order' => 'send_to_order_partner_hook'
);
?>