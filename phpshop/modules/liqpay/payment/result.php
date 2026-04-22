<?php

/**
 * Обработчик оповещения о платеже Ligpay
 */
session_start();

$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("file");
PHPShopObj::loadClass("xml");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("payment");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("system");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");

$PHPShopModules = new PHPShopModules($_classPath . "modules/");
$PHPShopModules->checkInstall('liqpay');

class LiqPayment extends PHPShopPaymentResult {

    function LiqPayment() {

        $this->option();
        parent::__construct();
    }

    /**
     * Настройка модуля 
     */
    function option() {
        $this->payment_name = 'Liqpay';
        include_once(dirname(__FILE__) . '/../hook/mod_option.hook.php');
        $PHPShopLiqpayArray = new PHPShopLiqpayArray();
        $this->option = $PHPShopLiqpayArray->getArray();
    }

    /**
     * Проверка подписи
     * @return boolean 
     */
    function check() {
        $xml = base64_decode($_REQUEST['operation_xml']);
        $this->result_var = readDatabase($xml, 'response', false);
        $this->result_var = $this->result_var[0];

        // Платеж выполнен
        if ($this->result_var['status'] == 'success') {

            $this->out_summ = $this->result_var['amount'];
            $this->inv_id = $this->true_num($this->result_var['order_id']);
            $this->crc = $_REQUEST['signature'];
            $this->my_crc = base64_encode(sha1($this->option['merchant_sig'] . base64_decode($_REQUEST['operation_xml']) . $this->option['merchant_sig'], 1));

            return true;
        }
    }

}

new LiqPayment();
?>