<?php

function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("Метод", "40%"), array("Номер заказа", "15%"), array("Дата", "15%"), array("Статус", "20%"));

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.pochta.pochta_log"));

    $PHPShopOrm->debug = false;

    $data = $PHPShopOrm->getList(array('*'), false, array('order' => 'id DESC'));

    foreach ($data as $row) {
        $PHPShopInterface->setRow(
            array('name' => $row['method'], 'link' => '?path=modules.dir.pochta&id=' . $row['id']),
            $row['order_uid'],
            PHPShopDate::get($row['date'], true),
            $row['status']
        );
    }

    $PHPShopInterface->Compile();
}