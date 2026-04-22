<?php

function addMoysklad($data) {
    global $PHPShopGUI;

    $Tab = $PHPShopGUI->setField("Внешний код", $PHPShopGUI->setInputText(null, 'moysklad_delivery_id_new', @$data['moysklad_delivery_id']));
    $PHPShopGUI->addTab(array("МойСклад", $Tab, true));
}

$addHandler = array(
    'actionStart' => 'addMoysklad',
    'actionDelete' => false,
    'actionUpdate' => false
);
?>