<?php
/**
 * ќбработчик оплаты заказа через PayAnyWay
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopPayment
 */


if(empty($GLOBALS['SysValue'])) exit(header("Location: /"));


// регистрационна€ информаци€
$payment_url = $SysValue['payanyway']['PAYMENT_URL'];
$mnt_id = $SysValue['payanyway']['MNT_ID'];
$mnt_dataintegrity_code = $SysValue['payanyway']['MNT_DATAINTEGRITY_CODE'];
$mnt_test_mode = $SysValue['payanyway']['MNT_TEST_MODE'];


// параметры магазина
$mrh_ouid = explode("-", $_POST['ouid']);
$inv_id = $mrh_ouid[0]."".$mrh_ouid[1];     //номер счета

// описание покупки
$inv_desc  = "PHPShopPaymentService";

// сумма покупки
$out_summ  = number_format($GLOBALS['SysValue']['other']['total'], 2, '.', '');

// код валюты в заказе
$mnt_currency = $GLOBALS['PHPShopSystem']->getDefaultValutaIso();

// библиотека корзины
$PHPShopCart = new PHPShopCart();

/**
 * Ўаблон вывода таблицы корзины
 */
function cartpaymentdetails($val) {
     $dis=$val['uid']."  ".$val['name']." (".$val['num']." шт. * ".$val['price'].") -- ".$val['total']."
";

    return $dis;
}



// проверочный код
$mnt_signature = md5($mnt_id . $inv_id . $out_summ . $mnt_currency . $mnt_test_mode . $mnt_dataintegrity_code);

// вывод HTML страницы с кнопкой дл€ оплаты
$disp= '
<div align="center">
<p>
ѕлатежи через сервис <b>PayAnyWay</b> Ц это быстрый и безопасный способ оплаты различных товаров и услуг.
</p>
 <p><br></p>
 
<form method="POST" name="pay" id="pay" action="https://'.$payment_url.'/assistant.htm?">
<input type="hidden" name="MNT_ID" value="'.$mnt_id.'">
<input type="hidden" name="MNT_TRANSACTION_ID" value="'.$inv_id.'">
<input type="hidden" name="MNT_AMOUNT" value="'.$out_summ.'">
<input type="hidden" name="MNT_CURRENCY_CODE" value="'.$mnt_currency.'">
<input type="hidden" name="MNT_TEST_MODE" value="'.$mnt_test_mode.'">
<input type="hidden" name="MNT_SIGNATURE" value="'.$mnt_signature.'">

<input type=hidden name="OrderDetails" value="'.$PHPShopCart->display('cartpaymentdetails').'">
	  <table>
<tr>
	<td><img src="images/shop/icon-client-new.gif"  width="16" height="16" border="0" align="left">
	<a href="javascript:pay.submit();">ќплатить через платежную систему</a></td>
</tr>
</table>
      </form>
</div>';

?>