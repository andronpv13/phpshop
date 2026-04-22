<?php

function addVksellerProductTab($data) {
    global $PHPShopGUI;

    // Очистка при копировании товара
    $data['export_vk'] = 0;

    // Размер названия поля
    $PHPShopGUI->field_col = 4;

    $tab = $PHPShopGUI->setField(null, $PHPShopGUI->setCheckbox('export_vk_new', 1, 'Включить экспорт в VK', $data['export_vk']));

    // Валюты
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            if ($data['baseinputvaluta'] == $val['id']) {
                $valuta_def_name = $val['code'];
            }
        }
    $tab .= $PHPShopGUI->setField('Цена VK', $PHPShopGUI->setInputText(null, 'price_vk_new', $data['price_vk'], 150, $valuta_def_name), 2);

    $PHPShopGUI->addTab(array("ВКонтакте", $tab, true));
}

$addHandler = array(
    'actionStart' => 'addVksellerProductTab',
);
?>