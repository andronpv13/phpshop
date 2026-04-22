<?php

function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("Функция", "50%"), array("Действие", "20%"), array("Дата", "12%"), array("Статус", "20%",array('align'=>'right')));

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.webhooks.webhooks_log"));
    $PHPShopOrm->debug = false;

    if(!empty($_GET['uid']))
        $where['form_id']='='.intval($_GET['uid']);
    else $where = false;

    $data = $PHPShopOrm->select(array('*'), $where, array('order' => 'id DESC'), array('limit' => 1000));

    if (is_array($data))
        foreach ($data as $row) {

            $PHPShopInterface->setRow(array('name' => $row['name'], 'link' => '?path=modules.dir.webhooks&id=' . $row['form_id']), array('name'=>$row['type'],'link' => '?path=modules.dir.webhooks.log&id=' . $row['id']) , PHPShopDate::get($row['date'], true), array('name'=>$row['status'],'align'=>'right'));
        }
    $PHPShopInterface->Compile();
}

?>