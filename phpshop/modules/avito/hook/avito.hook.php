<?php

/**
 * Элемент формы ссылки на товар в Avito
 */
function uid_mod_avito_hook($obj, $row, $rout) {
   
    
    if ($rout === 'MIDDLE') {

        // Настройки модуля
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['avito']['avito_system']);
        $options = $PHPShopOrm->select();
        
        if($options['link'] == 1 and !empty($row['export_avito_id'])){
            $obj->set('avito_link','https://www.avito.ru/' . $row['export_avito_id']);
        }
    }
}

$addHandler = array('UID' => 'uid_mod_avito_hook');