<?php

function send_to_order_mod_robokassa_hook($obj, $value, $rout) {
    global $PHPShopSystem;

    if ($rout === 'END' and (int) $value['order_metod'] === 10020) {

        // Настройки модуля
        include_once(dirname(__FILE__) . '/mod_option.hook.php');

        $PHPShopRobokassaArray = new PHPShopRobokassaArray();
        $option = $PHPShopRobokassaArray->getArray();

        // Контроль оплаты от статуса заказа
        if (empty($option['status'])) {

            // Номер счета
            $mrh_ouid = explode("-", $value['ouid']);
            $inv_id = $mrh_ouid[0] . $mrh_ouid[1];

            // Платежная форма
            $payment_forma = PHPShopText::setInput('hidden', 'MrchLogin', trim($option['merchant_login']), false, 10);
            $payment_forma .= PHPShopText::setInput('hidden', 'InvId', $inv_id, false, 10);
            $payment_forma .= PHPShopText::setInput('hidden', 'Desc', $value['ouid'], false, 10);

            // НДС
            if ($PHPShopSystem->getParam('nds_enabled') == '') {
                $tax = $tax_delivery = 'none';
            } else {
                $tax = 'vat' . $PHPShopSystem->objRow['nds'];
            }

            // Корзина
            $orders = unserialize($obj->order);
            $total = 0;
            foreach ($orders['Cart']['cart'] as $product) {
                if ((float) $obj->discount > 0 && empty($product['promo_price']))
                    $price = $product['price'] - ($product['price'] * (float) $obj->discount / 100);
                else
                    $price = $product['price'];

                $ym_merchant_receipt['items'][] = array(
                    'name' => $product['name'],
                    'quantity' => (int) $product['num'],
                    'sum' => (float) number_format($price, 2, '.', '') * (int) $product['num'],
                    'tax' => $tax,
                    'payment_method' => 'full_prepayment',
                    'payment_object' => 'commodity'
                );

                $total = number_format($total + (int) $product['num'] * $price, 2, '.', '');
            }

            // Доставка
            if ($obj->delivery > 0) {

                // НДС Доставки
                $tax_delivery = $obj->PHPShopDelivery->objRow['ofd_nds'];
                if ($tax_delivery == '')
                    $tax_delivery = $tax;
                else
                    $tax_delivery = 'vat' . $tax_delivery;

                $ym_merchant_receipt['items'][] = array(
                    'name' => 'Доставка',
                    'quantity' => 1,
                    'sum' => (float) number_format($obj->delivery, 2, '.', ''),
                    'tax' => $tax_delivery,
                    'payment_method' => 'full_prepayment',
                    'payment_object' => 'service'
                );

                $total = number_format($total + (float) number_format($obj->delivery, 2, '.', ''), 2, '.', '');
            }

            $Receipt = urlencode(PHPShopString::json_safe_encode($ym_merchant_receipt));

            // Подпись
            $crc = md5(trim($option['merchant_login']) . ':' . $total . ':' . $inv_id . ':' . $Receipt . ':' . trim($option['merchant_key']));
            $payment_forma .= PHPShopText::setInput('hidden', 'OutSum', $total, false, 10);
            $payment_forma .= PHPShopText::setInput('hidden', 'Receipt', $Receipt, false, 10);
            $payment_forma .= PHPShopText::setInput('hidden', 'SignatureValue', $crc, false, 10);
            $payment_forma .= PHPShopText::setInput('hidden', 'Encoding', 'utf-8', false, 10);
            $payment_forma .= PHPShopText::setInput('hidden', 'Email', $_POST['mail'], false, 10);

            if ($option['dev_mode'] == 1)
                $payment_forma .= PHPShopText::setInput('hidden', 'IsTest', '1', false, 10);

            $payment_forma .= PHPShopText::setInput('submit', 'send', $option['title'], 'none', 250);

            // Данные в лог
            $PHPShopRobokassaArray->log(array('action' => 'done', 'MrchLogin' => trim($option['merchant_login']), 'sum' => $total, 'Email' => $_POST['mail'], 'orderNumber' => $inv_id, 'Receipt' => $Receipt), $inv_id, 'форма готова к отправке', 'данные формы для отправки на оплату');

            if ($option['merchant_country'] == 'Россия')
                $obj->set('payment_forma', PHPShopText::form($payment_forma, 'pay', 'post', 'https://auth.robokassa.ru/Merchant/Index.aspx'));
            else
                $obj->set('payment_forma', PHPShopText::form($payment_forma, 'pay', 'post', 'https://auth.robokassa.kz/Merchant/Index.aspx'));

            $obj->set('payment_info', $option['title_end']);
            $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['robokassa']['robokassa_payment_forma'], true);
        } else {
            $obj->set('mesageText', $option['title_sub']);
            $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['order_forma_mesage']);
        }

        $obj->set('orderMesage', $forma);
    }
}

$addHandler = array
    (
    'send_to_order' => 'send_to_order_mod_robokassa_hook'
);
?>