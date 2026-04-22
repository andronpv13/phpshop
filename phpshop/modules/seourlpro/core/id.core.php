<?php

include_once("phpshop/core/shop.core.php");

class PHPShopSeoProCore extends PHPShopShop {

    function __construct() {

        // Отладка
        $this->debug = false;

        $this->path = '/shop';

        // Список экшенов
        $this->action = array("nav" => "index");

        parent::__construct();
    }

    function index() {
        parent::UID();
    }

    function image_gallery($row) {

        // Перехват модуля
        if ($this->setHook(get_parent_class(), __FUNCTION__, $row))
            return true;

        parent::doLoadFunction(__CLASS__, __FUNCTION__, $row, 'shop');
    }

    function option_select($row) {

        // Перехват модуля
        if ($this->setHook(get_parent_class(), __FUNCTION__, $row))
            return true;

        parent::doLoadFunction(__CLASS__, __FUNCTION__, $row, 'shop');
    }

    function comment_rate($row, $type = "") {

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__, array("row" => $row, "type" => "$type")))
            return true;
        $this->doLoadFunction(__CLASS__, __FUNCTION__, array("row" => $row, "type" => "$type"),'shop');
    }

    function rating($row) {

        // Перехват модуля
        if ($this->setHook(get_parent_class(), __FUNCTION__, $row))
            return true;

        parent::doLoadFunction(__CLASS__, __FUNCTION__, $row, 'shop');
    }

    function set_meta($row) {
        $this->PHPShopNav->objNav['nav'] = 'UID';
        $GLOBALS['SysValue']['nav']['nav'] = 'UID';
        return parent::doLoadFunction(__CLASS__, __FUNCTION__, $row, 'shop');
    }

    function sort_table($row) {

        // Перехват модуля
        if ($this->setHook(get_parent_class(), __FUNCTION__, $row))
            return true;

        parent::doLoadFunction(__CLASS__, __FUNCTION__, $row, 'shop');
    }

}

class PHPShopId extends PHPShopCore {

    function __construct() {

        // Обработка массива памяти
        //$GLOBALS['PHPShopSeoPro']->catCacheToMemory();
        $GLOBALS['PHPShopSeoPro']->setRout(2);

        $PHPShopSeoProCore = new PHPShopSeoProCore();
        $PHPShopSeoProCore->loadAction();
    }

}

?>