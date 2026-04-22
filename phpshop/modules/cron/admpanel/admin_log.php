<?php

$TitlePage=__("Журнал выполнения задач Cron");

function actionStart() {
    global $PHPShopInterface,$PHPShopModules,$TitlePage,$select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("Дата","10%"),array("Задача","20%"),array("Исполняемый файл","35%"),array("Статус","15%"));

    // SQL
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.cron.cron_log"));
    $data = $PHPShopOrm->select(array('*'),false,array('order'=>'id DESC'),array('limit'=>100));
    if(is_array($data))
        foreach($data as $row) {

            $PHPShopInterface->setRow(PHPShopDate::dataV($row['date']), array('name' => $row['name'], 'link' => '?path=modules.dir.cron&id=' . $row['job_id'], 'align' => 'left'),$row['path'],__($row['status']));
        }

    $PHPShopInterface->Compile();
}
?>