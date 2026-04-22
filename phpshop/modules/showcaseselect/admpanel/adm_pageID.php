<?php

function addShowcaseselectPageTab($data) {
    global $PHPShopGUI;

    if ($data['category'] == 1000)
        $tab= $PHPShopGUI->setField("Выбор города", $PHPShopGUI->setCheckbox('selector_enabled_new', 1, null, $data['selector_enabled']));


    $PHPShopGUI->addTab(array("Меню выбора", $tab, true));
}

function showcaseselectPageUpdate(){
    if(!isset($_POST['selector_enabled_new']) and !isset($_POST['ajax']))
        $_POST['selector_enabled_new']=0;
}

$addHandler = array(
    'actionStart' => 'addShowcaseselectPageTab',
    'actionDelete' => false,
    'actionUpdate' => 'showcaseselectPageUpdate',
    'actionSave' => 'showcaseselectPageUpdate',
);
