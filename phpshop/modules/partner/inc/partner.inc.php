<?php

// Тест
//setcookie("ps_partner", '2', time() + 60 * 60 * 24 * 90, "/", $_SERVER['SERVER_NAME'], 0);

if (!empty($_COOKIE['ps_partner']))
    $partner = intval($_COOKIE['ps_partner']);
else if (!empty($_GET['partner']))
    $partner = intval($_GET['partner']);

if (!empty($partner)) {
    require_once "./phpshop/modules/partner/class/partner.class.php";
    $PHPShopPartnerOrder = new PHPShopPartnerOrder();
    $PHPShopPartnerOrder->setPartner($partner);
}