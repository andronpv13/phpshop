<?php

function send_to_order_cdekfulfillment_hook($obj, $row, $rout) {

    // Удаление доставки
    /*
      if ($rout == 'MIDDLE') {
      $obj->cdekfulfillment_delivery_price = $obj->delivery;
      $obj->delivery = 0;
      $obj->total=$obj->total-$obj->cdekfulfillment_delivery_price;
      } */

    if ($rout == 'END') {

        require "./phpshop/modules/cdekfulfillment/class/CDEKFulfillment.php";
        $CDEKFulfillment = new CDEKFulfillment();

        $cdekfulfillment_delivery_price = $CDEKFulfillment->getDeliveryPrice($obj->weight);
        (new PHPShopOrm($GLOBALS['SysValue']['base']['orders']))->update(
                ['cdekfulfillment_delivery_price_new' => $cdekfulfillment_delivery_price], ['id' => '=' . (int) $obj->orderId]);
    }
}

$addHandler = array('send_to_order' => 'send_to_order_cdekfulfillment_hook');
