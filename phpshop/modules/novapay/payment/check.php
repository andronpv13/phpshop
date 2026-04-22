<?php

session_start();

$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
include_once($_classPath . "modules/novapay/class/NovaPay.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("lang");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("file");
PHPShopObj::loadClass("xml");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("payment");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("system");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");

$PHPShopModules = new PHPShopModules($_classPath . "modules/");
$PHPShopModules->checkInstall('novapay');

class Payment extends PHPShopPaymentResult {

    /** @var NovaPay */
    private $NovaPay;
    private $request;

    function __construct() {
        $this->NovaPay = new NovaPay();

        $source = file_get_contents('php://input');
        $this->request = json_decode($source, true);

        $this->option();
        parent::__construct();
    }

    function option() {
        $this->payment_name = 'NovaPay';
    }

    function check() {
        $source = file_get_contents('php://input');

        return $this->NovaPay->verifySignature($source, $_SERVER['HTTP_X_SIGN']);
    }

    function updateorder() {

        if($this->check()) {
            if($this->request['status'] === 'paid' || $this->request['status'] === 'holded') {
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
                $PHPShopOrm->debug = false;
                $row = $PHPShopOrm->getOne(array('id', 'uid'), array('uid' => "='" . $this->request['external_id'] . "'"));
                if (!empty($row['id'])) {
                    // Лог оплат
                    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['payment']);
                    $PHPShopOrm->insert(array('uid_new' => str_replace('-', '', $row['uid']), 'name_new' => $this->payment_name,
                        'sum_new' => $row['sum'], 'datas_new' => time()));

                    // Изменение статуса платежа
                    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
                    $PHPShopOrm->debug = false;
                    $PHPShopOrm->update(array('statusi_new' => $this->set_order_status_101(), 'paid_new' => 1), array('id' => '="' . $row['id'] . '"'));

                    $this->NovaPay->log($this->request, $row['id'], 'Заказ оплачен, статус заказа изменен', 'Уведомление NovaPay');
                } else {
                    $this->NovaPay->log($this->request, $this->request['external_id'], 'Заказ не найден', 'Уведомление NovaPay');
                }
            } else {
                $this->NovaPay->log($this->request, $this->request['external_id'], 'Заказ не оплачен', 'Уведомление NovaPay');
            }
        } else {
            $this->NovaPay->log(
                array('accepted_sig' => $_SERVER['HTTP_X_SIGN']),
                $this->request['external_id'],
                'Ошибка проверки signature',
                'signature_error'
            );
        }

        header("HTTP/1.0 200");
        exit();
    }
}

new Payment();

?>