<?php

/**
 * Добавление SEO ссылки к товарам
 */
function checkStore_seourlpro_element_hook($obj, $row) {
    if (PHPShopSecurity::true_param($row['id'], $row['name'])) {
        if (!empty($GLOBALS['PHPShopSeoPro'])){
            if (!empty($row['prod_seo_name']))
            $GLOBALS['PHPShopSeoPro']->setMemory($row['id'], $row['prod_seo_name'], 2,false);
            else 
            $GLOBALS['PHPShopSeoPro']->setMemory($row['id'], $row['name'], 2);
        }
        else {
            if (!empty($row['prod_seo_name']))
            $GLOBALS['modules']['seourlpro']['map_prod']['shop/UID_' . $row['id']] = 'id/' . $row['prod_seo_name']. '-' . $row['id'];
            else $GLOBALS['modules']['seourlpro']['map_prod']['shop/UID_' . $row['id']] = 'id/' . str_replace("_", "-", PHPShopString::toLatin($row['name'])) . '-' . $row['id'];
        }
    }
    return true;
}

$addHandler = array
    (
    'checkStore' => 'checkStore_seourlpro_element_hook'
);
?>