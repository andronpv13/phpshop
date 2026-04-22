<?php

function novaposhtaSend($data) {
    global $_classPath;

    if ($data['statusi'] != $_POST['statusi_new'] or !empty($_POST['novaposhta_send_now'])) {

        include_once($_classPath . 'modules/novaposhta/class/NovaPoshta.php');
        $NovaPoshta = new NovaPoshta();

        if(!empty($data['np_order_data'])) {
            if ($_POST['statusi_new'] == $NovaPoshta->option['status'] or !empty($_POST['novaposhta_send_now'])) {
                $order = unserialize($data['orders']);
                if(empty($data['fio']))
                    $name = $order['Person']['name_person'];
                else
                    $name = $data['fio'];

                $NovaPoshta->order->setOrder($data['uid'], $order['Cart']['weight'] / 1000, $order['Cart']['sum']);
                $NovaPoshta->order->setSender();
                $NovaPoshta->order->setRecipient(unserialize($data['np_order_data']), $data['city'], $name, str_replace(array('(', ')', ' ', '-'), '', $data['tel']));
                $request = $NovaPoshta->order->send();

                if($request['success']) {
                    $_POST['np_order_data_new'] = '';
                    $_POST['tracking_new'] = $request['data'][0]['IntDocNumber'];
                }
            }
        }
    }
}

function addNovaposhtaTab($data) {
    global $PHPShopGUI, $_classPath;

    include_once($_classPath . 'modules/novaposhta/class/NovaPoshta.php');
    $NovaPoshta = new NovaPoshta();

    $order = unserialize($data['orders']);

    if(in_array($order['Person']['dostavka_metod'], explode(",", $NovaPoshta->option['delivery_id']))) {
        if(!empty($data['np_order_data'])) {
            $Tab1 = $PHPShopGUI->setField('Синхронизация заказа', $PHPShopGUI->setCheckbox('novaposhta_send_now', 1, 'Создать экспресс-накладную', 0));
            $PHPShopGUI->addTab(array("Нова пошта", $Tab1, true));
        }
    }
}

$addHandler = array(
    'actionStart'  => 'addNovaposhtaTab',
    'actionDelete' => false,
    'actionUpdate' => 'novaposhtaSend'
);
?>