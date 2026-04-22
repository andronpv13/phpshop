<?php

function addCdekwidgetDelivery($data) {
    global $PHPShopGUI;

    $Tab = $PHPShopGUI->setField("Оплата в ПВЗ", $PHPShopGUI->setCheckbox('cdek_paid_new', 1, null, $data['cdek_paid']));

    if (empty($data['is_folder']))
        $PHPShopGUI->addTab(array("СДЭК", $Tab, true));
}

function cdekwidgetUpdate($data) {
    global $PHPShopOrm;
    
     // Корректировка пустых значений
     $PHPShopOrm->updateZeroVars('cdek_paid_new');
}

$addHandler = array(
    'actionStart' => 'addCdekwidgetDelivery',
    'actionDelete' => false,
    'actionUpdate' => 'cdekwidgetUpdate'
);
?>