<?php

function addYandexkassa($data) {
    global $PHPShopGUI;

    $vat[] = array(__('Общий'), 0, $data['yandex_vat_code']);
    $vat[] = array(__('Без НДС'), 1, $data['yandex_vat_code']);
    $vat[] = array(__('НДС по ставке 0%'), 2, $data['yandex_vat_code']);
    $vat[] = array(__('НДС по ставке 10%'), 3, $data['yandex_vat_code']);
    $vat[] = array(__('НДС чека по ставке 20%'), 4, $data['yandex_vat_code']);
    $vat[] = array(__('НДС чека по расчетной ставке 10/110'), 5, $data['yandex_vat_code']);
    $vat[] = array(__('НДС чека по расчетной ставке 20/120'), 6, $data['yandex_vat_code']);
    $vat[] = array(__('НДС по ставке 5%'), 7, $data['yandex_vat_code']);
    $vat[] = array(__('НДС по ставке 7%'), 8, $data['yandex_vat_code']);
    $vat[] = array(__('НДС по расчетной ставке 5/105'), 9, $data['yandex_vat_code']);
    $vat[] = array(__('НДС по расчетной ставке 7/107'), 10, $data['yandex_vat_code']);
    $vat[] = array(__('НДС по ставке 22%'), 11, $data['yandex_vat_code']);
    $vat[] = array(__('НДС по расчетной ставке 22/122'), 12, $data['yandex_vat_code']);

    $Tab3 .= $PHPShopGUI->setField('НДС', $PHPShopGUI->setSelect('yandex_vat_code_new', $vat));

    $PHPShopGUI->addTab(array("ЮKassa", $Tab3, true));
}

function addYandexkassaOptions($data) {
    global $PHPShopGUI;

    //$PHPShopGUI->field_col = 5;

    $vat[] = array(__('Общий'), 0, $data['yandex_vat_code']);
    $vat[] = array(__('Без НДС'), 1, $data['yandex_vat_code']);
    $vat[] = array(__('НДС по ставке 0%'), 2, $data['yandex_vat_code']);
    $vat[] = array(__('НДС по ставке 10%'), 3, $data['yandex_vat_code']);
    $vat[] = array(__('НДС чека по ставке 20%'), 4, $data['yandex_vat_code']);
    $vat[] = array(__('НДС чека по расчетной ставке 10/110'), 5, $data['yandex_vat_code']);
    $vat[] = array(__('НДС чека по расчетной ставке 20/120'), 6, $data['yandex_vat_code']);
    $vat[] = array(__('НДС по ставке 5%'), 7, $data['yandex_vat_code']);
    $vat[] = array(__('НДС по ставке 7%'), 8, $data['yandex_vat_code']);
    $vat[] = array(__('НДС по расчетной ставке 5/105'), 9, $data['yandex_vat_code']);
    $vat[] = array(__('НДС по расчетной ставке 7/107'), 10, $data['yandex_vat_code']);
    $vat[] = array(__('НДС по ставке 22%'), 11, $data['yandex_vat_code']);
    $vat[] = array(__('НДС по расчетной ставке 22/122'), 12, $data['yandex_vat_code']);

    $Tab3 .= $PHPShopGUI->setField('НДС', $PHPShopGUI->setSelect('yandex_vat_code_new', $vat, '100%', false, false, false, false, 1, false, false, 'form-control hidden-edit'));

    $PHPShopGUI->addTab(array("ЮKassa", $Tab3, true));
}

$addHandler = array(
    'actionStart' => 'addYandexkassa',
    'actionDelete' => false,
    'actionUpdate' => false,
    'actionOptionEdit' => 'addYandexkassaOptions'
);
?>