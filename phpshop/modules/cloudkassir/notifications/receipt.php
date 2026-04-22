<?php

session_start();

// Уведомление Receipt
if (!empty(intval($_POST['DocumentNumber']))) {

    $_classPath = $_SERVER['DOCUMENT_ROOT']."/phpshop/";
    include($_classPath . "class/obj.class.php");
    include_once($_classPath . 'modules/cloudkassir/class/cloudkassir.class.php');

    PHPShopObj::loadClass("base");
    $PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);

    PHPShopObj::loadClass("orm");
    PHPShopObj::loadClass("system");
    PHPShopObj::loadClass("text");
    PHPShopObj::loadClass("string");
    PHPShopObj::loadClass("modules");


    $PHPShopModules = new PHPShopModules($_classPath . "modules/");
    $PHPShopModules->checkInstall('cloudkassir');
    $CloudPaymentsRest = new CloudPaymentsRest($_POST['InvoiceId']);

    if(checkSign()){

        // Запись лога
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['cloudkassir']['cloudkassir_log']);
        $PHPShopOrmOrder = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);

        $uid = $_POST['InvoiceId'];
        $order = $PHPShopOrmOrder->select(array('*'), array('uid' => '="' . $_POST['InvoiceId'] . '"'));

        if($_POST['Type'] == 'Income')
            $operation = 'sell';
        elseif ($_POST['Type'] == 'IncomeReturn')
            $operation = 'sell_refund';

        $log = array(
            'message_new' => serialize($_POST),
            'order_id_new' => $order['id'],
            'order_uid_new' => $_POST['InvoiceId'],
            'status_new' => 1,
            'date_new' => time(),
            'operation_new' => $operation,
            'fiscal_new' => $_POST['FiscalSign']
        );
        $ofd['payload'] = $_POST;
        $ofd['log_id'] = $PHPShopOrm->insert($log);
        $ofd['operation'] = $operation;
        $t = serialize($ofd);
        // Статус заказа
        $tt = $PHPShopOrmOrder->update(array('ofd_new' => serialize($ofd), 'ofd_status_new' => 1), array('id' => '="' . $order['id'] . '"'));

    }
    echo json_encode(array('code' => 0));
}

function checkSign()
{
    $PHPShopOrm = new PHPShopOrm('phpshop_modules_cloudkassir_system');

    $postData = file_get_contents('php://input');

    foreach (getallheaders() as $name => $value) {

        $headers[$name] = $value;
    }

    $res = $headers['Content-HMAC'];

    $option = $PHPShopOrm->select();

    $apiKey = $option["apisecret"];

    $s = hash_hmac('sha256', $postData, $apiKey, true);

    $hash = base64_encode($s);

    if($res == $hash)
        return true;
    else
        return false;
}