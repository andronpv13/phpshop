<?php
/**
 * Вывод характеристик в поиске
 * @package PHPShopAjaxElements
 */

session_start();
$_classPath="../";
include($_classPath."class/obj.class.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath."inc/config.ini");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("category");
PHPShopObj::loadClass("sort");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("text");

class PHPShopSortAjax extends PHPShopSort{

    function display(){
         return PHPShopText::td($this->disp);
    }

}

$PHPShopSortAjax = new PHPShopSortAjax(intval($_REQUEST['category']));

$_RESULT = array(
        'sort' =>$PHPShopSortAjax->display()
);
 
?>