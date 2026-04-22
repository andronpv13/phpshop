<?php

/**
 * Ёлемент формы ссылки на товар в яћ
 */
function uid_mod_yandexcart_hook($obj, $row, $rout) {
   
    if ($rout === 'MIDDLE') {

        // Ќастройки модул€
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['yandexcart']['yandexcart_system']);
        $options = $PHPShopOrm->select();
        
        if($options['link'] == 1 and !empty($row['yandex_link'])){
            $obj->set('yandexmarket_link',$row['yandex_link']);
        }
    }
}

$addHandler = array('UID' => 'uid_mod_yandexcart_hook');
?>