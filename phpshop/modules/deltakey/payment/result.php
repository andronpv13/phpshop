<?php

/**
 * Обработчик оповещения о платеже DeltaKey
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
$PHPShopModules->checkInstall('deltakey');

class DeltaKeyPayment extends PHPShopPaymentResult {

    function hmac($key, $data) {
        // Вычисление подписи методом HMAC
        $b = 64; // byte length for md5
        $key = mb_convert_encoding($key, 'UTF-8', mb_detect_encoding($key));
        if (strlen($key) > $b) {
            $key = pack("H*", md5($key));
        }

        $key = str_pad($key, $b, chr(0x00));
        $k_ipad = $key ^ str_pad(null, $b, chr(0x36));
        $k_opad = $key ^ str_pad(null, $b, chr(0x5c));

        return md5($k_opad . pack("H*", md5($k_ipad . $data)));
    }

    function DeltaKeyPayment() {

        $this->option();
        parent::__construct();
    }

    /**
     * Настройка модуля 
     */
    function option() {
        $this->payment_name = 'DeltaKey';
        include_once(dirname(__FILE__) . '/../hook/mod_option.hook.php');
        $PHPShopDeltaKeyArray = new PHPShopDeltaKeyArray();
        $this->option = $PHPShopDeltaKeyArray->getArray();
    }

    /**
     * Проверка подписи
     * @return boolean 
     */
    function check() {
        $shop_id = $this->option['merchant_id'];    //ID магазина
        $shop_key = $this->option['merchant_key'];    //Рублевый счет
        $shop_skey = $this->option['merchant_skey'];    //Секретный ключ
        $sum = $_REQUEST['sum'];
        $order_id = $_REQUEST['ext_transact'];

        if ($_REQUEST['check'] == "1") {
            $param = $_REQUEST['ext_transact'] . $_REQUEST['num_shop'] . $_REQUEST['keyt_shop'] . $_REQUEST['identified'] . $_REQUEST['sum'] . $_REQUEST['comment'];
        } else {
            $param = $_REQUEST['transact'] . $_REQUEST['status'] . $_REQUEST['result'] . $_REQUEST['ext_transact'] . $_REQUEST['num_shop'] . $_REQUEST['keyt_shop'] . '1' . $_REQUEST['sum'] . $_REQUEST['comment'];
        }
        $sign = $this->hmac($shop_skey, $param);
        if ($_REQUEST['sign'] != $sign) {
            if ($_REQUEST['check'] == "1") {
                echo "bad sign\n";
                exit();
            } else {
                header('location:http://' . $_SERVER['SERVER_NAME'] . '/fail/');
                exit;
            }
        } else {
            if ($_REQUEST['check'] == "1") {
                die('ok');
            } else {
                if ($_REQUEST['result'] == "0") {
                    $this->out_summ = $_REQUEST['sum'];
                    $this->inv_id = $order_id;
                    $this->crc = $_REQUEST['sign'];
                    $this->my_crc = $sign;
                    return true;
                } else {
                    header('location:http://' . $_SERVER['SERVER_NAME'] . '/fail/');
                    exit();
                }
            }
        }
    }

}

new DeltaKeyPayment();
?>