<?php

if (!defined("OBJENABLED")) {
   exit();
}


//Modules settings
PHPShopObj::loadClass("array");
class PHPShopPaykeeperArray extends PHPShopArray {
    function __construct() {
        $this->objType=3;
        $this->objBase=$GLOBALS['SysValue']['base']['paykeeper']['paykeeper_system'];
        parent::__construct("status", "title", "title_end", "form_url", "secret","forced_discount_check");
    }
}
?>
