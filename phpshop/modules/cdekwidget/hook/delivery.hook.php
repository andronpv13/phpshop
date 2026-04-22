<?php

/**
 * Внедрение js функции
 *
 * param object $obj
 * param array $data
 */
function cdekwidget_delivery_hook($obj, $data) {

    $_RESULT = $data[0];
    $xid = $data[1];

    // API
    include_once '../modules/cdekwidget/class/CDEKWidget.php';
    $CDEKWidget = new CDEKWidget();

    if (in_array($xid, @explode(",",$CDEKWidget->option['delivery_id']))) {
        
        // Оплата в ПВЗ
        $PHPShopDelivery = new PHPShopDelivery($xid);
        $paid = (int)$PHPShopDelivery->getParam('cdek_paid');
        
        if(empty($paid))
          $_SESSION['cdek_delivery_paid']=1;
        else  $_SESSION['cdek_delivery_paid']=0;

        $hook['dellist'] = $_RESULT['dellist'];
        $hook['hook'] = 'cdekwidgetStart();';
        $hook['delivery'] = $_RESULT['delivery'];
        $hook['total'] = $_RESULT['total'];
        $hook['adresList'] = $_RESULT['adresList'];
        $hook['free_delivery'] = $_RESULT['free_delivery'];
        $hook['success'] = 1;
        $hook['paid']=$_SESSION['cdek_delivery_paid'];

        return $hook;
    }
}

$addHandler = array('delivery' => 'cdekwidget_delivery_hook');
?>
