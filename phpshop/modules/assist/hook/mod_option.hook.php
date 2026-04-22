<?php

// Настройки модуля
PHPShopObj::loadClass("array");
class PHPShopAssistmoneyArray extends PHPShopArray {
    function PHPShopAssistmoneyArray() {
        $this->objType=3;
        $this->objBase=$GLOBALS['SysValue']['base']['assist']['assist_system'];
        parent::__construct("status","title",'title_end','merchant_id','merchant_sig','assist_url');
    }
}

?>