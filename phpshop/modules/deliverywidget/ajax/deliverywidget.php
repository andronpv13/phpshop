<?php

/**
 * Доставка
 */
session_start();
$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("product");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("string");
PHPShopObj::loadClass("cart");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("user");
PHPShopObj::loadClass("lang");
PHPShopObj::loadClass("cache");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopSystem = new PHPShopSystem();
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'shop'));

// Модули
$PHPShopModules = new PHPShopModules($_classPath . "modules/");
$PHPShopModules->checkInstall('deliverywidget');

$PHPShopCache = new PHPShopCache(null);
if($PHPShopCache->checkBot()){
     header('HTTP/1.0 403 Forbidden', true, 403);
     header("Status: 403 Forbidden");
     exit;
}

// Настройки модуля
require_once($_classPath . "modules/deliverywidget/class/Deliverycache.php");
$DeliveryWidget = new DeliveryWidget();
$option = $DeliveryWidget->option;

if (class_exists('Memcache') and $option['cache'] == 1) {
    $cache = new Memcache();
} elseif (class_exists('Memcached') and $option['cache'] == 1) {
    $cache = new Memcached();
} else {

    if ($option['cache'] == 0) {
        $cache = new DeliveryWidgetMysqlcached();
    } else
        $cache = new DeliveryWidgetNocached();
}

$cache->addServer($option['server'], $option['port']);

if (empty($_GET['city']) or empty($_GET['productId']))
    exit;
else {
    $city = (string) $_GET['city'];
    $productId = (int) $_GET['productId'];
}

$product = new PHPShopProduct($productId);
$weight = $product->getParam('weight') ?: $option['weight'];
$length = $product->getParam('length');
$width = $product->getParam('length');
$height = $product->getParam('height');

$cache_key = md5($city.$weight);
$prices = json_decode($cache->get($cache_key), true);

if (empty($prices)) {
    $cityFromIndex = $option['index_from'];
    $cityToIndex = $DeliveryWidget->suggestCity($city);

    // Если не смог определить индекс конечного города то не возвращаем ничего
    if (empty($cityToIndex)) {
        exit;
    }

    // CDEK
    if (!empty($PHPShopModules->ModValue['base']['cdekwidget'])) {
        include_once($_classPath . 'modules/cdekwidget/class/CDEKWidget.php');
        $CDEKWidget = new CDEKWidget();
        $shipment = [
            'type' => 'pickup',
            'cityTo' => $city,
            'cityFrom' => iconv('cp1251', 'UTF-8', $CDEKWidget->option['city_from']),
            'cityFromId' => $CDEKWidget->option['city_from_code'],
            'goods' => $CDEKWidget->getCart([
                [
                    'num' => 1,
                    'id' => $productId,
                    'weight' => $product->getParam('weight'),
                ],
            ])
        ];

        $query = [
            'isdek_action' => 'calc',
            'shipment' => $shipment,
        ];

        $url = $_SERVER['SERVER_NAME'].$PHPShopBase->getParam('dir.dir'). '/phpshop/modules/cdekwidget/api/PHPShopCdekService.php?' . http_build_query($query);
        $result = $DeliveryWidget->get($url);

        $sdek_price = round($result['result']['price']);
        $sdek_days = [
            $result['result']['deliveryPeriodMin'],
            $result['result']['deliveryPeriodMax'],
        ];
    }

    // Почта 1 класс
    if (!empty($PHPShopModules->ModValue['base']['pochta'])) {
        $url = 'tariff.pochta.ru/v2/calculate/tariff/delivery?json&object=47030&from=' . $cityFromIndex . '&to=' . $cityToIndex . '&weight=' . $weight;

        $result = $DeliveryWidget->get($url,'https://');
        $pochta_price = ceil($result['paymoneynds'] / 100);
        $pochta_days = [
            $result['delivery']['min'],
            $result['delivery']['max'],
        ];
    }

    // Яндекс.Доставка
    if (!empty($PHPShopModules->ModValue['base']['yandexdelivery'])) {
        include_once($_classPath . "modules/yandexdelivery/class/YandexDelivery.php");
        $yandexDelivery = new YandexDelivery();
        $yandexDelivery->options['paid'] = 1;
        $data = new YandexDeliveryOrderData();
        $data->weight = $weight ?: $yandexDelivery->options['weight'];
        $data->length = $length ?: $yandexDelivery->options['length'];
        $data->width = $width ?: $yandexDelivery->options['width'];
        $data->height = $height ?: $yandexDelivery->options['height'];
        $data->address = $city;
        $data->delivery_variant_id = $yandexDelivery->getStation($data->address);

        $yandex_price = $yandexDelivery->getApproxDeliveryPrice($data);
        $yandex_days = $yandexDelivery->getApproxDeliveryDays($data);

        $origin = new DateTimeImmutable(date("Y-m-d", $yandex_days[0]['from']));
        $target = new DateTimeImmutable(date("Y-m-d", end($yandex_days)['to']));

        $interval = $origin->diff($target);
        $yandex_day = $interval->format('%a');
    }

    $prices = [
        'sdek' => $sdek_price,
        'sdek_days' => $sdek_days,
        'pochta' => $pochta_price,
        'pochta_days' => $pochta_days,
        'yandex' => $yandex_price,
        'yandex_days' => $yandex_day

    ];
    
    // Сохранение кеша
    if (class_exists('Memcache') and $option['cache'] == 1)
        $cache->set($cache_key, json_encode($prices), MEMCACHE_COMPRESSED, $option['time'] * 60 * 60 * 24);
    else if (class_exists('Memcached') and $option['cache'] == 1)
        $cache->set($cache_key, json_encode($prices), $option['time'] * 60 * 60 * 24);
    else
        $cache->set($cache_key, json_encode($prices), $option['time'] * 60 * 60 * 24);
}

if (is_array($prices)) {
    $disp = null;

    if (!empty($prices['sdek'])) {
        PHPShopParser::set('delivery_name', __('СДЭК'));
        PHPShopParser::set('delivery_days', $DeliveryWidget->printDays($prices['sdek_days']));
        PHPShopParser::set('delivery_price', __('от') . ' ' . $prices['sdek'] . ' руб');
        $disp .= PHPShopParser::file($GLOBALS['SysValue']['templates']['deliverywidget']['delivery'], true, false, true);
    }

    if (!empty($prices['pochta'])) {
        PHPShopParser::set('delivery_name', __('Почта 1 класс'));
        PHPShopParser::set('delivery_days', $DeliveryWidget->printDays($prices['pochta_days']));
        PHPShopParser::set('delivery_price', __('от') . ' ' . $prices['pochta'] . ' руб');
        $disp .= PHPShopParser::file($GLOBALS['SysValue']['templates']['deliverywidget']['delivery'], true, false, true);
    }

    if (!empty($prices['yandex'])) {
        PHPShopParser::set('delivery_name', __('Яндекс.Доставка'));
        PHPShopParser::set('delivery_days', $DeliveryWidget->printDays($prices['yandex_days']));
        PHPShopParser::set('delivery_price', __('от') . ' ' . $prices['yandex'] . ' руб');
        $disp .= PHPShopParser::file($GLOBALS['SysValue']['templates']['deliverywidget']['delivery'], true, false, true);
    }

    echo $disp;
}
