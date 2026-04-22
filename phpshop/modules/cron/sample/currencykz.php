<?php

/**
 * Обновление курсов валют из cbr.ru
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
if(@$_GET['s'] == md5($host.$dbname.$uname.$upass))
        $enabled = true;

if (empty($enabled))
    exit("Ошибка авторизации!");

$link_db = @mysqli_connect($host, $uname, $upass);
mysqli_select_db($link_db,$dbname);

$url = "https://nationalbank.kz/rss/rates_all.xml";
$curs = $iso = array();

function get_timestamp($date) {
    list($d, $m, $y) = explode('.', $date);
    return mktime(0, 0, 0, $m, $d, $y);
}

$sql = 'select * from `phpshop_valuta`';
$result = mysqli_query($link_db,$sql);
while (@$row = mysqli_fetch_array(@$result)) {
    $iso[]=$row['iso'];
}

if (!$xml = simplexml_load_file($url))
    die('XML Error Library');

foreach ($xml->channel->item as $m) {
    if(in_array($m->title,$iso)){
        $val_kurs = (float) str_replace(",", ".", (string) $m->description);
        $curs[(string) $m->title] = 1 / $val_kurs;
    }
}

foreach ($curs as $key => $value) {
    $sql = "UPDATE `phpshop_valuta` SET `kurs` = '" . $value . "' WHERE `iso` ='" . $key . "';";
    mysqli_query($link_db,$sql);
}
?>