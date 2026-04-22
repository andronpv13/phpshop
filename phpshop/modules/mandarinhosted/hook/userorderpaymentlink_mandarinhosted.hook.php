<?php

function userorderpaymentlink_mod_mandarin_hook($obj, $PHPShopOrderFunction) {
    include_once(dirname(__FILE__) . '/mod_option.hook.php');
    $PHPShopMandarinHosted = new PHPShopMandarinHostedArray();
    $option = $PHPShopMandarinHosted->option;

    if ($PHPShopOrderFunction->order_metod_id == 10027) 
        if ((int) $PHPShopOrderFunction->getParam('statusi') == $option['status']) {
            

            // Номер счета
            $mrh_ouid = explode("-", $PHPShopOrderFunction->objRow['uid']);
            $orderId = $mrh_ouid[0] . "" . $mrh_ouid[1];

            // Сумма покупки
            $price = $PHPShopOrderFunction->getTotal();
            $person = $PHPShopOrderFunction->getSerilizeParam('orders.Person');


            $content = array(
                'payment' => array(
                    'orderId' => $orderId,
                    'action' => 'pay',
                    'price' => $price,
                    'orderActualTill' => date('Y-m-d H:i:s')
                ),
                'customerInfo' => array(
                    'email' => $person['mail']
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
            $return = ParseTemplateReturn($GLOBALS['SysValue']['templates']['mandarinhosted']['mandarin_payment_form'], true);

        } elseif ($PHPShopOrderFunction->getSerilizeParam('orders.Person.order_metod') == 10027)
            $return =  $option['title_sub'];
    
    return $return;
}

$addHandler = array('userorderpaymentlink' => 'userorderpaymentlink_mod_mandarin_hook');
