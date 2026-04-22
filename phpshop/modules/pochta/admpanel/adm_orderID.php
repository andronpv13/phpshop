<?php

include_once dirname(__DIR__) . '/class/include.php';

function PochtaSend($data) {

    $order = unserialize($data['orders']);
    $Pochta = new Pochta();

    if($Pochta->isCourier((int) $order['Person']['dostavka_metod']) || $Pochta->isPostOffice((int) $order['Person']['dostavka_metod'])) {
        if ($data['statusi'] != $_POST['statusi_new']) {
            if ($_POST['statusi_new'] === $Pochta->settings->get('status')) {
                $Pochta->send($data);
            }
        }
    }
}

function addPochtaTab($data) {
    global $PHPShopGUI;

    $Pochta = new Pochta();

    $order = unserialize($data['orders']);

    if($Pochta->isCourier((int) $order['Person']['dostavka_metod']) || $Pochta->isPostOffice((int) $order['Person']['dostavka_metod'])) {

       $PHPShopGUI->addJSFiles('../modules/pochta/admpanel/gui/script.gui.js');

       $Tab = $Pochta->buildOrderTab($data);

       $PHPShopGUI->addTab(array("Почта РФ", $Tab));
    }
}

$addHandler = array(
    'actionStart'  => 'addPochtaTab',
    'actionDelete' => false,
    'actionUpdate' => 'PochtaSend'
);
?>