<?php
/**
 * Обработчик оплаты заказа через Interkassa
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopPayment
 */

if(empty($GLOBALS['SysValue'])) exit(header("Location: /"));


// регистрационная информация
$LMI_PAYEE_PURSE = $SysValue['interkassa']['LMI_PAYEE_PURSE'];    //кошелек
$LMI_SECRET_KEY = $SysValue['interkassa']['LMI_SECRET_KEY'];    //кошелек



//параметры магазина
$mrh_ouid = explode("-", $_POST['ouid']);
$inv_id = $mrh_ouid[0]."".$mrh_ouid[1];     //номер счета
$amount=$GLOBALS['SysValue']['other']['total'];

//описание покупки
$inv_desc  = "Оплата заказа №$inv_id";
$out_summ  = $sum_pol; //сумма покупки


$ik_shop_id = $LMI_PAYEE_PURSE;
$ik_payment_amount = $amount;
$ik_payment_id = $inv_id;
$ik_payment_desc = $inv_desc;
$ik_paysystem_alias = '';
$ik_sign_hash = '';
$ik_baggage_fields = '';
$secret_key = $LMI_SECRET_KEY;

$ik_sign_hash_str = $ik_shop_id.':'.
$ik_payment_amount.':'.
$ik_payment_id.':'.
$ik_paysystem_alias.':'.
$ik_baggage_fields.':'.
$secret_key;

$ik_sign_hash = md5($ik_sign_hash_str);



// вывод HTML страницы с кнопкой для оплаты
$disp= "
<div align=\"center\">

 <p><br></p>

<form name=\"pay\" action=\"https://interkassa.com/lib/payment.php\" method=\"post\" target=\"_top\">
<input type=\"hidden\" name=\"ik_shop_id\" value=\"$LMI_PAYEE_PURSE\">
<input type=\"hidden\" name=\"ik_payment_amount\" value=\"$amount\">
<input type=\"hidden\" name=\"ik_payment_id\" value=\"$inv_id\">
<input type=\"hidden\" name=\"ik_payment_desc\" value=\"$inv_desc\">
<input type=\"hidden\" name=\"ik_paysystem_alias\" value=\"\">
<input type=\"hidden\" name=\"ik_sign_hash\" value=\"$ik_sign_hash\">

	  <table>
<tr>
	<td><img src=\"images/shop/icon-client-new.gif\" alt=\"\" width=\"16\" height=\"16\" border=\"0\" align=\"left\">
	<a href=\"javascript:pay.submit();\"><u>Оплатить через платежную систему</u></a></td>
</tr>
</table>
      </form>
</div>";
?>