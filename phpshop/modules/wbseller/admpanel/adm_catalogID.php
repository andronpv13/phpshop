<?php

include_once dirname(__FILE__) . '/../class/WbSeller.php';

function addWbsellerTab($data) {
    global $PHPShopGUI, $category_wbseller;

    // Проверка на каталог страниц
    if (isset($data['skin_enabled'])) {

        // Проверка на подкаталоги
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
        $data_categories = $PHPShopOrm->getOne(['id'], ['parent_to' => '=' . (int) $data['id']]);
        if (is_array($data_categories))
            return false;

        $WbSeller = new WbSeller();
        $PHPShopGUI->addJSFiles('../modules/wbseller/admpanel/gui/order.gui.js');

        $tree_select = '
        <input data-set="3" name="category_wbseller_new" class="search_wbcategory form-control input-sm" type="search" data-trigger="manual" data-container="body" data-toggle="popover" data-placement="bottom" data-html="true"  data-content="" placeholder="' . __('Найти...') . '" value="' . $data['category_wbseller'] . '"><input type="hidden" name="category_wbseller_id_new" value="'.$data['category_wbseller_id'].'">';


        // Размещение
        $Tab1 = $PHPShopGUI->setCollapse('Размещение в WB', $tree_select);


        // Характеристики локальные
        $sort = unserialize($data['sort']);
        if (is_array($sort)) {
            $PHPShopSort = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
            $sort_data = $PHPShopSort->getList($select = array('id,name,attribute_wbseller'), array('id' => ' IN(' . implode(',', $sort) . ')'), array('order' => 'num,name'));
        }


        // Характеристики с Wb
        $Tab2 = null;
        $sort_wb_data = $WbSeller->getTreeAttribute($data['category_wbseller_id'])['data'];
        if (is_array($sort_wb_data)) {
            foreach ($sort_wb_data as $sort_wb_value) {
                
                if(empty($sort_wb_value['required']) and empty($sort_wb_value['popular']))
                    continue;
                else {
                    
                    if(!empty($sort_wb_value['required']))
                           $help=__('Обязательное');
                    if(!empty($sort_wb_value['popular']))
                           $help=__('Популярное');
                    else $help=null;
                }
                
                $name = PHPShopString::utf8_win1251($sort_wb_value['name']);
                
                $sort_select_value = [];
                if (is_array($sort_data)) {
                    $sort_select_value[] = array(__('Ничего не выбрано'), 0, $name);
                    foreach ($sort_data as $sort_value) {
                        
                        if($sort_value['attribute_wbseller'] == $sort_wb_value['charcID'])
                            $sel = 'selected';
                        else $sel=null;

                        $sort_select_value[] = array($sort_value['name'], $sort_value['id'], $sel);
                    }
                }
                
                $Tab2 .= $PHPShopGUI->setField($name, $PHPShopGUI->setSelect('attribute_wbseller[' . $sort_wb_value['charcID'] . ']', $sort_select_value, '100%'), 1, $help, null, 'control-label', false);
            }
        } else {
            $Tab2 = $PHPShopGUI->setHelp('Выберите размещение в WB для сопоставления характеристик и перегрузите страницу');
        }


        // Сопоставление характеристик
        $Tab2 = $PHPShopGUI->setCollapse('Сопоставление характеристик с WB', $Tab2);

        $PHPShopGUI->addTabSeparate(array("WB", $Tab1 . $Tab2, true));
    }
}


function updateWbseller() {
    if (is_array($_POST['attribute_wbseller'])) {
        $PHPShopSort = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
        $PHPShopSort->debug = false;
        foreach ($_POST['attribute_wbseller'] as $k => $v) {
            if (!empty($v)) {
                
                // Очистка старых значений
                $PHPShopSort->update(['attribute_wbseller_new' => null], ['attribute_wbseller' => '="' . (int)$k.'"']);
                
                // Новое значение
                $PHPShopSort->update(['attribute_wbseller_new' => (int)$k], ['id' => '="' .(int) $v.'"']);
            }
        }
    }
}

$addHandler = array(
    'actionStart' => 'addWbsellerTab',
    'actionDelete' => false,
    'actionUpdate' => 'updateWbseller'
);
?>