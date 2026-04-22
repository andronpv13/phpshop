<?php

session_start();

$_classPath = "../../../";
include_once($_classPath . "class/obj.class.php");
include_once($_classPath . "modules/yandexdelivery/class/include.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
PHPShopObj::loadClass('modules');
PHPShopObj::loadClass('orm');
PHPShopObj::loadClass('system');
PHPShopObj::loadClass('security');
PHPShopObj::loadClass('order');
PHPShopObj::loadClass("lang");
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'admin'));

$PHPShopBase->chekAdmin();

$YandexDelivery = new YandexDelivery();

if (isset($_REQUEST['operation']) && strlen($_REQUEST['operation']) > 2) {
    $result = [];
    try {
        switch ($_REQUEST['operation']) {
             case 'changeAddress':
                $YandexDelivery->changeAddress($_REQUEST);
                $result['success'] = true;
                break;
             case 'changeWarehouse':
                $PHPShopOrm = new PHPShopOrm('phpshop_modules_yandexdelivery_system');
                $PHPShopOrm->update(['warehouse_id_new'=>$_REQUEST['value']]);
                $result['success'] = true;
                break;
            case 'changePaymentStatus':
                $order = new PHPShopOrderFunction((int) $_REQUEST['orderId']);
                if (!empty($order->objRow)) {
                    $order->changePaymentStatus((int) $_REQUEST['value']);
                }
                $result['success'] = true;
                break;
            case 'send':
                $order = new PHPShopOrderFunction((int) $_REQUEST['orderId']);
                if (!empty($order->objRow)) {

                    $tracking = $YandexDelivery->setDataFromOrderEdit($order->objRow);
                    if ($tracking) {
                        (new PHPShopOrm($GLOBALS['SysValue']['base']['orders']))->update(['tracking_new'=>$tracking],['id'=>"=".(int) $_REQUEST['orderId']]);
                        $result['success'] = true;
                    }
                    else  $result = ['success' => false, 'error' =>  PHPShopString::win_utf8('Ошибка передачи заказа')];
                }
                
                break;
        }

    } catch (\Exception $exception) {
        $result = ['success' => false, 'error' => PHPShopString::win_utf8($exception->getMessage())];
    }
} else {
    $result = ['success' => false, 'error' => PHPShopString::win_utf8('Не найден параметр operation')];
}

echo (json_encode($result));
exit;
