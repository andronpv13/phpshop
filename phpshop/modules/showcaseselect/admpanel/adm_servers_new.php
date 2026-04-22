<?php
function addShowcaseselectProductTab($data) {
    global $PHPShopGUI;

    $tab .= $PHPShopGUI->setField('Город', $PHPShopGUI->setInputText(null, 'selector_name_new', $data['selector_name'],300));
    

    $PHPShopGUI->addTab(array("Меню выбора", $tab, true));
}

$addHandler = array(
    'actionStart' => 'addShowcaseselectProductTab',
);
