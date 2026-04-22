<?php

/**
 * Очистка памяти корзины после оформления
 */
function send_to_order_visyalcart_hook($obj, $row, $rout) {

    if ($rout == 'START' and PHPShopSecurity::true_search($_COOKIE['visualcart_memory'])) {
        $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['visualcart']['visualcart_system']);
        $options = $orm->select();

        if((int) $options['referal'] === 1) {
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['visualcart']['visualcart_memory']);
            $data = $PHPShopOrm->getOne(array('referal'), ['memory' => sprintf("='%s'", $_COOKIE['visualcart_memory'])]);

            if(!empty($data['referal']))
                $obj->manager_comment.=__('Источник').': '.$data['referal'];
        }
    }
    
    if ($rout == 'END' and PHPShopSecurity::true_search($_COOKIE['visualcart_memory'])) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['visualcart']['visualcart_memory']);
        $PHPShopOrm->delete(array('memory' => "='" . $_COOKIE['visualcart_memory'] . "'"));
        
        // Удаление cookie
        setcookie("ps_referal", '', time() - 10000, "/", $_SERVER['SERVER_NAME'], 0);
    }
}

$addHandler = array
    (
    'send_to_order' => 'send_to_order_visyalcart_hook'
);
?>