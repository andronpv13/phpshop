<?php

function addMegamarketProductTab($data) {
    global $PHPShopGUI;

    // Очистка при копировании товара
    $data['export_megamarket'] = $data['price_megamarket'] = 0;


    // Размер названия поля
    $PHPShopGUI->field_col = 4;

    $tab = $PHPShopGUI->setField(null, $PHPShopGUI->setCheckbox('export_megamarket_new', 1, 'Включить экспорт в ММ', $data['export_megamarket']));

    // Валюты
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            if ($data['baseinputvaluta'] == $val['id']) {
                $valuta_def_name = $val['code'];
            }
        }

    $tab .= $PHPShopGUI->setField('Цена ММ', $PHPShopGUI->setInputText(null, 'price_megamarket_new', $data['price_megamarket'], 150, $valuta_def_name), 2);

    $PHPShopGUI->addTab(array("Мегамаркет", $tab, true));
}

$addHandler = array(
    'actionStart' => 'addMegamarketProductTab',
);
?>