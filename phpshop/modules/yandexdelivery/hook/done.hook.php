<?php

include_once dirname(__DIR__) . '/class/include.php';

function send_to_order_yandexdelivery_hook($obj, $row, $rout) {
    $YandexDelivery = new YandexDelivery();

    if ($YandexDelivery->isYandexDeliveryMethod((int) $_POST['d'])) {
        if (!empty($_POST['yadelivery_sum'])) {


            if ($rout == 'START') {
                $obj->delivery_mod = number_format($_POST['yadelivery_sum'], 0, '.', '');
                $obj->manager_comment = $_POST['yadelivery_info'];
                $obj->set('deliveryInfo', $_POST['yadelivery_info']);
                $_POST['yadelivery_order_data_new'] = serialize(array(
                    'type' => $_POST['yadelivery_type'],
                    'city_id' => $_POST['yadelivery_city_id'],
                    'pvz_id' => $_POST['yadelivery_pvz_id'],
                    'tariff' => $_POST['yadelivery_tariff'],
                    'address' => $_POST['yadelivery_address'],
                    'status' => YandexDelivery::STATUS_ORDER_PREPARED,
                    'status_text' => __('ќжидает отправки'),
                    'delivery_info' => $_POST['yadelivery_info']
                ));

                if (empty($_POST['fio_new'])) {
                    $_POST['fio_new'] = $_POST['name_new'];
                }
            }

            if ($rout === 'END' and $YandexDelivery->options['status'] == 0) {
                $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
                $order = $orm->getOne(array('*'), array('uid' => "='" . $obj->ouid . "'"));

                if (is_array($order)) {
                    $tracking = $YandexDelivery->setDataFromOrderEdit($order);

                    if ($tracking) {
                        (new PHPShopOrm($GLOBALS['SysValue']['base']['orders']))->update(['tracking_new' => $tracking], ['uid' => "='" . $obj->ouid . "'"]);
                    }
                }
            }
        }
    }
}

$addHandler = ['send_to_order' => 'send_to_order_yandexdelivery_hook'];