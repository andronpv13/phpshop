<?php

function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("Функция", "40%"), array("ID", "10%"), array("Дата", "10%"), array("Статус", "15%"));

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.cdekfulfillment.cdekfulfillment_log"));
    $PHPShopOrm->debug = false;


    $data = $PHPShopOrm->select(array('*'), $where = false, array('order' => 'id DESC'), array('limit' => 1000));

    if (is_array($data))
        foreach ($data as $row) {

            $PHPShopInterface->setRow(array('name' => $row['type'], 'link' => '?path=modules.dir.cdekfulfillment&id=' . $row['id']), array('name' => $row['order_id']), PHPShopDate::get($row['date'], true), $row['status']);
        }
    $PHPShopInterface->Compile();
}
