<?php

/**
 * Расчет цены для сортировки по прайсу среди мультивалютных товаров
 */
// Включение [true/false]
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
if($_GET['s'] == md5($host.$dbname.$uname.$upass))
        $enabled = true;

if (empty($enabled))
    exit("Ошибка авторизации!");

$link_db = @mysqli_connect($host, $uname, $upass);
mysqli_select_db($link_db,$dbname);
$sql = "select * from " . $SysValue['base']['currency'];
$result = mysqli_query($link_db,$sql);
while ($row = mysqli_fetch_array($result)) {
    if (empty($row['kurs']))
        $row['kurs'] = 1;
    mysqli_query($link_db,"update phpshop_products set price_search=price/" . $row['kurs'] . " where baseinputvaluta=" . $row['id']) or die(mysqli_error($link_db));
}

echo "Выполнено";
?>