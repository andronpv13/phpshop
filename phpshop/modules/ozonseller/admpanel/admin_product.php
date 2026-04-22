<?php

function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("Иконка", "7%"), array("Название", "30%"), array("ID", "5%"), array("Ошибка", "45%"), array("Дата", "10%"));

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.ozonseller.ozonseller_export"));
    $PHPShopOrm->debug = false;

    if (!empty($_GET['uid']))
        $where = ['product_id' => '=' . (int) $_GET['uid']];
    else
        $where = false;

    $data = $PHPShopOrm->select(array('*'), $where, array('order' => 'id DESC'), array('limit' => 1000));

    if (is_array($data))
        foreach ($data as $row) {

            if (empty($row['order_id']))
                $row['order_id'] = null;

            if (!empty($row['product_image']))
                $icon = '<img src="' . $row['product_image'] . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
            else
                $icon = '<img class="media-object" src="./images/no_photo.gif">';

            $PHPShopInterface->setRow($icon, array('name' => $row['product_name'], 'link' => '?path=product&id=' . $row['product_id']), array('name' => $row['product_id'], 'link' => '?path=modules.dir.ozonseller&uid=' . $row['product_id']), array('name' => $row['message']), PHPShopDate::get($row['date'], true));
        }
    $PHPShopInterface->Compile();
}
