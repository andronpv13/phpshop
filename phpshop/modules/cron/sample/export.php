<?php
/**
 * Выгрузка CSV для запуска задач через PHPShop.Cron
 * Для включения поменяйте значение enabled на true
 */

// Включение для SSH Cron
$enabled=false;

$_classPath="../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass(array("base", "system", "admgui", "orm", "date", "xml", "security", "string", "parser", "mail", "lang"));
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini",true,true);

// Авторизация
if($_GET['s'] == md5($PHPShopBase->SysValue['connect']['host'].$PHPShopBase->SysValue['connect']['dbase'].$PHPShopBase->SysValue['connect']['user_db'].$PHPShopBase->SysValue['connect']['pass_db']))
        $enabled = true;

if (empty($enabled))
    exit("Ошибка авторизации!");

// Системные настройки
$PHPShopSystem = new PHPShopSystem();
$_SESSION['lang'] = $PHPShopSystem->getSerilizeParam("admoption.lang_adm");
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'admin'));
mb_internal_encoding($GLOBALS['PHPShopBase']->codBase);

// Редактор GUI
$PHPShopGUI = new PHPShopGUI();
$PHPShopInterface = new PHPShopInterface();

// Обработка CSV
include($_classPath . "admpanel/exchange/admin_export.php");

$_POST['exchanges'] = intval($_GET['id']);
$_REQUEST['file'] = $_GET['file'];
actionSave();
echo __('Обработано').' '.$GLOBALS['csv_export_count'].' '.__('строк');
?>