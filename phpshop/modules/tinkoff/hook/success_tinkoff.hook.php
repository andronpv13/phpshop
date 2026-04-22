<?php

function success_mod_tinkoff_hook($obj, $value) {
    if($_REQUEST['Success'] == true) {
        $obj->order_metod = 'modules" and id="10032';

        $mrh_ouid = explode("-", $_REQUEST['OrderId']);
        $obj->inv_id = (int) $mrh_ouid[0] . (int) $mrh_ouid[1];

        $obj->ofd();
        $obj->message();

        return true;
    } else
        $obj->error();
}

function message_mod_tinkoff_hook($obj) {
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['payment_systems']);
    $option = $PHPShopOrm->select(array('*'), array('id' => '=10032'), false, array('limit' => 1));

    if ($option) {
        $cart_clean = "
            <script>
            if (window.document.getElementById('num')){
            window.document.getElementById('num').innerHTML='0';
            window.document.getElementById('sum').innerHTML='0';
            }
            </script>";

        $message = $option['message'] ? $option['message'] : 'Спасибо за заказ';
        $text = PHPShopText::notice($option['message_header'] . PHPShopText::br(), $icon = false, '14px') . $message . $cart_clean;
        $obj->set('mesageText', $text);
        $obj->set('orderMesage', ParseTemplateReturn($obj->getValue('templates.order_forma_mesage')));
    }
}

$addHandler = array
(
    'index' => 'success_mod_tinkoff_hook',
    'message' => 'message_mod_tinkoff_hook'
);
?>