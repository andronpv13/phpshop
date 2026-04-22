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
$data = $PHPShopOrm->getList(['*'], ['export_ozon' => "='1'"], ['order' => 'datas desc'], ['limit' => 100]);
$count = 0;
if (is_array($data)) {

    include_once dirname(__FILE__) . '/../class/OzonSeller.php';
    $OzonSeller = new OzonSeller();

    // Склад
    if (is_array($OzonSeller->warehouse))
        foreach ($OzonSeller->warehouse as $warehouse) {
            $result = $OzonSeller->setProductStock($data, $warehouse['id'])['result'];
        }

    // Цены
    $OzonSeller->setProductPrice($data);
    
    // Результат
    if(is_array($result))
        foreach($result as $res){
            if(!empty($res['updated']))
                $count++;
        }

    echo "Данные успешно отправлены для " . $count . " товаров";
}
?>