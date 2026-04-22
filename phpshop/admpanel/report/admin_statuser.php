<?php

$TitlePage = __("Отчеты по покупателям");
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['system']);

// Стартовый вид
function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $PHPShopOrm, $PHPShopInterface, $PHPShopSystem;

    PHPShopObj::loadClass('valuta');
    PHPShopObj::loadClass('user');
    PHPShopObj::loadClass('order');

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
        $where .= ' a.datas between ' . (PHPShopDate::GetUnixTime($_GET['date_start']) - 1) . ' and ' . (PHPShopDate::GetUnixTime($_GET['date_end']) + 259200 / 2) . '  ';
    } else {
        $where .= ' a.datas between ' . ($time - 2592000) . ' and ' . ($time + 259200 / 2) . '  ';
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

    // Размер названия поля
    $PHPShopInterface->field_col = 3;
    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->addJSFiles('./js/bootstrap-datetimepicker.min.js', './js/bootstrap-datetimepicker.ru.js', 'report/gui/report.gui.js');
    $PHPShopInterface->addCSSFiles('./css/bootstrap-datetimepicker.min.css');
    $PHPShopInterface->setActionPanel($TitlePage, array('Export'), false, false);
    $PHPShopInterface->setCaption(array('№', '5%'), array("Покупатель", "70%"), array("Сумма", "20%", array('align' => 'right')));

    // Таблица с данными
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);

    $PHPShopOrm->sql = 'SELECT sum(a.sum) as total, a.* FROM phpshop_orders AS a where ' . $where . ' group by a.id';
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select();

    if (!empty($data[0]['total']))
        $max = $data[0]['total'];
    else
        $max = 0;

    $PHPShopOrm->clean();

    $PHPShopOrm->debug = false;
    $PHPShopOrm->sql = 'SELECT a.*, b.mail FROM ' . $GLOBALS['SysValue']['base']['orders'] . '  AS a 
        JOIN ' . $GLOBALS['SysValue']['base']['shopusers'] . ' AS b ON a.user = b.id   where  ' . $where . ' group by a.fio order by a.sum desc limit 10';
    $export = null;
    $data = $PHPShopOrm->select();
    $i = 1;
    if (is_array($data))
        foreach ($data as $row) {

            if (empty($row['fio'])) {
                $row['fio'] = $row['mail'];
            }

            $export .= '"' . $row['user'] . '",';
            $value = round(($row['sum'] * 100) / $max);

            $progress = '
<a href="?path=shopusers&id=' . $row['user'] . '&return=' . $_GET['path'] . '">' . $row['fio'] . '</a>
<div class="progress">
  <div class="progress-bar" role="progressbar" aria-valuenow="' . $value . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $value . '%;">
      ' . $value . '%
  </div>
</div>';

            $PHPShopInterface->setRow($i, $progress, array('name' => $row['sum'] . $currency, 'align' => 'right'));
            $i++;
        }

    $PHPShopInterface->_CODE .= '<span id="export" data-export=\'[' . substr($export, 0, strlen($export) - 1) . ']\' data-path="exchange.export.user&return=' . $_GET['path'] . '"></spam>';


    // Статус заказа
    $PHPShopInterface->field_col = 1;
    $searchforma = $PHPShopInterface->setInputDate("date_start", $date_start, 'margin-bottom:10px', null, 'Дата начала отбора');
    $searchforma .= $PHPShopInterface->setInputDate("date_end", $date_end, false, null, 'Дата конца отбора');
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'path', 'value' => $_GET['path']));
    $searchforma .= $PHPShopInterface->setButton('Показать', 'search', 'btn-order-search pull-right');

    if ($clean)
        $searchforma .= $PHPShopInterface->setButton('Сброс', 'remove', 'btn-order-cancel pull-left');

    $sidebarright[] = array('title' => 'Отчеты', 'content' => $PHPShopInterface->loadLib('tab_menu', false, './report/'));
    $sidebarright[] = array('title' => 'Интервал', 'content' => $PHPShopInterface->setForm($searchforma, false, "order_search", false, false, 'form-sidebar'));
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