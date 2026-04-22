<?php

include_once dirname(__FILE__) . '/../class/CDEKFulfillment.php';

function addCDEKFulfillmentProductTab($data) {
    global $PHPShopGUI;

    // Размер названия поля
    $PHPShopGUI->field_col = 4;

    $tab = $PHPShopGUI->setField(null, $PHPShopGUI->setCheckbox('export_cdek_new', 1, 'Включить экспорт в CDEK', $data['export_cdek']));

    $tab .= $PHPShopGUI->setField('CDEK ID', $PHPShopGUI->setInputText(null, 'export_cdek_id_new', $data['export_cdek_id'], 150), 1, 'Используется для обновления товара в CDEK');
    
    $tab .= $PHPShopGUI->setField('Штрихкод', $PHPShopGUI->setInputText(null, 'barcode_cdek_new', $data['barcode_cdek'], 150));

    $PHPShopGUI->addTab(array("CDEK", $tab, true));
}

function CDEKFulfillmentUpdate() {

    // Отключение 
    if (!isset($_POST['export_cdek_new']) and ! empty($_POST['name_new'])) {
        //$_POST['export_cdek_id_new'] = 0;
    }
}

function CDEKFulfillmentSave() {

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $data = $PHPShopOrm->getOne(['*'], ['id' => '=' . (int) $_POST['rowID']]);

    // Товар для CDEK
    if (!empty($data['export_cdek'])) {
        
        $CDEKFulfillment = new CDEKFulfillment();

        // Товар еще не выгружен
        if (empty($data['export_cdek_id'])) {

            $result = $CDEKFulfillment->sendProduct($data);

            if (is_array($result) and !empty($result['id'])) {

               $PHPShopOrm->update(['export_cdek_id_new' => (int)$result['id']], ['id' => '=' . (int) $data['id']]);

            }
            // Ошибка 
            elseif (!empty($result['detail'])) {

                
            }
        }
        // Товар выгружен, обновление цен
        else {

            // Цены
            $result = $CDEKFulfillment->updateProduct($data);
        }
    }
}

$addHandler = array(
    'actionStart' => 'addCDEKFulfillmentProductTab',
    'actionDelete' => false,
    'actionUpdate' => 'CDEKFulfillmentUpdate',
    'actionSave' => 'CDEKFulfillmentSave',
    '#actionOptionEdit' => 'addCDEKFulfillmentProductTab',
);
