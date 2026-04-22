<?php

function actionStart() {
    global $PHPShopInterface, $PHPShopModules,$select_name;

    $PHPShopInterface->setActionPanel(__('Обзор Промокодов'), $select_name, array('Добавить +',));
    $PHPShopInterface->setCaption(array("", "1%"), array("Название", "30%"), array("Скидка", "10%"), array("Код", "20%"), array("Дата создания", "15%"), array("", "10%"), array("Статус &nbsp;&nbsp;&nbsp;", "10%", array('align' => 'right')));

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.promotions.promotions_forms"));
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), $where = false, array('order' => 'id'), array('limit' => 1000));

    if (is_array($data))
        foreach ($data as $row) {

            if ($row['discount_tip'] == 1)
                $discount_tip_name = '%';
            else
                $discount_tip_name = '';

            $PHPShopInterface->setRow($row['id'], array('name' => $row['name'], 'link' => '?path=modules.dir.promotions&id=' . $row['id'], 'align' => 'left'), $row['discount'] . $discount_tip_name, $row['code'], $row['date_create'], array('action' => array('edit', '|','delete', 'id' => $row['id']), 'align' => 'center'), array('status' => array('enable' => $row['enabled'], 'align' => 'right', 'caption' => array('Выкл', 'Вкл'))));
        }
    $PHPShopInterface->Compile();
}

?>