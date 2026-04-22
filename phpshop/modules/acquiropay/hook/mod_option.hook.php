<?php

// Настройки модуля
PHPShopObj::loadClass("array");

class PHPShopAcquiroPayArray extends PHPShopArray
{
    public function __construct()
    {
        $this->objType = 3;
        $this->objBase = $GLOBALS['SysValue']['base']['acquiropay']['acquiropay_system'];
        parent::__construct(
            'status',
            'title',
            'title_sub',
            'product_id',
            'merchant_id',
            'merchant_skey',
            'endpoint_url',
            'use_cashbox'
        );
    }
}
