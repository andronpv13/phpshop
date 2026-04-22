<?php
function UID_acredit_product_hook($obj, $dataArray, $rout) {
    if ($rout == 'MIDDLE') {
        $dis = null;
        
        // Настройки модуля
        include_once dirname(__FILE__) . '/mod_option.hook.php';
        $PHPShopAlfaCreditArray = new PHPShopAlfaCreditArray();
        $option = $PHPShopAlfaCreditArray->getArray();
        
        if ($option['prod_mode'] == 1) {
            $price = str_replace(' ', '',$obj->get('productPrice'));
            $type = $PHPShopAlfaCreditArray->get_type($price);
                
            if ( !empty($option['inn']) && !empty($option['category_name']) && !empty($type) ) {
                // Товар
                $acredit_sum = number_format($price, 2, '.', '');
                $goods[] = array(
                    'id' => $dataArray['id'],
                    'name' => iconv("windows-1251", "utf-8", htmlspecialchars($dataArray['name'], ENT_COMPAT, 'cp1251', true)),
                    'num' => 1,
                    'price' => $acredit_sum,
                    'pic_small' => $_SERVER['SERVER_NAME'] . $dataArray['pic_small'] 
                );
                
                // Форма
                $reference = date('dmyHi') . $dataArray['id'];
                $xml = $PHPShopAlfaCreditArray->get_xml($reference, $goods);
                
                if (!empty($xml)) {
                    // Лог        
                    $PHPShopAlfaCreditArray->log($reference, $goods);
                    
                    $obj->set('acredit_xml', $xml);
                    $obj->set('acredit_name', $option[$type . '_name']);
                    $dis = ParseTemplateReturn($GLOBALS['SysValue']['templates']['alfacredit']['alfacredit_product'], true);    
                }
            }
        }
        
        $obj->set('acredit_product', $dis);
    }
}

$addHandler = array
(
    'UID' => 'UID_acredit_product_hook',
);


?>