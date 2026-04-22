<?php

PHPShopObj::loadClass("category");

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.gift.gift_forms"));

// Построение дерева категорий
function treegenerator($array, $i, $curent, $dop_cat_array) {
    global $tree_array;
    $del = '&brvbar;&nbsp;&nbsp;&nbsp;&nbsp;';
    $tree_select = $tree_select_dop = $check = false;

    $del = str_repeat($del, $i);
    if (is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {

            $check = treegenerator($tree_array[$k], $i + 1, $k, $dop_cat_array);

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
                $tree_select .= '<option value="' . $k . '" ' . $selected . $disabled . ' >' . $del . $v . '</option>';
            }

            $tree_select .= $check['select'];
        }
    }
    return array('select' => $tree_select);
}

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopOrm;

    $data['name'] = "Новый подарок";
    $data['enabled'] = 1;

    // Выбор даты
    $PHPShopGUI->addJSFiles('./js/jquery.tagsinput.min.js', './js/bootstrap-datetimepicker.min.js', './promotions/gui/promotions.gui.js');
    $PHPShopGUI->addCSSFiles('./css/jquery.tagsinput.css', './css/bootstrap-datetimepicker.min.css');

    $PHPShopGUI->field_col = 3;

    $Tab1 = $PHPShopGUI->setCollapse('Основное', $PHPShopGUI->setField('Название', $PHPShopGUI->setInputText('', 'name_new', $data['name'])) .
            $PHPShopGUI->setField('Статус', $PHPShopGUI->setRadio("enabled_new", 1, "Показывать", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new", 0, "Скрыть", $data['enabled'])).
            $PHPShopGUI->setField('Формула',
                    $PHPShopGUI->setRadio("discount_tip_new", 0, "При заказе товара А, получи в подарок товар В", $data['discount_tip']) . '<br>'.
                    $PHPShopGUI->setRadio("discount_tip_new", 1, "При заказе N количества товара А, получи в подарок M количество товара А", $data['discount_tip']). '<br>'.
            $PHPShopGUI->setRadio("discount_tip_new", 2, "При заказе получи в подарок товар A", $data['discount_tip'])).
            $PHPShopGUI->setField('Лейбл на сайте', $PHPShopGUI->setInputText('', 'label_new', $data['label']))
            );


    $Tab1 .= $PHPShopGUI->setCollapse('Активность', $PHPShopGUI->setField('Начало', $PHPShopGUI->setInputDate("active_date_ot_new", $data['active_date_ot'])) . $PHPShopGUI->setField('Завершение', $PHPShopGUI->setInputDate("active_date_do_new", $data['active_date_do'])));


    $PHPShopCategoryArray = new PHPShopCategoryArray($where);
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
        if ($k == $data['parent_to'])
            $tree_array[$k]['selected'] = true;
    }

    $GLOBALS['tree_array'] = &$tree_array;

    // Допкаталоги
    $dop_cat_array = preg_split('/,/', $data['categories'], -1, PREG_SPLIT_NO_EMPTY);

    if (is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator($tree_array[$k], 1, $k, $dop_cat_array);


            // Допкаталоги
            $selected = null;
            if (is_array($dop_cat_array))
                foreach ($dop_cat_array as $vs) {
                    if ($k == $vs)
                        $selected = "selected";
                }


            $disabled = null;

            $tree_select .= '<option value="' . $k . '"  ' . $selected . $disabled . '>' . $v . '</option>';

            $tree_select .= $check['select'];
        }


    $tree_select = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container=""  data-style="btn btn-default btn-sm" name="categories[]"  data-width="100%" multiple>' . $tree_select . '</select>';


    $Tab1 .= $PHPShopGUI->setCollapse('Условия', 
            $PHPShopGUI->setField('Категории', $PHPShopGUI->setHelp('Выберите категории товаров и/или укажите ID товаров для акции.') .
                    $PHPShopGUI->setCheckbox("categories_check_new", 1, "Учитывать категории товара", $data['categories_check']) . '<br>'.
                    $PHPShopGUI->setCheckbox("categories_all", 1, "Выбрать все категории?", 0). '<br><br>' .
                    $tree_select) .
            $PHPShopGUI->setField('Товары', $PHPShopGUI->setCheckbox("products_check_new", 1, "Учитывать товары", $data['products_check']) . '<br>'.
                    $PHPShopGUI->setCheckbox("block_old_price_new", 1, "Игнорировать товары со старой ценой", $data['block_old_price']) . '<br><br>' .
                    $PHPShopGUI->setTextarea('products_new', $data['products'], false, false, false, __('Укажите ID товаров или воспользуйтесь') . ' <a href="#" data-target="#products_new"  class="btn btn-sm btn-default tag-search"><span class="glyphicon glyphicon-search"></span> ' . __('поиском товаров') . '</a>')) 
           
    );

    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"), true);
    
    $oFCKeditor = new Editor('description_new', true);
    $oFCKeditor->Height = '120';
    $oFCKeditor->ToolbarSet = 'Normal';
    $oFCKeditor->Value = $data['description'];

   $Tab2 = $PHPShopGUI->setCollapse('Описание акции на сайте',$oFCKeditor->AddGUI());

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true,false,true), array("Дополнительно", $Tab2,true,false,true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "Сохранить", "right", false, false, false, "actionInsert.modules.create");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция записи
function actionInsert() {
    global $PHPShopOrm;

    $_POST['categories_new'] = "";
    if (is_array($_POST['categories']) and $_POST['categories'][0] != 'null') {
        
        $_POST['categories_check_new']=1;
        
        foreach ($_POST['categories'] as $v)
            if (!empty($v) and ! strstr($v, ','))
                $_POST['categories_new'] .= $v . ",";
    }

    // Убираем лишние настройки 
    if (!empty($_POST['active_date_do_new']))
        $_POST['active_check_new'] = 1;
    else
        $_POST['active_check_new'] = 0;

    $_POST['discount_check_new'] = 1;

    $PHPShopOrm->updateZeroVars('discount_tip_new', 'products_check_new', 'categories_check_new', 'block_old_price_new', 'active_check_new', 'enabled_new');

    $action = $PHPShopOrm->insert($_POST);
    header('Location: ?path=' . $_GET['path']);
    return $action;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>