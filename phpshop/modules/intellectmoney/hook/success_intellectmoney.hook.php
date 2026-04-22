<?php

//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);

/**
 * 
 */
function success_mod_intellectmoney_hook($obj, $value){
    include_once 'phpshop/modules/intellectmoney/class/intellectmoney.success.class.php';
    require_once 'phpshop/modules/intellectmoney/class/intellectmoney.logger.class.php';
    $module_settings = new DataBaseHelper('phpshop_modules_intellectmoney_settings');

    $handler = new IntellectMoneyPhpShopSuccessHandler();
    $result = $handler->HandleNotification($_REQUEST);
    $response = $result->processingResponse();

    if ($response->changeStatusResult) {
        $obj->inv_id = $_POST['orderId'];
        $obj->out_summ = $_POST['recipientAmount'];
        $obj->order_metod = $module_settings->getSetting('module_id');
        $obj->write_payment();
        $handler->UpdateOrderState($_POST['orderId'], $response->statusCMS);
    } else {
        IMLogger::Error('Can not change order status. Error: ' . $result->getMessage());
    }
    echo $result->getMessage();
    exit;
}

$addHandler = [
    'index' => 'success_mod_intellectmoney_hook'
];