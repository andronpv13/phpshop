<?php

include_once dirname(__FILE__) . '/../class/OzonSeller.php';

function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(["Название", "50%"], ["Участие", "10%",['align'=>'center']], ["Товаров", "10%",['align'=>'center']], ["Начало", "10%"], ["Окончание", "10%"]);

    
    $OzonSeller = new OzonSeller();
    $data = $OzonSeller->getActions()['result'];
    
    if (is_array($data))
        foreach ($data as $row) {
        
           if(!empty($row['is_participating']))
               $is_participating = '<span class="glyphicon glyphicon-ok"></span>';
           else $is_participating = '<span class="glyphicon glyphicon-remove"></span>';
        
            $PHPShopInterface->setRow(['name' => PHPShopString::utf8_win1251($row['title']), 'link' => '?path=modules.dir.ozonseller.action&id=' . $row['id'],'addon'=>PHPShopString::utf8_win1251('<p><small>'.$row['description'].'</small></p>')],['name'=>$is_participating,'align'=>'center'],['name'=>$row['participating_products_count'],'align'=>'center'],  $OzonSeller->getTime($row['date_start'], true), $OzonSeller->getTime($row['date_end'], true));
        }
    $PHPShopInterface->Compile();
}