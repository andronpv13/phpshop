<?php

session_start();

// Включение
$enabled = false;

if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_classPath = realpath(dirname(__FILE__)) . "/../../../";
    $enabled = true;
} else
    $_classPath = "../../../";

include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("date");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("cart");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("text");
PHPShopObj::loadClass("lang");
PHPShopObj::loadClass("security");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopSystem = new PHPShopSystem();

// Авторизация
if ($_GET['s'] == md5($PHPShopBase->SysValue['connect']['host'] . $PHPShopBase->SysValue['connect']['dbase'] . $PHPShopBase->SysValue['connect']['user_db'] . $PHPShopBase->SysValue['connect']['pass_db']))
    $enabled = true;

if (empty($enabled))
    exit("Ошибка авторизации!");


// Настройки модуля
PHPShopObj::loadClass("modules");
$PHPShopModules = new PHPShopModules($_classPath . "modules/");

$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
$count = 0;

include_once dirname(__FILE__) . '/../class/Avito.php';
$Avito = new Avito();

// Отправка по 200 товаров
function AvitoSendLimit($start, $end) {
    global $PHPShopOrm,$Avito,$count;

    $products = $PHPShopOrm->getList(['*'], ['export_avito' => "='1'"], ['order' => 'datas desc'], ['limit' => $start.','.$end]);
    if (is_array($products) and count($products) > 0) {

        // Цены
        foreach ($products as $product)
            $count += $Avito->updatePrices($product);
    }
    
}

AvitoSendLimit(0,200);
AvitoSendLimit(200,200);
AvitoSendLimit(400,200);
AvitoSendLimit(600,200);
AvitoSendLimit(800,200);

echo "Цены отправлены для " . $count . " товаров";
