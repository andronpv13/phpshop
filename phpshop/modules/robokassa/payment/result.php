<?php

/**
 * Обработчик оповещения о платеже Robokassa
 */
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
$PHPShopModules->checkInstall('robokassa');

class Payment extends PHPShopPaymentResult {

    function __construct() {

        $this->option();
        parent::__construct();
    }

    /**
     * Настройка модуля 
     */
    function option() {
        $this->payment_name = 'Robokassa';
        include_once('../hook/mod_option.hook.php');
        $this->PHPShopRobokassaArray = new PHPShopRobokassaArray();
        $this->option = $this->PHPShopRobokassaArray->getArray();
    }

    /**
     * Проверка подписи
     * @return boolean 
     */
    function check() {
        $data_return = $_REQUEST;

        $this->my_crc = strtoupper(md5($data_return['out_summ'] . ':' . $data_return['inv_id'] . ':' . $this->option['merchant_skey']));
        $this->crc = strtoupper($data_return['crc']);
        $this->out_summ = $data_return['out_summ'];
        $this->inv_id = $data_return['inv_id'];

        if ($this->my_crc == $this->crc) {
            return true;
        }
    }

    function updateorder() {

        if ($this->check()) {

            // Проверяем сущ. заказа
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
            $PHPShopOrm->debug = $this->debug;
            $row = $PHPShopOrm->select(array('uid', 'id', 'statusi'), array('uid' => "='" . $this->true_num($this->inv_id) . "'"), false, array('limit' => 1));
            if (!empty($row['uid'])) {

                // Лог оплат
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['payment']);
                $PHPShopOrm->insert(array('uid_new' => $this->inv_id, 'name_new' => $this->payment_name,
                    'sum_new' => $this->out_summ, 'datas_new' => time()));

                // Изменение статуса платежа
                (new PHPShopOrderFunction((int) $row['id']))->changeStatus((int) $this->set_order_status_101(), $row['statusi']);

                // данные в лог
                $this->PHPShopRobokassaArray->log($_REQUEST, $this->inv_id, 'заказ найден, статус изменён, запись в лог электронных плетежей занесена', 'запрос Result c сервера робокассы');

                // Сообщение ОК
                $this->done();
            } else {

                // данные в лог
                $this->PHPShopRobokassaArray->log($_REQUEST, $this->inv_id, 'ошибка разбора, заказа не существует', 'запрос Result c сервера робокассы');

                $this->error();
            }
        } else {

            // данные в лог
            $this->PHPShopRobokassaArray->log($_REQUEST, $this->inv_id, 'ошибка проверки md5', 'запрос Result c сервера робокассы');

            $this->error(2);
        }
    }

}

new Payment();
?>
