<?php

/**
 * Обработчик оповещения о платеже
 */
session_start();
header('Content-Type: text/html; charset=utf-8');

$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
include_once($_classPath . "class/mail.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("lang");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("file");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("payment");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("string");
PHPShopObj::loadClass("security");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
$PHPShopSystem = new PHPShopSystem();
$PHPShopModules = new PHPShopModules($_classPath . "modules/");
$PHPShopModules->checkInstall('idram');


class Payment extends PHPShopPaymentResult {

    private $Idram;
    
    function __construct() {

        $this->payment_name = 'Idram';
        $this->log = true;
        include_once(dirname(__FILE__) . '/../class/Idram.php');

        $this->Idram = new Idram();

        parent::__construct();
    }

    /**
     * Проверка crc
     * @return boolean
     */
    function check() {

        // Pre check
        if(isset($_REQUEST['EDP_PRECHECK']) && isset($_REQUEST['EDP_BILL_NO']) && isset($_REQUEST['EDP_REC_ACCOUNT']) && isset($_REQUEST['EDP_AMOUNT'])) {
            if($_REQUEST['EDP_PRECHECK'] === "YES"){
                if($_REQUEST['EDP_REC_ACCOUNT'] == $this->Idram->options['idram_id'] && PHPShopSecurity::true_order($_REQUEST['EDP_BILL_NO'])) {
                    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
                    $PHPShopOrm->debug = $this->debug;
                    $order = $PHPShopOrm->getOne(['id', 'sum'], ['uid' => "='" . $_REQUEST['EDP_BILL_NO'] . "'"]);
                    if(empty($order['id'])) {
                        $this->Idram->log($_REQUEST, $_REQUEST['EDP_BILL_NO'], 'Заказ не найден', 'Проверка заказа перед оплатой');
                        exit;
                    }
                    echo("OK"); exit;
                }
            }
        }

        // Check
        if(isset($_REQUEST['EDP_PAYER_ACCOUNT']) && isset($_REQUEST['EDP_BILL_NO']) && isset($_REQUEST['EDP_REC_ACCOUNT']) && isset($_REQUEST['EDP_AMOUNT'])&& isset($_REQUEST['EDP_TRANS_ID']) && isset($_REQUEST['EDP_CHECKSUM'])) {
            $txtToHash = $this->Idram->options['idram_id'] . ":" . $_REQUEST['EDP_AMOUNT'] . ":" . $this->Idram->options['secret_key'] . ":" .$_REQUEST['EDP_BILL_NO'] . ":" .$_REQUEST['EDP_PAYER_ACCOUNT'] . ":" . $_REQUEST['EDP_TRANS_ID'] . ":" . $_REQUEST['EDP_TRANS_DATE'];

            return strtoupper($_REQUEST['EDP_CHECKSUM']) === strtoupper(md5($txtToHash));
        }

        return false;
    }

    /**
     * Обновление данных по заказу
     */
    function updateorder() {

        if (!$this->check()) {
            $this->Idram->log($_REQUEST, $_REQUEST['EDP_BILL_NO'], 'Ошибка проверки hash ключа', 'Уведомление о платеже');
            exit();
        }

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $PHPShopOrm->debug = $this->debug;
        $row = $PHPShopOrm->select(['*'], ['uid' => "='" . $_REQUEST['EDP_BILL_NO'] . "'"]);

        if(empty($row))
            $this->Idram->log($_REQUEST, $_REQUEST['EDP_BILL_NO'], 'Заказ ' . $_REQUEST['EDP_BILL_NO'] . ' не найден', 'Уведомление о платеже');

        $this->Idram->log($_REQUEST, $_REQUEST['EDP_BILL_NO'], 'Заказ оплачен', 'Уведомление о платеже');
        $this->setPaymentLog($row['uid'], $row['sum']);

        $PHPShopOrm->debug = $this->debug;
        (new PHPShopOrderFunction((int) $row['id']))->changeStatus((int) $this->set_order_status_101(), $row['statusi']);

        $this->ofd($row);

        echo("OK"); exit;
    }

    private function setPaymentLog($id, $sum)
    {
        $uid = explode("-", $id);
        $PHPShopOrmPayment = new PHPShopOrm($GLOBALS['SysValue']['base']['payment']);
        $PHPShopOrmPayment->insert(['uid_new' => $uid[0] . $uid[1], 'name_new' => 'Idram',
            'sum_new' => $sum, 'datas_new' => time()]);
    }
}

new Payment();