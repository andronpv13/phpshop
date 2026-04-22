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

    function setError404() {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
        $PHPShopOrm->debug = false;
        $PHPShopOrm->mysql_error = false;
        $data = $PHPShopOrm->select(array('id'), array('cat_seo_name_old' => '="' . $_SERVER['REQUEST_URI'] . '"'), false, array('limit' => 1));
        if (is_array($data)) {
            header('Location: /shop/CID_' . $data['id'] . '.html', true, 301);
            return true;
        }
        else parent::setError404();
    }

    function index() {

        $this->page = $GLOBALS['PHPShopNav']->objNav['page'];
        $this->PHPShopNav->objNav['nav'] = 'CID';
        $GLOBALS['SysValue']['nav']['nav'] = 'CID';
        
        if (!empty($GLOBALS['PHPShopNav']->objNav['id'])) {
            parent::CID();
            // 404 если каталога не существует или мультибаза
            if (empty($this->category_name) or $this->errorMultibase($this->category) or $this->PHPShopCategory->getParam('skin_enabled') == 1 or $GLOBALS['PHPShopNav']->objNav['page'] == 1)
                parent::setError404();
        }
        else {
            // Обработка массива памяти категорий при большой вложенности
            $GLOBALS['PHPShopSeoPro']->catArrayToMemory();
            $GLOBALS['PHPShopSeoPro']->setRout();
            if (!empty($GLOBALS['PHPShopNav']->objNav['id']))
                parent::CID();
            else
                parent::setError404();
        }
        
        if(empty($this->category_name))
            parent::setError404();
    }

    function query_filter($where = false, $v = false) {

        // Перехват модуля
        $hook = $this->setHook(get_parent_class(), __FUNCTION__);
        if (!empty($hook))
            return $hook;

        return parent::doLoadFunction(__CLASS__, __FUNCTION__, false, 'shop');
    }

    function set_meta($row) {

        $seourl_option = $GLOBALS['PHPShopSeoPro']->getSettings();

        // Перехват модуля
        if ($this->setHook(get_parent_class(), __FUNCTION__, $row))
            return true;

        parent::doLoadFunction(__CLASS__, __FUNCTION__, $row, 'shop');
        $page = $this->PHPShopNav->getPage();
        if ($seourl_option['paginator'] == 2) {
            if ($page > 1) {
                $this->doLoadFunction('PHPShopShop', 'set_meta', $row);
                $this->description .= ' | ' . __('Страница') . ' ' . $this->PHPShopNav->getPage();
                $this->title .= ' | ' . __('Страница') . ' ' . $this->PHPShopNav->getPage();
                return true;
            } 
        }
    }

}

class PHPShopIndex extends PHPShopCore {

    function __construct() {

        $GLOBALS['PHPShopSeoPro']->setRout();

        $PHPShopSeoProCore = new PHPShopSeoProCore();
        $PHPShopSeoProCore->loadAction();
    }

}

?>