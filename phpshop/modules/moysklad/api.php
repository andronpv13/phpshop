<?php

$_classPath = "../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("text");
PHPShopObj::loadClass("string");
PHPShopObj::loadClass("product");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("mail");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("security");

$PHPShopModules = new PHPShopModules($_classPath . "modules/");
$PHPShopModules->checkInstall('moysklad');

$PHPShopSystem = new PHPShopSystem();
$PHPShopValutaArray = new PHPShopValutaArray();

include_once($_classPath . 'modules/moysklad/class/MoySklad.php');
$MoySklad = new MoySklad();

$postData = file_get_contents('php://input');
$json = json_decode($postData, true);
$_POST['events']=$json['events'][0];

if (is_array($_POST['events'])) {

    if ($MoySklad->checkauth($_POST['events']['accountId'])) {

        switch ($_POST['events']['action']) {

            case "UPDATE":

                switch ($_POST['events']['meta']['type']) {
                
                    case "product":
                        $MoySklad->updateProducts($_POST['events']['meta']['href']);
                        break;
                }
                
                break;

            case "CREATE";
                
                switch ($_POST['events']['meta']['type']) {

                    // Приемка
                    case "supply":
                        $MoySklad->updateWarehouse($_POST['events']['meta']['href']);
                        break;
                    
                    // Оприходование
                    case "enter":
                        $MoySklad->updateWarehouse($_POST['events']['meta']['href']);
                        break;
                    
                    // Отгрузка
                    case "demand":
                        $MoySklad->updateWarehouse($_POST['events']['meta']['href']);
                        break;
                    
                    // Списание
                    case "loss":
                        $MoySklad->updateWarehouse($_POST['events']['meta']['href']);
                        break;
                    
                    // Розничная продажа
                    case "retaildemand":
                        $MoySklad->updateWarehouse($_POST['events']['meta']['href']);
                        break;
                    
                }

                break;
        }
    } else
        exit('Unauthorized');
}
 
?>