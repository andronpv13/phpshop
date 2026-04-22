<?php
function hmac($key, $data) {
		// Вычисление подписи методом HMAC
		$b = 64; // byte length for md5
		
		if ( strlen($key) > $b ) {
			$key = pack("H*",md5($key));
		}
		
		$key = str_pad($key, $b, chr(0x00));
		$k_ipad = $key ^ str_pad(null, $b, chr(0x36));
		$k_opad = $key ^ str_pad(null, $b, chr(0x5c));
		
		return md5($k_opad . pack("H*",md5($k_ipad . $data)));
	}
function userorderpaymentlink_mod_deltakey_hook($obj, $PHPShopOrderFunction) {

	// Настройки модуля
	include_once(dirname(__FILE__) . '/mod_option.hook.php');
	$PHPShopDeltaKeyArray = new PHPShopDeltaKeyArray();
	$option = $PHPShopDeltaKeyArray->getArray();


	// Контроль оплаты от статуса заказа
	if ($PHPShopOrderFunction->getParam('statusi') == $option['status']) {

		// Номер счета
		$mrh_ouid = explode("-", $PHPShopOrderFunction->objRow['uid']);
		$inv_id = $mrh_ouid[0] . "" . $mrh_ouid[1];

		// Сумма покупки
		$out_summ = $PHPShopOrderFunction->getTotal();

		// Подпись
		$param =	$inv_id.
					$option['merchant_id'].
					$option['merchant_key'].
					'1'.
					$out_summ.
					'Order '.$inv_id;
		$sign = hmac($option['merchant_skey'], $param);

		// Платежная форма
		$payment_forma =PHPShopText::setInput('hidden', 'keyt_shop', $option['merchant_key']);
		$payment_forma.=PHPShopText::setInput('hidden', 'num_shop', $option['merchant_id']);
		$payment_forma.=PHPShopText::setInput('hidden', 'identified', '1');
		$payment_forma.=PHPShopText::setInput('hidden', 'comment', 'Order '.$inv_id);
		$payment_forma.=PHPShopText::setInput('hidden', 'ext_transact', $inv_id);
		$payment_forma.=PHPShopText::setInput('hidden', 'sum', $out_summ);
		$payment_forma.=PHPShopText::setInput('hidden', 'sign', $sign);
		$payment_forma.=PHPShopText::setInput('submit', 'send', $option['title'], $float = "none", 250);

		$return = PHPShopText::form($payment_forma, 'pay', 'post', 'https://merchant.deltakey.net/index.py');
	}
	elseif($PHPShopOrderFunction->getSerilizeParam('orders.Person.order_metod') == 10011)
		$return = ', Заказ обрабатывается менеджером';

	return $return;
}

$addHandler = array('userorderpaymentlink' => 'userorderpaymentlink_mod_deltakey_hook');
?>