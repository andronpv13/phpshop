<?php

class Hit extends PHPShopProductElements {

    public function getCatalogHits($catalogId) {
        global $PHPShopProductIconElements;

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);

        $where['hit'] = "='1'";
        $where['enabled'] = "='1'";
        $where['parent_enabled'] = "='0'";
        $where['category'] = '="' . $catalogId . '"';

        // Мультибаза
        $queryMultibase = $this->queryMultibase();
        if (!empty($queryMultibase))
            $where['enabled'].= ' ' . $queryMultibase;

        $result = $PHPShopOrm->select(array('*'), $where);

        if (!$result) {
            return '';
        }

        // фикс единичной выборки орма
        if (isset($result['id'])) {
            $products = array($result);
        } else {
            $products = $result;
        }
        
        $GLOBALS['SysValue']['templates']['hit_product'] = $GLOBALS['SysValue']['templates']['hit']['hit_product'];

        // Поиск шаблона модуля в основном шаблоне
        $path_template = str_replace('./phpshop', $GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'], $GLOBALS['SysValue']['templates']['hit_product']);
        if (is_file($path_template))
            $GLOBALS['SysValue']['templates']['hit_product'] = $path_template;

        PHPShopParser::set('hit_products', $PHPShopProductIconElements->seamply_forma($products, 10000, 'hit_product', true, true));

        return PHPShopParser::file($GLOBALS['SysValue']['templates']['hit']['hit_catalog'], true, false, true);
    }

}