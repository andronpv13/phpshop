<?php
/**
 * Платежный шлюз сообщение.
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopPayment
 */

if(empty($GLOBALS['SysValue'])) exit(header("Location: /"));

$PHPShopPayment = new PHPShopPayment(intval($_POST['order_metod']));
$mesageText=PHPShopText::notice($PHPShopPayment->getValue('message_header'),false, '14px');
$mesageText.=$PHPShopPayment->getValue('message');
$GLOBALS['SysValue']['other']['mesageText']=$mesageText;
$disp=ParseTemplateReturn($GLOBALS['SysValue']['templates']['order_forma_mesage']);
$disp.="<script>
// Очистка корзина
if(window.document.getElementById('num')){
window.document.getElementById('num').innerHTML='0';
window.document.getElementById('sum').innerHTML='0';
}</script>";

?>