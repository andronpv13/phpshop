<?php

/**
 * Обработчик оповещения о платеже PayPal
 * @author PHPShop Software
 * @version 1.0
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
$PHPShopModules->checkInstall('paypal');

class Payment extends PHPShopPaymentResult {

    function Payment() {
        $this->debug = false;
        $this->option();
        parent::__construct();
    }

    /**
     * Настройка модуля 
     */
    function option() {
        $this->payment_name = 'PayPal';
        $this->log = false;
        include_once(dirname(__FILE__) . '/../hook/mod_option.hook.php');
        $PHPShopPayPalArray = new PHPShopPayPalArray();
        $this->option = $PHPShopPayPalArray->getArray();
    }

    /**
     * Проверка подписи
     * @return boolean 
     */
    function check() {
        echo "OK\n";

        $option = $this->option;

        // Библиотека
        include_once('../class/paypal.class.php');

        $paypal = new Paypal();
        $paypal->_credentials['USER'] = $option['merchant_id'];
        $paypal->_credentials['PWD'] = $option['merchant_pwd'];
        $paypal->_credentials['SIGNATURE'] = $option['merchant_sig'];

        // Режим песочницы
        if ($option['sandbox'] == 2) {
            $paypal->_endPoint = 'https://www.paypal.com/cgi-bin/webscr?cmd=_notify-validate';
        }
        else
            $paypal->_endPoint = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_notify-validate';

        $response = $paypal->request('IPN', $_POST);

        // Лог
        $paypal->log(array('Запрос' => $_POST, 'Ответ' => $response), $_POST['mc_gross'], null, 'IPN');

        if (isset($response["VERIFIED"])) {  // Response contains VERIFIED - process notification
            $this->out_summ = $_POST['mc_gross'];
            $this->inv_id = $this->true_num($_POST['invoice']);
            $this->crc = 1;
            $this->my_crc = 1;

            return true;

            // Authentication protocol is complete - OK to process notification contents
            // Possible processing steps for a payment include the following:
            // Check that the payment_status is Completed
            // Check that txn_id has not been previously processed
            // Check that receiver_email is your Primary PayPal email
            // Check that payment_amount/payment_currency are correct
            // Process payment
        } else if (isset($response["INVALID"])) { //Response contains INVALID - reject notification
            // Authentication protocol is complete - begin error handling
            // Send an email announcing the IPN message is INVALID
        }
    }

}

new Payment();
?>