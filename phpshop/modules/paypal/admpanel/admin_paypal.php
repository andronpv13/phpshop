<?php

function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name;


    $PHPShopInterface->checkbox_action=false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("Функция", "50%"),array("ID Заказа", "10%"),array("Дата", "10%"), array("Статус", "20%"));


    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.paypal.paypal_log"));
    $PHPShopOrm->debug = false;

    // Сортировка по дате
    /*
    if (empty($_REQUEST['var3']))
        $pole1 = date("U") - 86400;
    else
        $pole1 = PHPShopDate::GetUnixTime($_REQUEST['var3']) - 86400;

    if (empty($_REQUEST['var4']))
        $pole2 = date("U");
    else
        $pole2 = PHPShopDate::GetUnixTime($_REQUEST['var4']) + 86400;

    $where['date'] = ' BETWEEN ' . $pole1 . ' AND ' . $pole2;
*/

    $data = $PHPShopOrm->select(array('*'), $where=false, array('order' => 'id DESC'), array('limit' => 1000));

    if (is_array($data))
        foreach ($data as $row) {
            $PHPShopInterface->setRow( array('name'=>$row['type'],'link'=>'?path=modules.dir.paypal&id=' . $row['id']),array('name'=>$row['order_id'],'link'=>'?path=order&id=' . $row['order_id']), PHPShopDate::get($row['date'], true), $row['status']);
        }

    $PHPShopInterface->Compile();
}

?>