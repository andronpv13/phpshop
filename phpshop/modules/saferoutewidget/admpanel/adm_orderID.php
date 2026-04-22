<?php

include_once dirname(__DIR__) . '/class/Saferoute.php';

function saferoutewidgetSend($data) {

    if ($data['statusi'] != $_POST['statusi_new'] or !empty($_POST['saferoute_send_now'])) {
        $Saferoute = new Saferoute();

        if ($_POST['statusi_new'] == $Saferoute->options['status'] or !empty($_POST['saferoute_send_now'])) {

            $order = unserialize($data['orders']);
            $PHPShopPayment = new PHPShopPayment($order['Person']['order_metod']);

            $params = array(
                'id' => $data['saferoute_token'],
                'cmsId' => $data['uid'],
                'paymentMethod' => PHPShopString::win_utf8($PHPShopPayment->getName())
            );

            $result = json_decode($Saferoute->sendOrder($params), true); //print_r($result);

            if($result['status'] == 200)
                $_POST['saferoute_token_new'] = '';
        }
    }
	
}

function addSaferoutewidgetTab($data) {
    global $PHPShopGUI;


    if (!empty($data['saferoute_token'])) {
        $Tab1 = $PHPShopGUI->setField('Синхронизация заказа', $PHPShopGUI->setCheckbox('saferoute_send_now', 1, 'Отправить заказ в Saferoute.ru сейчас', 0));
        $PHPShopGUI->addTab(array("Saferoute", $Tab1, true));
    }
}

$addHandler = array(
    'actionStart' => 'addSaferoutewidgetTab',
    'actionDelete' => false,
    'actionUpdate' => 'saferoutewidgetSend'
);
?>