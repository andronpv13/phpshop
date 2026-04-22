<?php

$_classPath = "../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("text");
PHPShopObj::loadClass("string");
PHPShopObj::loadClass("product");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("mail");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("delivery");
PHPShopObj::loadClass("lang");
PHPShopObj::loadClass("order");

$PHPShopLang = new PHPShopLang();

$PHPShopModules = new PHPShopModules($_classPath . "modules/");
$PHPShopModules->checkInstall('yandexcart');
$PHPShopSystem = new PHPShopSystem();
$PHPShopValutaArray = new PHPShopValutaArray();

// Лог
function setYandexcartLog($data) {
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['yandexcart']['yandexcart_log']);

    $log = array(
        'message_new' => serialize($data),
        'order_id_new' => $data['order']['id'],
        'date_new' => time(),
        'status_new' => $data['order']['status'],
        'path_new' => $_SERVER["PATH_INFO"]
    );

    $PHPShopOrm->insert($log);
}

// Настройки модуля
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['yandexcart']['yandexcart_system']);
$option = $PHPShopOrm->select();

// Компания 3
$option['model'] = $option['model_3'];

$jsonOptions = unserialize($option['options']);
isset($jsonOptions['statuses']) && is_array($jsonOptions['statuses']) ? $statuses = $jsonOptions['statuses'] : $statuses = [];
isset($jsonOptions['payments']) && is_array($jsonOptions['payments']) ? $payments = $jsonOptions['payments'] : $payments = [];

// Входящие данные
$data = json_decode(file_get_contents('php://input'), true);

// Авторизация
if ($option['auth_token'] !== $_REQUEST['auth-token']) {
    $data['order']['status'] = 'Invalid token';
    $data['parameters'] = $_REQUEST;
    setYandexcartLog($data);
    header("HTTP/1.1 403 Unauthorized");
    die('Invalid token');
}


// Роутер
switch ($_SERVER["PATH_INFO"]) {

    // Добавление в корзину
    case('/cart'):
        $result = [];
        $weight = 0;
        $sum = 0;
        if (is_array($data['cart']['items']))
            foreach ($data['cart']['items'] as $item) {

                // Ключ обновления
                if ($option['type'] == 2) {
                    $key = 'uid';
                    $offerId = str_replace(['-','_'], [' ','-'], PHPShopString::utf8_win1251($item['offerId']));
                } else {
                    $key = 'id';
                    $offerId = $item['offerId'];
                }

                $PHPShopProduct = new PHPShopProduct($offerId, $key);

                // Блокировка приема заказов
                if (!empty($option['stop']))
                    $PHPShopProduct->setParam('items', 0);

                $result['cart']['items'][] = [
                    'feedId' => $item['feedId'],
                    'offerId' => $item['offerId'],
                    'count' => (int) $PHPShopProduct->getParam('items') > 0 ? (int) $PHPShopProduct->getParam('items') : 0
                ];


                $weight += (float) $PHPShopProduct->getParam('weight');
                $sum += (float) $PHPShopProduct->getPrice();
            }

        if ($option['model'] === 'DBS') {
            $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['delivery']);

            $storeDeliveries = $orm->getList(['*'], ['yandex_enabled' => '="2"'], ['order' => 'num ASC']);

            $regionIds = [];
            $getRegionIds = function ($region) use (&$getRegionIds) {
                $ids = array();

                $ids[] = $region['id'];

                if (isset($region['parent']) && is_array($region['parent'])) {
                    $ids = array_merge($getRegionIds($region['parent']), $ids);
                }

                return $ids;
            };

            $regionIds = $getRegionIds($data['cart']['delivery']['region']);

            $deliveries = [];

            // Доставка в конкретный город
            foreach ($storeDeliveries as $delivery) {
                if ((int) $delivery['yandex_region_id_3'] === (int) $data['cart']['delivery']['region']['id'] or (int) $delivery['yandex_region_id_3'] === 0) {
                    $deliveries[] = $delivery;
                }
            }

            // Если его нет - ищем доставку в регион
            if (count($deliveries) === 0) {
                foreach ($storeDeliveries as $delivery) {
                    if (in_array($delivery['yandex_region_id_3'], $regionIds) or (int) $delivery['yandex_region_id_3'] === 0) {
                        $deliveries[] = $delivery;
                    }
                }
            }

            if (count($deliveries) === 0) {
                setYandexcartLog([
                    'error' => __('Не найдено доставок для региона ') . (int) $data['cart']['delivery']['region']['name'],
                    'parameters' => $data
                ]);
                exit;
            }

            $deliveryOptions = [];
            foreach ($deliveries as $delivery) {
                if ((int) $delivery['yandex_type'] === 1) {
                    $type = 'DELIVERY';
                } elseif ((int) $delivery['yandex_type'] === 2) {
                    $type = 'PICKUP';
                } else {
                    $type = 'POST';
                }

                $dateTimeFrom = new DateTime();
                $dateTimeTo = new DateTime();
                $from = $dateTimeFrom->modify('+' . $delivery['yandex_day_min'] . ' days')->setTime(0, 0);
                $to = $dateTimeTo->modify('+' . $delivery['yandex_day'] . ' days')->setTime(0, 0);

                $deliveryOption = array(
                    'id' => $delivery['id'],
                    'price' => (float) yandexDeliveryPrice($delivery, $sum, $weight),
                    'serviceName' => PHPShopString::win_utf8(substr($delivery['city'], 0, 50)),
                    'type' => $type,
                    'dates' => array(
                        'fromDate' => $from->format('d-m-Y'),
                        'toDate' => $to->format('d-m-Y')
                    )
                );

                if ($type === 'PICKUP') {
                    $outlets = array_unique(unserialize($delivery['yandex_delivery_points_3']));
                    if (!is_array($outlets)) {
                        $outlets = [];
                    }

                    $deliveryOutlets = [];
                    foreach ($outlets as $outlet) {
                        $deliveryOutlets[] = [
                            'code' => $outlet
                        ];
                    }

                    $deliveryOption['outlets'] = $deliveryOutlets;
                }

                $deliveryOptions[] = $deliveryOption;
            }

            $result['cart']['deliveryCurrency'] = 'RUR';
            $result['cart']['deliveryOptions'] = $deliveryOptions;
        }

        setYandexcartLog([
            'parameters' => $data,
            'response' => $result
        ]);

        break;

    case "/order/accept":
        $sum = 0;
        $weight = 0;
        // Корзина
        if (is_array($data['order']['items']))
            foreach ($data['order']['items'] as $product) {
                $sum += $product['price'] * $product['count'];
                
                // Ключ обновления
                if ($option['type'] == 2) {
                    $key = 'uid';
                    $offerId = str_replace(['-','_'], [' ','-'], PHPShopString::utf8_win1251($product['offerId']));
                } else {
                    $key = 'id';
                    $offerId = $product['offerId'];
                }

                $PHPShopProduct = new PHPShopProduct($offerId, $key);

                $order["Cart"]["cart"][$PHPShopProduct->getParam('id')] = [
                    'id' => $PHPShopProduct->getParam('id'),
                    'name' => $PHPShopProduct->getName(),
                    'price' => $product['price'],
                    'uid' => $PHPShopProduct->getParam('uid'),
                    'num' => $product['count'],
                    'pic_small' => $PHPShopProduct->getParam('pic_small'),
                    'category' => $PHPShopProduct->getParam('category')
                ];
                $weight += (float) $PHPShopProduct->getParam('weight');
            }
        $order["Cart"]["num"] = count($order["Cart"]["cart"]);
        $order["Cart"]["sum"] = $sum;

        // Доставка
        if ($option['model'] === 'DBS') {
            $deliveryId = $data['order']['delivery']['shopDeliveryId'];
            $order["Cart"]["dostavka"] = $data['order']['delivery']['price'];
        } else {
            $deliveryId = $option['delivery_id'];
            $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['delivery']);
            $delivery = $orm->getOne(['*'], ['id' => '="' . $option['delivery_id'] . '"']);
            $order["Cart"]["dostavka"] = yandexDeliveryPrice($delivery, $sum, $weight);
        }

        // Номер заказа
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $row = $PHPShopOrm->select(array('id'), false, array('order' => 'id desc'), array('limit' => 1));

        switch ($data['order']['paymentMethod']) {
            case 'YANDEX':
                $payment = $payments['yandex'];
                break;
            case 'APPLE_PAY':
                $payment = $payments['apple_pay'];
                break;
            case 'GOOGLE_PAY':
                $payment = $payments['google_pay'];
                break;
            case 'CREDIT':
                $payment = $payments['credit'];
                break;
            case 'EXTERNAL_CERTIFICATE':
                $payment = $payments['certificate'];
                break;
            case 'CARD_ON_DELIVERY':
                $payment = $payments['card_on_delivery'];
                break;
            default:
                $payment = $payments['cash_on_delivery'];
        }

        $orderNum = (int) $row['id'] + 1 . "-" . rand(10, 99);
        // Данные покупателя
        $order["Person"] = array(
            "ouid" => $orderNum,
            "data" => date("U"),
            "time" => date("H:s a"),
            "mail" => __('market@yandex.ru'),
            "name_person" => __('Яндекс.Маркет'),
            "org_name" => null,
            "org_inn" => null,
            "org_kpp" => null,
            "tel_code" => null,
            "tel_name" => null,
            "adr_name" => null,
            "dostavka_metod" => $deliveryId,
            "discount" => 0,
            "user_id" => null,
            "dos_ot" => null,
            "dos_do" => null,
            "order_metod" => $payment
        );

        $insert['fio_new'] = __('Яндекс.Маркет');
        $insert['datas_new'] = time();
        $insert['uid_new'] = $orderNum;
        $insert['orders_new'] = serialize($order);
        $insert['yandex_order_id_new'] = $data['order']['id'];
        $insert['sum_new'] = $sum + $order["Cart"]["dostavka"];

        if ($option['model'] === 'DBS') {
            $insert['country_new'] = PHPShopString::utf8_win1251($data['order']['delivery']['address']['country']);
            $insert['city_new'] = PHPShopString::utf8_win1251($data['order']['delivery']['address']['city']);
            $insert['street_new'] = PHPShopString::utf8_win1251($data['order']['delivery']['address']['street']);
            $insert['house_new'] = PHPShopString::utf8_win1251($data['order']['delivery']['address']['house']);
            $insert['flat_new'] = PHPShopString::utf8_win1251($data['order']['delivery']['address']['floor']);
            $insert['dop_info_new'] = PHPShopString::utf8_win1251($data['order']['notes']);
        }

        // Запись заказа в БД
        $PHPShopOrm->insert($insert);

        $result['order'] = array(
            'accepted' => true,
            'id' => $orderNum,
        );
        if ($option['model'] === 'DBS') {
            $result['shipmentDate'] = $data['order']['delivery']['dates']['fromDate'];
        }

        setYandexcartLog([
            'parameters' => $data,
            'response' => $result
        ]);

        break;

    case "/order/status":

        // Проведен
        if ($data['order']['status'] === 'PROCESSING') {

            $row = (new PHPShopOrm($GLOBALS['SysValue']['base']['orders']))
                    ->getOne(['*'], ['yandex_order_id_3' => sprintf("='%s'", $data['order']['id'])]);


            // Статус заказа подтвержден клиентом 
            $update['statusi_new'] = $statuses['processing_started'];

            /* (new PHPShopOrm($GLOBALS['SysValue']['base']['orders']))
              ->update($update, array('id' => sprintf('="%s"', $row['id']))); */

            // Смена статуса с учетом списания товара со склада
            $PHPShopOrderFunction = new PHPShopOrderFunction((int) $row['id']);
            $PHPShopOrderFunction->changeStatus((int) $update['statusi_new'], (int) $row['statusi']);

            // Сообщение о новом заказе администрации
            new PHPShopMail($PHPShopSystem->getEmail(), $PHPShopSystem->getEmail(), 'Поступил заказ №' . $row['uid'], 'Заказ оформлен на Яндекс.Маркет', false, false);
            
            // Товар
            $orders = unserialize($row['orders']);
            $cart = $order['Cart']['cart'];
            
            if (is_array($cart))
            foreach ($cart as $key => $val) {
                $product['name'].=$val['name'].',';
            }
            
            $product['name']=substr($product['name'],0,strlen($product['name'])-1);

            // Telegram
            $chat_id_telegram = $PHPShopSystem->getSerilizeParam('admoption.telegram_admin');
            if (!empty($chat_id_telegram) and $PHPShopSystem->ifSerilizeParam('admoption.telegram_order', 1)) {

                PHPShopObj::loadClass('bot');

                $bot = new PHPShopTelegramBot();
                $msg = $PHPShopBase->SysValue['lang']['mail_title_adm'] . $row['id'] . " - " . $product['name'] . " [" . $row['sum'] . " " . $PHPShopOrderFunction->default_valuta_name . ']';
                $bot->send($chat_id_telegram, PHPShopString::win_utf8($msg));
            }

            // VK
            $chat_id_vk = $PHPShopSystem->getSerilizeParam('admoption.vk_admin');
            if (!empty($chat_id_vk) and $PHPShopSystem->ifSerilizeParam('admoption.vk_order', 1)) {

                PHPShopObj::loadClass('bot');

                $bot = new PHPShopVKBot();
                $msg = $PHPShopBase->SysValue['lang']['mail_title_adm'] . $row['id'] . " - " . $product['name'] . " [" . $row['sum'] . " " . $PHPShopOrderFunction->default_valuta_name . ']';
                $bot->send($chat_id_vk, PHPShopString::win_utf8($msg));
            }
        }

        // Отменен пользователем
        if ($data['order']['status'] === 'CANCELLED') {


            switch ($data['order']['substatus']) {
                case 'DELIVERY_SERVICE_UNDELIVERED':
                    $update['statusi_new'] = $statuses['cancelled_delivery_service_undelivered'];
                    break;
                case 'PROCESSING_EXPIRED':
                    $update['statusi_new'] = $statuses['cancelled_processing_expired'];
                    break;
                case 'REPLACING_ORDER':
                    $update['statusi_new'] = $statuses['cancelled_replacing_order'];
                    break;
                case 'RESERVATION_EXPIRED':
                    $update['statusi_new'] = $statuses['cancelled_reservation_expired'];
                    break;
                case 'RESERVATION_FAILED':
                    $update['statusi_new'] = $statuses['cancelled_reservation_failed'];
                    break;
                case 'USER_CHANGED_MIND':
                    $update['statusi_new'] = $statuses['cancelled_user_changed_mind'];
                    break;
                case 'USER_NOT_PAID':
                    $update['statusi_new'] = $statuses['cancelled_user_not_paid'];
                    break;
            }

            if (!empty($data['order']['id'])) {
                //$PHPShopOrm->update($update, ['yandex_order_id' => sprintf("='%s'", $data['order']['id'])]);

                $row = (new PHPShopOrm($GLOBALS['SysValue']['base']['orders']))
                        ->getOne(['*'], ['yandex_order_id_3' => sprintf("='%s'", $data['order']['id'])]);

                // Смена статуса с учетом возврата товара на склад
                $PHPShopOrderFunction = new PHPShopOrderFunction((int) $row['id']);
                $PHPShopOrderFunction->changeStatus((int) $update['statusi_new'], (int) $row['statusi']);
            }
        }

        // Доставлен в ПВЗ
        if ($data['order']['status'] === 'PICKUP') {

            $row = (new PHPShopOrm($GLOBALS['SysValue']['base']['orders']))
                    ->getOne(['*'], ['yandex_order_id_3' => sprintf("='%s'", $data['order']['id'])]);

            $update['statusi_new'] = $statuses['pickup'];

            /* (new PHPShopOrm($GLOBALS['SysValue']['base']['orders']))
              ->update($update, array('id' => sprintf('="%s"', $row['id']))); */

            // Смена статуса с учетом списания товара со склада
            if ($row['sklad_action'] == 1) {
                $PHPShopOrderFunction = new PHPShopOrderFunction((int) $row['id']);
                $PHPShopOrderFunction->changeStatus((int) $update['statusi_new'], (int) $row['statusi']);
            }
        }

        setYandexcartLog([
            'parameters' => $data
        ]);

        header("HTTP/1.1 200");
        die('OK');
        exit();
        break;

    case "/stocks":
        $skus = [];

        if (is_array($data['skus']) && count($data['skus']) > 0) {

            // Ключ обновления Артикул
            if ($option['type'] == 2) {
                
                foreach ($data['skus'] as $k => $val){
                    $val = PHPShopString::utf8_win1251($val);
                    $val = str_replace(['-','_'], [' ','-'], $val);
                    $data['skus'][$k] = '"' . $val . '"';
                }

                $where = ['uid' => sprintf(' IN(%s)', implode(',', $data['skus']))];

                $counts = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))
                        ->getList(['items', 'uid'], $where);

                foreach ($counts as $count) {

                    $count['uid'] = str_replace(['-',' '], ['_','-'], $count['uid']);

                    $skus[] = [
                        'sku' => PHPShopString::win_utf8($count['uid']),
                        'warehouseId' => $data['warehouseId'],
                        'items' => [
                            [
                                'type' => 'FIT',
                                'count' => $count['items'],
                                'updatedAt' => (new DateTime())->format('c')
                            ]
                        ]
                    ];
                }
            }
            // Ключ обновления ID
            else {
                $where = ['id' => sprintf(' IN(%s)', implode(',', $data['skus']))];

                $counts = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))
                        ->getList(['items', 'id'], $where);

                foreach ($counts as $count) {
                    $skus[] = [
                        'sku' => PHPShopString::win_utf8($count['id']),
                        'warehouseId' => $data['warehouseId'],
                        'items' => [
                            [
                                'type' => 'FIT',
                                'count' => $count['items'],
                                'updatedAt' => (new DateTime())->format('c')
                            ]
                        ]
                    ];
                }
            }
        }

        $response = [
            'skus' => $skus
        ];

        // Запись в журнал
        $data['response'] = $response;
        //$data['sql'] = $where;
        setYandexcartLog($data);

        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
        break;

    default:
        $data['order']['status'] = 'Bad Request';
        setYandexcartLog($data);
        header("HTTP/1.1 400 Bad Request");
        die('Bad Request');
}

function yandexDeliveryPrice($delivery, $sum, $weight) {

    if ($delivery['price_null_enabled'] == 1 and $sum >= $delivery['price_null']) {
        return 0;
    }

    if ($delivery['taxa'] > 0) {
        $addweight = $weight - 500;
        if ($addweight < 0) {
            $addweight = 0;
        }
        $addweight = ceil($addweight / 500) * $delivery['taxa'];
        $endprice = $delivery['price'] + $addweight;

        return $endprice;
    }

    return $delivery['price'];
}

header("HTTP/1.1 200");
header("Content-Type: application/json");
echo json_encode($result);
?>