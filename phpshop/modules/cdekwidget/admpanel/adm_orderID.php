<?php

function cdekwidgetSend($data) {
    global $_classPath;

    $order = unserialize($data['orders']);

    include_once($_classPath . 'modules/cdekwidget/class/CDEKWidget.php');
    $CDEKWidget = new CDEKWidget();

    if(in_array($order['Person']['dostavka_metod'], explode(",", $CDEKWidget->option['delivery_id']))) {
        if ($data['statusi'] != $_POST['statusi_new']) {

            include_once($_classPath . 'modules/cdekwidget/class/CDEKWidget.php');
            $CDEKWidget = new CDEKWidget();

            if ($_POST['statusi_new'] == $CDEKWidget->option['status']) {
                $CDEKWidget->send($data);
            }
        }
    }
}

function addCdekTab($data) {
    global $PHPShopGUI, $_classPath;

    include_once($_classPath . 'modules/cdekwidget/class/CDEKWidget.php');
    $CDEKWidget = new CDEKWidget();

    $order = unserialize($data['orders']);

    if(in_array($order['Person']['dostavka_metod'], explode(",", $CDEKWidget->option['delivery_id']))) {

        $PHPShopGUI->addJSFiles('../modules/cdekwidget/admpanel/gui/script.gui.js');

        $data = $CDEKWidget->updateOrderStatus($data);

        $Tab1 = $CDEKWidget->buildInfoTable($data);
        $PHPShopGUI->addTab(array("СДЭК", $Tab1, false, 101));

        // Обновление трекинга
        if((isset($data['tracking']) and empty($data['tracking'])) && !empty($data['cdek_order_data'])) {
            $CDEKWidget->checkTracking($data);
        }    
    }
}

$addHandler = array(
    'actionStart'  => 'addCdekTab',
    'actionDelete' => false,
    'actionUpdate' => 'cdekwidgetSend'
);
?>