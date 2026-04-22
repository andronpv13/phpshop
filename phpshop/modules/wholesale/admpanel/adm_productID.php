<?php

function addModWholesale($data) {
    global $PHPShopGUI, $ed_izm;

    include_once '../.' . $GLOBALS['SysValue']['class']['wholesale'];
    $GLOBALS['PHPShopWholesale'] = new PHPShopWholesale();
    $opt = $GLOBALS['PHPShopWholesale']->getOpt($data);

    // Товар участвует в акции
    if (is_array($opt)) {

        // Единица измерения
        if (empty($data['ed_izm']))
            $ed_izm = 'шт.';
        else
            $ed_izm = $data['ed_izm'];

        // Скидка
        if ($opt['tip'] == 0) {
            $Tab = $PHPShopGUI->setField('Количество товара', $PHPShopGUI->setInputText(null, 'wholesale_check_new', $data['wholesale_check'], 100, $ed_izm));
            $Tab .= $PHPShopGUI->setField('Скидка', $PHPShopGUI->setInputText(null, 'wholesale_discount_new', $data['wholesale_discount'], 100, '%'));
        } 
        // Колонка
        else{
            $Tab = $PHPShopGUI->setField('Количество товара', $PHPShopGUI->setInputText(null, 'wholesale_check_new', $data['wholesale_check'], 100, $ed_izm));
            $Tab .= $PHPShopGUI->setField("Колонка цен", $PHPShopGUI->setSelect('wholesale_price_new', $PHPShopGUI->setSelectValue($data['wholesale_price'], 5), 100));
        }

        $PHPShopGUI->addTab(array("Оптовая цена", $Tab, true));
    }
}

$addHandler = array(
    'actionStart' => 'addModWholesale',
    'actionDelete' => false,
    'actionUpdate' => false
);
?>