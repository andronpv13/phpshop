<?php
session_start();

$_classPath = "../../../../";
include_once($_classPath . "class/obj.class.php");
include_once($_classPath . "modules/yandexcart/class/YandexMarket.php");
PHPShopObj::loadClass('base');
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
PHPShopObj::loadClass('modules');
PHPShopObj::loadClass('orm');
PHPShopObj::loadClass('system');
PHPShopObj::loadClass('security');
PHPShopObj::loadClass('string');

$PHPShopBase->chekAdmin();

$YandexMarket = new YandexMarket();

if(isset($_REQUEST['id']) && (int) $_REQUEST['id'] > 0) {
    $region = $YandexMarket->getRegionById((int) $_REQUEST['id']);
    if(!$region) {
        $result = array('success' => false);
    } else {
        $result = array(
            'success' => true,
            'region' => $region
        );
    }
} else {
    $result = $YandexMarket->findRegion(PHPShopSecurity::TotalClean(PHPShopString::utf8_win1251($_REQUEST['term'])));
}

echo (json_encode($result)); exit;