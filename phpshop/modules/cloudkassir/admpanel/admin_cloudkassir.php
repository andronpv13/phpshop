<?php

$TitlePage = __("Журнал операций");

function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("№ Чека", "15%"),array("Дата", "15%"), array("№ Заказа", "25%"), array("Действие", "20%"), array("Статус", "10%", array('align' => 'right')));
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.cloudkassir.cloudkassir_log"));
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), $where = false, array('order' => 'id DESC'), array('limit' => 1000));

    if (is_array($data))
        foreach ($data as $row) {

            if ($row['status'] == 1) {
                $status = '<span class="glyphicon glyphicon-ok pull-right"></span>';
            } else {
                $status = '<span class="glyphicon glyphicon-remove pull-right text-danger"></span>';
            }

            if ($row['operation'] == 'sell') {
                $operation = 'Продажа';
            } else {
                $operation = '<span class="text-warning">Возврат</span>';
            }
            
            if(empty($row['fiscal']))
                $row['fiscal']='Ошибка №'.$row['id'];


            $PHPShopInterface->setRow(array('name' => $row['fiscal'], 'link' => '?path=modules.dir.cloudkassir&id=' . $row['id']), PHPShopDate::get($row['date'], true), array('name' => $row['order_uid'], 'link' => '?path=order&id=' . $row['order_id']),$operation, $status);
        }
    $PHPShopInterface->Compile();
}

?>