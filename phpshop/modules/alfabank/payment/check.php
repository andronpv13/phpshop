<?php

/**
 * Обработчик оповещения о платеже Альфабанка
 * @author PHPShop Software
 * @version 1.0
 */
session_start();

$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
include_once($_classPath . "modules/alfabank/hook/mod_option.hook.php");
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

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
$PHPShopSystem = new PHPShopSystem();
$PHPShopModules = new PHPShopModules($_classPath . "modules/");
$PHPShopModules->checkInstall('alfabank');

class Payment extends PHPShopPaymentResult {

    private $orderNumber;

    function __construct() {

        // Проверка номера заказа.
        $this->orderNumber = $_REQUEST['orderNumber'];
        if (strstr($this->orderNumber, "#")) {
            $orderUidArr = explode("#", $this->orderNumber);
            $this->orderNumber = $orderUidArr[0];
        }
        if (!PHPShopSecurity::true_order($this->orderNumber)) {
            return;
        }

        $this->option();
        parent::__construct();
    }

    /**
     * Настройка модуля 
     */
    function option() {

        $this->payment_name = 'Альфабанк';
        $this->log = false;
    }

    /**
     * Обновление данных по заказу 
     */
    function updateorder() {

        // Не доверяем полученному уведомлению и делаем повторный запрос в Альфабанк.
        $paid = $this->isOrderPaid($this->orderNumber, $_REQUEST['mdOrder']);

        if ($paid) {
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
            $PHPShopOrm->debug = false;
            $row = $PHPShopOrm->getOne(array('*'), array('uid' => "='" . $this->orderNumber . "'"));
            if (!empty($row['id'])) {
                (new PHPShopOrderFunction((int) $row['id']))->changeStatus((int) $this->set_order_status_101(), $row['statusi']);

                // Лог оплат
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['payment']);
                $PHPShopOrm->insert(array('uid_new' => str_replace('-', '', $row['uid']), 'name_new' => $this->payment_name,
                    'sum_new' => $row['sum'], 'datas_new' => time()));

                $this->ofd($row);
            }
        }

        header("HTTP/1.0 200");
        exit();
    }

    private function isOrderPaid($orderNumber, $merchantId) {
        // Настройки модуля
        $PHPShopAlfabankArray = new PHPShopAlfabankArray();
        $conf = $PHPShopAlfabankArray->getArray();

        // Проверка статуса
        $params = array(
            "orderId" => $merchantId,
            "userName" => $conf["login"],
            "password" => $conf["password"],
        );

        // Режим разработки и боевой режим
        if ($conf["dev_mode"] == "0")
            $url = str_replace('register.do','getOrderStatusExtended.do',$conf["api_url"]);
        else
            $url = str_replace('register.do','getOrderStatusExtended.do',$conf["dev_mode"]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . "?" . http_build_query($params)); // set url to post to
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // allow redirects
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        $r = json_decode(curl_exec($ch), true); // run the whole process
        curl_close($ch);

        if (isset($r['ErrorCode'])) {
            $r['errorMessage'] = PHPShopString::utf8_win1251($r['errorMessage']);
            $PHPShopAlfabankArray->log($r, $orderNumber, 'Ошибка проведения платежа', 'Запрос состояния заказа');
        } elseif ($r['orderStatus'] != 2) {
            $code_description = PHPShopString::utf8_win1251($r['actionCodeDescription']);
            $PHPShopAlfabankArray->log($r, $orderNumber, $code_description, 'Запрос состояния заказа');
        } else {
            $PHPShopAlfabankArray->log($r, $orderNumber, 'Платеж проведен', 'Запрос состояния заказа');
        }

        return (int) $r['orderStatus'] === 2;
    }

}

new Payment();
?>