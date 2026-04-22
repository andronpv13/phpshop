<?
/*
+-------------------------------------+
|  PHPShop Enterprise                 |
|  Success Function Platron           |
+-------------------------------------+
*/

if(empty($GLOBALS['SysValue'])) exit(header("Location: /"));

if(isset($_GET['pg_order_id'])) {

    $order_metod="Platron";
    $success_function=true; // Включаем функцию обновления статуса заказа
    $PrivateSecurityKey=$SysValue['platron']['secret_key'];
    $MerchantId=$SysValue['platron']['merchant_id'];
    $crc = $_GET["pg_sig"];
    $my_crc_str=null;
    $url=$_SERVER['REQUEST_URI'];
    $url_array=parse_url($url);
    parse_str($url_array["query"],$get_array);
	ksort($get_array);
    array_pop($get_array);
    if(is_array($get_array)) {
        foreach($get_array as $v)
            $my_crc_str.=$v.';';


    }

    $my_crc = md5('success.html;'.$my_crc_str.$PrivateSecurityKey);

    $inv_id = $_GET['pg_order_id'];
    $out_summ = $_GET['amount'];
}

?>