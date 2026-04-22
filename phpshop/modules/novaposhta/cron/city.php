<?php

$enabled=false;

$_classPath="../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");

include_once dirname(__FILE__) . '/../class/NovaPoshta.php';

// Авторизация
if($_GET['s'] == md5($PHPShopBase->SysValue['connect']['host'].$PHPShopBase->SysValue['connect']['dbase'].$PHPShopBase->SysValue['connect']['user_db'].$PHPShopBase->SysValue['connect']['pass_db']))
        $enabled = true;

if (empty($enabled))
    exit("Ошибка авторизации!");

$NovaPoshta = new NovaPoshta();
$NovaPoshta->loader->getCities();

echo 'Справочник обновлен.';

?>