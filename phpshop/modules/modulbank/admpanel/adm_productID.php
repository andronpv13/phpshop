<?php

function addModulbank($data) {
    global $PHPShopGUI;

    $Tab = $PHPShopGUI->setField('ÍÄÑ', $PHPShopGUI->setInputText(false, 'modulbank_vat_code_new', $data['modulbank_vat_code'], 100,'%'));

    $PHPShopGUI->addTab(array("ÌîäóëüÁàíê", $Tab, true));
}

$addHandler = array(
    'actionStart' => 'addModulbank',
    'actionDelete' => false,
    'actionUpdate' => false,
    'actionOptionEdit' => 'addModulbank'
);
