<?php

function actionStart() {
    global $PHPShopInterface, $PHPShopModules;

    $PHPShopInterface->setCaption(array("", "1%"), array("Название", "40%"), array("Маркер", "20%"), array("Шаблон", "10%"), array("", "7%"), array("Статус &nbsp;&nbsp;&nbsp;", "10%", array('align' => 'right')));

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.sticker.sticker_forms"));
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'id DESC'), array('limit' => 1000));

    if (is_array($data))
        foreach ($data as $row) {


            $PHPShopInterface->setRow($row['id'], array('name' => $row['name'], 'link' => '?path=modules.dir.sticker&id=' . $row['id'], 'align' => 'left'), "@sticker_".$row['path'].'@',  $row['skin'], array('action' => array('edit', '|','delete', 'id' => $row['id']), 'align' => 'center'), array('status' => array('enable' => $row['enabled'], 'align' => 'right', 'caption' => array('Выкл', 'Вкл'))));
        }
    $PHPShopInterface->Compile();
}

?>