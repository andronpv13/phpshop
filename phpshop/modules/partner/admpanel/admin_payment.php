<?php

$TitlePage = "Партнеры -> Заявки на вывод средств";

function actionStart() {
    global $PHPShopInterface, $PHPShopSystem, $PHPShopModules;

    //$PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setCaption(array("", "1%"),array("Логин", "30%"), array("Дата", "20%"), array("Сумма (" . $PHPShopSystem->getDefaultValutaCode() . ')', "20%"), array("", "10%"), array("Статус", "10%", array('align' => 'right')));

    // SQL
    $PHPShopOrm = new PHPShopOrm();
    $result = $PHPShopOrm->query('SELECT a.*, b.login FROM ' . $PHPShopModules->getParam("base.partner.partner_payment") . ' AS a JOIN ' . $PHPShopModules->getParam("base.partner.partner_users") . ' AS b ON a.partner_id = b.id order by a.id DESC limit 1000');

    while ($row = mysqli_fetch_array($result)) {
        $sum = number_format($row['sum'], "2", ".", "");

        if (!empty($row['enabled']))
            $row['enabled'] = __('Выполнен');
        else
            $row['enabled'] = __('Новая заявка');

        $PHPShopInterface->setRow($row['id'],array('name' => $row['login'], 'link' => '?path=modules.dir.partner.payment&id=' . $row['id']), PHPShopDate::dataV($row['date'], true), $sum, array('action' => array('edit', '|', 'delete', 'id' => $row['id']), 'align' => 'center'), $row['enabled']);
    }

    $PHPShopInterface->Compile();
}

?>