<?php

class PbKredit {

    const CREDIT_PERIOD = 12;

    public function __construct()
    {
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_pbkredit_system');

        $this->option = $PHPShopOrm->select();
    }

    public function render($product)
    {
        if((int) $product['price'] === 0) {
            return '';
        }

        PHPShopParser::set('pbkredit_tt_code', $this->option['tt_code']);
        PHPShopParser::set('pbkredit_tt_name', $this->option['tt_name']);
        PHPShopParser::set('pbkredit_tt_product_price', $product['price']);
        PHPShopParser::set('pbkredit_tt_product_name', $product['name']);
        PHPShopParser::set('pbkredit_cost', number_format($product['price'] / self::CREDIT_PERIOD, 0, '.', ' '));

        return ParseTemplateReturn($GLOBALS['SysValue']['templates']['pbkredit']['pbkredit_template'], true);
    }
}