<?php

function addModGift($data) {
    global $PHPShopGUI, $ed_izm;

    include_once '../.' . $GLOBALS['SysValue']['class']['gift'];
    $GLOBALS['PHPShopGift'] = new PHPShopGift();
    $gift = $GLOBALS['PHPShopGift']->getGift($data);

    // Товар участвует в акции
    if (is_array($gift)) {


        $PHPShopGUI->addJSFiles('../modules/gift/admpanel/gui/gift.gui.js');

        // A+B или A
        if ($gift['gift'] == 0 or $gift['gift'] == 2) {
            $Tab = $PHPShopGUI->setField('Товары в подарок', $PHPShopGUI->setTextarea('gift_new', $data['gift'], false, false, false, __('Укажите ID товаров или воспользуйтесь') . ' <a href="#" data-target="#gift_new"  class="btn btn-sm btn-default tag-search"><span class="glyphicon glyphicon-search"></span> ' . __('поиском товаров') . '</a>'));
        }
        // NA+MA
        elseif ($gift['gift'] == 1) {

            // Единица измерения
            if (empty($data['ed_izm']))
                $ed_izm = 'шт.';
            else
                $ed_izm = $data['ed_izm'];

            $Tab = $PHPShopGUI->setField('Количество товара', $PHPShopGUI->setInputText(null, 'gift_check_new', $data['gift_check'], 100, $ed_izm));
            $Tab .= $PHPShopGUI->setField('Количество подарка', $PHPShopGUI->setInputText(null, 'gift_items_new', $data['gift_items'], 100, $ed_izm));
        }

        $PHPShopGUI->addTab(array("Подарок", $Tab, true));
    }
}

$addHandler = array(
    'actionStart' => 'addModGift',
    'actionDelete' => false,
    'actionUpdate' => false
);
?>