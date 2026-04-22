<?php

/**
 * Обработчик оповещения о платеже Яндекс.Деньги
 * @author PHPShop Software
 * @version 1.0
 * @tutorial http://yadi.sk/d/SMh5GIF17d_C
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
$PHPShopModules->checkInstall('yandexmoney');

class Payment extends PHPShopPaymentResult {

    function Payment() {
        $this->option();
        parent::__construct();
    }

    /**
     * Настройка модуля 
     */
    function option() {
        $this->payment_name = 'Yandexmoney';
        $this->log = true;
        include_once(dirname(__FILE__) . '/../hook/mod_option.hook.php');
        $PHPShopYandexmoneyArray = new PHPShopYandexmoneyArray();
        $this->option = $PHPShopYandexmoneyArray->getArray();
    }

    /**
     * Проверка подписи
     * @return boolean 
     */
    function check() {
        $this->my_crc = sha1($_REQUEST['notification_type'] . '&' . $_REQUEST['operation_id'] . '&' . $_REQUEST['amount'] . '&' . $_REQUEST['currency'] . '&' . $_REQUEST['datetime'] . '&' . $_REQUEST['sender'] . '&' . $_REQUEST['codepro'] . '&' . $this->option['merchant_sig'] . '&' . $_REQUEST['label']);
        $this->out_summ = $_REQUEST['amount'];
        $this->inv_id = $_REQUEST['label'];
        $this->crc = $_REQUEST['sha1_hash'];

        // Отладка
        /*
          ob_start();
          print_r($_REQUEST);
          $this->query= ob_get_clean();
         */

        if ($this->my_crc == $this->crc)
            return true;
    }

}

new Payment();
?>