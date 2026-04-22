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

include_once dirname(__FILE__) . '/../class/VkSeller.php';
$VkSeller = new VkSeller();



if (isset($_GET['date_start']))
    $date_start = $_GET['date_start'];
else
    $date_start = PHPShopDate::get((time() - 2592000), false, true);

if (isset($_GET['date_end']))
    $date_end = $_GET['date_end'];
else
    $date_end = PHPShopDate::get((time() - 1), false, true);

// Заказы
if ($VkSeller->model == 'API') {
    $orders = $VkSeller->getOrderList($date_start, $date_end)['response']['items'];
}

$count = 0;
if (is_array($orders))
    foreach ($orders as $row) {

        $order = [];

        // Номер заказа
        $posting_number = $row['id'];

        // Заказ уже загружен
        if ($VkSeller->checkOrderBase($row['id']))
            continue;

        // Данные по заказу
        $order_info = $VkSeller->getOrder($row['id'])['response']['order'];

        // Проверка статуса
        if ($order_info['status'] != $VkSeller->status_import) {
            continue;
        }

        $name = PHPShopString::utf8_win1251($order_info['recipient']['name'], true);
        $phone = PHPShopString::utf8_win1251($order_info['recipient']['phone'], true);
        $mail = null;
        $comment = PHPShopString::utf8_win1251($order_info['comment'], true);
        $pay = PHPShopString::utf8_win1251($order_info['payment']['status'], true);

        // Таблица заказов
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $qty = $sum = $weight = 0;

        $data = $order_info['preview_order_items'];
        if (is_array($data))
            foreach ($data as $row) {

                $product = new PHPShopProduct($row['item_id'], 'export_vk_id');

                // Проверка по ИД и артикулу
                if (empty($product->getName())) {
                    if ($VkSeller->vk_options['type'] == 1)
                        $product = new PHPShopProduct($row['item']['sku'], 'id');
                    else
                        $product = new PHPShopProduct($row['item']['sku'], 'uid');
                }

                if (empty($product->getName()))
                    continue;

                $id = $product->getParam('id');
                $price = round($row['price']['amount'] / 100);
                $order['Cart']['cart'][$id]['id'] = $product->getParam('id');
                $order['Cart']['cart'][$id]['uid'] = $product->getParam("uid");
                $order['Cart']['cart'][$id]['name'] = $product->getName();
                $order['Cart']['cart'][$id]['price'] = $price;
                $order['Cart']['cart'][$id]['num'] = $row['quantity'];
                $order['Cart']['cart'][$id]['weight'] = '';
                $order['Cart']['cart'][$id]['ed_izm'] = '';
                $order['Cart']['cart'][$id]['pic_small'] = $product->getImage();
                $order['Cart']['cart'][$id]['parent'] = 0;
                $order['Cart']['cart'][$id]['user'] = 0;
                $qty += $row['quantity'];
                $sum += $price * $row['quantity'];
                $weight += $product->getParam('weight');
            }

        $order['Cart']['num'] = $qty;
        $order['Cart']['sum'] = $sum;
        $order['Cart']['weight'] = $weight;
        $order['Cart']['dostavka'] = intval($order_info['total_price']['amount'] / 100 - $sum);

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
        $order['Person']['dostavka_metod'] = (int) $VkSeller->delivery;
        $order['Person']['discount'] = 0;
        $order['Person']['user_id'] = '';
        $order['Person']['order_metod'] = '';
        $insert['dop_info_new'] = $comment;

        // данные для записи в БД
        $insert['datas_new'] = time();
        $insert['uid_new'] = $VkSeller->setOrderNum();
        $insert['orders_new'] = serialize($order);
        $insert['fio_new'] = $name;
        $insert['tel_new'] = $phone;
        $insert['city_new'] = PHPShopString::utf8_win1251($order_info['delivery']['address'] . ' ' . $order_info['delivery']['type'], true);
        $insert['statusi_new'] = $VkSeller->status;
        $insert['status_new'] = serialize(array("maneger" => __('VK заказ &#8470;' . $posting_number) . ', ' . $pay));
        $insert['sum_new'] = $order['Cart']['sum'];
        $insert['vkseller_order_data_new'] = $posting_number;

        // Запись в базу
        $orderId = $PHPShopOrm->insert($insert);

        // Оповещение пользователя о новом статусе и списание со склада
        if (!empty($insert['statusi_new'])) {
            PHPShopObj::loadClass("order");
            $PHPShopOrderFunction = new PHPShopOrderFunction($orderId);
            $PHPShopOrderFunction->changeStatus($insert['statusi_new'], 0);
        }

        // Telegram
        $chat_id_telegram = $PHPShopSystem->getSerilizeParam('admoption.telegram_admin');
        if (!empty($chat_id_telegram) and $PHPShopSystem->ifSerilizeParam('admoption.telegram_order', 1)) {

            PHPShopObj::loadClass('bot');

            $bot = new PHPShopTelegramBot();
            $msg = $PHPShopBase->SysValue['lang']['mail_title_adm'] . $posting_number . " - " . $product->getName() . " [" . $insert['sum_new'] . " " . $PHPShopOrderFunction->default_valuta_name . ']';
            $bot->send($chat_id_telegram, PHPShopString::win_utf8($msg));
        }

        // VK
        $chat_id_vk = $PHPShopSystem->getSerilizeParam('admoption.vk_admin');
        if (!empty($chat_id_vk) and $PHPShopSystem->ifSerilizeParam('admoption.vk_order', 1)) {

            PHPShopObj::loadClass('bot');

            $bot = new PHPShopVKBot();
            $msg = $PHPShopBase->SysValue['lang']['mail_title_adm'] . $posting_number . " - " . $product->getName() . " [" . $insert['sum_new'] . " " . $PHPShopOrderFunction->default_valuta_name . ']';
            $bot->send($chat_id_vk, PHPShopString::win_utf8($msg));
        }


        $count++;
    }

echo "Загружено " . (int) $count . " заказов с VK";
