<?php

$TitlePage = __("Журнал операций");

function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name,$PHPShopSystem;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("№ ККМ", "15%"), array("Дата", "15%"), array("№ Заказа", "15%"), array("Сумма", "15%"), array("Действие", "10%",array('align' => 'right')));

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.pechka54.pechka54_log"));
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), $where = false, array('order' => 'id DESC'), array('limit' => 1000));

    // Знак рубля
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();

    if (is_array($data))
        foreach ($data as $row) {

            if ($row['operation'] == 'registration') {
                $operation = 'Продажа';
            } elseif($row['operation'] == 'return') {
                $operation = '<span class="text-warning">Возврат</span>';
            }
            else $operation = '<span class="text-danger">Ошибка</span>';

            if (empty($row['fiscal']))
                $row['fiscal'] = 'Ошибка №' . $row['id'];


            $PHPShopInterface->setRow(array('name' => $row['fiscal'], 'link' => '?path=modules.dir.pechka54&id=' . $row['id']), PHPShopDate::get($row['date'], true), array('name' => $row['order_uid'], 'link' => '?path=order&id=' . $row['order_id']), $row['sum'].$currency,array('name'=>$operation,'align' => 'right'));
        }
    $PHPShopInterface->Compile();
}

?>