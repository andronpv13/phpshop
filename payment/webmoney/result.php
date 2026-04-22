<?php

/**
 * Обработчик оплаты заказа через Webmoney
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopPayment
 */
function WriteLog($MY_LMI_HASH) {
    $handle = fopen("../paymentlog.log", "a+");

    $post = null;
    foreach ($_POST as $k => $v)
        $post.=$k . "=" . $v . "\r\n";


    $str = "
  WebMoney Payment Start ------------------
  date=" . date("F j, Y, g:i a") . "
  $post
  MY_LMI_HASH=$MY_LMI_HASH
  REQUEST_URI=$_SERVER[REQUEST_URI]
  IP=$_SERVER[REMOTE_ADDR]
  WebMoney Payment End --------------------
  ";
    fwrite($handle, $str);
    fclose($handle);
}

function UpdateNumOrder($uid) {
    $last_num = substr($uid, -2);
    $total = strlen($uid);
    $ferst_num = substr($uid, 0, ($total - 2));
    return $ferst_num . "-" . $last_num;
}

// Парсируем установочный файл
$SysValue = parse_ini_file("../../phpshop/inc/config.ini", 1);
while (list($section, $array) = each($SysValue))
    while (list($key, $value) = each($array))
        $SysValue['other'][chr(73) . chr(110) . chr(105) . ucfirst(strtolower($section)) . ucfirst(strtolower($key))] = $value;

// as a part of ResultURL script
// your registration data

$LMI_SECRET_KEY = $SysValue['webmoney']['LMI_SECRET_KEY'];

// extract _POST
@extract($_POST, EXTR_SKIP);

// build own CRC
$HASH = $LMI_PAYEE_PURSE . $LMI_PAYMENT_AMOUNT . $LMI_PAYMENT_NO . $LMI_MODE . $LMI_SYS_INVS_NO . $LMI_SYS_TRANS_NO . $LMI_SYS_TRANS_DATE . $LMI_SECRET_KEY . $LMI_PAYER_PURSE . $LMI_PAYER_WM;

//$MY_LMI_HASH = strtoupper(md5("$HASH")); больше не используется
if (function_exists('hash'))
    $MY_LMI_HASH = strtoupper(hash('sha256', $HASH)); // sha256
else
    exit('hash() not support');

if (strtoupper($MY_LMI_HASH) != strtoupper((string) $LMI_HASH)) {
    echo "bad sign\n";
    WriteLog($MY_LMI_HASH);
    exit();
} else {

    // Подключаем базу MySQL
    $link_db=mysqli_connect($SysValue['connect']['host'], $SysValue['connect']['user_db'], $SysValue['connect']['pass_db']);
    mysqli_select_db($link_db,$SysValue['connect']['dbase']);

    $new_uid = UpdateNumOrder($LMI_PAYMENT_NO);


    // Приверяем сущ. заказа
    $sql = "select uid from " . $SysValue['base']['table_name1'] . " where uid='$new_uid'";
    $result = mysqli_query($link_db,$sql);
    $row = mysqli_fetch_array($result);
    $uid = $row['uid'];

    if ($uid == $new_uid) {
        // Записываем платеж в базу
        $sql = "INSERT INTO " . $SysValue['base']['table_name33'] . " VALUES 
('$LMI_PAYMENT_NO','WebMoney, $LMI_PAYER_PURSE, WMId$LMI_PAYER_WM','$LMI_PAYMENT_AMOUNT','" . date("U") . "')";
        $result = mysqli_query($link_db,$sql);
        WriteLog($MY_LMI_HASH);
        
        // print OK signature
        echo "OK$LMI_PAYMENT_NO\n";
    } else {
        WriteLog($MY_LMI_HASH);
        echo "bad order num\n";
        exit();
    }
}
?>