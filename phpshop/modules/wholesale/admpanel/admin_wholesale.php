<?php

function actionStart() {
    global $PHPShopInterface, $PHPShopModules,$select_name;

    $PHPShopInterface->setActionPanel(__('Обзор Оптовых акций'), $select_name, array('Добавить +',));
    $PHPShopInterface->setCaption(array("", "1%"), array("Название", "50%"), array("Формула", "15%"), array("Дата создания", "15%"), array("", "10%"), array("Статус &nbsp;&nbsp;&nbsp;", "10%", array('align' => 'right')));

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.wholesale.wholesale_forms"));
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), $where = false, array('order' => 'id'), array('limit' => 1000));

    if (is_array($data))
        foreach ($data as $row) {

            if ($row['discount_tip'] == 0)
                $discount_tip_name = 'Скидка';
            else
                $discount_tip_name = 'Колонка цен';

            $PHPShopInterface->setRow($row['id'], array('name' => $row['name'], 'link' => '?path=modules.dir.wholesale&id=' . $row['id'], 'align' => 'left'),  $discount_tip_name, $row['date_create'], array('action' => array('edit', '|','delete', 'id' => $row['id']), 'align' => 'center'), array('status' => array('enable' => $row['enabled'], 'align' => 'right', 'caption' => array('Выкл', 'Вкл'))));
        }
    $PHPShopInterface->Compile();
}

?>