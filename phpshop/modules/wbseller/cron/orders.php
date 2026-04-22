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
PHPShopObj::loadClass("valuta");

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
include_once dirname(__FILE__) . '/../class/WbSeller.php';
$WbSeller = new WbSeller();
$status = $WbSeller->status_import;

if (isset($_GET['date_start']))
    $date_start = $_GET['date_start'];
else
    $date_start = PHPShopDate::get((time() - 2592000 / 30), false, true);

if (isset($_GET['date_end']))
    $date_end = $_GET['date_end'];
else
    $date_end = PHPShopDate::get((time() - 1), false, true);

// Заказы
if (!empty($status))
    $orders = $WbSeller->getOrderList($date_start, $date_end, $status)['orders'];

$count = 0;

if (is_array($orders)) {

    foreach ($orders as $order_info) {

        $order = [];

        // Заказ уже загружен
        if ($WbSeller->checkOrderBase($order_info['id']))
            continue;

        if ($WbSeller->type == 2) {
            $type = 'uid';
        } else {
            $type = 'id';
        }

        // Данные по товару
        $product = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['id,uid,name,pic_small'], [$type => '="' . (string) PHPShopString::utf8_win1251($order_info['article']) . '"']);
        
        
        if (empty($product) and !empty($WbSeller->create_products)) {

            // Создание товара
            $product_id = $WbSeller->addProduct($order_info['skus'][0]);
            $product = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['id,uid,name,pic_small'], ['id' => '=' .(int) $product_id]); 
        }
        
        if(empty($product))
           continue;

        $name = 'WB';
        $phone = null;
        $mail = null;
        $comment = null;

        // Таблица заказов
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $sum = $weight = 0;

        $order['Cart']['cart'][$product['id']]['id'] = $product['id'];
        $order['Cart']['cart'][$product['id']]['uid'] = $product["uid"];
        $order['Cart']['cart'][$product['id']]['name'] = $product['name'];
        $order['Cart']['cart'][$product['id']]['price'] = $order_info['price'] / 100;
        $order['Cart']['cart'][$product['id']]['num'] = 1;
        $order['Cart']['cart'][$product['id']]['weight'] = '';
        $order['Cart']['cart'][$product['id']]['ed_izm'] = '';
        $order['Cart']['cart'][$product['id']]['pic_small'] = $product['pic_small'];
        $order['Cart']['cart'][$product['id']]['parent'] = 0;
        $order['Cart']['cart'][$product['id']]['user'] = 0;
        $qty = 1;
        $sum = $order_info['price'] / 100;
        $weight = $product['weight'];


        $order['Cart']['num'] = $qty;
        $order['Cart']['sum'] = $sum;
        $order['Cart']['weight'] = $weight;
        $order['Cart']['dostavka'] = 0;

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
        $order['Person']['dostavka_metod'] = '';
        $order['Person']['discount'] = 0;
        $order['Person']['user_id'] = '';
        $order['Person']['order_metod'] = '';
        $insert['dop_info_new'] = $comment;

        // данные для записи в БД
        $insert['datas_new'] = time();
        $insert['uid_new'] = $WbSeller->setOrderNum();
        $insert['orders_new'] = serialize($order);
        $insert['fio_new'] = $name;
        $insert['tel_new'] = $phone;
        $insert['city_new'] = PHPShopString::utf8_win1251($order_info['prioritySc'][0]);
        $insert['statusi_new'] = $WbSeller->status;
        $insert['status_new'] = serialize(array("maneger" => __('WB заказ &#8470;' . $order_info['id'])));
        $insert['sum_new'] = $order['Cart']['sum'];
        $insert['wbseller_order_data_new'] = $order_info['id'];

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
}


echo "Загружено " . (int) $count . " заказов с WB";
