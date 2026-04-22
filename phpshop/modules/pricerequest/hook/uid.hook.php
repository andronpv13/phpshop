<?php

/**
 * Элемент формы обратного звонка
 */
class AddToTemplatePricerequestElement extends PHPShopElements {

    var $debug = false;

    /**
     * Конструктор
     */
    function __construct() {
        parent::__construct();
        $this->option();
    }

    /**
     * Настройки
     */
    function option() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['pricerequest']['pricerequest_system']);
        $PHPShopOrm->debug = $this->debug;
        $this->option = $PHPShopOrm->select();
    }

    /**
     * Вывод формы
     */
    function display() {
        $this->set('pricerequest_name', $this->option['name']);
        $dis = PHPShopParser::file($GLOBALS['SysValue']['templates']['pricerequest']['pricerequest_forma'], true, false, true);
        $this->set('pricerequest', $dis);
    }

}

function uid_mod_pricerequest_hook($obj, $row, $rout) {
    if ($rout === 'MIDDLE') {

        if (PHPShopParser::get('productPrice') == 0) {

            $AddToTemplatePricerequestElement = new AddToTemplatePricerequestElement();
            $AddToTemplatePricerequestElement->display();
            
            PHPShopParser::set('oneclick','');
        }
    }
}

$addHandler = array('UID' => 'uid_mod_pricerequest_hook');
