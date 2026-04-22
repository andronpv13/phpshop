<?php
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("order");

function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $subpath, $TitlePage, $select_name;

    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);

    $PHPShopInterface->setCaption(array("", "1%"), array("Имя", "30%"), array("Дата", "15%"), array("Телефон", "20%"), array("Время", "15%"), array("", "10%"), array("Статус", "10%",array('align'=>'right')));


    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $OrderStatusArray[0] = array('name'=>__('Новый'),'color'=>'');
    
   
    // SQL
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.returncall.returncall_jurnal"));
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'id DESC'), array("limit" => "1000"));
    if (is_array($data))
        foreach ($data as $row) {
        
            $time = null;
            $PHPShopInterface->setRow($row['id'], array('name' => $row['name'], 'link' => '?path=modules.dir.' . $subpath[2] . '&id=' . $row['id'], 'align' => 'left'), PHPShopDate::get($row['date'], true), $row['tel'], $row['time_start'] . ' ' . $row['time_end'], array('action' => array('edit','|', 'delete', 'id' => $row['id']), 'align' => 'center'), '<span style="color:'.$OrderStatusArray[$row['status']]['color'].'">'.$OrderStatusArray[$row['status']]['name'].'</span>');
        }

    $PHPShopInterface->Compile();
}

?>