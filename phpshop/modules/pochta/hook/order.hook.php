<?php

include_once dirname(__DIR__) . '/class/include.php';

function order_pochta_hook($obj, $row, $rout) {
    if ($rout === 'MIDDLE') {
        $pochta = new Pochta();

        $PHPShopCart = new PHPShopCart();
        $weight = $PHPShopCart->getWeight();

        if (empty($weight))
            $weight = $pochta->settings->get('weight') > 0 ? $pochta->settings->get('weight') : 100;

        PHPShopParser::set('pochta_widget_id', $pochta->settings->get('widget_id'));
        PHPShopParser::set('pochta_courier_widget_id', $pochta->settings->get('courier_widget_id'));
        PHPShopParser::set('pochta_weight', $weight);
        PHPShopParser::set('pochta_ins_value', (int) $PHPShopCart->getSum(false) * $pochta->settings->get('declared_percent'));

        $cart = $PHPShopCart->getArray();
        if (is_array($cart)) {
            foreach ($cart as $val) {
                $list[] = array('quantity' => (int)$val['num'], 'length' => (int)$val['length'], 'width' => (int)$val['width'], 'height' => (int)$val['height']);
            }
        }


        PHPShopParser::set('pochta_order_lines', json_encode($list));
        $obj->set('order_action_add', ParseTemplateReturn($GLOBALS['SysValue']['templates']['pochta']['pochta_template'], true), true);
    }
}

$addHandler = array('order' => 'order_pochta_hook');
?>