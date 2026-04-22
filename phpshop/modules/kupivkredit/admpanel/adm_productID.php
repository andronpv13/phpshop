<?php

function addOptionKVKPromo($data) {
    global $PHPShopGUI;

    // Опции вывода
    $Tab3 = $PHPShopGUI->setField("Опции вывода", $PHPShopGUI->setCheckbox('kvk_enabled_new', 1, 'Кредит доступен', $data['kvk_enabled']));
    $Tab3 .= $PHPShopGUI->setField("Промокод", $PHPShopGUI->setInput("text", "kvk_promo_new", $data['kvk_promo'], "left"), 1, 'Указывается в случае, если на товар распространяется акции (например, рассрочки).');


    $PHPShopGUI->addTab(array("Кредит", $Tab3, true));
}

function updateOptionKVKPromo($data) {
    if (empty($_POST['ajax']) and empty($_POST['kvk_enabled_new'])) {
        $_POST['kvk_enabled_new'] = 0;
    }
}

$addHandler = array(
    'actionStart' => 'addOptionKVKPromo',
    'actionDelete' => false,
    'actionUpdate' => 'updateOptionKVKPromo'
);
?>