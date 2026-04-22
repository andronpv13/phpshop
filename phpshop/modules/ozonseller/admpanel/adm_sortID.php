<?php

function addOzonsellerSort($data) {
    global $PHPShopGUI;

    $Tab3= $PHPShopGUI->setField("Attribute ID", $PHPShopGUI->setInputText(null, 'attribute_ozonseller_new', $data['attribute_ozonseller']));

    $PHPShopGUI->addTab(array("Ozon", $Tab3, true));
}

$addHandler = array(
    'actionStart' => 'addOzonsellerSort',
    'actionDelete' => false,
    'actionUpdate' => false
);
?>