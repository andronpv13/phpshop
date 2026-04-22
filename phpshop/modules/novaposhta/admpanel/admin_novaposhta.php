<?php
/**
 * Функция вывода истории платежей
 */
function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("Модель", "25%"), array("Функция", "25%"), array("Номер заказа", "10%"), array("Дата", "10%"), array("Статус", "20%"));

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.novaposhta.novaposhta_log"));

    $PHPShopOrm->debug = false;

    $data = $PHPShopOrm->getList(array('*'), false, array('order' => 'id DESC'));

    foreach ($data as $row) {
        $PHPShopInterface->setRow(array('name' => $row['model'], 'link' => '?path=modules.dir.novaposhta&id=' . $row['id']), array('name' => $row['method'], 'link' => '?path=modules.dir.novaposhta&id=' . $row['id']), $row['order_id'], PHPShopDate::get($row['date'], true), $row['status']);
    }

    $PHPShopInterface->Compile();
}