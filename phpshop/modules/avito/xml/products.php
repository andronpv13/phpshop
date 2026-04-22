<?php

$_classPath = "../../../";
include_once($_classPath . "class/obj.class.php");
include_once($_classPath . "modules/avito/class/AvitoXML.php");
include_once($_classPath . "modules/avito/class/Avito.php");

PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("product");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("string");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("file");
PHPShopObj::loadClass("lang");

$PHPShopValutaArray = new PHPShopValutaArray();

$PHPShopSystem = new PHPShopSystem();

$PHPShopLang = new PHPShopLang(array('locale'=>$_SESSION['lang'],'path'=>'shop'));
$PHPShopModules = new PHPShopModules($_classPath . "modules/");

// XML
$AvitoXML = new AvitoXML();
header("HTTP/1.1 200");
header("Content-Type: application/xml; charset=utf-8");

ob_start();
echo $AvitoXML->compile();
$xml = ob_get_clean();
echo str_replace(' x ',' × ',$xml);
