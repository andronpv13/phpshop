<?php

include_once dirname(__FILE__) . '/../class/Avito.php';
$TitlePage = __('Заказы из Avito');

function actionStart() {
    global $PHPShopInterface, $PHPShopSystem, $TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->addJSFiles('./js/bootstrap-datetimepicker.min.js', '../modules/avito/admpanel/gui/avito.gui.js');
    $PHPShopInterface->addCSSFiles('./css/bootstrap-datetimepicker.min.css');
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("&#8470; Заказа", "7%"), array("Наименование", "30%"), array("Обработан", "15%"), array("Итого", "10%", array('align' => 'right')));

    // Знак рубля
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();

    if (isset($_GET['date_start']))
        $date_start = $_GET['date_start'];
    else
        $date_start = PHPShopDate::get((time() - 2592000 / 30), false, false);

    if (isset($_GET['date_end']))
        $date_end = $_GET['date_end'];
    else
        $date_end = PHPShopDate::get((time() + 2592000 / 30), false, false);

    $Avito = new Avito();

    if (empty($_GET['limit']))
        $_GET['limit'] = 10;

    $orders = $Avito->getOrderList(PHPShopDate::GetUnixTime($date_start), PHPShopDate::GetUnixTime($date_end), $_GET['status'], $_GET['limit']);

    $total = 0;

    if ($Avito->type == 2) {
        $type_name = __('Арт');
        $type = 'uid';
    } else {
        $type_name = 'ID';
        $type = 'id';
    }

    if (is_array($orders['orders']))
        foreach ($orders['orders'] as $row) {

            // Заказ уже загружен
            if ($Avito->checkOrderBase($row['id']))
                continue;

            $sum = 0;
            $icon = null;
            if (is_array($row['items']))
                foreach ($row['items'] as $product)
                    $sum += $product['prices']['total'] * $product['count'];

            $prod = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['id,uid,name,pic_small'], [$type => '="' . (string) PHPShopString::utf8_win1251($row['id']) . '"']);

            if (empty($prod)) {
                $product_info = $Avito->getProductList($visibility = "ALL", $product['offerId'], null, $limit = 1)['resources'][0];
                $link = $product_info['url'];
                $prod['name'] = PHPShopString::utf8_win1251($product['title']);
                $uid = '<div class="text-muted">' . $type_name . ' ' . PHPShopString::utf8_win1251($product['id']) . '</div>';
            } else {
                $link = '?path=product&id=' . $prod['id'] . '&return=modules.dir.yandexcart';
                $uid = '<div class="text-muted">' . $type_name . ' ' . $product['offerId'] . '</div>';
            }

            $status = '<div class="text-muted">' . __($Avito->getStatus($row['status'])) . '</div>';

            $total += $sum;

            $PHPShopInterface->setRow(['name' => $row['id'], 'link' => '?path=modules.dir.avito.orders&id=' . $row['id']], array('name' => $prod['name'], 'addon' => $uid, 'link' => $link, 'target' => '_blank'), ['name' => str_replace(['T', 'Z'], ' ', $row['updatedAt']), 'addon' => $status], $sum . $currency);
        }

    $order_status_value[] = array(__('Все заказы'), null, $_GET['status']);

    foreach ($Avito->status_list as $k => $status_val) {
        $order_status_value[] = array(__($status_val), $k, $_GET['status']);
    }


    $searchforma = $PHPShopInterface->setInputDate("date_start", $date_start, 'margin-bottom:10px', null, 'Дата начала отбора');
    $searchforma .= $PHPShopInterface->setInputDate("date_end", $date_end, false, null, 'Дата конца отбора');
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'path', 'value' => $_GET['path']));
    $searchforma .= $PHPShopInterface->setSelect('status', $order_status_value, '100%');
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'limit', 'placeholder' => 'Лимит заказов', 'value' => $_GET['limit']));
    $searchforma .= $PHPShopInterface->setButton('Показать', 'search', 'btn-order-search pull-right');

    if (isset($_GET['date_start']))
        $searchforma .= $PHPShopInterface->setButton('Сброс', 'remove', 'btn-order-cancel pull-left');
    else
        $searchforma .= $PHPShopInterface->setButton('Сброс', 'remove', 'btn-order-cancel hide pull-left');


    // Правый сайдбар
    if ($total > 0) {
        $stat = '<div class="order-stat-container">' . __('Сумма:') . ' <b>' . number_format($total, 0, ',', ' ') . '</b> ' . $currency . '<br>' . __('Количество:') . ' <b>' . count($orders) . '</b> ' . __('шт.');
        $sidebarright[] = array('title' => 'Статистика', 'content' => $stat);
    }

    $sidebarright[] = array('title' => 'Интервал', 'content' => $PHPShopInterface->setForm($searchforma, false, "order_search", false, false, 'form-sidebar'));

    $PHPShopInterface->setSidebarRight($sidebarright, 2, 'hidden-xs');

    $PHPShopInterface->Compile(2);
}
