<?php

session_start();
$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
include_once($_classPath . "class/mail.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("lang");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("file");
PHPShopObj::loadClass("xml");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("payment");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("string");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopSystem = new PHPShopSystem();
$PHPShopModules = new PHPShopModules($_classPath . "modules/");
$PHPShopModules->checkInstall('mandarinhosted');

class MandarinHostedPayment extends PHPShopPaymentResult {

    function __construct() {
        $this->option();
        parent::__construct();
    }

    function option() {
        $this->payment_name = 'MandarinHosted';
        include_once('../hook/mod_option.hook.php');
        $PHPShopMandarinHostedArray = new PHPShopMandarinHostedArray();
        $this->option = $PHPShopMandarinHostedArray->getArray();
    }

    function done() {
        echo 'OK';
        $order = (new PHPShopOrm($GLOBALS['SysValue']['base']['orders']))
                ->getOne(['id','uid','statusi'], ['uid' => sprintf('="%s"', $this->true_num($this->inv_id))]);
        (new PHPShopOrderFunction((int) $order['id']))->changeStatus((int) $this->set_order_status_101(), $order['statusi']);


        $PHPShopSystem = new PHPShopSystem();
        $content = 'Заказ №' . $order['uid'] . ' оплачен платежной системой Mandarin';
        new PHPShopMail($PHPShopSystem->getParam('adminmail2'), $PHPShopSystem->getParam('adminmail2'), "Оплата заказа №" . $order['uid'], $content);

        $this->log();
    }

    function error($type = 1) {
        if ($type == 1)
            echo 'bad order num';
        else
            echo 'bad cost';

        $this->log();
    }

    function check() {
        $req = $_POST;
        
        if($req['status'] != 'success')
            return false;
        
        $secret = $this->option['merchant_skey'];
        $sign = $req['sign'];
        unset($req['sign']);
        $to_hash = '';
        if (!is_null($req) && is_array($req)) {
            ksort($req);
            $to_hash = implode('-', $req);
        }

        $to_hash = $to_hash . '-' . $secret;
        $calculated_sign = hash('sha256', $to_hash);

        if ($calculated_sign === $sign) {
            $this->out_summ = $req['price'];
            $this->inv_id = $req['orderId'];
            $this->crc = 1;
            $this->my_crc = 1;

            return true;
        }

        return false;
    }

}

header('Content-Type: text/html; charset=utf-8');
new MandarinHostedPayment();
