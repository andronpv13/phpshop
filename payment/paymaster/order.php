<?php
/**
 * Обработчик оплаты заказа через IntellectMoney
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopPayment
 */
if (empty($GLOBALS['SysValue']))
    exit(header("Location: /"));


// регистрационная информация
$LMI_PAYEE_PURSE = $SysValue['paymaster']['LMI_PAYEE_PURSE'];    //кошелек
//
//параметры магазина
$mrh_ouid = explode("-", $_POST['ouid']);
$inv_id = $mrh_ouid[0] . "" . $mrh_ouid[1];     //номер счета

//описание покупки
$inv_desc = "Оплата заказа №$inv_id";
$out_summ = $GLOBALS['SysValue']['other']['total']; //сумма покупки

$url = ($_SERVER['HTTPS'] ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
$success_url = "$url/success/?LMI_PAYMENT_NO=" . $inv_id . '&payment=paymaster';
$fail_url = "$url/fail/";


// вывод HTML страницы с кнопкой для оплаты
$disp = "
<div align='center'>
	<form id=pay name=pay method='POST' action='https://paymaster.ru/Payment/Init' name='pay'>
                <input type=hidden name=LMI_MERCHANT_ID value='".$SysValue['paymaster']['LMI_MERCHANT_ID']."'>
		<input type=hidden name=LMI_PAYMENT_AMOUNT value='$out_summ'>
		<input type=hidden name=LMI_PAYMENT_DESC value='$inv_desc'>
		<input type=hidden name=LMI_PAYMENT_NO value='$inv_id'>
		<input type=hidden name=LMI_PAYEE_PURSE value='$LMI_PAYEE_PURSE'>
		<input type=hidden name=LMI_SIM_MODE value='0'>
		<input type=hidden name=LMI_SUCCESS_URL value='$success_url'>
		<input type=hidden name=LMI_FAIL_URL value='$fail_url'>
		<table>
			<tr>
				<td>
					<img src='images/shop/icon-client-new.gif' alt='' width='16' height='16' border='0' align='left'>
					<a href='javascript:pay.submit();'>Оплатить через платежную систему</a>
				</td>
			</tr>
		</table>
	</form>
</div>";
?>