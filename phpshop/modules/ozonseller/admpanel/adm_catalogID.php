<?php

include_once dirname(__FILE__) . '/../class/OzonSeller.php';

function addOzonsellerTab($data) {
    global $PHPShopGUI, $category_ozonseller;

    // Проверка на каталог страниц
    if (isset($data['skin_enabled'])) {

        // Проверка на подкаталоги
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
        $data_categories = $PHPShopOrm->getOne(['id'], ['parent_to' => '=' . (int) $data['id']]);
        if (is_array($data_categories))
            return false;

        $OzonSeller = new OzonSeller();

        $PHPShopGUI->addJSFiles('../modules/ozonseller/admpanel/gui/order.gui.js');
        $category_ozonseller = (new PHPShopOrm('phpshop_modules_ozonseller_type'))->getOne(['name','parent_to','id'],['id'=>'='.$data['category_ozonseller']]);
        $category_ozonseller_parent = (new PHPShopOrm('phpshop_modules_ozonseller_categories'))->getOne(['name','parent_to','id'],['id'=>'='.$category_ozonseller['parent_to']]);
        
        if(!empty($category_ozonseller_parent['name']))
        $value = $category_ozonseller_parent['name'].' - '.$category_ozonseller['name'];
        
        $tree_select = '
        <input data-set="3" name="category_ozonseller" class="search_ozoncategory form-control input-sm" type="search" data-trigger="manual" data-container="body" data-toggle="popover" data-placement="bottom" data-html="true"  data-content="" placeholder="' . __('Найти...') . '" value="' . $value . '"><input name="category_ozonseller_new" type="hidden" value="' . $data['category_ozonseller'] . '">';

        // Размещение
        $Tab1 = $PHPShopGUI->setCollapse('Размещение в OZON', $tree_select);

        // Характеристики локальные
        $sort = unserialize($data['sort']);
        if (is_array($sort)) {
            $PHPShopSort = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
            $sort_data = $PHPShopSort->getList($select = array('id,name,attribute_ozonseller'), array('id' => ' IN(' . implode(',', $sort) . ')'), array('order' => 'num,name'));
        }

        // Характеристики с Ozon
        $Tab2 = null;
        $sort_ozon_data = $OzonSeller->getTreeAttribute(["description_category_id" => $category_ozonseller['parent_to'],"type_id"=>$category_ozonseller['id']]);

        if (is_array($sort_ozon_data['result'])) {
            foreach ($sort_ozon_data['result'] as $sort_ozon_value) {
                $name = PHPShopString::utf8_win1251($sort_ozon_value['name']);
                
                if($sort_ozon_value['is_required'] != 1 or $name == 'Тип' or $name == 'Название')
                    continue;

                $sort_select_value = [];
                if (is_array($sort_data)) {
                    $sort_select_value[] = array(__('Ничего не выбрано'), 0, $sort_ozon_value['id']);
                    foreach ($sort_data as $sort_value) {

                        if ($sort_ozon_value['id'] == $sort_value['attribute_ozonseller'])
                            $sel = 'selected';
                        else
                            $sel = null;

                        $sort_select_value[] = array($sort_value['name'], $sort_value['id'], $sel);
                    }
                }

                $help_list = $OzonSeller->getAttributesValues($sort_ozon_value['id'], $category_ozonseller['parent_to'], null, true,$category_ozonseller['id']);
                if (count($help_list) > 0)
                    $help = '<a data-toggle="collapse" href="#collapseOzonValue' . $sort_ozon_value['id'] . '" aria-expanded="false" aria-controls="collapseExample">' . __('Доступные значения') . '</a><div class="collapse" id="collapseOzonValue' . $sort_ozon_value['id'] . '"><div class="well well-sm">' . implode('<br>', $help_list) . '</div></div>';
                else
                    $help = null;

                //if (empty($sort_ozon_value['dictionary_id']) and $name == 'Название') {
                    //continue;
                    //$sort_ozon_value['description'] = __('Будет заполнено автоматически из имени товара.');
                //}

                $Tab2 .= $PHPShopGUI->setField($name, $PHPShopGUI->setSelect('attribute_ozonseller[' . $sort_ozon_value['id'] . ']', $sort_select_value, '100%') . $PHPShopGUI->setHelp(PHPShopString::utf8_win1251($sort_ozon_value['description']) . '<br>' . $help,false,false),1,  $sort_ozon_value['id'], null,'control-label', false);
            }
        } else {
            $Tab2 = $PHPShopGUI->setHelp('Выберите размещение в OZON для сопоставления характеристик и перегрузите страницу');
        }


        // Сопоставление характеристик
        $Tab2 = $PHPShopGUI->setCollapse('Сопоставление характеристик с OZON', $Tab2);

        $PHPShopGUI->addTabSeparate(array("OZON", $Tab1 . $Tab2, true));
    }
}

function treegenerator_ozonseller($array, $i, $curent) {
    global $tree_array, $category_ozonseller;
    $del = '&brvbar;&nbsp;&nbsp;&nbsp;&nbsp;';
    $tree_select = $check = false;

    $del = str_repeat($del, $i);
    if (!empty($array) and is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {

            $check = treegenerator_ozonseller($tree_array[$k], $i + 1, $k);

            if ($k == $category_ozonseller)
                $selected = 'selected';
            else
                $selected = null;

            if (empty($check)) {
                $disabled = null;
                $tree_select .= '<option value="' . $k . '" ' . $selected . $disabled . '>' . $del . $v . '</option>';

                $i = 1;
            } else {
                 $disabled = ' disabled ';
                 $tree_select .= '<option value="' . $k . '" ' . $selected . $disabled . '>' . $del . $v . '</option>';
            }

            $tree_select .= $check;
        }
    }
    return $tree_select;
}


function updateOzonseller() {
    if (is_array($_POST['attribute_ozonseller'])) {
        $PHPShopSort = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
        $PHPShopSort->debug = false;
        foreach ($_POST['attribute_ozonseller'] as $k => $v) {
            if (!empty($v)) {
                
                // Очистка старых значений
                $PHPShopSort->update(['attribute_ozonseller_new' => null], ['attribute_ozonseller' => '=' . intval($k)]);
                
                // Новое значение
                $PHPShopSort->update(['attribute_ozonseller_new' => $k], ['id' => '=' . intval($v)]);
            }
        }
    }
}

class PHPShopCategoryOzonArray extends PHPShopArray {

    function __construct($sql = false, $select = ["id", "name", "parent_to"]) {
        global $PHPShopModules;

        $this->objSQL = $sql;
        $GLOBALS['SysValue']['my']['array_limit'] = 1000000;
        $this->cache = false;
        $this->debug = false;
        $this->ignor = false;
        $this->order = ['order' => 'name'];
        $this->objBase = $PHPShopModules->getParam("base.ozonseller.ozonseller_categories");
        parent::__construct(...$select);
    }

}

$addHandler = array(
    'actionStart' => 'addOzonsellerTab',
    'actionDelete' => false,
    'actionUpdate' => 'updateOzonseller'
);
?>