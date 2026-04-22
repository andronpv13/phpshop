<?
/*
+-------------------------------------+
|  PHPShop                            |
|  Success Function PayAnyWay         |
+-------------------------------------+
*/

if(empty($GLOBALS['SysValue'])) exit(header("Location: /"));

if(isset($_GET['MNT_TRANSACTION_ID'])){
$order_metod="PayAnyWay";
$success_function=true; // Выключаем функцию обновления статуса заказа
$my_crc = "NoN";
$crc = "NoN";
$inv_id = $_GET['MNT_TRANSACTION_ID'];
}
?>
