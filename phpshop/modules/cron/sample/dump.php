<?php
/**
 * Дампер для запуска задач через PHPShop.Cron
 * Для включения поменяйте значение enabled на true
 */

// Включение
$enabled=false;

if (empty($_SERVER['DOCUMENT_ROOT'])){
    $_classPath = realpath(dirname(__FILE__)) . "/../../../";
    $enabled = true;
}
else
    $_classPath = "../../../";

include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");

// Авторизация
if($_GET['s'] == md5($PHPShopBase->SysValue['connect']['host'].$PHPShopBase->SysValue['connect']['dbase'].$PHPShopBase->SysValue['connect']['user_db'].$PHPShopBase->SysValue['connect']['pass_db']))
        $enabled = true;

if (empty($enabled))
    exit("Ошибка авторизации!");

// Пишем GZIP файлы
function gzcompressfile($source,$level=false) {
    $dest=$source.'.gz';
    $mode='wb'.$level;
    $error=false;
    if($fp_out=gzopen($dest,$mode)) {
        if($fp_in=fopen($source,'rb')) {
            while(!feof($fp_in))
                gzwrite($fp_out,fread($fp_in,1024*512));
            fclose($fp_in);
        }
        else $error=true;
        gzclose($fp_out);
        unlink($source);
        rename($dest, $source.'.gz');
    }
    else $error=true;
    if($error) return false;
    else return $dest;
}


$file = $_classPath.'admpanel/dumper/backup/base_' . date("d_m_y_His") . '.sql';
include_once($_classPath.'admpanel/dumper/dumper.php');
mysqlbackup($GLOBALS['SysValue']['connect']['dbase'],$file);
gzcompressfile($file);
echo "Архивная копия БД создана.";