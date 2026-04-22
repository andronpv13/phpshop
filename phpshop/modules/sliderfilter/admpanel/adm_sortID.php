<?php

function addSliderfilter($data) {
    global $PHPShopGUI;

    if (!empty($data['filtr'])) {
        $Tab3 = $PHPShopGUI->setField('Вывод', $PHPShopGUI->setRadio('sliderfilter_enabled_new', 0, 'Выключить', $data['sliderfilter_enabled'], false, 'text-warning') .
                $PHPShopGUI->setRadio('sliderfilter_enabled_new', 1, 'Включить', $data['sliderfilter_enabled']));

        $PHPShopGUI->addTab(array("Слайдер", $Tab3, true));
    }
}

function updateSliderfilter($data) {
    $_POST['name_value']= str_replace(',', '.', $_POST['name_value']);

}

$addHandler = array(
    'actionStart' => 'addSliderfilter',
    'actionDelete' => null,
    'actionUpdate' => null
);
?>