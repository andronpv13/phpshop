<?php
if (!defined("OBJENABLED")) {
    exit();
}

PHPShopObj::loadClass("order");
PHPShopObj::loadClass('delivery');
PHPShopObj::loadClass('array');
PHPShopObj::loadClass('valuta');
PHPShopObj::loadClass('cart');

include_once dirname(__DIR__) . '/class/Pochta.php';
include_once dirname(__DIR__) . '/class/PochtaRequest.php';
include_once dirname(__DIR__) . '/class/Settings.php';