<?php

/**
 * Функция хук, обратотка результата выполнения платежа
 * @param object $obj объект функции
 * @param array $value данные о заказе
 */
function success_mod_uniteller_hook($obj, $value) {
    if (isset($_REQUEST['Order_ID'])) {

        if($_REQUEST['status'] === 'success'){
            $obj->order_metod = 'modules" and id="10022';

            $mrh_ouid = explode("-", $_REQUEST['Order_ID']);
            $obj->inv_id = $mrh_ouid[0] . $mrh_ouid[1];

            $obj->ofd();

            $obj->message();

            return true;
        } else
            $obj->error();
    }
}
$addHandler = array('index' => 'success_mod_uniteller_hook');
?>