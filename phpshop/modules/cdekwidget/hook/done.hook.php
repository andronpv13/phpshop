<?php

/**
 * Добавление данных в заказ, регистрация заказа в службе доставки
 * param obj $obj
 * param array $row
 * param string $rout
 */
function send_to_order_cdekwidget_hook($obj, $row, $rout)
{
    include_once 'phpshop/modules/cdekwidget/class/CDEKWidget.php';
    $CDEKWidget = new CDEKWidget();


    if(in_array($_POST['d'], @explode(",", $CDEKWidget->option['delivery_id']))) {
        if(!empty($_POST['cdekInfo'])) {
            if ($rout === 'START') {
                $obj->delivery_mod = number_format($_POST['cdekSum'], 0, '.', '');
                $obj->set('deliveryInfo', $_POST['cdekInfo']);
                $obj->manager_comment = $_POST['cdekInfo'];
                $_POST['cdek_order_data_new'] = serialize(array(
                    'type'          => $_POST['cdek_type'],
                    'city_id'       => $_POST['cdek_city_id'],
                    'delivery_info' => $_POST['cdekInfo'],
                    'cdek_pvz_id'   => $_POST['cdek_pvz_id'],
                    'tariff'        => $_POST['cdek_tariff'],
                    'status'        => CDEKWidget::STATUS_ORDER_PREPARED,
                    'status_text'   => __('Ожидает отправки в СДЭК')
                ));
            }

            if ($rout === 'END' and $CDEKWidget->option['status'] == 0) {
                $orm = new PHPShopOrm('phpshop_orders');
                $order = $orm->getOne(array('*'), array('uid' => "='" . $obj->ouid . "'"));

                if(is_array($order)) {
                    $CDEKWidget->send($order);
                }
            }
        }
    }
}

$addHandler = array ('send_to_order' => 'send_to_order_cdekwidget_hook');
?>