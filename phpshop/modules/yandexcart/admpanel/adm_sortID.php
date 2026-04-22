<?php

function addYandexcartSort($data) {
    global $PHPShopGUI;

    $Tab3=$PHPShopGUI->setField('Яндекс.Маркет', $PHPShopGUI->setRadio('yandex_param_new', 1, 'Выключить', $data['yandex_param'], false, 'text-warning') .
            $PHPShopGUI->setRadio('yandex_param_new', 2, 'Включить', $data['yandex_param']),1,'Выгружать характеристику для Яндекс.Маркет');
    
    
    $Tab3.= $PHPShopGUI->setField("Единица измерения", $PHPShopGUI->setInputText(null, 'yandex_param_unit_new', $data['yandex_param_unit'], 100));

    $PHPShopGUI->addTab(array("Яндекс.Маркет", $Tab3, true));
}

$addHandler = array(
    'actionStart' => 'addYandexcartSort',
    'actionDelete' => false,
    'actionUpdate' => false
);
?>