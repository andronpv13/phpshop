<?php

function webmoney_users_repay($obj,$PHPShopOrderFunction) {
    global $PHPShopBase;

    // Регистрационная информация
    $LMI_PAYEE_PURSE = $PHPShopBase->getParam('webmoney.LMI_PAYEE_PURSE');    //кошелек
    $wmid = $PHPShopBase->getParam('webmoney.wmid');   //аттестат

    //параметры магазина
    $mrh_ouid = explode("-", $PHPShopOrderFunction->objRow['uid']);
    $inv_id = $mrh_ouid[0]."".$mrh_ouid[1];     //номер счета

    //описание покупки
    $inv_desc  = __('Оплата заказа').' '.$inv_id;
    $out_summ  = $PHPShopOrderFunction->getTotal(); //сумма покупки

    // Если заказ не оплачен
    if($PHPShopOrderFunction->getParam('statusi') != 101)
        $disp="<form id=pay name=paywebmoney method=\"POST\" action=\"https://merchant.webmoney.ru/lmi/payment.asp\" name=\"paywebmoney\">
        <input type=hidden name=LMI_PAYMENT_AMOUNT value=\"$out_summ\">
	<input type=hidden name=LMI_PAYMENT_DESC value=\"$inv_desc\">
	<input type=hidden name=LMI_PAYMENT_NO value=\"$inv_id\">
	<input type=hidden name=LMI_PAYEE_PURSE value=\"$LMI_PAYEE_PURSE\">
	<input type=hidden name=LMI_SIM_MODE value=\"0\">
	<a href=\"javascript:void(0)\" class=b title=\"".__('Оплатить')." ".$PHPShopOrderFunction->getOplataMetodName()."\" onclick=\"javascript:paywebmoney.submit();\" ><img src=\"images/shop/coins.gif\" alt=\"Оплатить\" width=\"16\" height=\"16\" border=\"0\" align=\"absmiddle\"  hspace=5>".$PHPShopOrderFunction->getOplataMetodName()."</a></form>";
    else $disp=PHPShopText::b($PHPShopOrderFunction->getOplataMetodName());

    return $disp;
}
?>