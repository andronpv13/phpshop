<?php

function success_mod_Assistmoney_hook($obj, $value) {
    if ($_GET['payment_name'] == 'assist') {

        $return = array();
        $return['order_metod'] = 'modules';
        $return['success_function'] = false; // Включаем функцию обновления статуса заказа
        $return['crc'] = null;
        $return['my_crc'] = null;
        $return['inv_id'] = $_GET['ordernumber'];
        $return['out_summ'] = false;

        return $return;
    }
}

function message_mod_Assistmoney_hook($obj) {

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['payment_systems']);
    $option = $PHPShopOrm->select(array('*'), array('id' => '=10010'), false, array('limit' => 1));

    if (is_array($option)) {


        $cart_clean = "
<script>
if(window.document.getElementById('num')){
window.document.getElementById('num').innerHTML='0';
window.document.getElementById('sum').innerHTML='0';
}
</script>";

        // Сообщение пользователю об успешном платеже
        $text = PHPShopText::notice($option['message_header'] . PHPShopText::br(), $icon = false, '14px') . $option['message'] . $cart_clean;
        $obj->set('mesageText', $text);
        $obj->set('orderMesage', ParseTemplateReturn($obj->getValue('templates.order_forma_mesage')));
    }
}

$addHandler = array
    (
    'index' => 'success_mod_Assistmoney_hook',
    'message' => 'message_mod_Assistmoney_hook'
);
?>