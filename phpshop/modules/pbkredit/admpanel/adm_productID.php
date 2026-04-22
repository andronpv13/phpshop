<?php

function addPbkreditTab($data) {
    global $PHPShopGUI;

    $Tab1 = $PHPShopGUI->setField('Отключить покупку в кредит', $PHPShopGUI->setCheckbox('pbkredit_disabled_new', 1, 'Отключает вывод кнопки "Купить в кредит"', $data['pbkredit_disabled']));

    $PHPShopGUI->addTab(array("Кредит Почта Банк", $Tab1, true));
}

function savePbkreditTab($data) {
    if (empty($_POST['ajax']))
        if (!isset($data['pbkredit_disabled_new'])) {
            $_POST['pbkredit_disabled_new'] = '0';
        }
}

$addHandler = array('actionStart' => 'addPbkreditTab', 'actionDelete' => false, 'actionUpdate' => 'savePbkreditTab');
?>