<?php

function startPbrf($data) {
    global $PHPShopGUI;

    $Tab5 = $PHPShopGUI->loadLib('tab_pbrf_new', $data, '../modules/pbrf/admpanel/');
    $PHPShopGUI->addTab(array("Почта России ", $Tab5));
}

$addHandler = array(
    'actionStart' => 'startPbrf',
    'actionDelete' => false,
    'actionUpdate' => false
);
?>