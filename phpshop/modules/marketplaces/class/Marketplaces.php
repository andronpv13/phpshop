<?php

class Marketplaces
{
    const SBERMARKET = 'sbermarket';
    const ALIEXPRESS = 'aliexpress';
    const CDEK       = 'yandexmarket';
    const RETAIL_CRM = 'retailcrm';
    const IDENTIFIER = 'marketplace';

    public static function isMarketplace()
    {
        if(isset($_GET[self::IDENTIFIER]) and in_array($_GET[self::IDENTIFIER],[self::SBERMARKET,self::ALIEXPRESS,self::CDEK,self::RETAIL_CRM])){
            return true;
        }
        else {
            return false;
        }
    }

    public static function isSbermarket()
    {
        return self::isMarketplace() && $_GET[self::IDENTIFIER] === self::SBERMARKET;
    }

    public static function isAliexpress()
    {
        return self::isMarketplace() && $_GET[self::IDENTIFIER] === self::ALIEXPRESS;
    }

    public static function isCdek()
    {
        return self::isMarketplace() && $_GET[self::IDENTIFIER] === self::CDEK;
    }

    public static function isRetailCRM()
    {
        return self::isMarketplace() && $_GET[self::IDENTIFIER] === self::RETAIL_CRM;
    }

    public static function getProtocol()
    {
        if(!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])) {
            return 'https://';
        }

        return 'http://';
    }
}