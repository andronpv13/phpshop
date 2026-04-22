<?php

include_once dirname(__FILE__) . '/../class/OzonSeller.php';
$TitlePage = __('Заказы из Ozon');

function actionStart() {
    global $PHPShopInterface, $PHPShopSystem, $TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->addJSFiles('./js/bootstrap-datetimepicker.min.js', '../modules/ozonseller/admpanel/gui/order.gui.js');
    $PHPShopInterface->addCSSFiles('./css/bootstrap-datetimepicker.min.css');
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("&#8470; Заказа", "7%"), array("Иконка", "7%"),  array("Наименование", "30%"), array("Обработан", "15%"),  array("Итого", "10%", array('align' => 'right')));

    // Знак рубля
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();

    if (isset($_GET['date_start']))
        $date_start = $_GET['date_start'];
    else
        $date_start = PHPShopDate::get((time() - 2592000 / 30), false, true);

    if (isset($_GET['date_end']))
        $date_end = $_GET['date_end'];
    else
        $date_end = PHPShopDate::get((time() - 1), false, true);

    $OzonSeller = new OzonSeller();


    // Заказы FBS
    $ordersFbs = $OzonSeller->getOrderListFbs($date_start, $date_end, $_GET['status']);
    if (is_array($ordersFbs['result']['postings'])) {
        foreach ($ordersFbs['result']['postings'] as $k => $order_list)
            $ordersFbs['result']['postings'][$k]['type'] = 'fbs';
    }

    // Заказы FBO
    $ordersFbo = $OzonSeller->getOrderListFbo($date_start, $date_end, $_GET['status']);

    if (is_array($ordersFbs['result']['postings']) and is_array($ordersFbo['result']))
        $orders = array_merge($ordersFbs['result']['postings'], $ordersFbo['result']);
    elseif (is_array($ordersFbs['result']['postings']))
        $orders = $ordersFbs['result']['postings'];
    else
        $orders = $ordersFbo['result'];

    $total = 0;
    
    if ($OzonSeller->type == 2) {
        $type_name = __('Арт');
        $type = 'uid';
    } else {
        $type_name = 'ID';
        $type = 'id';
    }

    if (is_array($orders))
        foreach ($orders as $row) {

            // Заказ уже загружен
            if ($OzonSeller->checkOrderBase($row['posting_number']))
                continue;

            $sum = 0;
            $icon = null;
            if (is_array($row['products']))
                foreach ($row['products'] as $product)
                    $sum += $product['price'] * $product['quantity'];

            if ($row['type'] == 'fbs')
                $order_type = '<div class="text-muted">FBS</div>';
            else
                $order_type = '<div class="text-muted">FBO</div>';
            

            $prod = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['id,uid,name,pic_small'], [$type => '="' . (string) PHPShopString::utf8_win1251($product['offer_id']) . '"']);

            if (empty($prod)) {
                $product_info = $OzonSeller->getProductAttribures($product['offer_id'], 'offer_id')['result'][0];
                $image = $product_info['primary_image'] ;
                $link = 'https://www.ozon.ru/product/' . $product['sku'];
                $prod['name']=PHPShopString::utf8_win1251($row['products'][0]['name']);
                $uid = '<div class="text-muted">' . __('Арт') . ' ' . PHPShopString::utf8_win1251($product['offer_id']) . '</div>';
            } else {
                $image = $prod['pic_small'];
                $link = '?path=product&id=' . $prod['id'] . '&return=modules.dir.ozonseller';
                $uid = '<div class="text-muted">' . __('Арт') . ' ' . $prod['uid'] . '</div>';
         
            }
            
            $status = '<div class="text-muted">' . __($OzonSeller->getStatus($row['status'])) . '</div>';

           if (!empty($image))
                $icon .= '<img src="' . $image . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
            else
                $icon .= '<img class="media-object" src="./images/no_photo.gif">';
            

            $total += $sum;

            $PHPShopInterface->setRow(['name' => $row['posting_number'],'addon' => $order_type, 'link' => '?path=modules.dir.ozonseller.order&id=' . $row['posting_number'] . '&type=' . $row['type']], $icon, array('name' => $prod['name'], 'addon' => $uid, 'link' => $link,'target'=>'_blank'), ['name'=>$OzonSeller->getTime($row['in_process_at']),'addon'=>$status],  $sum . $currency);
        }

    $order_status_value[] = array(__('Все заказы'), null, $_GET['status']);
    foreach ($OzonSeller->status_list as $k => $status_val) {
        $order_status_value[] = array(__($status_val), $k, $_GET['status']);
    }


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
        $stat = '<div class="order-stat-container">' . __('Сумма:') . ' <b>' . number_format($total, 0, ',', ' ') . '</b> ' . $currency . '<br>' . __('Количество:') . ' <b>' . count($orders) . '</b> ' . __('шт.');
        $sidebarright[] = array('title' => 'Статистика', 'content' => $stat);
    }

    $sidebarright[] = array('title' => 'Интервал', 'content' => $PHPShopInterface->setForm($searchforma, false, "order_search", false, false, 'form-sidebar'));

    $PHPShopInterface->setSidebarRight($sidebarright, 2, 'hidden-xs');

    $PHPShopInterface->Compile(2);
}
