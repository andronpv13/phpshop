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

$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.visualcart.visualcart_system"));
$option = $PHPShopOrm->getOne(array('day'));

$time = time();
$day = (int) $option['day'];

if (!empty($day)) {
    $until = $time - (2592000 / 30)*$day;
    
    // Удаление журнала
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.visualcart.visualcart_log"));
    $PHPShopOrm->delete(['date' => "<" . $until]);

    // Удаление корзин
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.visualcart.visualcart_memory"));
    $result = $PHPShopOrm->delete(['date' => "<" . $until]);
    if (!empty($result))
        echo "Удалено ".$PHPShopOrm->get_affected_rows()." корзин";
}
?>