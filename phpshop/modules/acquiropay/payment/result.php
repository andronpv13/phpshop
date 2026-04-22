<?php

/**
 * Обработчик оповещения о платеже AcquiroPay
 */
session_start();

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
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("string");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopSystem = new PHPShopSystem();
$PHPShopModules = new PHPShopModules($_classPath . "modules/");
$PHPShopModules->checkInstall('acquiropay');

class AcquiroPayPayment extends PHPShopPaymentResult {

    function __construct() {

//        $this->log = true;
        $this->option();

        parent::__construct();
    }

    /**
     * Настройка модуля 
     */
    function option() {
        $this->payment_name = 'AcquiroPay';
        include_once('../hook/mod_option.hook.php');
        $options = new PHPShopAcquiroPayArray();
        $this->option = $options->getArray();
    }

    /**
     * Удачное завершение поверки 
     */
    function done() {
        echo "ok";
        $this->log();
    }

    /**
     * Ошибка 
     */
    function error($type = 1) {
        if ($type == 1)
            echo "bad order num\n";
        else
            echo "bad cost\n";
        $this->log();
    }

    /**
     * Проверка подписи
     * @return boolean 
     */
    function check() {

        $this->crc = $_REQUEST['sign'];
        $this->my_crc = md5(
            (int)$this->option['merchant_id']
            . $_REQUEST['payment_id']
            . $_REQUEST['status']
            . $_REQUEST['cf']
            . $_REQUEST['cf2']
            . $_REQUEST['cf3']
            . trim($this->option['merchant_skey'])
        );
        $this->inv_id = str_replace('-', '', $_REQUEST['cf']);
        $this->inv_id = trim($this->inv_id);

        $this->out_summ = (float)$_REQUEST['amount'];

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $data = $PHPShopOrm->select(array('*'), array('uid' => '="' . $this->true_num($this->inv_id) . '"'), false, array('limit' => 1));

        if (!is_array($data)) {
            return false;
        }
        if ($_REQUEST['status'] !== 'OK') {
            return false;
        }
//        if (number_format($data['sum'], 2, '.', '') !== number_format($this->out_summ, 2, '.', '')) {
//            return false;
//        }
        if ($this->crc != $this->my_crc) {
            return false;
        }

        (new PHPShopOrderFunction((int) $data['id']))->changeStatus((int) $this->set_order_status_101(), $data['statusi']);

        return true;
    }
}

new AcquiroPayPayment();

