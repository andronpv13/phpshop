<?php

include_once dirname(__DIR__) . '/class/include.php';

function send_to_order_pochta_hook($obj, $row, $route) {

    $Pochta = new Pochta();

    if($Pochta->isCourier((int) $_POST['d']) || $Pochta->isPostOffice((int) $_POST['d'])) {

        if ($route === 'START') {
            $obj->delivery_mod = round($_POST['pochta_cost'], $Pochta->settings->format);
            $obj->set('deliveryInfo', $_POST['cdekInfo']);

            $_POST['pochta_settings_new'] = serialize([
                'mail-type'     => $_POST['pochta_mail_type'],
                'delivery_info' => $_POST['pochta_delivery_info'],
                'address'       => $_POST['pochta_address'] === 'null' ?  null : $_POST['pochta_address'], // да, иногда с виджета почты РФ возвращается строка null.
                'pvz_type'      => $_POST['pochta_pvz_type'],
                'pvz_index'     => $_POST['pochta_index']
            ]);

            // Заполнение полей адреса доставки данными с виджета, если они пустые.
            if(empty($_POST['state_new'])) {
                $_POST['state_new'] = $_POST['pochta_region'];
            }
            if(empty($_POST['city_new'])) {
                $_POST['city_new']  = $_POST['pochta_city'];
            }
            if(empty($_POST['index_new'])) {
                $_POST['index_new'] = $_POST['pochta_index'];
            }
        }

        if ($route === 'END' && (int) $Pochta->settings->get('status') === 0) {
            $orm = new PHPShopOrm('phpshop_orders');
            $order = $orm->getOne(array('*'), array('uid' => "='" . $obj->ouid . "'"));
            if(is_array($order)) {
                $Pochta->send($order);
            }
        }
    }
}

$addHandler = ['send_to_order' => 'send_to_order_pochta_hook'];