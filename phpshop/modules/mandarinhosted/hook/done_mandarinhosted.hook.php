<?php

function send_to_order_mod_mandarinhosted_hook($obj, $value, $rout) {
    if ($rout == 'END' and $value['order_metod'] == 10027) {
        
        // Настройки модуля
        include_once(dirname(__FILE__) . '/mod_option.hook.php');
        $PHPShopMandarinHosted = new PHPShopMandarinHostedArray();
        $option = $PHPShopMandarinHosted->option;

        // При активной системе оплаты
        if (empty($option['status'])) {

            // Номер счета
            $mrh_ouid = explode("-", $value['ouid']);
            $orderId = $mrh_ouid[0] . $mrh_ouid[1];

            // Сумма покупки
            $price = number_format($obj->get('total'), 2, '.', '');
            $email = $value['mail_new'];

            $content = array(
                'payment' => array(
                    'orderId' => $orderId,
                    'action' => 'pay',
                    'price' => $price,
                    'orderActualTill' => date('Y-m-d H:i:s')
                ),
                'customerInfo' => array(
                    'email' => $email
                ),
                'urls' => array(
                    'callback' => $PHPShopMandarinHosted->siteURL() . 'phpshop/modules/mandarinhosted/payment/result.php'
                )
            );

            $json_response = $PHPShopMandarinHosted->send($content);
  
            $json = json_decode($json_response);
            $operationId = $json->jsOperationId;

            $obj->set('operationId', $operationId);
            $obj->set('payment_info', $option['title']);
            $form = ParseTemplateReturn($GLOBALS['SysValue']['templates']['mandarinhosted']['mandarin_payment_form'], true);
        } else {
            $obj->set('mesageText', $option['title_sub']);
            $form = ParseTemplateReturn($GLOBALS['SysValue']['templates']['order_forma_mesage']);
        }

        $obj->set('orderMesage', $form);
    }
}

$addHandler = array('send_to_order' => 'send_to_order_mod_mandarinhosted_hook');
