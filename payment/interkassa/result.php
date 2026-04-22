<?php

/*
  +-------------------------------------+
  |  Модуль ResultUrl Interkassa        |
  +-------------------------------------+
 */

function WriteLog($MY_LMI_HASH) {
    $handle = fopen("../paymentlog.log", "a+");

    foreach ($_POST as $k => $v)
        @$post.=$k . "=" . $v . "\r\n";


    $str = "
  Interkassa Payment Start ------------------
  date=" . date("F j, Y, g:i a") . "
  $post
  MY_LMI_HASH=$MY_LMI_HASH
  REQUEST_URI=" . $_SERVER['REQUEST_URI'] . "
  IP=" . $_SERVER['REMOTE_ADDR'] . "
  Interkassa Payment End --------------------
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


$secret_key = $SysValue['interkassa']['secret_key'];


$r = ':';
$HASH = $_POST['ik_shop_id'] . $r . $_POST['ik_payment_amount'] . $r . $_POST['ik_payment_id'] . $r . $_POST['ik_paysystem_alias'] . $r . $_POST['ik_baggage_fields'] . $r . $_POST['ik_payment_state'] . $r . $_POST['ik_trans_id'] . $r . $_POST['ik_currency_exch'] . $r . $_POST['ik_fees_payer'] . $r . $secret_key;
$MY_HASH = strtoupper(md5("$HASH"));

if (strtoupper($MY_HASH) != strtoupper((string)$_POST['ik_sign_hash'])) {
    echo "bad sign\n";
    WriteLog($MY_HASH);
    exit();
} else {
// perform some action (change order state to paid)
// Подключаем базу MySQL
    $link_db=mysqli_connect($SysValue['connect']['host'], $SysValue['connect']['user_db'], $SysValue['connect']['pass_db']);
    mysqli_select_db($link_db,$SysValue['connect']['dbase']);

// Номер заказа
    $new_uid = UpdateNumOrder($_POST['ik_payment_id']);

// Приверяем сущ. заказа
    $sql = "select uid from " . $SysValue['base']['table_name1'] . " where uid='$new_uid'";
    $result = mysqli_query($link_db,$sql);
    $row = mysqli_fetch_array($result);
    $uid = $row['uid'];

    if ($uid == $new_uid) {
// Записываем платеж в базу
        $sql = "INSERT INTO " . $SysValue['base']['table_name33'] . " VALUES 
('$new_uid','Interkassa " . $_POST['ik_paysystem_alias'] . "','" . $_POST['ik_payment_amount'] . "','" . time() . "')";
        $result = mysqli_query($link_db,$sql);
        WriteLog($MY_HASH);

// print OK signature
        echo "OK" . $_POST['ik_trans_id'] . "\n";
    } else {
        WriteLog($MY_HASH);
        echo "bad order num\n";
        exit();
    }
}
?>
