<?php

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

$PHPShopModules->checkInstall('assist');

class Payment extends PHPShopPaymentResult {

    function Payment() {
        $this->option();
        parent::__construct();
    }

    /**
     * Настройка модуля 
     */
    function option() {
        $this->payment_name = 'Assist';
        $this->log = true;
        include_once(dirname(__FILE__) . '/../hook/mod_option.hook.php');
        $PHPShopAssistmoneyArray = new PHPShopAssistmoneyArray();
        $this->option = $PHPShopAssistmoneyArray->getArray();
    }

    /**
     * Проверка подписи
     * @return boolean 
     */
    function check() {
        $data_return = $_REQUEST;

        $this->my_crc = strtoupper(md5(strtoupper(md5($this->option['merchant_sig']) . md5($data_return['merchant_id'] . $data_return['ordernumber'] . $data_return['orderamount'] . $data_return['ordercurrency'] . $data_return['orderstate']))));
        $this->crc = $data_return['checkvalue'];
        $this->out_summ = $data_return['orderamount'];
        $this->inv_id = $data_return['ordernumber'];
        if ($this->my_crc == $this->crc && $data_return['orderstate'] == "Approved") {
            return true;
        }
    }

    function updateorder() {

        if ($this->check()) {

            // Проверяем сущ. заказа
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
            $PHPShopOrm->debug = $this->debug;
            $row = $PHPShopOrm->select(array('uid'), array('uid' => "='" . $this->true_num($this->inv_id) . "'"), false, array('limit' => 1));
            if (!empty($row['uid'])) {

                // Лог оплат
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['payment']);
                $PHPShopOrm->insert(array('uid_new' => $this->inv_id, 'name_new' => $this->payment_name,
                    'sum_new' => $this->out_summ, 'datas_new' => time()));

                // Изменение статуса платежа
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
                $PHPShopOrm->debug = $this->debug;

                $PHPShopOrm->update(array('statusi_new' => $this->set_order_status_101(), 'paid_new' => 1), array('uid' => '="' . $this->true_num($this->inv_id) . '"'));

                // Сообщение ОК
                $this->done();
            }
            else
                $this->error();
        }
        else
            $this->error(2);
    }

}

new Payment();
?>