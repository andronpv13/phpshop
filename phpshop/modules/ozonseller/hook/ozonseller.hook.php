<?php

/**
 * Элемент формы ссылки на товар в VK
 */
function uid_mod_ozonseller_hook($obj, $row, $rout) {
   
    
    if ($rout === 'MIDDLE') {

        // Настройки модуля
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['ozonseller']['ozonseller_system']);
        $options = $PHPShopOrm->select();
        
        if($options['link'] == 1 and !empty($row['sku_ozon'])){
            $obj->set('ozonseller_link','https://www.ozon.ru/product/' . $row['sku_ozon']);
        }
    }
}

$addHandler = array('UID' => 'uid_mod_ozonseller_hook');
?>