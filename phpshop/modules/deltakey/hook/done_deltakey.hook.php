<?php

	function hmac($key, $data) {
		// Вычисление подписи методом HMAC
		$b = 64; // byte length for md5
		$key = mb_convert_encoding($key,'UTF-8',mb_detect_encoding($key));
		if ( strlen($key) > $b ) {
		  $key = pack("H*",md5($key));
		}
		
		$key = str_pad($key, $b, chr(0x00));
		$k_ipad = $key ^ str_pad(null, $b, chr(0x36));
		$k_opad = $key ^ str_pad(null, $b, chr(0x5c));
		
		return md5($k_opad . pack("H*",md5($k_ipad . $data)));
	}
	
function send_to_order_mod_deltakey_hook($obj, $value, $rout) {

    if ($rout == 'MIDDLE' and $value['order_metod'] == 10011) {

        // Настройки модуля
        include_once(dirname(__FILE__) . '/mod_option.hook.php');
        $PHPShopDeltaKeyArray = new PHPShopDeltaKeyArray();
        $option = $PHPShopDeltaKeyArray->getArray();

        // Контроль оплаты от статуса заказа
        if (empty($option['status'])) {

            // Номер счета
            $mrh_ouid = explode("-", $value['ouid']);
            $inv_id = $mrh_ouid[0] . $mrh_ouid[1];

            // Сумма покупки
            $out_summ = number_format($obj->get('total'), 2, '.', '');

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
		$payment_forma.=PHPShopText::setInput('submit', 'send', 'Оплатить через платежную систему DeltaKey', $float = "none", 250);
            $obj->set('payment_forma', PHPShopText::form($payment_forma, 'pay', 'post', 'https://merchant.deltakey.net/index.py'));
            $obj->set('payment_info', $option['title_end']);
            $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['deltakey']['deltakey_payment_forma'], true);
        } else {

            $clean_cart = "
<script language=\"JavaScript1.2\">
if(window.document.getElementById('num')){
window.document.getElementById('num').innerHTML='0';
window.document.getElementById('sum').innerHTML='0';
}
</script>";
            $obj->set('mesageText', $option['title_end'] . $clean_cart);
            $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['order_forma_mesage']);

            // Очищаем корзину
            unset($_SESSION['cart']);
        }

        $obj->set('orderMesage', $forma);
    }
}

$addHandler = array
    (
    'send_to_order' => 'send_to_order_mod_deltakey_hook'
);
?>