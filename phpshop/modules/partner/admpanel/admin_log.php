<?php

function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name, $PHPShopSystem;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("№", "10%"), array("Дата", "15%"), array("Партнер ID", "10%"), array("Реферал", "30%"), array("Статус", "10%"), array("Сумма", "10%", array('align' => 'right')));

    // Знак рубля
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();


    if (!empty($_GET['where']['partner_id']))
        $where = array('partner_id'=>'='.intval($_GET['where']['partner_id']));
    else
        $where = false;

    // SQL
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.partner.partner_log"));
    $data = $PHPShopOrm->select(array('*'), $where, array('order' => 'id DESC'), array('limit' => 1000));
    if (is_array($data))
        foreach ($data as $row) {

            if (!empty($row['enabled']))
                $row['enabled'] = 'Выполнен';
            else
                $row['enabled'] = 'В обработке';

            $PHPShopInterface->setRow(array('name' => $row['order_uid'], 'link' => '?path=order&id=' . $row['order_id'] . '&return=modules.dir.partner.log'), PHPShopDate::dataV($row['date'], true), array('name' => $row['partner_id'], 'link' => '?path=modules.dir.partner&id=' . $row['partner_id'] . '&return=modules.dir.partner'), PHPShopSecurity::TotalClean($row['path'], 2), $row['enabled'],  $row['sum'] . ' ' . $currency);
        }

    $PHPShopInterface->Compile();
}

?>