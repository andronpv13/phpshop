<?php

function success_mod_deltakey_hook($obj, $value) {
  
   if($_REQUEST['ext_transact']){
        $return=array();
        $return['order_metod']='modules';
        $return['order_metod_name'] = 'DeltaKey';
        $return['success_function']=true;// Выключаем функцию обновления статуса заказа
        $return['crc'] = null;
        $return['my_crc'] = null;
        $return['inv_id'] = $_REQUEST['ext_transact'];
        $return['out_summ'] = false;

        return $return;
    }
}

$addHandler = array('index' => 'success_mod_deltakey_hook');
?>