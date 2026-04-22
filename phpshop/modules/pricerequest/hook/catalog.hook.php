<?php

/**
 * Элемент формы обратного звонка
 */
class AddToTemplatePricerequestElementAll extends PHPShopElements {

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

        if ($this->option['display'] == 0)
            return true;

        $this->set('pricerequest_name', $this->option['name']);
        $dis = PHPShopParser::file($GLOBALS['SysValue']['templates']['pricerequest']['pricerequest_forma_list'], true, false, true);
        $this->set('pricerequest', $dis);
    }

}

function product_grid_mod_pricerequest_hook($obj, $row) {

    if (PHPShopParser::get('productPrice') == 0) {

        $AddToTemplatePricerequestElementAll = new AddToTemplatePricerequestElementAll();
        $AddToTemplatePricerequestElementAll->display();

        PHPShopParser::set('oneclick', '');
    } else
        PHPShopParser::set('pricerequest', '');
}

$addHandler = array
    (
    'product_grid' => 'product_grid_mod_pricerequest_hook',
);
?>