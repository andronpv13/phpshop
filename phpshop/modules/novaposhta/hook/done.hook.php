<?php

/**
 * Добавление данных в заказ, регистрация заказа в службе доставки
 * param obj $obj
 * param array $row
 * param string $rout
 */
function send_to_order_novaposhta_hook($obj, $row, $route)
{
    include_once 'phpshop/modules/novaposhta/class/NovaPoshta.php';
    $NovaPoshta = new NovaPoshta();


    if(in_array($_POST['d'], explode(",", $NovaPoshta->option['delivery_id']))) {
        if(!empty($_POST['novaposhtaDeliveryCost'])) {
            if ($route === 'START') {
                $obj->delivery_mod = number_format($_POST['novaposhtaDeliveryCost'], 0, '.', '');
                $obj->manager_comment = $_POST['novaposhtaInfo'];
                $obj->set('deliveryInfo', $_POST['novaposhtaInfo']);

                $_POST['np_order_data_new'] = serialize(array(
                    'pvz'                     => $_POST['novaposhtaPvz'],
                    'region'                  => $_POST['novaposhtaCityRegion'],
                    'recipient_city_ref'      => $_POST['recipientCityRef'],
                    'recipient_warehouse_ref' => $_POST['recipientWarehouseRef']
                ));
            }

            if ($route === 'MIDDLE' and $NovaPoshta->option['status'] == 0) {
                $PHPShopCart = new PHPShopCart();

                if(empty($_POST['fio_new']))
                    $name = $_POST['name_new'];
                else
                    $name = $_POST['fio_new'];

                $NovaPoshta->order->setOrder($_POST['ouid'], $PHPShopCart->getWeight() / 1000, $PHPShopCart->getSum());
                $NovaPoshta->order->setSender();
                $NovaPoshta->order->setRecipient(array('pvz' => $_POST['novaposhtaPvz'], 'region' => $_POST['novaposhtaCityRegion']), $_POST['city_new'], $name, str_replace(array('(', ')', ' ', '-'), '', $_POST['tel_new']));
                $request = $NovaPoshta->order->send();

                if($request['success']) {
                    $_POST['np_order_data_new'] = '';
                    $_POST['tracking_new'] = $request['data'][0]['IntDocNumber'];
                }
            }
        }
    }
}

$addHandler = array
    (
    'send_to_order' => 'send_to_order_novaposhta_hook'
);
?>