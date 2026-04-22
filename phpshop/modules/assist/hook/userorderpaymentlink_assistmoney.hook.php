<?php

function userorderpaymentlink_mod_Assistmoney_hook($obj, $PHPShopOrderFunction) {
    global $PHPShopSystem;

    // Настройки модуля
    include_once(dirname(__FILE__) . '/mod_option.hook.php');
    $PHPShopAssistmoneyArray = new PHPShopAssistmoneyArray();
    $option = $PHPShopAssistmoneyArray->getArray();

    // Тип оплаты
    $order_metod = $PHPShopOrderFunction->getSerilizeParam('orders.Person.order_metod');

    // Контроль оплаты от статуса заказа
    if ($order_metod == 10010)
        if (intval($PHPShopOrderFunction->getParam('statusi')) == intval($option['status'])) {
            // Номер счета
            $mrh_ouid = explode("-", $PHPShopOrderFunction->objRow['uid']);
            $inv_id = $mrh_ouid[0] . "" . $mrh_ouid[1];

            // Сумма покупки
            $out_summ = $PHPShopOrderFunction->getTotal();

			$hashcode = strtoupper(md5(strtoupper(md5( $option['merchant_sig'] ).md5( $option['merchant_id'] . $inv_id .  $out_summ . "RUB"))));

			$fio_arr = explode(" ", $PHPShopOrderFunction->objRow[fio]);
			$firstname = $fio_arr[0];
			$lastname = $fio_arr[1];

			if (trim($firstname) == "") {
				$firstname = "---";
			}
			if (trim($lastname) == "") {
				$lastname = "---";
			}

            // Платежная форма
            $payment_forma = PHPShopText::setInput('hidden', 'Merchant_ID', trim($option['merchant_id']), false, 10);
            $payment_forma.= PHPShopText::setInput('hidden', 'OrderNumber', $inv_id, false, 10);
			$payment_forma.=PHPShopText::setInput('hidden', 'OrderAmount', $out_summ, false, 10);
			$payment_forma.=PHPShopText::setInput('hidden', 'CheckValue', $hashcode, false, 10);
            $payment_forma.= PHPShopText::setInput('hidden', 'OrderComment', $PHPShopSystem->getParam('name') . ': Заказ ' . $PHPShopOrderFunction->objRow['uid'], false, 10);
			$payment_forma.=PHPShopText::setInput('hidden', 'OrderCurrency', 'RUB', false, 10);
			$payment_forma.=PHPShopText::setInput('hidden', 'URL_RETURN_OK', "http://$_SERVER[HTTP_HOST]/success/?payment_name=assist", false, 10);
			$payment_forma.=PHPShopText::setInput('hidden', 'URL_RETURN_NO', "http://$_SERVER[HTTP_HOST]/fail/?payment_name=assist", false, 10);		
			$payment_forma.=PHPShopText::setInput('hidden', 'Language', 'RU', false, 10);
			$payment_forma.=PHPShopText::setInput('hidden', 'LastName', $lastname, false, 10);
			$payment_forma.=PHPShopText::setInput('hidden', 'FirstName', $firstname, false, 10);
			$payment_forma.=PHPShopText::setInput('hidden', 'Email', $PHPShopOrderFunction->getMail(), false, 10);
			$payment_forma.=PHPShopText::setInput('hidden', 'MobilePhone', $PHPShopOrderFunction->objRow[tel], false, 10);


            $payment_forma.=PHPShopText::setInput('submit', 'send', $option['title'], $float = "none", 250);

            $return = PHPShopText::form($payment_forma, 'Assistpay', 'post', $option['assist_url'], '_blank');
        } elseif ($PHPShopOrderFunction->getSerilizeParam('orders.Person.order_metod') == 10010)
            $return = ', Заказ обрабатывается менеджером';

    return $return;
}

$addHandler = array
    (
    'userorderpaymentlink' => 'userorderpaymentlink_mod_Assistmoney_hook'
);
?>