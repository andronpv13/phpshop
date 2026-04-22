<?php

// Настройки модуля
PHPShopObj::loadClass("array");
class PHPShopPaypalArray extends PHPShopArray {
    function __construct() {
        $this->objType=3;
        $this->objBase=$GLOBALS['SysValue']['base']['paypal']['paypal_system'];
        parent::__construct("status","sandbox","title","title_end",'merchant_id','merchant_sig','merchant_pwd','message_header','message','link','currency_id');
    }
}

?>
