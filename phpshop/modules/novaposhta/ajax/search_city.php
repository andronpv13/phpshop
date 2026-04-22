<?php
session_start();

$_classPath = "../../../";
include_once($_classPath . "class/obj.class.php");
include_once($_classPath . "modules/novaposhta/class/NovaPoshta.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("security");

$NovaPoshta = new NovaPoshta();

if(isset($_REQUEST['city']) && strlen($_REQUEST['city']) > 2) {
    $isValid = true;

    try {
        $city = $NovaPoshta->getCity(PHPShopSecurity::TotalClean(iconv('UTF-8', 'windows-1251', $_REQUEST['city'])));
        $city['city'] = iconv('windows-1251', 'UTF-8', $city['city']);
        $city['area_description'] = iconv('windows-1251', 'UTF-8', $city['area_description']);
        $city['area_description_ru'] = iconv('windows-1251', 'UTF-8', $city['area_description_ru']);
        $pvz = $NovaPoshta->getPvz($city['ref']);
    } catch (\Exception $exception) {
        $isValid = false;
    }
    $result = array('valid' => $isValid, 'city' => $city, 'pvz' => $pvz);

} else {
    $result = $NovaPoshta->findCity(PHPShopSecurity::TotalClean(iconv('UTF-8', 'windows-1251', $_REQUEST['term'])));
}

echo (json_encode($result)); exit;