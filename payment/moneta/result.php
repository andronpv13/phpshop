<?
/*
  +-------------------------------------+
  |  PHPShop 2.1                        |
  |  Модуль ResultUrl Moneta            |
  +-------------------------------------+
 */

function WriteLog($MY_LMI_HASH, $Message) {
    global $REQUEST_URI, $REMOTE_ADDR, $_REQUEST;
    $handle = fopen("../paymentlog.log", "a+");

    foreach ($_REQUEST as $k => $v)
        @$post.=$k . "=" . $v . "\r\n";


    $str = "
PayanyWay Payment Start ------------------
date=" . date("F j, Y, g:i a") . "
$Message
$post
MY_LMI_HASH=$MY_LMI_HASH
REQUEST_URI=$REQUEST_URI
IP=$REMOTE_ADDR
PayAnyWay Payment End --------------------
";
    fwrite($handle, $str);
    fclose($handle);
}

// Преобразует 101-11 в 10111
function UpdateNumOrder($uid) {
    $all_num = explode("-", $uid);
    $ferst_num = $all_num[0];
    $last_num = $all_num[1];
    return $ferst_num . $last_num;
}

// Преобразует 10111 в 101-11
function UpdateNumOrderBack($uid) {
    $first_num = substr($uid, 0, strlen($uid) - 2);
    $last_num = substr($uid, -2);
    return $first_num . "-" . $last_num;
}

// Парсируем установочный файл
$SysValue = parse_ini_file("../../phpshop/inc/config.ini", 1);
while (list($section, $array) = each($SysValue))
    while (list($key, $value) = each($array))
        $SysValue['other'][chr(73) . chr(110) . chr(105) . ucfirst(strtolower($section)) . ucfirst(strtolower($key))] = $value;

// as a part of ResultURL script
// your registration data
$mnt_dataintegrity_code = $SysValue['payanyway']['MNT_DATAINTEGRITY_CODE'];
$signature = md5($_REQUEST['MNT_ID'] . $_REQUEST['MNT_TRANSACTION_ID'] . $_REQUEST['MNT_OPERATION_ID'] . $_REQUEST['MNT_AMOUNT'] . $_REQUEST['MNT_CURRENCY_CODE'] . $_REQUEST['MNT_TEST_MODE'] . $mnt_dataintegrity_code);


if (isset($_REQUEST['MNT_ID']) && isset($_REQUEST['MNT_TRANSACTION_ID']) && isset($_REQUEST['MNT_OPERATION_ID']) && isset($_REQUEST['MNT_AMOUNT']) && isset($_REQUEST['MNT_CURRENCY_CODE']) && isset($_REQUEST['MNT_TEST_MODE']) && isset($_REQUEST['MNT_SIGNATURE'])) {
    $link_db=mysqli_connect($SysValue['connect']['host'], $SysValue['connect']['user_db'], $SysValue['connect']['pass_db']);
    mysqli_select_db($link_db,$SysValue['connect']['dbase']);

    // Проверяем сущ. заказа
    $sql = "select id from " . $SysValue['base']['table_name1'] . " where uid=\"" . UpdateNumOrderBack($_REQUEST['MNT_TRANSACTION_ID']) . "\" limit 1";
    $result = mysqli_query($link_db,$sql);
    $num = @mysqli_num_rows($result);

    if (!empty($num)) {
        if ($_REQUEST['MNT_SIGNATURE'] == $signature) {

            // Записываем платеж в базу
            $sql = "INSERT INTO " . $SysValue['base']['table_name33'] . " VALUES
            ({$_REQUEST['MNT_TRANSACTION_ID']},'PayanyWay','{$_REQUEST['MNT_AMOUNT']}','" . date("U") . "')";
            $result = mysqli_query($link_db,$sql);
            WriteLog($signature, 'Result true, add order to base');


            die("SUCCESS");
        } else {
            WriteLog($signature, 'Result false, bad sign');
            die("FAIL");
        }
    } else {
        WriteLog($signature, 'Result false, order does not exist');
        die("FAIL");
    }
} else {
    WriteLog($signature, 'Result false, bad request');
    die("FAIL");
}
?>
