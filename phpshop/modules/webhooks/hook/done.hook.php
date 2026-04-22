<?php

/*
 * Создание сделки 
 */
function webhooks_send_to_order_hook($obj, $data, $rout) {

    if ($rout === 'END') {
        include_once('./phpshop/modules/webhooks/class/webhooks.class.php');

        $orm = new PHPShopOrm('phpshop_orders');
        $order = $orm->getOne(array('*'), array('uid' => "='" . $obj->ouid . "'"));

        if (!empty($order['id'])) {
            $PHPShopWebhooks = new PHPShopWebhooks($order);
            $PHPShopWebhooks->getHooks(1);
            $PHPShopWebhooks->init();
        }
    }
}

$addHandler = array('send_to_order' => 'webhooks_send_to_order_hook');
