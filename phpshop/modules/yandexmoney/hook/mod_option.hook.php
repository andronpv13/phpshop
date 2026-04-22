<?php

// Настройки модуля
PHPShopObj::loadClass("array");
class PHPShopYandexmoneyArray extends PHPShopArray {
    function __construct() {
        $this->objType=3;
        $this->objBase=$GLOBALS['SysValue']['base']['yandexmoney']['yandexmoney_system'];
        parent::__construct("status","title",'title_end','merchant_id','merchant_sig');
    }
}

?>
