<?php

session_start();

// Включение
$enabled = false;

if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_classPath = realpath(dirname(__FILE__)) . "/../../../";
    $enabled = true;
} else
    $_classPath = "../../../";

include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("date");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("cart");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("text");
PHPShopObj::loadClass("lang");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("product");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopSystem = new PHPShopSystem();
$_SESSION['lang'] = $PHPShopSystem->getSerilizeParam("admoption.lang_adm");
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'admin'));

// Авторизация
if ($_GET['s'] == md5($PHPShopBase->SysValue['connect']['host'] . $PHPShopBase->SysValue['connect']['dbase'] . $PHPShopBase->SysValue['connect']['user_db'] . $PHPShopBase->SysValue['connect']['pass_db']))
    $enabled = true;

if (empty($enabled))
    exit("Ошибка авторизации!");

// Настройки модуля
include_once dirname(__FILE__) . '/../class/OzonSeller.php';
$OzonSeller = new OzonSeller();


if (isset($_GET['date_start']))
    $date_start = $_GET['date_start'];
else
    $date_start = PHPShopDate::get((time() - 2592000 / 30), false, true);

if (isset($_GET['date_end']))
    $date_end = $_GET['date_end'];
else
    $date_end = PHPShopDate::get((time() - 1), false, true);

// Заказы FBS
$ordersFbs = $OzonSeller->getOrderListFbs($date_start, $date_end, $option['status_import']);
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

$count = 0;

if (is_array($orders))
    foreach ($orders as $row) {

        $order = [];

        // Номер заказа
        $posting_number = $row['posting_number'];

        // Заказ уже загружен
        if ($OzonSeller->checkOrderBase($posting_number))
            continue;

        // Данные по заказу Озон
        $order_info = $OzonSeller->getOrderFbs($posting_number)['result'];

        // Проверка статуса
        if ($order_info['status'] != $OzonSeller->status_import) {
            continue;
        }


        $name = 'OZON';
        $phone = null;
        $mail = null;
        $comment = null;

        // Таблица заказов
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $qty = $sum = $weight = 0;

        // Ключ обновления
        if ($OzonSeller->type == 2)
            $type = 'uid';
        else
            $type = 'id';

        $data = $order_info['products'];
        if (is_array($data))
            foreach ($data as $row) {

                // Данные по товару
                $product = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['id,uid,name,pic_small'], [$type => '="' . (string) PHPShopString::utf8_win1251($row['offer_id']) . '"']);


                if (empty($product) and ! empty($OzonSeller->create_products)) {

                    // Создание товара
                    $product_id = $OzonSeller->addProduct($row['offer_id']);
                    $product = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['id,uid,name,pic_small'], ['id' => '=' . (int) $product_id]);
                }


                if (empty($product))
                    continue;

                $order['Cart']['cart'][$product['id']]['id'] = $product['id'];
                $order['Cart']['cart'][$product['id']]['uid'] = $product['uid'];
                $order['Cart']['cart'][$product['id']]['name'] = $product['name'];
                $order['Cart']['cart'][$product['id']]['price'] = $row['price'];
                $order['Cart']['cart'][$product['id']]['num'] = $row['quantity'];
                $order['Cart']['cart'][$product['id']]['weight'] = '';
                $order['Cart']['cart'][$product['id']]['ed_izm'] = '';
                $order['Cart']['cart'][$product['id']]['pic_small'] = $product['pic_small'];
                $order['Cart']['cart'][$product['id']]['parent'] = 0;
                $order['Cart']['cart'][$product['id']]['user'] = 0;
                $qty += $row['quantity'];
                $sum += $row['price'] * $row['quantity'];
                $weight += $product['weight'];
            }


        if (empty($order['Cart']['cart']))
            continue;

        $order['Cart']['num'] = $qty;
        $order['Cart']['sum'] = $sum;
        $order['Cart']['weight'] = $weight;
        $order['Cart']['dostavka'] = (int) $order_info['delivery_price'];

        $order['Person']['ouid'] = '';
        $order['Person']['data'] = time();
        $order['Person']['time'] = '';
        $order['Person']['mail'] = $mail;
        $order['Person']['name_person'] = $name;
        $order['Person']['org_name'] = '';
        $order['Person']['org_inn'] = '';
        $order['Person']['org_kpp'] = '';
        $order['Person']['tel_code'] = '';
        $order['Person']['tel_name'] = '';
        $order['Person']['adr_name'] = '';
        $order['Person']['dostavka_metod'] = (int) $OzonSeller->delivery;
        $order['Person']['discount'] = 0;
        $order['Person']['user_id'] = '';
        $order['Person']['dos_ot'] = '';
        $order['Person']['dos_do'] = '';
        $order['Person']['order_metod'] = '';
        $insert['dop_info_new'] = $comment;

        // данные для записи в БД
        $insert['datas_new'] = time();
        $insert['uid_new'] = $OzonSeller->setOrderNum();
        $insert['orders_new'] = serialize($order);
        $insert['fio_new'] = $name;
        $insert['tel_new'] = $phone;
        $insert['city_new'] = PHPShopString::utf8_win1251($order_info['result']['delivery_method']['name'], true);
        $insert['statusi_new'] = $OzonSeller->status;
        $insert['status_new'] = serialize(array("maneger" => __('OZON заказ') . ' &#8470;' . $posting_number));
        $insert['sum_new'] = $order['Cart']['sum'];
        $insert['ozonseller_order_data_new'] = $posting_number;

        // Запись в базу
        $orderId = $PHPShopOrm->insert($insert);


        // Оповещение пользователя о новом статусе и списание со склада
        PHPShopObj::loadClass("order");
        $PHPShopOrderFunction = new PHPShopOrderFunction($orderId);
        $PHPShopOrderFunction->changeStatus($insert['statusi_new'], 0);

        // Telegram
        $chat_id_telegram = $PHPShopSystem->getSerilizeParam('admoption.telegram_admin');
        if (!empty($chat_id_telegram) and $PHPShopSystem->ifSerilizeParam('admoption.telegram_order', 1)) {

            PHPShopObj::loadClass('bot');

            $bot = new PHPShopTelegramBot();
            $msg = $PHPShopBase->SysValue['lang']['mail_title_adm'] . $order_info['id'] . " - " . $product['name'] . " [" . $insert['sum_new'] . " " . $PHPShopOrderFunction->default_valuta_name . ']';
            $bot->send($chat_id_telegram, PHPShopString::win_utf8($msg));
        }

        // VK
        $chat_id_vk = $PHPShopSystem->getSerilizeParam('admoption.vk_admin');
        if (!empty($chat_id_vk) and $PHPShopSystem->ifSerilizeParam('admoption.vk_order', 1)) {

            PHPShopObj::loadClass('bot');

            $bot = new PHPShopVKBot();
            $msg = $PHPShopBase->SysValue['lang']['mail_title_adm'] . $order_info['id'] . " - " . $product['name'] . " [" . $insert['sum_new'] . " " . $PHPShopOrderFunction->default_valuta_name . ']';
            $bot->send($chat_id_vk, PHPShopString::win_utf8($msg));
        }


        $count++;
    }


echo "Загружено " . (int) $count . " заказов с OZON";
