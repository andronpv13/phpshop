<?php

session_start();

// Включение
$enabled = false;

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
$option = (new PHPShopOrm($PHPShopModules->getParam("base.vkseller.vkseller_system")))->getOne(['*']);

$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
$data = $PHPShopOrm->getList(['*'], ['export_vk' => "='1'", 'export_vk_id' => '>0'], ['order' => 'datas desc'], ['limit' => 1000]);
$result=0;
if (is_array($data)) {

    include_once dirname(__FILE__) . '/../class/VkSeller.php';
    $VkSeller = new VkSeller();

    foreach ($data as $prod) {

        $result+= (int) $VkSeller->updateProduct($prod)['response'];
    }

    echo "Данные успешно обновлены для " . $result . " товаров";
}
?>