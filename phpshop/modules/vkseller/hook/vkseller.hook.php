<?php

/**
 * Элемент формы ссылки на товар в VK
 */
function uid_mod_vkseller_hook($obj, $row, $rout) {
   
    
    if ($rout === 'MIDDLE') {

        // Настройки модуля
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['vkseller']['vkseller_system']);
        $options = $PHPShopOrm->select();
        
        if($options['link'] == 1 and !empty($row['export_vk_id'])){
            $obj->set('vkseller_link','https://vk.ru/market-' . $options['owner_id'] . '?screen=cart&w=product-' . $options['owner_id'] . '_' . $row['export_vk_id'] . '%2Fquery');
        }
    }
}

$addHandler = array('UID' => 'uid_mod_vkseller_hook');
?>