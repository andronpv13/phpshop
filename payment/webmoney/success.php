<?
/*
+-------------------------------------+
|  PHPShop Enterprise                 |
|  Success Function WebMoney          |
+-------------------------------------+
*/

if(empty($GLOBALS['SysValue'])) exit(header("Location: /"));

if(isset($_GET['LMI_PAYMENT_NO'])){
$order_metod="WebMoney";
$success_function=true; // Выключаем функцию обновления статуса заказа
$my_crc = "NoN";
$crc = "NoN";
$inv_id = $_GET['LMI_PAYMENT_NO'];
}
?>
