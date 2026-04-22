<?php
if (!defined("OBJENABLED")) {
    exit();
}

PHPShopObj::loadClass("order");
PHPShopObj::loadClass('delivery');
PHPShopObj::loadClass('array');
PHPShopObj::loadClass('valuta');
PHPShopObj::loadClass('cart');

include_once dirname(__DIR__) . '/class/YandexDelivery.php';