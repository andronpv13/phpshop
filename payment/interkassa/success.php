<?
/*
+-------------------------------------+
|  PHPShop Enterprise                 |
|  Success Function Interkassa        |
+-------------------------------------+
*/

if(empty($GLOBALS['SysValue'])) exit(header("Location: /"));


if(isset($_GET['ik_payment_id'])) {
    $order_metod="Interkassa";
    $success_function=false; // Выключаем функцию обновления статуса заказа
    $my_crc = "NoN";
    $crc = "NoN";
    $inv_id = $_GET['ik_payment_id'];
}
?>
