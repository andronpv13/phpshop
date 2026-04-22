<?php

function payment_mod_yandexmoney_hook($obj) {
    
    // Настройки модуля
    include_once(dirname(__FILE__).'/mod_option.hook.php');
    $option = new PHPShopYandexmoneyArray();
    $obj->value[10002] =array($option->getParam('title'),10002,false);
}


$addHandler=array
        (
        'payment'=>'payment_mod_yandexmoney_hook'
);
?>