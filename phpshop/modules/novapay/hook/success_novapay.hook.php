<?php

/**
 * Функция хук, обратотка результата выполнения платежа
 * @param object $obj объект функции
 * @param array $value данные о заказе
 */
function success_mod_novapay_hook($obj, $value) {
    if (isset($_REQUEST['uid']) && (int) $_REQUEST['novapay'] === 1) {
        if($_REQUEST['status'] === 'success'){
            $obj->order_metod = 'modules" and id="10023';

            $mrh_ouid = explode("-", $_REQUEST['uid']);
            $obj->inv_id = $mrh_ouid[0] . $mrh_ouid[1];

            $obj->ofd();

            $obj->message();

            return true;
        } else
            $obj->error();
    }
}

$addHandler = array('index' => 'success_mod_novapay_hook');
?>