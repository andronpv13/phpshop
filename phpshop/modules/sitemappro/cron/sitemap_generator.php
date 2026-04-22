<?php

// Включение
$enabled = false;

if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_classPath = realpath(dirname(__FILE__)) . "/../../../";
    $enabled = true;
    $ssl = true;
} else
    $_classPath = "../../../";

$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
include_once dirname(__DIR__) . '/class/SitemapPro.php';
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("date");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);

// Авторизация
if ($_GET['s'] == md5($PHPShopBase->SysValue['connect']['host'] . $PHPShopBase->SysValue['connect']['dbase'] . $PHPShopBase->SysValue['connect']['user_db'] . $PHPShopBase->SysValue['connect']['pass_db']))
    $enabled = true;

if (empty($enabled))
    exit("Ошибка авторизации!");

// Настройки модуля
PHPShopObj::loadClass("modules");
$PHPShopModules = new PHPShopModules($_classPath . "modules/");

if (!empty($_GET['hostID']))
    define("HostID", $_GET['hostID']);
else
    define("HostMain", true);

// SSL
if(isset($_GET['ssl']))
    $ssl = true;

(new SitemapPro())->generateSitemap($ssl);

echo "Sitemap.xml done!";