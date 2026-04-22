<?php

include_once dirname(__DIR__) . '/class/include.php';

function order_yandexdelivery_hook($obj, $row, $rout) {
    if ($rout === 'MIDDLE') {

        $YandexDelivery = new YandexDelivery();

        $PHPShopCart = new PHPShopCart();
        $weight = $PHPShopCart->getWeight();

        if (empty($weight))
            $weight = $YandexDelivery->options['weight'];

        PHPShopParser::set('yandexdelivery_weight', $weight);
        PHPShopParser::set('yandexdelivery_city', $YandexDelivery->options['city']);
        PHPShopParser::set('yandexdelivery_station', $YandexDelivery->options['warehouse_id']);

        $obj->set('order_action_add', ParseTemplateReturn($GLOBALS['SysValue']['templates']['yandexdelivery']['yandexdelivery_template'], true), true);
    }
}

$addHandler = ['order' => 'order_yandexdelivery_hook'];
