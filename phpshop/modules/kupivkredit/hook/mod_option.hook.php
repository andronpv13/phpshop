<?php

// Настройки модуля
PHPShopObj::loadClass("array");

class PHPShopKVKArray extends PHPShopArray
{
    function __construct()
    {
        $this->objType = 3;
        $this->objBase = $GLOBALS['SysValue']['base']['kupivkredit']['kupivkredit_system'];
        parent::__construct('shop_id', 'showcase_id', 'promo');
    }
}
