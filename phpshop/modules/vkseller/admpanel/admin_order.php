<?php

include_once dirname(__FILE__) . '/../class/VkSeller.php';
$TitlePage = __('Заказы из ВКонтакте');

function actionStart() {
    global $PHPShopInterface, $PHPShopSystem, $TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->addJSFiles('./js/bootstrap-datetimepicker.min.js', '../modules/vkseller/admpanel/gui/vkseller.gui.js');
    $PHPShopInterface->addCSSFiles('./css/bootstrap-datetimepicker.min.css');
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("&#8470; Заказа", "15%"),array("Статус", "15%"), array("Дата", "15%"), array("Покупатель", "20%"), array("Телефон", "15%"),  array("Итого", "10%", array('align' => 'right')));

    // Знак рубля
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();

    if (isset($_GET['date_start']))
        $date_start = $_GET['date_start'];
    else
        $date_start = PHPShopDate::get((time() - 2592000), false, true);

    if (isset($_GET['date_end']))
        $date_end = $_GET['date_end'];
    else
        $date_end = PHPShopDate::get((time() - 1), false, true);

    if (empty($_GET['status']))
        $_GET['status'] = 'new';

    $VkSeller = new VkSeller();

    // Заказы
    if ($VkSeller->model == 'API') {
    $orders = $VkSeller->getOrderList($date_start, $date_end)['response']['items'];
    }

    $total = 0;
    
    $status_array=['Новый заказ','Согласовывается','Собирается','Доставляется','Выполнен','Отменен','Возврат'];

    if (is_array($orders))
        foreach ($orders as $row) {
        
            // Только новые заказы
            if($_GET['status'] == 'new' and $row['status'] != 0)
                continue;

            // Заказ уже загружен
            if ($VkSeller->checkOrderBase($row['id']))
               continue;


            $PHPShopInterface->setRow(['name' => $row['id'], 'link' => '?path=modules.dir.vkseller.order&id=' . $row['id'] . '&user=' . $row['user_id']], __($status_array[$row['status']]),PHPShopDate::get($row['date'],true), PHPShopString::utf8_win1251($row['recipient']['name']),PHPShopString::utf8_win1251($row['recipient']['phone'],true),  ['name'=>number_format(round($row['total_price']['amount'] / 100), 0, '', ' ') . $currency, 'link' => '?path=modules.dir.vkseller.order&id=' . $row['id'] . '&user=' . $row['user_id'],'align'=>'right']);
        }

    $order_status_value[] = array(__('Новые заказы'), 'new', $_GET['status']);
    $order_status_value[] = array(__('Все заказы'), 'all', $_GET['status']);


    $searchforma = $PHPShopInterface->setInputDate("date_start", $date_start, 'margin-bottom:10px', null, 'Дата начала отбора');
    $searchforma .= $PHPShopInterface->setInputDate("date_end", $date_end, false, null, 'Дата конца отбора');
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'path', 'value' => $_GET['path']));
    $searchforma .= $PHPShopInterface->setSelect('status', $order_status_value, '100%');
    $searchforma .= $PHPShopInterface->setButton('Показать', 'search', 'btn-order-search pull-right');

    if (isset($_GET['date_start']))
        $searchforma .= $PHPShopInterface->setButton('Сброс', 'remove', 'btn-order-cancel pull-left');
    else
        $searchforma .= $PHPShopInterface->setButton('Сброс', 'remove', 'btn-order-cancel hide pull-left');


    // Правый сайдбар
    if ($total > 0) {
        $stat = '<div class="order-stat-container">' . __('Сумма:') . ' <b>' . number_format($total, 2, ',', ' ') . '</b> ' . $currency . '<br>' . __('Количество:') . ' <b>' . count($orders) . '</b> ' . __('шт.');
        $sidebarright[] = array('title' => 'Статистика', 'content' => $stat);
    }

    $sidebarright[] = array('title' => 'Интервал', 'content' => $PHPShopInterface->setForm($searchforma, false, "order_search", false, false, 'form-sidebar'));

    $PHPShopInterface->setSidebarRight($sidebarright, 2, 'hidden-xs');

    $PHPShopInterface->Compile(2);
}
