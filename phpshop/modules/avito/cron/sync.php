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

$products = $PHPShopOrm->getList(['id', 'uid'], ['export_avito' => "='1'", 'export_avito_id' => '=""'], ['order' => 'datas desc'], ['limit' => 1000]);
if (is_array($products) and count($products) > 0) {

    // Ключ обновления 
    if ($Avito->options['type'] == 1)
        $key = 'id';
    else
        $key = 'uid';

    foreach ($products as $product) {
        $id[] = PHPShopString::win_utf8($product[$key]);
    }

    // Синхронизация  AvitoID
    $result = $Avito->getAvitoID($id)['items'];
    
    if (is_array($result) and count($result) > 0) {
        foreach ($result as $product) {
            if($PHPShopOrm->update(['export_avito_id_new' => $product['avito_id']], [$key => '="' . $product['ad_id'] . '"']))
                    $count++;
        }
    }
}

echo "AvitoID синхронизированы для " . $count . " товаров";
