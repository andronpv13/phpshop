<?php

/**
 * Корзина
 * @package PHPShopAjaxElements
 */
session_start();

$_classPath = "../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("string");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("parser");


$country = intval($_REQUEST['country']);
$region = intval($_REQUEST['region']);
$par = $_REQUEST['par'];

header('Content-type: text/html; charset=windows-1251');
switch ($par) {
    case "country_new":
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_region']);
        //$PHPShopOrm->debug=true;
        $data = $PHPShopOrm->select(array('*'), array('country_id' => "=$country"),false,array('limit'=>300));
        // регион
        $disOpt = "<option value='' for='0'>-----------</option>";
        foreach ($data as $row) {
            $disOpt .= "<option value='" . $row['name'] . "' for='" . $row['region_id'] . "' >" . $row['name'] . "</option>";
        }
        echo $disOpt;
        exit();

    case "state_new":
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_city']);
        $data = $PHPShopOrm->select(array('*'), array('region_id' => "=$region"),false,array('limit'=>300));
        
        // регион
        $disOpt = "<option value='' for='0'>-----------</option>";
        foreach ($data as $row) {
            $disOpt .= "<option value='" . $row['name'] . "' >" . $row['name'] . "</option>";
        }
        echo $disOpt;
        exit();
}
?>