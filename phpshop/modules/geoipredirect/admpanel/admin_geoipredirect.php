<?php

function actionStart() {
    global $PHPShopInterface,$PHPShopModules,$subpath,$TitlePage, $select_name;
    

    $PHPShopInterface->setCaption(array("","1%"),array("Адрес","30%"),array("Город","30%"),array("", "10%"),array("Статус","10%"));

    // SQL
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.geoipredirect.geoipredirect_city"));
    $data = $PHPShopOrm->select(array('*'),false,array('order'=>'id DESC'),array("limit"=>"100"));
    if(is_array($data))
        foreach($data as $row) {

            $PHPShopInterface->setRow($row['id'],array('name' => $row['host'], 'link' => '?path=modules.dir.'.$subpath[2].'&id=' . $row['id'], 'align' => 'left'),$row['name'],array('action' => array('edit','|', 'delete', 'id' => $row['id']), 'align' => 'center'),array('status' => array('enable' => $row['enabled'], 'align' => 'right', 'caption' => array('Выкл', 'Вкл'))));
        }
    
    $PHPShopInterface->Compile();
}
?>