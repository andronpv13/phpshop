<?php

function addAvitoSort($data) {
    global $PHPShopGUI;

    $Tab3= $PHPShopGUI->setField("Attribute Tag", $PHPShopGUI->setInputText(null, 'attribute_avitoapi_new', $data['attribute_avitoapi']));

    $PHPShopGUI->addTab(array("Avito", $Tab3, true));
}

$addHandler = array(
    'actionStart' => 'addAvitoSort',
    'actionDelete' => false,
    'actionUpdate' => false
);
?>