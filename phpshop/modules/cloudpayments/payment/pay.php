<?php
/**
 * Фиксация факта платежа
 * @author PHPShop Software
 * @version 1.0
 */

$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("orm");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);

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

if ($hash === $res) {

    include_once(dirname(__FILE__) . '/../hook/mod_option.hook.php');

    unset($_POST["Data"]);
    $CPay = new PHPShopcloudpaymentArray();
    $CPay->log($_POST, $_POST["InvoiceId"], 'Факт платежа зафиксирован', 'Запрос Pay с сервера CloudPayments');

    $result = array("code" => 0);
    $code = json_encode($result);
    echo $code; die();

} else {
    include_once(dirname(__FILE__) . '/../hook/mod_option.hook.php');

    $CPay = new PHPShopcloudpaymentArray();
    $CPay->log($_POST, $_POST["InvoiceId"], 'Не совпал hash', 'Запрос Pay с сервера CloudPayments');
}