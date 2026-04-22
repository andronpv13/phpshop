<?php
//error_reporting(E_ALL);
//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);

/**
 * Метод обрабатывающий создание заказа.
 */
function send_to_order_mod_intellectmoney_hook($obj, $value, $rout) {
    include_once 'phpshop/modules/intellectmoney/class/intellectmoney.done.class.php';
    require_once 'phpshop/modules/intellectmoney/class/intellectmoney.logger.class.php';
    $handler = new IntellectMoneyPhpShopDoneHandler();
    if (!$handler->CanHandleOrder($value['order_metod'], $rout)) {
        return;
    }

    if ($rout == 'MIDDLE') {
        $obj->cart_clean_enabled = false;
    }

    if ($rout == 'END') {
        $payment = $handler->ReadOrderData($obj, $value);
        if ($payment == null)
        {
            IMLogger::Log('Can not process order.');
            return true;
        }
        $obj->write();
        IMLogger::Good('Order inserted into database: ' . $obj->ouid);
        $obj->PHPShopCart->clean();
        IMLogger::Log('Cart cleared, redirecting to IntellectMoney.');
        $handler->RenderForm($payment, $obj);
        return true;
    }
}

$addHandler = [
    'send_to_order' => 'send_to_order_mod_intellectmoney_hook'
];