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

$PHPShopSystem = new PHPShopSystem();

$NovaPoshta = new NovaPoshta();
$isSuccess = true;
try {
    $cost = $NovaPoshta->getCost(PHPShopSecurity::TotalClean($_REQUEST['cityRef']), PHPShopSecurity::TotalClean($_REQUEST['weight']));
} catch (\Exception $exception) {
    $isSuccess = false;
}

$result = array('success' => $isSuccess, 'price' => $cost);

echo (json_encode($result)); exit;

