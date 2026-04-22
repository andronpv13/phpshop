<?php

function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("Название", "40%"), array("ID", "5%"), array("Ошибка", "45%"), array("Дата", "10%"));

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.vkseller.vkseller_export"));
    $PHPShopOrm->debug = false;

    $data = $PHPShopOrm->select(array('*'), $where = false, array('order' => 'id DESC'), array('limit' => 1000));

    if (is_array($data))
        foreach ($data as $row) {
        
            if(empty($row['order_id']))
                $row['order_id']=null;
        
            $PHPShopInterface->setRow(array('name' => $row['product_name'], 'link' => '?path=product&id=' . $row['product_id']), $row['product_id'], array('name' => $row['message']), PHPShopDate::get($row['date'], true));
        }
    $PHPShopInterface->Compile();
}