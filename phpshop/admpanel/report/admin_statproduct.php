<?php

$TitlePage = __("Отчеты по товарам");
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['system']);

// Построение дерева категорий
function treegenerator($array, $i, $curent) {
    global $tree_array;
    $del = '&brvbar;&nbsp;&nbsp;&nbsp;&nbsp;';
    $tree_select = $check = false;

    $del = str_repeat($del, $i);
    if (!empty($array['sub']) and is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {

            $check = treegenerator(@$tree_array[$k], $i + 1, $curent);

            if (is_array($_GET['cat']) and in_array($k, $_GET['cat']))
                $selected = 'selected';
            else
                $selected = null;

            if (empty($check['select'])) {
                $tree_select .= '<option value="' . $k . '" ' . $selected . '>' . $del . $v . '</option>';
                $i = 1;
            } else {
                $tree_select .= '<option value="' . $k . '" ' . $selected . ' disabled>' . $del . $v . '</option>';
                //$i++;
            }

            $tree_select .= $check['select'];
        }
    }
    return array('select' => $tree_select);
}

// Стартовый вид
function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $PHPShopOrm, $PHPShopInterface, $PHPShopSystem;

    PHPShopObj::loadClass(array('valuta', 'user', 'order', 'category', 'product'));


    // Поиск
    $where = $clean = null;

    // Знак рубля
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB')
        $currency = ' <span class="rubznak">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();


    $time = time();

    // Дата
    if (!empty($_GET['date_start']) and ! empty($_GET['date_end'])) {
        $clean = true;
        $where .= ' datas between ' . (PHPShopDate::GetUnixTime($_GET['date_start']) - 1) . ' and ' . (PHPShopDate::GetUnixTime($_GET['date_end']) + 259200 / 2) . '  ';
    } else {
        $where .= ' datas between ' . ($time - 2592000) . ' and ' . ($time + 259200 / 2) . '  ';
    }

    if (isset($_GET['date_start']))
        $date_start = $_GET['date_start'];
    else
        $date_start = PHPShopDate::get(time() - 2592000);

    if (isset($_GET['date_end']))
        $date_end = $_GET['date_end'];
    else
        $date_end = PHPShopDate::get(time() - 1);

    $TitlePage .= ' ' . __('с') . ' ' . $date_start . ' ' . __('по') . ' ' . $date_end;

    // Каталоги
    $PHPShopCategoryArray = new PHPShopCategoryArray();
    $CategoryArray = $PHPShopCategoryArray->getArray();

    $CategoryArray[0]['name'] = '- Все каталоги -';
    $tree_array = array();

    foreach ($PHPShopCategoryArray->getKey('parent_to.id', true) as $k => $v) {
        foreach ($v as $cat) {
            $tree_array[$k]['sub'][$cat] = $CategoryArray[$cat]['name'];
        }
        $tree_array[$k]['name'] = $CategoryArray[$k]['name'];
        $tree_array[$k]['id'] = $k;
    }

    $GLOBALS['tree_array'] = &$tree_array;

    $tree_select = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container="body"  data-style="btn btn-default btn-sm" name="cat[]" data-width="100%" multiple>';

    if (empty($_GET['cat']))
        $_GET['cat'] = null;

    if (is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator(@$tree_array[$k], 1, $_GET['cat']);

            if (is_array($_GET['cat']) and in_array($k, $_GET['cat']))
                $selected = 'selected';
            else
                $selected = null;

            if (empty($tree_array[$k]))
                $disabled = null;
            else
                $disabled = ' disabled';

            $tree_select .= '<option value="' . $k . '" ' . $selected . $disabled . '>' . $v . '</option>';

            $tree_select .= $check['select'];
        }
    $tree_select .= '</select>';


    // Размер названия поля
    $PHPShopInterface->field_col = 3;
    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->addJSFiles('./js/bootstrap-datetimepicker.min.js', './js/bootstrap-datetimepicker.ru.js', 'report/gui/report.gui.js');
    $PHPShopInterface->addCSSFiles('./css/bootstrap-datetimepicker.min.css');
    $PHPShopInterface->setActionPanel($TitlePage, array('Export'), false, false);
    $PHPShopInterface->setCaption(array('№', '5%'), array("Категория", "65%"), array("Кол-во", "15%", array('align' => 'center')), array("Прибыль", "15%", array('align' => 'center')), array("Выручка", "15%", array('align' => 'right')));

    // Выборка ИД товаров
    $PHPShopOrm = new PHPShopOrm();
    $PHPShopOrm->debug = false;
    $PHPShopOrm->sql = 'SELECT id,orders,sum FROM ' . $GLOBALS['SysValue']['base']['orders'] . ' where ' . $where . ' order by id ';
    $data = $PHPShopOrm->select();

    if (is_array($data))
        foreach ($data as $row) {
            $order = unserialize($row['orders']);
            $cart = $order['Cart']['cart'];
            $discount = $order['Person']['discount'];

            if (is_array($cart) and sizeof($cart) != 0)
                foreach ($cart as $key => $val) {

                    // Поиск товара
                    if (!empty($_GET['where']['name'])) {

                        if ($val['id'] == trim($_GET['where']['name']) or $val['uid'] == trim($_GET['where']['name']) or stristr($val['name'], trim($_GET['where']['name'])))
                            true;
                        else
                            continue;
                    }

                    if (!empty($val['name'])) {
                        $productIds[] = intval($key);
                        if ($order['Cart']['num'] > 1) {

                            // Продажа
                            $sum = $val['price'] * $val['num'];


                            $totalIds[$key] = number_format($sum - ($sum * $discount / 100), 0, ".", '');
                        } else {
                            $totalIds[$key] = $row['sum'];
                        }

                        // Закупка
                        if (!empty($val['price_purch']))
                            $sum_purch = $val['price_purch'] * $val['num'];
                        else
                            $sum_purch = round(($row['price'] * intval($_GET['where']['margin'])) / 100);

                        // Закупка
                        $totalIdsPurch[$key] = $sum_purch;

                        $orderIds[$key] = $row['id'];
                    }
                }
        }

    $catCount = array();

    if (!empty($productIds) and is_array($productIds)) {
        $PHPShopProductArray = new PHPShopProductArray(array('id' => ' IN(' . implode(',', $productIds) . ')'));
        $data = $PHPShopProductArray->getArray();

        if (is_array($data))
            foreach ($data as $row) {

                if (is_array($_GET['cat']))
                    if (@in_array($row['category'], $_GET['cat']) or empty($_GET['cat'])) {

                        if (key_exists($row['category'], $catCount)) {
                            $catCount[$row['category']]['count'] ++;
                            $catCount[$row['category']]['sum'] += $totalIds[$row['id']];
                            $catCount[$row['category']]['sum_purch'] += $totalIdsPurch[$row['id']];
                        } else {
                            $catCount[$row['category']]['count'] = 1;
                            $catCount[$row['category']]['sum'] = $totalIds[$row['id']];
                            $catCount[$row['category']]['sum_purch'] = $totalIdsPurch[$row['id']];
                        }

                        $catCount[$row['category']]['export'][] = $orderIds[$row['id']];
                    }
            }
    }



    $max = 0;
    foreach ($catCount as $key => $val) {
        $max += $val['count'];
    }

    $export = null;
    $i = 1;

    if (is_array($catCount))
        foreach ($catCount as $key => $row) {

            $export .= '"' . @implode(',', $row['export']) . '",';
            $value = round(($row['count'] * 100) / $max);

            if (!empty($row['sum_purch']))
                $margin = round($row['sum'] - $row['sum_purch']);
            elseif (!empty($_GET['where']['margin']))
                $margin = round(($row['sum'] * intval($_GET['where']['margin'])) / 100);

            $progress = '
<a href="?path=catalog&cat=' . $key . '&return=' . $_GET['path'] . '">' . $CategoryArray[$key]['name'] . '</a>
<div class="progress">
  <div class="progress-bar" role="progressbar" aria-valuenow="' . $value . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $value . '%;">
      ' . $value . '%
  </div>
</div>';

            $PHPShopInterface->setRow($i, $progress, array('name' => $row['count'], 'align' => 'center'), array('name' => (int) $margin . $currency, 'align' => 'center'), array('name' => $row['sum'] . $currency, 'align' => 'right', 'order' => $row['sum']));
            $i++;
        }

    $PHPShopInterface->_CODE .= '<span id="export" data-export=\'[' . substr($export, 0, strlen($export) - 1) . ']\' data-path="exchange.export.order&return=' . $_GET['path'] . '"></spam>';


    // Дата
    $PHPShopInterface->field_col = 1;
    $searchforma = $PHPShopInterface->setInputDate("date_start", $date_start, 'margin-bottom:10px', null, 'Дата начала отбора');
    $searchforma .= $PHPShopInterface->setInputDate("date_end", $date_end, false, null, 'Дата конца отбора');
    $searchforma .= $tree_select;
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'where[name]', 'placeholder' => 'Товар', 'value' => $_GET['where']['name']));
    $searchforma .= '<p>' . $PHPShopInterface->setInputArg(array('type' => 'text', 'caption' => '%', 'name' => 'where[margin]', 'placeholder' => 'Наценка', 'value' => @$_GET['where']['margin'])) . '</p>';
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'path', 'value' => $_GET['path']));
    $searchforma .= $PHPShopInterface->setButton('Показать', 'search', 'btn-order-search pull-right');

    if ($clean)
        $searchforma .= $PHPShopInterface->setButton('Сброс', 'remove', 'btn-product-cancel pull-left');

    $sidebarright[] = array('title' => 'Отчеты', 'content' => $PHPShopInterface->loadLib('tab_menu', false, './report/'));
    $sidebarright[] = array('title' => 'Поиск', 'content' => $PHPShopInterface->setForm($searchforma, false, "order_search", false, false, 'form-sidebar'));

    $PHPShopInterface->setSidebarRight($sidebarright, 2);

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Футер
    $PHPShopInterface->Compile(2);
    return true;
}

// Обработка событий
$PHPShopInterface->getAction();
?>