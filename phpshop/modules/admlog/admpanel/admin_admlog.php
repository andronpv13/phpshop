<?php

$TitlePage = __("Журнал событий");

function actionStart() {
    global $PHPShopInterface, $_classPath,$TitlePage, $select_name;

    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setCaption(array("Раздел", "65%"), array("Дата", "15%"), array("Имя", "15%"), array("Раздел", "10%"), array("IP", "10%",array('align' => 'right')));

    $PHPShopModules = new PHPShopModules($_classPath . "modules/");
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.admlog.admlog_log"));
    $PHPShopOrm->debug = false;

    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'id DESC'), array('limit' => 100));

    if (is_array($data))
        foreach ($data as $row) {


            $PHPShopInterface->setRow(array('name' => $row['title'], 'link' => '?path=modules.dir.admlog&id=' . $row['id'], 'align' => 'left'), PHPShopDate::get($row['date'],true), $row['user'], $row['file'], $row['ip']);
        }


    $PHPShopInterface->Compile();
}

?>