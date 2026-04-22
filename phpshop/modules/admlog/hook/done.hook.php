<?php

/*
 * Запись в журнал
 */

function admlog_send_to_order_hook($obj, $data, $rout) {

    if ($rout === 'END') {

        $orm = new PHPShopOrm('phpshop_orders');
        $order = $orm->getOne(array('*'), array('uid' => "='" . $obj->ouid . "'"));

        $PHPShopOrm = new PHPShopOrm('phpshop_modules_admlog_log');
        $TitlePage = __('Новый заказ') . ' №' . $obj->ouid;

        $log = array(
            'date_new' => date('U'),
            'user_new' => $_POST['mail'],
            'ip_new' => $_SERVER['REMOTE_ADDR'],
            'file_new' => 'order',
            'title_new' => $TitlePage,
            'content_new' => serialize($order)
        );

        $PHPShopOrm->insert($log);
    }
}

$addHandler = array('send_to_order' => 'admlog_send_to_order_hook');
