<?php

function addOptionDolyame($data) {
    global $PHPShopGUI;

    // Опции вывода
    $Tab3 = $PHPShopGUI->setField("Опции вывода", $PHPShopGUI->setCheckbox('dolyame_enabled_new', 1, 'Рассрочка доступна', $data['dolyame_enabled']));


    $PHPShopGUI->addTab(array("Долями", $Tab3, true));
}

function updateOptionDolyame() {
    if (empty($_POST['ajax']) and empty($_POST['dolyame_enabled_new'])) {
        $_POST['dolyame_enabled_new'] = 0;
    }
}

$addHandler = array(
    'actionStart' => 'addOptionDolyame',
    'actionDelete' => false,
    'actionUpdate' => 'updateOptionDolyame'
);
?>