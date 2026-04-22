<?php

function platron_users_repay($obj, $PHPShopOrderFunction) {
    global $PHPShopBase, $SysValue;

    // параметры магазина
    $mrh_ouid = explode("-", $PHPShopOrderFunction->objRow['uid']);
    $inv_id = $mrh_ouid[0] . "" . $mrh_ouid[1];     //номер счета
    // сумма
    $out_summ = $PHPShopOrderFunction->getTotal(); //сумма покупки

    $OrderId = $inv_id;
    $Amount = $out_summ;
    $Currency = "RUB";

    // регистрационная информация
    $PrivateSecurityKey = $SysValue['platron']['secret_key'];
    $MerchantId = $SysValue['platron']['merchant_id'];


    $arrReq = array();

    /* Обязательные параметры */
    $arrReq['pg_merchant_id'] = $MerchantId; // Идентификатор магазина
    $arrReq['pg_order_id'] = $inv_id;  // Идентификатор заказа в системе магазина
    $arrReq['pg_amount'] = $Amount;  // Сумма заказа
    $arrReq['pg_currency'] = 'RUR';
    $arrReq['pg_description'] = $SysValue['platron']['description']; // Описание заказа (показывается в Платёжной системе)
    $arrReq['pg_salt'] = rand(21, 43433);

    $arrReq['pg_sig'] = md5('payment.php;' . $arrReq['pg_amount'] . ';' . $arrReq['pg_amount'] . ';' . $arrReq['pg_currency'] . ';' . $arrReq['pg_description'] . ';' . $arrReq['pg_merchant_id'] . ';' . $arrReq['pg_order_id'] . ';' . $arrReq['pg_salt'] . ';' . $PrivateSecurityKey);
    // Если заказ не оплачен
    if ($PHPShopOrderFunction->getParam('statusi') != 101)
        $disp = "
<div align=\"center\">
<form name=\"PaymentForm\" action=\"https://www.platron.ru/payment.php\" method=\"get\" target=\"_top\" >
<input type=\"hidden\" name=\"pg_merchant_id\" value=\"$arrReq[pg_merchant_id]\">
<input type=\"hidden\" name=\"pg_order_id\" value=\"$arrReq[pg_order_id]\">
<input type=\"hidden\" name=\"pg_amount\" value=\"$arrReq[pg_amount]\">
<input type=\"hidden\" name=\"pg_currency\" value=\"$arrReq[pg_currency]\">
<input type=\"hidden\" name=\"pg_description\" value=\"$arrReq[pg_description]\">
<input type=\"hidden\" name=\"amount\" value=\"$arrReq[pg_amount]\">
<input type=\"hidden\" name=\"pg_salt\" value=\"$arrReq[pg_salt]\">
<input type=\"hidden\" name=\"pg_sig\" value=\"$arrReq[pg_sig]\">
	<table>
<tr>
	<td><img src=\"images/shop/icon-client-new.gif\" alt=\"\" width=\"16\" height=\"16\" border=\"0\" align=\"left\">
	<a href=\"javascript:PaymentForm.submit();\">" . __('Оплатить') . " " . $PHPShopOrderFunction->getOplataMetodName() . "</a></td>
</tr>
</table>
</form>

</div>";
    else
        $disp = PHPShopText::b($PHPShopOrderFunction->getOplataMetodName());

    return $disp;
}

?>