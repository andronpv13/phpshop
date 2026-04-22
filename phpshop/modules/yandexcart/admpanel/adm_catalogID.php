<?php

function addYandexcart($data) {
    global $PHPShopGUI;

    // Проверка на каталог страниц
    if (isset($data['skin_enabled'])) {

        // Проверка на подкаталоги
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
        $data_categories = $PHPShopOrm->getOne(['id'], ['parent_to' => '=' . (int) $data['id']]);
        if (is_array($data_categories))
            return false;

       $PHPShopGUI->addJSFiles('../modules/yandexcart/admpanel/gui/yandexcart.gui.js');
       $category_yandexcart_parent = (new PHPShopOrm('phpshop_modules_yandexcart_categories'))->getOne(['name','parent_to','id'],['id'=>'='.$data['category_yandexcart']]);
        
        if(!empty($category_yandexcart_parent['name']))
        $value = $category_yandexcart_parent['name'];
        
        $tree_select = '
        <input data-set="3" name="category_yandexcart" class="search_yandexcartcategory form-control input-sm" type="search" data-trigger="manual" data-container="body" data-toggle="popover" data-placement="bottom" data-html="true"  data-content="" placeholder="' . __('Найти...') . '" value="' . $value . '"><input name="category_yandexcart_new" type="hidden" value="' . $data['category_yandexcart'] . '">';

        // Размещение
        $Tab1 = $PHPShopGUI->setCollapse('Размещение в Яндекс.Маркет', $tree_select);

        $PHPShopGUI->addTabSeparate(array("Яндекс.Маркет", $Tab1, true));
    }
}


$addHandler = array(
    'actionStart' => 'addYandexcart',
    'actionDelete' => false,
    'actionUpdate' => false
);