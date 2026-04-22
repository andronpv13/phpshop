<?php

/**
 * Запись UTM-метки в заказ
 */
function send_to_order_adanalyzer_hook($obj, $row, $rout) {

    if ($rout == 'START' and PHPShopSecurity::TotalClean($_COOKIE['ps_adanalyzer'])) {
        $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['adanalyzer']['adanalyzer_system']);
        $options = $orm->select();

        if ((int) $options['enabled'] === 1) {
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['adanalyzer']['adanalyzer_campaign']);
            $data = $PHPShopOrm->getOne(array('*'), array('utm' => '="'.$_COOKIE['ps_adanalyzer'].'"'));

            if (!empty($data['utm']))
                $obj->manager_comment .= __('Рекламная кампания') . ': ' . $data['name'];
        }
    }

    if ($rout == 'END' and PHPShopSecurity::TotalClean($_COOKIE['ps_adanalyzer'])) {
        
        $orm_adanalyzer = new PHPShopOrm($GLOBALS['SysValue']['base']['adanalyzer']['adanalyzer_system']);
        $options = $orm_adanalyzer->select();

        $orm = new PHPShopOrm('phpshop_orders');
        $result = $orm->update(array('utm_new' => $_COOKIE['ps_adanalyzer']), array('uid' => "='" . $obj->ouid . "'"));

        // Удаление cookie
        if ($result and (int) $options['status'] === 1)
            setcookie("ps_adanalyzer", '', time() - 10000, "/", $_SERVER['SERVER_NAME'], 0);
    }
}

$addHandler = array
    (
    'send_to_order' => 'send_to_order_adanalyzer_hook'
);
?>