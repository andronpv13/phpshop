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
$PHPShopModules->checkInstall('megamarket');
$PHPShopSystem = new PHPShopSystem();
$PHPShopValutaArray = new PHPShopValutaArray();

// Настройки модуля
include_once dirname(__FILE__) . '/class/Megamarket.php';
$Megamarket = new Megamarket();

// Входящие данные
$data = json_decode(file_get_contents('php://input'), true);
$success = 0;

// Таблица заказов
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);

// Роутер
switch ($_SERVER["PATH_INFO"]) {

    // Новый заказа
    case('/' . md5($Megamarket->api_key) . '/new'):
        
       

        if (!empty($data['data']['shipments'][0]['shipmentId'])) {
            
            // Заказ уже загружен
            if ($Megamarket->checkOrderBase($data['data']['shipments'][0]['shipmentId']))
                continue;
            
            $name = PHPShopString::utf8_win1251($data['data']['shipments'][0]['label']['fullName']);
            if(empty($name))
                $name=__('Мегамаркет');
            
            $phone = null;
            $mail = null;
            $comment = null;


            $qty = $sum = $weight = 0;

            if (is_array($data['data']['shipments'][0]['items'])) {

                if ($Megamarket->type == 2) {
                    $type = 'uid';
                } else {
                    $type = 'id';
                }

                foreach ($data['data']['shipments'][0]['items'] as $row) {

                    // Данные по товару
                    $product = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['id,uid,name,pic_small'], [$type => '="' . (string) PHPShopString::utf8_win1251($row['offerId']) . '"']);

                    if (empty($product))
                        continue;

                    $order['Cart']['cart'][$product['id']]['id'] = $product['id'];
                    $order['Cart']['cart'][$product['id']]['uid'] = $product['uid'];
                    $order['Cart']['cart'][$product['id']]['name'] = $product['name'];
                    $order['Cart']['cart'][$product['id']]['price'] = $row['finalPrice'];
                    $order['Cart']['cart'][$product['id']]['weight'] = '';
                    $order['Cart']['cart'][$product['id']]['ed_izm'] = '';
                    $order['Cart']['cart'][$product['id']]['pic_small'] = $product['pic_small'];
                    $order['Cart']['cart'][$product['id']]['parent'] = 0;
                    $order['Cart']['cart'][$product['id']]['user'] = 0;
                    $qty += $row['quantity'];
                    $order['Cart']['cart'][$product['id']]['num'] += $row['quantity'];
                    $sum += $row['finalPrice'] * $row['quantity'];
                    $weight += $product['weight'];
                }

                if (empty($order['Cart']['cart']))
                    continue;

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
                $order['Person']['dostavka_metod'] = (int) $Megamarket->delivery;
                $order['Person']['discount'] = 0;
                $order['Person']['user_id'] = '';
                $order['Person']['dos_ot'] = '';
                $order['Person']['dos_do'] = '';
                $order['Person']['order_metod'] = '';
                $insert['dop_info_new'] = PHPShopString::utf8_win1251($data['data']['shipments'][0]['label']['address']);

                // данные для записи в БД
                $insert['datas_new'] = time();
                $insert['uid_new'] = $Megamarket->setOrderNum();
                $insert['orders_new'] = serialize($order);
                $insert['fio_new'] = $name;
                $insert['tel_new'] = $phone;
                $insert['city_new'] = PHPShopString::utf8_win1251($data['data']['shipments'][0]['label']['city']);
                $insert['state_new'] = PHPShopString::utf8_win1251($data['data']['shipments'][0]['label']['region']);
                $insert['statusi_new'] = $Megamarket->status;
                $insert['status_new'] = serialize(array("maneger" => __('Мегамаркет заказ') . ' &#8470;' . $data['data']['shipments'][0]['shipmentId']));
                $insert['sum_new'] = $order['Cart']['sum'];
                $insert['megamarket_order_id_new'] = $data['data']['shipments'][0]['shipmentId'];

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
                    $msg = $PHPShopBase->SysValue['lang']['mail_title_adm'] . $data['data']['shipments'][0]['shipmentId'] . " - " . $product['name'] . " [" . $insert['sum_new'] . " " . $PHPShopOrderFunction->default_valuta_name . ']';
                    $bot->send($chat_id_telegram, PHPShopString::win_utf8($msg));
                }

                // VK
                $chat_id_vk = $PHPShopSystem->getSerilizeParam('admoption.vk_admin');
                if (!empty($chat_id_vk) and $PHPShopSystem->ifSerilizeParam('admoption.vk_order', 1)) {

                    PHPShopObj::loadClass('bot');

                    $bot = new PHPShopVKBot();
                    $msg = $PHPShopBase->SysValue['lang']['mail_title_adm'] . $data['data']['shipments'][0]['shipmentId'] . " - " . $product['name'] . " [" . $insert['sum_new'] . " " . $PHPShopOrderFunction->default_valuta_name . ']';
                    $bot->send($chat_id_vk, PHPShopString::win_utf8($msg));
                }

                $Megamarket->log($data, $data['data']['shipments'][0]['shipmentId'], $_SERVER["PATH_INFO"]);
                $success = 1;
            }
        }


        break;

    // Отмена заказа
    case '/' . md5($Megamarket->api_key) . '/cancel':
        
        $Megamarket->log($data, $data['data']['shipments'][0]['shipmentId'], $_SERVER["PATH_INFO"]);

        // Заказ не загружен
        if (!$Megamarket->checkOrderBase($data['data']['shipments'][0]['shipmentId']))
            continue;
        else
            $orderId = $PHPShopOrm->getOne(['id'], ['megamarket_order_id' => '="' . $data['data']['shipments'][0]['shipmentId'].'"'])['id'];

        if (empty($orderId))
            continue;

        PHPShopObj::loadClass("order");
        $PHPShopOrderFunction = new PHPShopOrderFunction($orderId);
        $PHPShopOrderFunction->changeStatus(1, $Megamarket->setOrderNum());

        $success = 1;

        break;

    default:
        exit('Login Error!');
}


$result = [
    "data" => [],
    "meta" => [],
    "success" => $success
];

header("HTTP/1.1 200");
header("Content-Type: application/json");
echo json_encode($result);
