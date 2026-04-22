<?php

/**
 * Обновление курсов валют из privatbank.ua
 * Для включения поменяйте значение enabled на true
 */

// Включение
$enabled = false;

if (empty($_SERVER['DOCUMENT_ROOT'])){
    $_classPath = realpath(dirname(__FILE__)) . "/../../../";
    $enabled = true;
}
else
    $_classPath = "../../../";

$SysValue = parse_ini_file($_classPath . "inc/config.ini", 1);
$host = $SysValue['connect']['host'];
$dbname = $SysValue['connect']['dbase'];
$uname = $SysValue['connect']['user_db'];
$upass = $SysValue['connect']['pass_db'];

// Авторизация
if ($_GET['s'] == md5($host . $dbname . $uname . $upass))
    $enabled = true;

if (empty($enabled))
    exit("Ошибка авторизации!");

$link_db = @mysqli_connect($host, $uname, $upass);
mysqli_select_db($link_db, $dbname);

$url = "https://api.privatbank.ua/p24api/pubinfo?exchange&json&coursid=11 ";
$curs = $iso = array();

function get_timestamp($date) {
    list($d, $m, $y) = explode('.', $date);
    return mktime(0, 0, 0, $m, $d, $y);
}

$sql = 'select * from `phpshop_valuta`';
$result = mysqli_query($link_db, $sql);
while (@$row = mysqli_fetch_array(@$result)) {
    $iso[] = $row['iso'];
}

$сurl = curl_init();
curl_setopt_array($сurl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
));

$currency = json_decode(curl_exec($сurl), true);
curl_close($сurl);

if (is_array($currency)) {
    foreach ($currency as $row) {
        if (in_array($row['ccy'], $iso)) {
            $curs[$row['ccy']] = 1 / $row['buy'];
        }
    }

    foreach ($curs as $key => $value) {
        $sql = "UPDATE `phpshop_valuta` SET `kurs` = '" . $value . "' WHERE `iso` ='" . $key . "';";
        mysqli_query($link_db, $sql);
    }
}
?>