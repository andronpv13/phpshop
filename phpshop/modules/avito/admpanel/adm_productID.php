<?php

include_once dirname(__FILE__) . '/../class/Avito.php';

function addAvitoProductTab($data) {
    global $PHPShopGUI;

    // Размер названия поля
    $PHPShopGUI->field_col = 5;

    // Валюты
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            if ($data['baseinputvaluta'] == $val['id']) {
                $valuta_def_name = $val['code'];
            }
        }

    $tab = $PHPShopGUI->setField('Экспорт в Avito', $PHPShopGUI->setCheckbox('export_avito_new', 1, '', $data['export_avito']));
    $tab .= $PHPShopGUI->setField('Цена Avito', $PHPShopGUI->setInputText(null, 'price_avito_new', $data['price_avito'], 150, $valuta_def_name), 2);
    $tab .= $PHPShopGUI->setField("Название товара:", $PHPShopGUI->setInput('text', 'name_avito_new', $data['name_avito']));
    $tab .= $PHPShopGUI->setField("Avito ID:", $PHPShopGUI->setInput('text', 'export_avito_id_new', $data['export_avito_id']));

    $PHPShopGUI->addTab(array("Avito", $tab, true));
}

function avitoUpdate() {

    if (empty($_POST['export_avito_new']) and ! isset($_REQUEST['ajax'])) {
        $_POST['export_avito_new'] = 0;
    }
}

function avitoSave() {
    global $PHPShopOrm;

    // Обновление цен и остатков
    include_once dirname(__FILE__) . '/../class/Avito.php';
    $Avito = new Avito();
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);

    $products = $PHPShopOrm->getOne(['*'], ['export_avito' => "='1'", 'id' => '=' . $_POST['rowID']]);
    if (is_array($products) and count($products) > 0) {
        $Avito->updateStocks([$products]);
        $Avito->updatePrices($products);
    }
}

$addHandler = array(
    'actionStart' => 'addAvitoProductTab',
    'actionDelete' => false,
    'actionSave' => 'avitoSave',
    'actionUpdate' => 'avitoUpdate'
);
