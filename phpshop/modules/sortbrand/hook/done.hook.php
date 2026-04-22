<?php

/**
 * Добавление кнопки быстрого заказа
 */
function mail_hook($obj,$row,$rout) {

    if($rout == 'MIDDLE' and !empty($_COOKIE['ps_referal'])){
        $IP='
REF: '.base64_decode($_COOKIE['ps_referal']);
        $obj->set('payment',$IP,true);
    }

}

$addHandler=array
        (
        'mail'=>'mail_hook'
);

?>