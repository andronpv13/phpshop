<?php

function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("Функция", "50%"), array("ID Заказа", "10%"), array("Дата", "10%"), array("Статус", "20%"));

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.novapay.novapay_log"));
    $PHPShopOrm->debug = false;

    $data = $PHPShopOrm->getList(array('*'), false, array('order' => 'id DESC'));

    foreach ($data as $row) {
        $PHPShopInterface->setRow(array(
            'name' => $row['type'],
            'link' => '?path=modules.dir.novapay&id=' . $row['id']),
            array('name' => $row['order_id'], 'link' => '?path=order&id=' . $row['order_id']),
            PHPShopDate::get($row['date'], true),
            $row['status']
        );
    }
    $PHPShopInterface->Compile();
}

?>