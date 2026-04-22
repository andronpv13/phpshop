<?php
/**
 * Контроль склада у подтипов
 */

// Включение [true/false]
$enabled = false;

// 1 - убирать с сайта, 2 - под заказ
$option = 2;

if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_classPath = realpath(dirname(__FILE__)) . "/../../../";
    $enabled = true;
} else
    $_classPath = "../../../";

$SysValue = parse_ini_file($_classPath . "inc/config.ini", 1);
$host = $SysValue['connect']['host'];
$dbname = $SysValue['connect']['dbase'];
$uname = $SysValue['connect']['user_db'];
$upass = $SysValue['connect']['pass_db'];

// Авторизация
if (@$_GET['s'] == md5($host . $dbname . $uname . $upass))
    $enabled = true;

if (empty($enabled))
    exit("Ошибка авторизации!");

$link_db = @mysqli_connect($host, $uname, $upass);
mysqli_select_db($link_db, $dbname);

$sql_main = "select id,parent from " . $SysValue['base']['products'] . " where parent_enabled='0' and parent!=''";
$result_main = mysqli_query($link_db, $sql_main);
while ($row_main = mysqli_fetch_array($result_main)) {

    $sql_parent = "select id,items from " . $SysValue['base']['products'] . " where id IN (" . $row_main['parent'] . ")";
    $result_parent = mysqli_query($link_db, $sql_parent);
    $items = 0;
    while ($row_parent = mysqli_fetch_array($result_parent)) {
        $items += $row_parent['items'];
    }

    // Убирать с сайта
    if (empty($items) and $option == 1)
        $control = "enabled='0'";
    else if (empty($items) and $option == 2)
        $control = "sklad='1'";
    else if (!empty($items) and $option == 1)
        $control = "enabled='1'";
    else if (!empty($items) and $option == 2)
        $control = "sklad='0'";

    mysqli_query($link_db, "update phpshop_products set items=" . $items . " and " . $control . " where id=" . $row_main['id']);
}


echo "Выполнено";
?>