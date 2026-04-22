<?php
session_start();

$_classPath = "../../../";
include_once($_classPath . "class/obj.class.php");
include_once($_classPath . "modules/branches/class/include.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("security");

$Geolocation = (new Branches())->Geolocation;

if(isset($_REQUEST['city']) && strlen(trim($_REQUEST['city'])) > 2) {
    if ($Geolocation->isCityExist(PHPShopSecurity::TotalClean(iconv('UTF-8', 'windows-1251', $_REQUEST['city'])))) {
        $Geolocation->changeCity(PHPShopSecurity::TotalClean(iconv('UTF-8', 'windows-1251', $_REQUEST['city'])));
        $result['success'] = true;
    } else {
        $result['success'] = false;
    }
} elseif (isset($_REQUEST['loadRegions']) && (int) $_REQUEST['loadRegions'] === 1) {
    $result = $Geolocation->loadRegions();
    $result['success'] = true;
} elseif (isset($_REQUEST['changedRegionId']) && (int) $_REQUEST['changedRegionId'] > 0) {
    $result['cities'] = $Geolocation->getCities((int) $_REQUEST['changedRegionId']);
    $result['success'] = true;
} else {
    $result = $Geolocation->findCity(PHPShopSecurity::TotalClean(iconv('UTF-8', 'windows-1251', $_REQUEST['term'])));
}

echo (json_encode($result)); exit;