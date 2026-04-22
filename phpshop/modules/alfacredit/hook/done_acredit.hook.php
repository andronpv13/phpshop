<?php

function send_to_order_mod_acredit_hook($obj, $value, $rout)
{
    if ($rout == 'MIDDLE' && $value['order_metod'] == 10045) {

        // Настройки модуля
        include_once dirname(__FILE__) . '/mod_option.hook.php';
        $PHPShopAlfaCreditArray = new PHPShopAlfaCreditArray();
        $option = $PHPShopAlfaCreditArray->getArray();
        
        $cart = $obj->PHPShopCart->getArray();
        foreach ($cart as $k => $v) {
            $goods[] = array(
                'id' => $v['id'],
                'name' => iconv("windows-1251", "utf-8", htmlspecialchars($v['name'], ENT_COMPAT, 'cp1251', true)),
                'num' => $v['num'],
                'price' => number_format($v['price'], 2, '.', ''),
                'pic_small' => $_SERVER['SERVER_NAME'] . $v['pic_small'] 
            );
        }

        // Форма
        $reference = date('dmy') . str_replace('-', '', $obj->ouid);
        $xml = $PHPShopAlfaCreditArray->get_xml($reference, $goods);
        $obj->set('acredit_xml', $xml);
        $form = ParseTemplateReturn($GLOBALS['SysValue']['templates']['alfacredit']['alfacredit_cart'], true);
        
        // Лог        
        $PHPShopAlfaCreditArray->log($reference, $goods);

        $obj->set('orderMesage', $form);
    }
}

$addHandler = array
(
    'send_to_order' => 'send_to_order_mod_acredit_hook'
);

?>