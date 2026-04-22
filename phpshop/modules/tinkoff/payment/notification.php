<?php

session_start();
$_classPath = "../../../";
include $_classPath . "class/obj.class.php";
include_once($_classPath . "class/mail.class.php");
PHPShopObj::loadClass("payment");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("lang");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("file");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("string");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
$PHPShopModules = new PHPShopModules($_classPath . "modules/");
$PHPShopModules->checkInstall('tinkoff');
$PHPShopSystem = new PHPShopSystem();
include_once dirname(__FILE__) . '/../class/tinkoff.class.php';

class TinkoffPayment extends PHPShopPaymentResult {

    function __construct() {
        $this->option();
        parent::__construct();
    }

    function option() {
        $this->payment_name = 'Tinkoff';
        $tinkoff = new Tinkoff();
        $this->option = $tinkoff->settings;
    }

    function updateorder() {
        global $PHPShopOrm;
        
        $request = json_decode(file_get_contents("php://input"));
        $request->Success = $request->Success ? 'true' : 'false';
        $requestData = array();

        foreach ($request as $key => $item) {
            $requestData[$key] = $item;
        }

        if ($requestData['Token'] == $this->getToken($requestData)) {
            
            // Нормализация номера заказа 416513-310-XXX
            $order_array = explode('-',$requestData['OrderId']);
            if(is_array($order_array) and count($order_array)>2)
                $OrderId = $order_array[0].'-'.$order_array[1];
            else $OrderId=$requestData['OrderId'];
			$requestData['OrderId'] = $OrderId;
            
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
            $order = $PHPShopOrm->select(array('*'), array('uid' => '="' . $requestData['OrderId'] . '"'), false, array('limit' => 1));

            if ($order && (float) $requestData['Amount'] >= (float) $order['sum']) {
                $this->inv_id = str_replace("-", '', $requestData['OrderId']);
                $this->out_summ = $requestData['Amount'];

                // Подтвержден
                if ($requestData['Status'] == 'CONFIRMED' and $order['paid'] != 1) {
                    (new PHPShopOrderFunction((int) $order['id']))->changeStatus((int) $this->option['status_confirmed'], $order['statusi']);
                    $PHPShopOrm->update(['paid_new' => 1], ['uid' => '="' . $order['uid'] . '"']);
                    $status = $requestData['Status'];

                    /*
                      $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
                      $user = $PHPShopOrm->select(array('*'), array('id' => '="' . $order['user'] . '"'), false, array('limit' => 1));

                      $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['order_status']);
                      $status = $PHPShopOrm->select(array('*'), array('id' => '="' . $order['statusi'] . '"'), false, array('limit' => 1));

                      $this->sendEmail($order['uid'], $status['name'], $user['mail']);
                      $this->sendEmail($order['uid'], $status['name']);

                     */
                }
                // Зарезервирован
                if ($requestData['Status'] == 'AUTHORIZED' and $order['paid'] != 1) {
                    (new PHPShopOrderFunction((int) $order['id']))->changeStatus((int) $this->set_order_status_101(), $order['statusi']);
                    $status = $requestData['Status'];
                }

                // Отмена
                elseif ($requestData['Status'] == 'CANCELED' or $requestData['Status'] == 'REVERSED' or $requestData['Status'] == 'REFUNDED') {
                    (new PHPShopOrderFunction((int) $order['id']))->changeStatus((int) 1, $order['statusi']);
                    $PHPShopOrm->update(['paid_new' => 0], ['uid' => '="' . $order['uid'] . '"']);
                    $status = $requestData['Status'];
                }
            }

            $this->tinkoff->log(['request' => $requestData], $order['uid'], $status, $requestData['Status']);
            header("HTTP/1.0 200");
            die('OK');
        }
    }

    function getToken($data) {
        $data['Password'] = $this->option['secret_key'];
        ksort($data);

        if (isset($data['Token'])) {
            unset($data['Token']);
        }

        $values = implode('', array_values($data));

        return hash('sha256', $values);
    }

    function sendEmail($orderId, $status, $to = null) {
        global $PHPShopSystem;

        if (!$to) {
            $to = $PHPShopSystem->getParam('adminmail2');
        }

        $PHPShopMail = new PHPShopMail($to, $PHPShopSystem->getParam('adminmail2'), 'Оплата заказа #' . $orderId, '', false, true);
        $PHPShopMail->sendMailNow('Заказ #' . $orderId . ' оплачен, текущий статус заказа ' . $status . '.');
    }

}

new TinkoffPayment();
