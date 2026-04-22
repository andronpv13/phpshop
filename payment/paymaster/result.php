<?php

/**
 * Обработчик оплаты заказа через Paymaster
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopPayment
 */
// Библиотеки
$_classPath = "../../";
include($_classPath . "phpshop/class/obj.class.php");
PHPShopObj::loadClass("base");

// Подключение к БД
$PHPShopBase = new PHPShopBase($_classPath . "phpshop/inc/config.ini");

function WriteLog($MY_LMI_HASH) {
    $handle = fopen("../paymentlog.log", "a+");
    $post = null;

    foreach ($_POST as $k => $v)
        $post.=$k . "=" . $v . "\r\n";

    $str = "
	  Paymaster Payment Start ------------------
	  date=" . date("F j, Y, g:i a") . "
	  $post
	  MY_LMI_HASH=$MY_LMI_HASH
	  REQUEST_URI=" . $_SERVER['REQUEST_URI'] . "
	  IP=" . $_SERVER['REMOTE_ADDR'] . "
	  Paymaster Payment End --------------------
	  ";
    fwrite($handle, $str);
    fclose($handle);
}

/**
 * Форматирование имени заказа
 * @package PHPShopCoreDepricated
 * @param int $uid номер заказа
 * @return string 
 */
function UpdateNumOrder($uid) {
    $last_num = substr($uid, -2);
    $total = strlen($uid);
    $ferst_num = substr($uid, 0, ($total - 2));
    return $ferst_num . "-" . $last_num;
}

/**
 * Запись статуса заказа 101
 * @package PHPShopCoreDepricated
 * @return int 
 */
function CheckStatusReady() {
    global $SysValue,$link_db;
    $sql = "select id from " . $SysValue['base']['table_name32'] . " where id=101 limit 1";
    $result = mysqli_query($link_db,$sql);
    $num = @mysqli_num_rows(@$result);

    // Запись нового статуса
    if (empty($num)) {
        $q = "INSERT INTO " . $SysValue['base']['table_name32'] . " VALUES (101, 'Оплачено платежными системами', '#ccff00','')";
        mysqli_query($link_db,'SET NAMES cp1251');
        mysqli_query($link_db,$q);
    }

    return 101;
}

/**
 * Изменение статуса оплаченного заказа
 * @package PHPShopCoreDepricated
 * @param int $inv_id номер заказа
 * @param float $out_summ сумма заказа
 * @param int $order_method  способ оплаты
 */
function Success($inv_id, $out_summ, $order_method) {
    global $SysValue,$link_db;

    $CheckStatusReady = CheckStatusReady();
    $sql = "UPDATE " . $SysValue['base']['table_name1'] . " SET statusi='$CheckStatusReady' where uid='$inv_id'";
    mysqli_query($link_db,$sql);
}

if ($_REQUEST['LMI_PREREQUEST']) {
    echo "YES";
    exit;
}


$LMI_SECRET_KEY = $SysValue['paymaster']['LMI_SECRET_KEY'];
$LMI_HASH = $_REQUEST['LMI_HASH'];
$LMI_PAYEE_PURSE = $_REQUEST['LMI_PAYEE_PURSE'];
$LMI_PAYMENT_AMOUNT = $_REQUEST['LMI_PAYMENT_AMOUNT'];
$LMI_PAYMENT_NO = $_REQUEST['LMI_PAYMENT_NO'];
$LMI_MODE = $_REQUEST['LMI_MODE'];
$LMI_SYS_INVS_NO = $_REQUEST['LMI_SYS_INVS_NO'];
$LMI_SYS_TRANS_NO = $_REQUEST['LMI_SYS_TRANS_NO'];
$LMI_SYS_TRANS_DATE = $_REQUEST['LMI_SYS_TRANS_DATE'];
$LMI_PAYER_PURSE = $_REQUEST['LMI_PAYER_PURSE'];
$LMI_PAYER_WM = $_REQUEST['LMI_PAYER_WM'];

// build own CRC
$HASH = $LMI_PAYEE_PURSE . $LMI_PAYMENT_AMOUNT . $LMI_PAYMENT_NO . $LMI_MODE . $LMI_SYS_INVS_NO . $LMI_SYS_TRANS_NO . $LMI_SYS_TRANS_DATE . $LMI_SECRET_KEY . $LMI_PAYER_PURSE . $LMI_PAYER_WM;
//$MY_LMI_HASH = strtoupper(md5($HASH));

if (function_exists('hash'))
    $MY_LMI_HASH = strtoupper(hash('sha256', $HASH)); // sha256
else
    exit('hash() not support');

if (strtoupper($MY_LMI_HASH) == strtoupper((string)$LMI_HASH)) {

    $new_uid = UpdateNumOrder($LMI_PAYMENT_NO);

    // Проверяем сущ. заказа
    $sql = "select uid from " . $SysValue['base']['table_name1'] . " where uid='$new_uid'";
    $result = mysqli_query($link_db,$sql);
    $num = mysqli_num_rows($result);
    $row = mysqli_fetch_array($result);
    $uid = $row['uid'];

    if ($uid == $new_uid) {

        // Записываем платеж в базу
        $sql = "INSERT INTO " . $SysValue['base']['table_name33'] . " VALUES 
			('$LMI_PAYMENT_NO','Paymaster, $LMI_PAYER_PURSE, WMId$LMI_PAYER_WM','$LMI_PAYMENT_AMOUNT','" . date("U") . "')";
        $result = mysqli_query($link_db,$sql);

        // Заказ есть в БД
        if ($num > 0) {
            // Перевод статуса заказа в оплачено
            Success($uid, $LMI_PAYMENT_AMOUNT, 'paymaster');
        }

        WriteLog($MY_LMI_HASH);

        // print OK signature
        echo "OK$LMI_PAYMENT_NO\n";
    } else {
        WriteLog($MY_LMI_HASH);

        echo "bad order num\n";
        exit();
    }
} else {
    echo "bad sign\n";
    WriteLog($MY_LMI_HASH);
    exit();
}
?>