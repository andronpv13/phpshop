<?php

function actionStart() {
    global $PHPShopInterface,$PHPShopModules,$TitlePage, $select_name,$PHPShopSystem;
    
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, array('Добавить'));
    
        // Знак рубля
if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
    $currency = ' <span class="rubznak hidden-xs">p</span>';
else
    $currency = $PHPShopSystem->getDefaultValutaCode();


    $PHPShopInterface->setCaption(array("", "1%"),array("Логин","25%"),array("Имя","25%"),array("ID","10%"),array("Регистрация","15%"),array("Баланс","15%"),array("", "10%"),array("Статус", "10%", array('align' => 'right')));


    // SQL
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.partner.partner_users"));
    $data = $PHPShopOrm->select(array('*'),false,array('order'=>'id DESC'),array('limit'=>1000));
    if(is_array($data))
        foreach($data as $row) {
            $PHPShopInterface->setRow($row['id'],array('name'=>$row['login'],'link'=>'?path=modules.dir.partner&id=' . $row['id']),array('name'=>$row['name'],'link'=>'?path=modules.dir.partner&id=' . $row['id']),$row['id'],$row['date'],intval($row['money']).' '.$currency,array('action' => array('edit','|', 'delete', 'id' => $row['id']), 'align' => 'center'),array('status' => array('enable' => $row['enabled'], 'align' => 'right', 'caption' => array('Выкл', 'Вкл'))));
        }

    $PHPShopInterface->Compile();
}
?>