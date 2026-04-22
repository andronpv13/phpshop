<?php

PHPShopObj::loadClass("category");

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.promotions.promotions_forms"));

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

    global $PHPShopGUI, $PHPShopSystem, $PHPShopOrm, $PHPShopModules;

    // Выборка
    $select = $PHPShopOrm->select(array('max(id) as end'));

    $data['name'] = "Новая скидка";
    $data['enabled'] = 1;

    // Выбор даты
    $PHPShopGUI->addJSFiles('./js/jquery.tagsinput.min.js', './js/bootstrap-datetimepicker.min.js', './promotions/gui/promotions.gui.js', '../modules/promotions/admpanel/gui/promotions.gui.js');
    $PHPShopGUI->addCSSFiles('./css/jquery.tagsinput.css', './css/bootstrap-datetimepicker.min.css');

    $PHPShopGUI->field_col = 3;

    $Tab1 = $PHPShopGUI->setCollapse('Основное', $PHPShopGUI->setField('Название', $PHPShopGUI->setInputText('', 'name_new', $data['name'], 300)) .
            $PHPShopGUI->setField('Статус', $PHPShopGUI->setRadio("enabled_new", 1, "Показывать", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new[]", 0, "Скрыть", $data['enabled']))
    );

    $Tab1 .= $PHPShopGUI->setCollapse('Активность', $PHPShopGUI->setField('Начало', $PHPShopGUI->setInputDate("active_date_ot_new", $data['active_date_ot'])) . $PHPShopGUI->setField('Завершение', $PHPShopGUI->setInputDate("active_date_do_new", $data['active_date_do'])));

    $Tab1 .= $PHPShopGUI->setCollapse('Скидка', $PHPShopGUI->setField('Тип', $PHPShopGUI->setRadio("discount_tip_new", 1, "%", $data['discount_tip']) . $PHPShopGUI->setRadio("discount_tip_new", 0, "сумма", $data['discount_tip']), 'left') .
            $PHPShopGUI->setField('Скидка', $PHPShopGUI->setInputText('', 'discount_new', $data['discount'], '100'))
    );

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.promotions.promotions_codes"));
    $PHPShopOrm->debug = false;
    $id = (int) $select['end'] + 1;
    $count_all = $PHPShopOrm->select(array('count("id") as count'), array('promo_id' => "='$id'"));

    $qty_all_count = $count_all['count'];

    $PHPShopOrm->clean();

    $count_active = $PHPShopOrm->select(array('count("id") as count'), array('promo_id' => "='$id'", 'enabled' => '="1"'));
    $qty_active_count = $count_active['count'];

    // способ оплаты
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['payment_systems']);
    $data_payment_systems = $PHPShopOrm->select(array('id,name'), false, array('order' => 'name'), array('limit' => 100));

    $value_payment_systems[] = array(__('Не выбрано'), 0, $data['delivery_method']);
    foreach ($data_payment_systems as $value) {
        if ($value['id'] == $data['delivery_method'])
            $sel = 'selected';
        else
            $sel = false;
        $value_payment_systems[] = array($value['name'], $value['id'], $sel);
    }


    $qty_all = $PHPShopGUI->setDiv(false, $qty_all_count, '" class="badge  badge-info', 'qty-all" data-toggle="tooltip" title="Всего промокодов');
    $qty_active = $PHPShopGUI->setDiv(false, $qty_active_count, '" class="badge btn-success', 'qty-active" data-toggle="tooltip" title="Активных промокодов');
    $qty_off_count = $qty_all_count - $qty_active_count;

    if ($qty_off_count > 0)
        $qty_off = '<button class="btn btn-danger btn-sm" type="button" id="qty_del" name="qty_del"> Удалить <span id="qty_off_count" data-count="' . $qty_off_count . '">' . $qty_off_count . '</span> использованных промокодов
</button>';

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

    if ($qty_all_count)
        $qty_all = $PHPShopGUI->setDiv(false, $qty_all_count, '" class="badge  badge-info', 'qty-all" data-toggle="tooltip" title="Всего промокодов');
    if ($qty_active_count)
        $qty_active = $PHPShopGUI->setDiv(false, $qty_active_count, '" class="badge btn-success', 'qty-active" data-toggle="tooltip" title="Активных промокодов');
    $qty_off_count = $qty_all_count - $qty_active_count;

    if ($qty_off_count > 0)
        $qty_off = '<button class="btn btn-danger btn-sm" type="button" id="qty_del" name="qty_del"> Удалить <span id="qty_off_count" data-count="' . $qty_off_count . '">' . $qty_off_count . '</span> использованных промокодов
</button>';

    // Статусы покупателя
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers_status']);
    $data_user_status = $PHPShopOrm->select(array('id,name'), false, array('order' => 'name'), array('limit' => 100));
    $status_array = unserialize($data['statuses']);
    $data_user_status[]=array('id' => '-', 'name' => __('Покупатели без статуса'));

    foreach ($data_user_status as $value) {
        if (is_array($status_array) && in_array($value['id'], $status_array))
            $sel = 'selected';
        else
            $sel = false;
        $value_user_status[] = array($value['name'], $value['id'], $sel);
    }

    $Tab1 .= $PHPShopGUI->setCollapse('Условия', $PHPShopGUI->setField('Статус покупателя', $PHPShopGUI->setCheckbox('status_check_new', 1, 'Учитывать статус покупателя', $data['status_check']) . '<br>' .
                    $PHPShopGUI->setSelect('statuses[]', $value_user_status, '300', true, false, false, false, false, true)) .
            $PHPShopGUI->setField('Категории', $PHPShopGUI->setHelp('Выберите категории товаров и/или укажите ID товаров для акции.') .
                    $PHPShopGUI->setCheckbox("categories_check_new", 1, "Учитывать категории товара", $data['categories_check']) .'<br>'.
                    $PHPShopGUI->setCheckbox("categories_all", 1, "Выбрать все категории?", 0) .
                    $tree_select) .
            $PHPShopGUI->setField('Товары', 
                    $PHPShopGUI->setCheckbox("products_check_new", 1, "Учитывать товары", $data['products_check']) . '<br>'.
                    $PHPShopGUI->setCheckbox("block_old_price_new", 1, "Игнорировать товары со старой ценой", $data['block_old_price']) .
                    $PHPShopGUI->setTextarea('products_new', $data['products'], false, false, false, __('Укажите ID товаров или воспользуйтесь') . ' <a href="#" data-target="#products_new"  class="btn btn-sm btn-default tag-search"><span class="glyphicon glyphicon-search"></span> ' . __('поиском товаров') . '</a>')) .
            $PHPShopGUI->setField('Сумма заказа', $PHPShopGUI->setInputText(null, 'sum_order_new', $data['sum_order'], '300', $PHPShopSystem->getDefaultValutaCode())) .
            $PHPShopGUI->setField('Способ оплаты', $PHPShopGUI->setSelect('delivery_method_new', $value_payment_systems, 300))
    );


    $Tab1 .= $PHPShopGUI->setCollapse('Купоны', $PHPShopGUI->setField('Код', $PHPShopGUI->setInputText('', 'code_new', $data['code'], '170', false, 'left') . '&nbsp;' .
                    $PHPShopGUI->setInput('button', 'gen', __('Сгенерировать'), $float = "none", 120, "randAa(10);", 'btn-sm btn-success')) .
            $PHPShopGUI->setField($qty_all . ' ' . $qty_active . '  Коды', '<div class="form-inline">' . $PHPShopGUI->setInputText('Кол-во', 'qty_new', '1', '130', false, 'left') . '&nbsp;'
                    . $PHPShopGUI->setInput('button', 'qty_gen', __('Сгенерировать'), '', 120, false, 'btn-sm btn-success') . '&nbsp;' . $qty_off . '&nbsp;<button class="btn btn-default btn-sm" type="button" id="download_codes" name="download_codes"> '.__('Скачать активные промокоды').'</button></div>') .
            $PHPShopGUI->setAlert("Коды успешно сгенерированы", "success hide col-md-3 col-md-offset-2") .
            $PHPShopGUI->setLine() .
            $PHPShopGUI->setProgress('Идет генерация...', 'hide') . $PHPShopGUI->setAlert("Использованные коды успешно удалены", "success hide col-md-3 col-md-offset-2") . $PHPShopGUI->setLine() .
            $PHPShopGUI->setField('Использование', $PHPShopGUI->setRadio("code_tip_new", 1, "Одноразово", $data['code_tip']) .
                    $PHPShopGUI->setRadio("code_tip_new", 0, "Многоразово", $data['code_tip']), 'left')
    );

    // Заголовок
    $kupon = $PHPShopGUI->setInputText('', 'header_mail_new', $data['header_mail']) . $PHPShopGUI->setHelp('Письмо будет отправлено пользователю при успешном введении промокода.');

    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"), true);
    $oFCKeditor = new Editor('content_mail_new', true);
    $oFCKeditor->Height = '350';
    $oFCKeditor->ToolbarSet = 'Normal';
    $oFCKeditor->Value = $data['content_mail'];

    $Tab2 .= $PHPShopGUI->setCollapse('Содержимое письма', $oFCKeditor->AddGUI());
    $Tab2 .= $PHPShopGUI->setCollapse('Тема письма уведомления', $kupon);

    $oFCKeditor = new Editor('description_new', true);
    $oFCKeditor->Height = '230';
    $oFCKeditor->ToolbarSet = 'Normal';
    $oFCKeditor->Value = $data['description'];

    $Tab2 .= $PHPShopGUI->setCollapse('Описание акции на сайте', '<div>'.$oFCKeditor->AddGUI().'</div>');

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1,true,false,true), array("Дополнительно", $Tab2,true,false,true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", (int) $select['end'] + 1, "right", 70, "", "but") . $PHPShopGUI->setInput("submit", "saveID", "Сохранить", "right", false, false, false, "actionInsert.modules.create");

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

    if (is_array($_POST['statuses']))
        $_POST['statuses_new'] = serialize($_POST['statuses']);

    // Убираем лишние настройки 
    if (!empty($_POST['active_date_do_new']))
        $_POST['active_check_new'] = 1;
    else
        $_POST['active_check_new'] = 0;

    $_POST['discount_check_new'] = 1;

    if (!empty($_POST['sum_order_new']))
        $_POST['sum_order_check_new'] = 1;
    else
        $_POST['sum_order_check_new'] = 0;

    if (!empty($_POST['delivery_method_new']))
        $_POST['delivery_method_check_new'] = 1;
    else
        $_POST['delivery_method_check_new'] = 0;

    $PHPShopOrm->updateZeroVars('block_old_price_new', 'status_check_new', 'hide_old_price_new', 'discount_tip_new', 'products_check_new', 'categories_check_new', 'discount_check_new', 'active_check_new', 'enabled_new', 'code_tip_new', 'code_check_new', 'delivery_method_check_new', 'sum_order_check_new', 'free_delivery_new');

    $_POST['code_check_new'] = 1;

    if ($_POST['code_new'] != '*') {
        if ($_POST['code_new'] != ''):
            $indata = $PHPShopOrm->select(array('code'), array('code' => '="' . $_POST['code_new'] . '"'));
            if ($indata['code'] != ''):
                echo '<span style="color:red;">Ошибка. Код <b>' . $_POST['code_new'] . '</b> уже сущестует в базе</span>';
                exit();
            endif;
        endif;
    }

    //Общая скидка
    if ($_POST['code_new'] == "") {
        $_POST['code_new'] = 'promo';
    }

    $action = $PHPShopOrm->insert($_POST);
    header('Location: ?path=' . $_GET['path']);
    return $action;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>