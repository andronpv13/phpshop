<?php

session_start();

$_classPath = "../../../";
include_once($_classPath . "class/obj.class.php");
include_once($_classPath . "modules/pochta/class/include.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("order");

$PHPShopBase->chekAdmin();

$Pochta = new Pochta();

if (isset($_REQUEST['operation']) && strlen($_REQUEST['operation']) > 2) {
    $result = array();
    try {
        switch ($_REQUEST['operation']) {
            case 'changeSettings':
                $Pochta->settings->changeSettings(PHPShopSecurity::TotalClean($_REQUEST['field'], 4), PHPShopSecurity::TotalClean($_REQUEST['value'], 4), (int) $_REQUEST['orderId']);
                $result['success'] = true;
                break;
            case 'changePaymentStatus':
                $order = new PHPShopOrderFunction((int) $_REQUEST['orderId']);
                if (!empty($order->objRow)) {
                    $order->changePaymentStatus((int) $_REQUEST['value']);
                }
                $result['success'] = true;
                break;
            case 'changeAddress':
                $Pochta->changeAddress($_REQUEST);
                $result['success'] = true;
                break;
            case 'send':
                $order = $Pochta->settings->getOrderById((int) $_REQUEST['orderId']);
                $result = $Pochta->send($order);
                break;
        }
    } catch (\Exception $exception) {
        $result = array('success' => false, 'error' => PHPShopString::win_utf8($exception->getMessage()));
    }
} else {
    $result = array('success' => false, 'error' => PHPShopString::win_utf8('Не найден параметр operation'));
}

echo (json_encode($result));
exit;
