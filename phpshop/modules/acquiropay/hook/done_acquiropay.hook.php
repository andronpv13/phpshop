<?php

function send_to_order_mod_acquiropay_hook($obj, $value, $rout)
{
    if ($rout === 'MIDDLE' && (int)$value['order_metod'] === 10018) {

        // Настройки модуля
        include_once __DIR__ . '/mod_option.hook.php';
        $options = new PHPShopAcquiroPayArray();
        $options = $options->getArray();

        // Контроль оплаты от статуса заказа
        if (empty($options['status'])) {
            $orderId = $value['ouid'];
            // Сумма покупки
            $amount = number_format($obj->get('total'), 2, '.', '');


            $cf = $orderId;
            $cf2 = '';
            $cf3 = '';

            $domainUrl = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';
            $domainUrl .= '://' . trim($_SERVER['SERVER_NAME'], '/');

            $formParams = array(
                'product_id' => (int)$options['product_id'],
                'token' => md5(
                    (int)$options['merchant_id']
                    . (int)$options['product_id']
                    . $amount
                    . $cf
                    . $cf2
                    . $cf3
                    . trim($options['merchant_skey'])
                ),
                'amount' => $amount,
                'cf' => $cf,
                'cf2' => $cf2,
                'cf3' => $cf3,

                'ok_url' => $domainUrl . '/success/',
                'ko_url' => $domainUrl . '/fail/',
                'cb_url' => $domainUrl . '/phpshop/modules/acquiropay/payment/result.php',
            );

            // Email Плательщика
            if (!empty($value['mail'])) {
                $formParams['email'] = $value['mail'];
            }


            if ((int)$options['use_cashbox'] > 0 && !empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['system']);
                $systemSettings = $PHPShopOrm->select(array('nds'));

                $receipt = array();
                $taxLabels = array(
                    0 => 'vat0',
                    10 => 'vat10',
                    18 => 'vat18'
                );
                $tax = 'none';
                if (isset($systemSettings['nds'])) {
                    $tax = isset($taxLabels[$systemSettings['nds']]) ? $taxLabels[$systemSettings['nds']] : '';
                }

                foreach ($_SESSION['cart'] as $item) {
                    $name = str_replace(array('"', "'"), '', $item['name']);
                    $name = mb_convert_encoding($name, 'UTF-8', 'windows-1251');
                    $receipt[] = array(
                        'sum' => number_format($item['price'] * $item['num'], 2, '.', ''),
                        'tax' => $tax,
                        'name' => $name,
                        'price' => number_format($item['price'], 2, '.', ''),
                        'quantity' => number_format($item['num'], 2, '.', ''),
                    );
                }
                $receipt = array('items' => $receipt);
                $formParams['receipt'] = (string)json_encode($receipt);
            }


            $hiddenFields = '';
            foreach ($formParams as $formParamName => $formParamValue) {
                $hiddenFields .= "<input type='hidden' name='" . $formParamName . "' value='" . $formParamValue . "'/>";
            }
            $obj->set('hiddenFields', $hiddenFields);

            $formUrl = trim($options['endpoint_url']);

            $obj->set('payment_forma_action', $formUrl);
            $obj->set('payment_forma_title', 'Оплатить заказ № ' . $orderId);
            $obj->set('payment_info', $options['title']);
            $forma = ParseTemplateReturn(
                $GLOBALS['SysValue']['templates']['acquiropay']['acquiropay_payment_forma'],
                true
            );
        } else {
            $obj->set('mesageText', $options['title_sub']);
            $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['order_forma_mesage']);
        }

        $obj->set('orderMesage', $forma);
    }
}

$addHandler = array(
    'send_to_order' => 'send_to_order_mod_acquiropay_hook'
);
