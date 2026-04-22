<?php

function actionStart() {
    global $PHPShopInterface, $PHPShopModules;


    $PHPShopInterface->setCaption(array("", "1%"), array("Название", "20%"), array("Метка", "20%"), array("Описание", "30%"),  array("", "10%"), array("Статус &nbsp;&nbsp;&nbsp;", "10%", array('align' => 'right')));


    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.adanalyzer.adanalyzer_campaign"));
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'num'), array('limit' => 100));

    if (is_array($data))
        foreach ($data as $row) {

            $PHPShopInterface->setRow($row['id'], array('name' => $row['name'], 'link' => '?path=modules.dir.adanalyzer&id=' . $row['id'], 'align' => 'left'), $row['utm'], $row['content'], array('action' => array('edit', '|','delete', 'id' => $row['id']), 'align' => 'center'), array('status' => array('enable' => $row['enabled'], 'align' => 'right', 'caption' => array('Выкл', 'Вкл'))));
        }
    $PHPShopInterface->Compile();
}

?>