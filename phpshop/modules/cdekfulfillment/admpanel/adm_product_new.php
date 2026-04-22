<?php
function addCDEKFulfillmentProductTab($data) {
    global $PHPShopGUI;

    // Размер названия поля
    $PHPShopGUI->field_col = 4;

    $tab = $PHPShopGUI->setField(null, $PHPShopGUI->setCheckbox('export_cdek_new', 1, 'Включить экспорт в CDEK', $data['export_cdek']));

    $tab .= $PHPShopGUI->setField('CDEK ID', $PHPShopGUI->setInputText(null, 'export_cdek_id_new', $data['export_cdek_id'], 150), 1, 'Используется для обновления товара в CDEK');
    
    $tab .= $PHPShopGUI->setField('Штрихкод', $PHPShopGUI->setInputText(null, 'barcode_cdek_new', $data['barcode_cdek'], 150));

    $PHPShopGUI->addTab(array("CDEK", $tab, true));
}

$addHandler = array(
    'actionStart' => 'addCDEKFulfillmentProductTab',
);
