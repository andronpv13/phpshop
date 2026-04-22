<?php

function addRetailCRM($data) {
    global $PHPShopGUI;

    $Tab = $PHPShopGUI->setField("Внешний код", $PHPShopGUI->setInputText(null, 'retail_product_id_new', $data['retail_product_id']));
    $PHPShopGUI->addTab(array("RetailCRM", $Tab, true));
}

$addHandler = array(
    'actionStart' => 'addRetailCRM',
    'actionDelete' => false,
    'actionUpdate' => false
);
?>