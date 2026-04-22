<?php

/*
 * Создание сделки и счета в Битрикс24.
 */
function bitrix24_send_to_order_hook($obj, $data, $rout)
{
    if($rout === 'END') {

        require "./phpshop/modules/bitrix24/class/Bitrix24.php";

        $orm = new PHPShopOrm('phpshop_orders');
        $order = $orm->getOne(array('*'), array('uid' => "='" . $obj->ouid . "'"));

        if(isset($order['id']) && !empty($order['id'])) {
            $Bitrix24 = new Bitrix24($order);
            $Bitrix24->init();
        }
    }
}

$addHandler = array('send_to_order' => 'bitrix24_send_to_order_hook');