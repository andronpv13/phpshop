<?php

/**
 * Функция хук, обработка результата выполнения платежа
 * @param object $obj объект функции
 * @param array $value данные о заказе
 */
function success_mod_payonline_hook($obj, $value) {
    if (isset($_REQUEST['Order_ID'])) {

        include_once 'phpshop/modules/payonline/class/PayOnline.php';

        if($_REQUEST['status'] === 'success'){
            $obj->order_metod = 'modules" and id="' . PayOnline::PAYMENT_ID;

            $mrh_ouid = explode("-", $_REQUEST['Order_ID']);
            $obj->inv_id = $mrh_ouid[0] . $mrh_ouid[1];

            $obj->message();

            return true;
        } else
            $obj->error();
    }
}
$addHandler = array('index' => 'success_mod_payonline_hook');
?>