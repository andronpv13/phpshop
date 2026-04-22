<?php

include_once dirname(__FILE__) . '/../class/YandexMarket.php';
$TitlePage = __('Заказы из Яндекс.Маркет');

function actionStart() {
    global $PHPShopInterface, $PHPShopSystem, $TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->addJSFiles('./js/bootstrap-datetimepicker.min.js', '../modules/yandexcart/admpanel/gui/yandexcart.gui.js');
    $PHPShopInterface->addCSSFiles('./css/bootstrap-datetimepicker.min.css');
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("&#8470; Заказа", "7%"), array("Иконка", "7%"), array("Наименование", "30%"), array("Обработан", "15%"), array("Итого", "10%", array('align' => 'right')));

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

    $YandexMarket = new YandexMarket();

    if (empty($_GET['limit']))
        $_GET['limit'] = 10;

    // Компания 1
    $orders1 = $YandexMarket->getOrderList($date_start, $date_end, $_GET['status'], $_GET['limit']);
    if (is_array($orders1['orders']))
        foreach ($orders1['orders'] as $order) {
            $order['campaign_num']=false;
            $order['model']=$YandexMarket->options['model'];
            $orders[] = $order;
        }

    // Компания 2
    $orders2 = $YandexMarket->getOrderList($date_start, $date_end, $_GET['status'], $_GET['limit'], 2);
    if (is_array($orders2['orders']))
        foreach ($orders2['orders'] as $order) {
            $order['campaign_num']=2;
            $order['model']=$YandexMarket->options['model_2'];
            $orders[] = $order;
        }

    // Компания 3
    $orders3 = $YandexMarket->getOrderList($date_start, $date_end, $_GET['status'], $_GET['limit'], 3);

    if (is_array($orders3['orders']))
        foreach ($orders1['orders'] as $order) {
            $order['campaign_num']=3;
            $order['model']=$YandexMarket->options['model_3'];
            $orders[] = $order;
        }


    $total = 0;

    if ($YandexMarket->type == 2) {
        $type_name = __('Арт');
        $type = 'uid';
    } else {
        $type_name = 'ID';
        $type = 'id';
    }

    if (is_array($orders))
        foreach ($orders as $row) {

            // Заказ уже загружен
            if ($YandexMarket->checkOrderBase($row['id']))
                continue;

            $sum = 0;
            $icon = null;
            if (is_array($row['items']))
                foreach ($row['items'] as $product)
                    $sum += $product['price'] * $product['count'];

            $prod = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['id,uid,name,pic_small'], [$type => '="' . (string) PHPShopString::utf8_win1251($product['offerId']) . '"']);

            if (empty($prod)) {
                $product_info = $YandexMarket->getProductList($visibility = "ALL", $product['offerId'], null, $limit = 1)['result']['offerMappings'][0]['offer'];
                $image = $product_info['pictures'][0];
                $link = 'https://partner.market.yandex.ru/shop/' . $product_info['campaigns'][0]['campaignId'] . '/assortment/offer-card?article=' . $product_info['offerId'];
                $prod['name'] = PHPShopString::utf8_win1251($product['offerName']);
                $uid = '<div class="text-muted">' . $type_name . ' ' . PHPShopString::utf8_win1251($product['offerId']) . '</div>';
            } else {
                $image = $prod['pic_small'];
                $link = '?path=product&id=' . $prod['id'] . '&return=modules.dir.yandexcart';
                $uid = '<div class="text-muted">' . $type_name . ' ' . $product['offerId'] . '</div>';
            }

            $status = '<div class="text-muted">' . __($YandexMarket->getStatus($row['status'])) . '</div>';
            $model = '<div class="text-muted">' . $row['model'] . '</div>';

            if (!empty($image))
                $icon .= '<img src="' . $image . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
            else
                $icon .= '<img class="media-object" src="./images/no_photo.gif">';


            $total += $sum;

            $PHPShopInterface->setRow(['name' => $row['id'], 'addon' => $model, 'link' => '?path=modules.dir.yandexcart.orders&id=' . $row['id'].'&campaign_num='.$row['campaign_num']], $icon, array('name' => $prod['name'], 'addon' => $uid, 'link' => $link, 'target' => '_blank'), ['name' => $row['updatedAt'], 'addon' => $status], $sum . $currency);
        }

    $order_status_value[] = array(__('Все заказы'), null, $_GET['status']);
    foreach ($YandexMarket->status_list as $k => $status_val) {
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
