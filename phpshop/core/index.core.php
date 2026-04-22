<?php

/**
 * Обработчик приветственного сообщения на главной странице
 * @author PHPShop Software
 * @version 1.4
 * @package PHPShopCore
 */
class PHPShopIndex extends PHPShopCore {


    function __construct() {
        $this->objBase = $GLOBALS['SysValue']['base']['page'];
        $this->debug = false;
        $this->template = 'templates.index';
        parent::__construct();
    }

    function index() {

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
            return true;
        
        if($this->PHPShopNav->objNav['truepath'] != '/')
            return header('Location: /404/');
        
        $where['category'] = "=2000";
        $where['enabled'] = "='1'";

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif(defined("HostMain"))
            $where['enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        // Выборка данных
        $row = parent::getFullInfoItem(array('id,name,content'), $where);

        // Определяем переменные
        $this->set('mainContent', Parser($row['content'], $this->getValue('templates.index')));
        $this->set('mainContentTitle', Parser($row['name'], $this->getValue('templates.index')));
        $this->PHPShopNav->objNav['id'] = $row['id'];

        // Перехват модуля
        $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');
    }
}

?>