<?php
/**
 * Проверка успешности платежа
 * @author PHPShop Software
 * @version 1.0
 */
session_start();

$_classPath = $_SERVER['DOCUMENT_ROOT']."/phpshop/";
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
PHPShopObj::loadClass("user");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("string");


$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
$PHPShopSystem = new PHPShopSystem();
$PHPShopModules = new PHPShopModules($_classPath . "modules/");
$PHPShopModules->checkInstall('cloudpayments');


class Payment extends PHPShopPaymentResult 
{

	function __construct()
	{
	    $this->check_order();

	}
	function check_order(){

	    include_once(dirname(__FILE__) . '/../hook/mod_option.hook.php');
        $CPay = new PHPShopcloudpaymentArray();

        // check hash
	    $hash = $this->checkSign();

	    if($hash == true){
            //check order
            $nUserID = $this->getUserId($_POST["AccountId"]);

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
            $row = $PHPShopOrm->select(array('id','uid','sum','user'), array('uid' => "='" .$_POST[InvoiceId]. "'", 'user' => "=$nUserID"), array('order'=>'id DESC'), array('limit' => 1));

            $orderId = $row["id"];

            if($row["sum"] != $_POST["Amount"]){
                unset($_POST["Data"]);
                $CPay->log($_POST, $_POST["InvoiceId"], 'Оплаченная сумма не совпадает с суммой заказа', 'Запрос Check с сервера CloudPayments');

                $result = array("code" => 11);
                $code = json_encode($result);
                echo $code; die();
            }

            if (!is_null($orderId)){
                $this->updateorder($orderId);
            } else{
                unset($_POST["Data"]);
                $CPay->log($_POST, $_POST["InvoiceId"], 'Заказ не найден', 'Запрос Check с сервера CloudPayments');

                $result = array("code" => 10);
                $code = json_encode($result);
                echo $code; die();
            }
        } else {
            unset($_POST["Data"]);
            $CPay->log($_POST, $_POST["InvoiceId"], 'Нарушена целостность запроса', 'Запрос Check с сервера CloudPayments');

            $result = array("code" => 13);
            $code = json_encode($result);
            echo $code; die();

        }

    }
	function getUserId($userLogin)
	{
		// check user 
		$PHPShopOrm = new PHPShopOrm('phpshop_shopusers');
		$aUser = $PHPShopOrm->select(array('id'), array('login' => "='" .$userLogin. "'"), array('order'=>'id DESC'), array('limit' => 1));
		return $aUser[id];
	}

	function checkSign()
	{
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_cloudpayment_system');

        $postData = file_get_contents('php://input');

        foreach (getallheaders() as $name => $value) {

            $headers[$name] = $value;
        }

        $res = $headers['Content-HMAC'];

        $option = $PHPShopOrm->select();
        @extract($option);

        $apiKey = $option["api"];

        $s = hash_hmac('sha256', $postData, $apiKey, true);
        $hash = base64_encode($s);

        if($res == $hash)
            return true;
        else
            return false;
	}

	 function updateorder($orderId) {

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);

            $row = $PHPShopOrm->select(array('id','uid','statusi'), array('id' => "=" .$orderId. ""), array('order'=>'id DESC'), array('limit' => 1));

            if (!empty($row['uid'])) {
                (new PHPShopOrderFunction((int) $row['id']))->changeStatus((int) $this->set_order_status_101(), $row['statusi']);

                include_once(dirname(__FILE__) . '/../hook/mod_option.hook.php');
                $CPay = new PHPShopcloudpaymentArray();

                unset($_POST["Data"]);
                $CPay->log($_POST, $_POST["InvoiceId"], 'Заказ оплачен, статус заказа изменен', 'Запрос Check с сервера CloudPayments');

                $result = array("code" => 0);
                $code = json_encode($result);
                echo $code; die();
            }
    }
}
	
$payment = new Payment();


?>