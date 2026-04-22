<?php

// Настройки модуля
PHPShopObj::loadClass("array");
class PHPShopDeltaKeyArray extends PHPShopArray {
    function __construct() {
        $this->objType=3;
        $this->objBase=$GLOBALS['SysValue']['base']['deltakey']['deltakey_system'];
        parent::__construct("status","title",'title_end','merchant_id','merchant_key','merchant_skey');
    }
}

?>
