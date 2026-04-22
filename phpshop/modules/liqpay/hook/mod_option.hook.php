<?php

// Настройки модуля
PHPShopObj::loadClass("array");
class PHPShopLiqpayArray extends PHPShopArray {
    function __construct() {
        $this->objType=3;
        $this->objBase=$GLOBALS['SysValue']['base']['liqpay']['liqpay_system'];
        parent::__construct("status","title",'title_end','merchant_id','merchant_sig');
    }
}

?>
