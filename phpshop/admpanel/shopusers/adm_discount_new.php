<?php

$TitlePage = __('Создание скидки');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['discount']);
PHPShopObj::loadClass(['user', 'category']);

// Построение дерева категорий
function treegenerator($array, $i, $curent, $dop_cat_array) {
    global $tree_array;
    $del = '&brvbar;&nbsp;&nbsp;&nbsp;';
    $tree_select = $tree_select_dop = $check = false;

    $del = str_repeat($del, $i);
    if (!empty($array['sub']) and is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {

            $check = treegenerator(@$tree_array[$k], $i + 1, $k, $dop_cat_array);

            $selected = null;
            $disabled = null;

            if (is_array($dop_cat_array))
                foreach ($dop_cat_array as $vs) {
                    if ($k == $vs)
                        $selected = "selected";
                }

            if (empty($check['select'])) {
                $tree_select .= '<option value="' . $k . '" ' . $selected . $disabled . '>' . $del . $v . '</option>';

                $i = 1;
            } else {
                $tree_select .= '<option value="' . $k . '" ' . $selected . ' disabled>' . $del . $v . '</option>';
            }

            $tree_select .= $check['select'];
        }
    }
    return array('select' => $tree_select);
}

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage;

    // Начальные данные
    $data['enabled'] = 1;
    $data = $PHPShopGUI->valid($data,'action','block_categories','sum','discount','block_old_price');

    // Размер названия поля
    $PHPShopGUI->field_col = 4;
    $PHPShopGUI->setActionPanel(__("Покупатели") . ' / ' . $TitlePage, false, array('Сохранить и закрыть', 'Создать и редактировать'));

    $action_value[] = array('Максимально возможная скидка', 1, $data['action']);
    $action_value[] = array('Суммировать с пользовательской скидкой', 2, $data['action']);

    $PHPShopCategoryArray = new PHPShopCategoryArray();
    $CategoryArray = $PHPShopCategoryArray->getArray();
    $GLOBALS['count'] = count($CategoryArray);

    $CategoryArray[0]['name'] = '- ' . __('Корневой уровень') . ' -';
    $tree_array = array();

    foreach ($PHPShopCategoryArray->getKey('parent_to.id', true) as $k => $v) {
        foreach ($v as $cat) {
            $tree_array[$k]['sub'][$cat] = $CategoryArray[$cat]['name'];
        }
        $tree_array[$k]['name'] = $CategoryArray[$k]['name'];
        $tree_array[$k]['id'] = $k;
        if (!empty($data['parent_to']) and $k == $data['parent_to'])
            $tree_array[$k]['selected'] = true;
    }

    $GLOBALS['tree_array'] = &$tree_array;

    $blockCategories = preg_split('/,/', $data['block_categories'], -1, PREG_SPLIT_NO_EMPTY);
    $tree_select=null;

    if (is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator(@$tree_array[$k], 1, $k, $blockCategories);

            // Допкаталоги
            $selected = null;
            if (is_array($blockCategories))
                foreach ($blockCategories as $vs) {
                    if ($k == $vs)
                        $selected = "selected";
                }

            if (empty($tree_array[$k]))
                $disabled = null;
            else
                $disabled = ' disabled';

            $tree_select .= '<option value="' . $k . '"  ' . $selected . $disabled . '>' . $v . '</option>';

            $tree_select .= $check['select'];
        }

    $tree_select = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container=""  data-style="btn btn-default btn-sm" name="block_categories[]"  data-width="300px" multiple>' . $tree_select . '</select>';

    // Содержание закладки 1
    $Tab1 = $PHPShopGUI->setCollapse(__('Информация'), $PHPShopGUI->setField("Сумма", $PHPShopGUI->setInput('text.required', "sum_new", $data['sum'], null, 100)) .
            $PHPShopGUI->setField("Скидка", $PHPShopGUI->setInputText('%', "discount_new", $data['discount'], 100)) .
            $PHPShopGUI->setField("Статус", $PHPShopGUI->setRadio("enabled_new", 1, "Вкл.", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new", 0, "Выкл.", $data['enabled'])) .
            $PHPShopGUI->setField("Формула расчета", $PHPShopGUI->setSelect('action_new', $action_value, 300)) .
            $PHPShopGUI->setField("Не учитывать товары с категорий", $tree_select) .
            $PHPShopGUI->setField("Старая цена", $PHPShopGUI->setCheckbox("block_old_price_new", 1, "Игнорировать товары со старой ценой", $data['block_old_price']))
            , 'in', false);

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1,true,false,'block-grid'));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "ОК", "right", 70, "", "but", "actionInsert.shopusers.create");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция записи
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules;

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $_POST['block_categories_new'] = "";
    if (is_array($_POST['block_categories']) and $_POST['block_categories'][0] != 'null') {
        foreach ($_POST['block_categories'] as $v)
            if (!empty($v) and !strstr($v, ','))
                $_POST['block_categories_new'] .= $v . ",";
    }

    $action = $PHPShopOrm->insert($_POST);

    if ($_POST['saveID'] == 'Создать и редактировать')
        header('Location: ?path=' . $_GET['path'] . '&id=' . $action);
    else
        header('Location: ?path=' . $_GET['path']);

    return $action;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>