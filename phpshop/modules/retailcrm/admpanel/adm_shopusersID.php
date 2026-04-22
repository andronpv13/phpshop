<?php

function addRetailCRM($data) {
    global $PHPShopGUI;

    $Tab = $PHPShopGUI->setField("Внешний код", $PHPShopGUI->setInputText(null, 'retail_user_id_new', $data['retail_user_id']));
    $PHPShopGUI->addTab(array("RetailCRM", $Tab, true));
}

$addHandler = array(
    'actionStart' => 'addRetailCRM',
    'actionDelete' => false,
    'actionUpdate' => false
);
?>