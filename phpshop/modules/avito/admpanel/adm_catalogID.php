<?php

include_once dirname(__FILE__) . '/../class/Avito.php';

function addAvitoTab($data) {
    global $PHPShopGUI, $PHPShopModules;

    // Проверка на каталог страниц
    if (isset($data['skin_enabled'])) {

        // Проверка на подкаталоги
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
        $data_categories = $PHPShopOrm->getOne(['id'], ['parent_to' => '=' . (int) $data['id']]);
        if (is_array($data_categories))
            return false;

        $PHPShopGUI->addJSFiles('../modules/avito/admpanel/gui/avito.gui.js');
        $category_avitoapi = (new PHPShopOrm($PHPShopModules->getParam("base.avito.avitoapi_categories")))->getOne(['name', 'parent_to', 'id'], ['id' => '="' . $data['category_avitoapi'] . '"']);

        if (!empty($category_avitoapi['name']))
            $value = $category_avitoapi['name'];

        $tree_select = '
        <input data-set="3" name="category_avitoapi" class="search_avitocategory form-control input-sm" type="search" data-trigger="manual" data-container="body" data-toggle="popover" data-placement="bottom" data-html="true"  data-content="" placeholder="' . __('Найти...') . '" value="' . $value . '"><input name="category_avitoapi_new" type="hidden" value="' . $data['category_avitoapi'] . '">';

        // Размещение
        $Tab1 = $PHPShopGUI->setCollapse('Размещение в Avito', $tree_select);


        // Характеристики локальные
        $sort = unserialize($data['sort']);
        if (is_array($sort)) {
            $PHPShopSort = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
            $sort_data = $PHPShopSort->getList($select = array('id,name,attribute_avitoapi'), array('id' => ' IN(' . implode(',', $sort) . ')'), array('order' => 'num,name'));
        }

        // Характеристики с Avito
        $Avito = new Avito();
        $sort_avito_data = $Avito->getTreeAttribute($data['category_avitoapi']);

        // Пропускаем
        $stop_sorts = ['Id', 'Address', 'Title', 'Description', 'Images', 'ImageUrls', 'ImageNames', 'Category', 'GoodsType', 'ProductType', 'GoodsSubType', 'AdType', 'Condition'];

        if (is_array($sort_avito_data['fields'])) {
            foreach ($sort_avito_data['fields'] as $sort_key => $sort_avito_value) {


                $name = PHPShopString::utf8_win1251($sort_avito_value['label']);

                if ($sort_avito_value['content'][0]['required'] != 1 or in_array($sort_avito_value['tag'], $stop_sorts))
                    continue;

                $sort_select_value = [];
                if (is_array($sort_data)) {
                    $sort_select_value[] = array(__('Ничего не выбрано'), 0, $sort_avito_value['tag']);
                    foreach ($sort_data as $sort_value) {

                        if ($sort_avito_value['tag'] == $sort_value['attribute_avitoapi'])
                            $sel = 'selected';
                        else
                            $sel = null;

                        $sort_select_value[] = array($sort_value['name'], $sort_value['id'], $sel);
                    }
                }

                // Доступные значения
                if (is_array($sort_avito_value['content'][0]['values']))
                    $help_list = [];


                if (is_array($sort_avito_value['content'][0]['values']))
                    foreach ($sort_avito_value['content'][0]['values'] as $avito_value) {
                        $help_list[] = PHPShopString::utf8_win1251($avito_value['value']);
                    }

                if (count($help_list) > 0)
                    $help = '<a data-toggle="collapse" href="#collapseAvitoValue' . $sort_key . '" aria-expanded="false" aria-controls="collapseExample">' . __('Доступные значения') . '</a><div class="collapse" id="collapseAvitoValue' . $sort_key . '"><div class="well well-sm">' . implode('<br>', $help_list) . '</div></div>';
                else
                    $help = PHPShopString::utf8_win1251($sort_avito_value['descriptions']);

                $Tab2 .= $PHPShopGUI->setField($name, $PHPShopGUI->setSelect('attribute_avitoapi[' . $sort_avito_value['tag'] . ']', $sort_select_value, '100%') . $PHPShopGUI->setHelp(PHPShopString::utf8_win1251($sort_avito_value['descriptions']) . '<br>' . $help, false, false), 1, $sort_avito_value['tag'], null, 'control-label', false);
            }

            if (!is_array($sort_select_value))
                $Tab2 = $PHPShopGUI->setHelp('Дополнительные характеристики не требуются');
        } else {
            $Tab2 = $PHPShopGUI->setHelp('Выберите размещение в Avito для сопоставления характеристик и перегрузите страницу');
        }


        // Сопоставление характеристик
        $Tab2 = $PHPShopGUI->setCollapse('Сопоставление характеристик с Avito', $Tab2);


        $PHPShopGUI->addTabSeparate(array("Avito", $Tab1 . $Tab2, true));
    }
}

function avitoUpdate() {

    if (is_array($_POST['attribute_avitoapi'])) {
        $PHPShopSort = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
        $PHPShopSort->debug = false;
        foreach ($_POST['attribute_avitoapi'] as $k => $v) {
            if (!empty($v)) {

                // Очистка старых значений
                $PHPShopSort->update(['attribute_avitoapi_new' => ''], ['attribute_avitoapi' => '="' . $k . '"']);

                // Новое значение
                $PHPShopSort->update(['attribute_avitoapi_new' => $k], ['id' => '=' . intval($v)]);
            }
        }
    }
}

$addHandler = array(
    'actionStart' => 'addAvitoTab',
    'actionDelete' => false,
    'actionUpdate' => 'avitoUpdate'
);
