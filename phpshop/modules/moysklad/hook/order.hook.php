<?php

/*
 * Создание сделки и счета в MoySklad.
 */

function moysklad_send_to_order_hook($obj, $data, $rout) {
    
    if ($rout === 'END') {

        require "./phpshop/modules/moysklad/class/MoySklad.php";
        $MoySklad = new MoySklad();

        // Контроль оплаты от статуса заказа
        if (empty($MoySklad->option['status'])) {

            $orm = new PHPShopOrm('phpshop_orders');
            $order = $orm->getOne(array('*'), array('uid' => "='" . $obj->ouid . "'"));

            if (isset($order['id']) && !empty($order['id'])) {
                $MoySklad = new MoySklad($order);
                $MoySklad->init();
            }
        }
    }
}

$addHandler = array('send_to_order' => 'moysklad_send_to_order_hook');
