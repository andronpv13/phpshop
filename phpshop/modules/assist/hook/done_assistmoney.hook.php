<?php

function send_to_order_mod_Assistmoney_hook($obj, $value, $rout) {
    global $PHPShopSystem;

    if ($rout === 'END' and $value['order_metod'] == 10010) {

        // Настройки модуля
        include_once(dirname(__FILE__) . '/mod_option.hook.php');
        $PHPShopAssistmoneyArray = new PHPShopAssistmoneyArray();
        $option = $PHPShopAssistmoneyArray->getArray();

        // Контроль оплаты от статуса заказа
        if (empty($option['status'])) {

            // Номер счета
            $mrh_ouid = explode("-", $value['ouid']);
            $inv_id = $mrh_ouid[0] . $mrh_ouid[1];

            // Сумма покупки
            $out_summ = $obj->total;

			$hashcode = strtoupper(md5(strtoupper(md5( $option['merchant_sig'] ).md5( $option['merchant_id'] . $inv_id .  $out_summ . "RUB"))));

			$fio_arr = explode(" ", $_POST[fio_new]);
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
            $payment_forma.= PHPShopText::setInput('hidden', 'OrderComment', PHPShopString::win_utf8($PHPShopSystem->getParam('name') . ': Заказ ' . $value['ouid']), false, 10);
			$payment_forma.=PHPShopText::setInput('hidden', 'OrderCurrency', $PHPShopSystem->getDefaultValutaIso(), false, 10);
			$payment_forma.=PHPShopText::setInput('hidden', 'URL_RETURN_OK', "http://$_SERVER[HTTP_HOST]/success/?payment_name=assist", false, 10);
			$payment_forma.=PHPShopText::setInput('hidden', 'URL_RETURN_NO', "http://$_SERVER[HTTP_HOST]/fail/?payment_name=assist", false, 10);		
			$payment_forma.=PHPShopText::setInput('hidden', 'Language', 'RU', false, 10);
			$payment_forma.=PHPShopText::setInput('hidden', 'LastName', PHPShopString::win_utf8($lastname), false, 10);
			$payment_forma.=PHPShopText::setInput('hidden', 'FirstName', PHPShopString::win_utf8($firstname), false, 10);
			$payment_forma.=PHPShopText::setInput('hidden', 'Email', PHPShopString::win_utf8($_POST['mail']), false, 10);
			$payment_forma.=PHPShopText::setInput('hidden', 'MobilePhone', $_POST[tel_new], false, 10);
            
            $payment_forma.=PHPShopText::setInput('submit', 'send', $option['title'], $float = "left; margin-left:10px;", 250);

            $obj->set('payment_forma', PHPShopText::form($payment_forma, 'Assistpay', 'post', $option['assist_url'],'_blank'));
            $obj->set('payment_info', $option['title_end']);
            $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['assist']['assistmoney_payment_forma'], true);
        } else {
            $obj->set('mesageText', $option['title_end']);
            $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['order_forma_mesage']);
        }

        $obj->set('orderMesage', $forma);
    }
}

$addHandler = array
    (
    'send_to_order' => 'send_to_order_mod_Assistmoney_hook'
);
?>