<?php

function moneta_users_repay($obj, $PHPShopOrderFunction) {
    global $PHPShopBase, $SysValue;

    // регистрационная информация
    $payment_url = $SysValue['payanyway']['PAYMENT_URL'];
    $mnt_id = $SysValue['payanyway']['MNT_ID'];
    $mnt_dataintegrity_code = $SysValue['payanyway']['MNT_DATAINTEGRITY_CODE'];
    $mnt_test_mode = $SysValue['payanyway']['MNT_TEST_MODE'];


    // параметры магазина
    $mrh_ouid = explode("-", $PHPShopOrderFunction->objRow['uid']);
    $inv_id = $mrh_ouid[0] . "" . $mrh_ouid[1];     //номер счета

    // сумма покупки
    $out_summ  = number_format($PHPShopOrderFunction->getTotal(), 2, '.', '');
    
    // код валюты в заказе
    $mnt_currency = $GLOBALS['PHPShopSystem']->getDefaultValutaIso();

    // библиотека корзины
    $PHPShopCart = new PHPShopCart();


    // проверочный код
    $mnt_signature = md5($mnt_id . $inv_id . $out_summ . $mnt_currency . $mnt_test_mode . $mnt_dataintegrity_code);

    // Если заказ не оплачен
    if ($PHPShopOrderFunction->getParam('statusi') != 101)
        $disp = '<form method="POST" name="pay'.$inv_id.'" id="pay'.$inv_id.'" action="https://' . $payment_url . '/assistant.htm?">
<input type="hidden" name="MNT_ID" value="' . $mnt_id . '">
<input type="hidden" name="MNT_TRANSACTION_ID" value="' . $inv_id . '">
<input type="hidden" name="MNT_AMOUNT" value="' . $out_summ . '">
<input type="hidden" name="MNT_CURRENCY_CODE" value="' . $mnt_currency . '">
<input type="hidden" name="MNT_TEST_MODE" value="' . $mnt_test_mode . '">
<input type="hidden" name="MNT_SIGNATURE" value="' . $mnt_signature . '">
    <input type=hidden name="OrderDetails" value="'.$PHPShopCart->display('cartpaymentdetails').'">
	<a href="javascript:void(0)" class=b title="' . __('Оплатить') . ' ' . $PHPShopOrderFunction->getOplataMetodName() . '" onclick="pay'.$inv_id.'.submit()" >
            <img src="images/shop/coins.gif" alt="Оплатить" width="16" height="16" border="0" align="absmiddle"  hspace=5>' .
                $PHPShopOrderFunction->getOplataMetodName() . "</a></form>";
    else
        $disp = PHPShopText::b($PHPShopOrderFunction->getOplataMetodName());

    return $disp;
}

    /**
     * Шаблон вывода таблицы корзины
     */
    function cartpaymentdetails($val) {
        $dis = $val['uid'] . "  " . $val['name'] . " (" . $val['num'] . " шт. * " . $val['price'] . ") -- " . $val['total'] . "
";

        return $dis;
    }

?>