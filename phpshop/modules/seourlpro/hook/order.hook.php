<?php

function ordercartforma_seourlpro_hook($row, $option, $rout) {

    if ($rout == 'START') {
        //Запрос к базе товаров
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
        $url = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($row['id'])), false, array('limit' => 1));
        

        if (!empty($url['prod_seo_name']))
            $GLOBALS['PHPShopSeoPro']->setMemory($row['id'], $url['prod_seo_name'], 2, false);
        else{
            if(empty($row['parent']))
            $GLOBALS['PHPShopSeoPro']->setMemory($row['id'], $row['name'], 2);
        }
    }
}

$addHandler = array
    (
    'ordercartforma' => 'ordercartforma_seourlpro_hook'
);
?>