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
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("xml");
PHPShopObj::loadClass("string");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
$PHPShopSystem = new PHPShopSystem();
$PHPShopModules = new PHPShopModules($_classPath . "modules/");
$PHPShopModules->checkInstall('avangard');


class Payment extends PHPShopPaymentResult {

    private $Avangard;
    private $status;

    function __construct() {

        $this->payment_name = 'Avangard';
        $this->log = true;
        include_once(dirname(__FILE__) . '/../class/Avangard.php');

        $this->Avangard = new Avangard();
        $this->orderNumber = $_POST['order_number'];
        $this->status = (int) $_POST['status_code'];
        $this->Avangard->setOrderNumber($_POST['order_number']);
        $this->Avangard->setAmount((int) $_POST['amount']);

        parent::__construct();
    }

    /**
     * Проверка signature
     * @return boolean
     */
    function check() {
        return $this->Avangard->getSignature(true) === $_POST['signature'];
    }

    /**
     * Обновление данных по заказу
     */
    function updateorder() {

        if (!$this->check()) {
            $this->Avangard->log($_POST, $_POST['order_number'], 'Ошибка проверки Signature', 'Уведомление о платеже');
            header("HTTP/1.1 202 Accepted");
            exit();
        }

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $PHPShopOrm->debug = $this->debug;
        $row = $PHPShopOrm->select(array('*'), array('uid' => "='" . $this->Avangard->getOrderNumber() . "'"), false, array('limit' => 1));

        if(empty($row))
            $this->Avangard->log($_POST, $this->Avangard->getOrderNumber(), 'Заказ ' . $this->Avangard->getOrderNumber() . ' не найден', 'Уведомление о платеже');

        if($this->status === Avangard::STATUS_SUCCESS) {
            $this->Avangard->log($_POST, $this->Avangard->getOrderNumber(), 'Заказ оплачен', 'Уведомление о платеже');
            $this->Avangard->orderState($this->Avangard->getOrderNumber(), Avangard::LOG_STATUS_PAID);

            $this->setPaymentLog($row['id']);
            (new PHPShopOrderFunction((int) $row['id']))->changeStatus((int) $this->set_order_status_101(), $row['statusi']);
        }
        else
            $this->Avangard->log($_POST, $this->Avangard->getOrderNumber(), 'Ошибка оплаты заказа ' . $this->Avangard->getOrderNumber(), 'Уведомление о платеже');

        header("HTTP/1.1 202 Accepted");
        exit();
    }

    private function setPaymentLog($id)
    {
        $PHPShopOrmPayment = new PHPShopOrm($GLOBALS['SysValue']['base']['payment']);
        $PHPShopOrmPayment->insert(array('uid_new' => $id, 'name_new' => 'Avangard',
            'sum_new' => $this->Avangard->getAmount() / 100, 'datas_new' => time()));
    }
}

new Payment();