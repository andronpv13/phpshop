<?php

function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(["Функция", "50%"], ["Номер заказа", "10%"], ["Дата", "10%"], ["Статус", "20%"]);

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.idram.idram_log"));
    $PHPShopOrm->debug = false;

    $data = $PHPShopOrm->getList(['*'], false, ['order' => 'id DESC']);

    foreach ($data as $row) {
        $PHPShopInterface->setRow(['name' => $row['type'], 'link' => '?path=modules.dir.idram&id=' . $row['id']], $row['order_id'], PHPShopDate::get($row['date'], true), $row['status']);
    }

    $PHPShopInterface->Compile();
}