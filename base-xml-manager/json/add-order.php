<?php
/**
 * Создание заказа пользователем через JSON
 * @package PHPShopExchange
 * @author PHPShop Software
 * @version 1.0
 */

// Включение
$enabled= false;

// Токен
$TOKEN = '{TOKEN}';

// Способ оплаты Счет в банк
$payment = 3;

if (empty($enabled)){
     header('HTTP/1.1 503 Service Temporarily Unavailable');
     header('Status: 503 Service Temporarily Unavailable');
     exit('<h1>503 Service Temporarily Unavailable</h1>');
}


$_classPath = "../../phpshop/";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("basexml");
PHPShopObj::loadClass("string");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("lang");

// Подключаем БД
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopSystem = new PHPShopSystem();
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'shop'));

$API_URL = 'https://' . $_SERVER['SERVER_NAME'] . '/base-xml-manager/json/';

function request($properties) {
    global $API_URL, $TOKEN;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $API_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($properties));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'TOKEN: ' . $TOKEN,
        'Content-Type: application/json',
    ));

    return json_decode(curl_exec($ch), true);
}

function status($status) {
    header("Content-Type: application/json");
    exit(json_encode(['status' => $status]));
}

function mailcartforma($cart) {
    global $PHPShopSystem;

    $disp = null;
    if (is_array($cart))
        foreach ($cart as $val) {

            $val['uid'] = $val['uid'];
            $val['ed_izm'] = __('шт.');

            $price = number_format($val['price'], intval($PHPShopSystem->getSerilizeParam("admoption.price_znak")), '.', ' ');
            $price_n = number_format($val['price_n'], intval($PHPShopSystem->getSerilizeParam("admoption.price_znak")), '.', ' ');
            $sum = number_format($val['price'] * $val['num'], intval($PHPShopSystem->getSerilizeParam("admoption.price_znak")), '.', ' ');

            PHPShopParser::set('product_mail_price', $price);
            PHPShopParser::set('product_mail_price_n', $price_n);
            PHPShopParser::set('product_mail_pic', $val['pic_small']);
            PHPShopParser::set('product_mail_uid', $val['uid']);
            PHPShopParser::set('product_mail_name', $val['name']);
            PHPShopParser::set('product_mail_num', $val['num']);
            PHPShopParser::set('product_mail_sum', $sum);
            PHPShopParser::set('product_mail_ed_izm', $val['ed_izm']);
            PHPShopParser::set('product_mail_currency', 'руб.');
            PHPShopParser::set('product_mail_id', $val['id']);
            PHPShopParser::set('serverShop', $_SERVER['SERVER_NAME']);

            $disp .= PHPShopParser::file('../../phpshop/lib/templates/order/product_mail.tpl', true, true, true);
        }
    return $disp;
}

// Авторизация
if (PHPShopSecurity::true_email($_SERVER['HTTP_LOGIN']) and PHPShopSecurity::true_passw($_SERVER['HTTP_PASSWORD'])) {

    $properties = [
        'from' => 'shopusers',
        'method' => 'select',
        'vars' => '*',
        'where' => "login='" . $_SERVER['HTTP_LOGIN'] . "'",
        'order' => 'id desc',
        'limit' => '1'
    ];

    $user = request($properties)['data'];

    // Реквизиты
    if (is_array($user['data_adres']['list'])) {
        $org_inn = $user['data_adres']['list'][0]['org_inn_new'];
        $org_name = $user['data_adres']['list'][0]['org_name_new'];
        $tel = $user['data_adres']['list'][0]['tel_new'];
        $org_yur_adres = $user['data_adres']['list'][0]['org_yur_adres_new'];
    }

    if (base64_decode($user['password']) != $_SERVER['HTTP_PASSWORD']) {
        status('user login error');
    }
} else {
    status('user login error');
}


// Номер заказа
$get_order_num = request(['method' => 'get_order_num']);

// Что заказать
$product = json_decode(file_get_contents("php://input"), true);

if (is_array($product))
    foreach ($product as $k => $item) {
        $where .= ' uid="' . $k . '" or';
    }

$properties = [
    'from' => 'products',
    'method' => 'select',
    'vars' => '*',
    'where' => substr($where, 0, strlen($where) - 2),
    'order' => 'id desc',
    'limit' => '100'
];

// Данные по товарам
$product_list = request($properties)['data'];

if (is_array($product_list))
    foreach ($product_list as $item) {
        $cart[$item['id']] = [
            'id' => $item['id'],
            'name' => str_replace(['-'],[' '],PHPShopString::toLatin(PHPShopString::utf8_win1251($item['name']),false)),
            'price' => $item['price'],
            'uid' => $item['uid'],
            'num' => $product[$item['uid']],
            'pic_small' => $item['pic_small'],
            'total' => intval($item['price'] * $product[$item['uid']])
        ];
        $total += intval($item['price'] * $product[$item['uid']]);
        $num += $product[$item['uid']];
    } else {
    status('no items');
}

$properties = array(
    'from' => 'orders',
    'method' => 'insert',
    'vars' => array(
        'datas' => time(),
        'fio' => $user['name'],
        'tel' => $user['tel'],
        'uid' => $get_order_num['data']['uid'],
        'sum' => $total,
        'user' => $user['id'],
        'org_name' => $org_name,
        'tel' => $tel,
        'org_inn' => $org_inn,
        'org_yur_adres' => $org_yur_adres,
        'orders' => serialize(array(
            'Cart' => array(
                'cart' => $cart,
                'num' => $num,
                'sum' => $total,
                'weight' => 0,
                'dostavka' => 0
            ), $cart,
            'Person' => array(
                'mail' => $user['login'],
                'dostavka_metod' => 0,
                'discount' => 0,
                'order_metod' => $payment
            ),
        )),
    ),
    'where' => '',
    'order' => '',
    'limit' => ''
);

$result = request($properties);
$result['order'] = $get_order_num['data']['uid'];
header("Content-Type: application/json");
echo json_encode($result);

// Сообщение покупателю
if ($result['status'] == 'succes') {

    PHPShopParser::set('sum', $total);
    PHPShopParser::set('cart', mailcartforma($cart));
    PHPShopParser::set('currency', 'руб.');
    PHPShopParser::set('discount', 0);
    PHPShopParser::set('discount_sum', 0);
    PHPShopParser::set('deliveryPrice', 0);
    PHPShopParser::set('total', $total);
    PHPShopParser::set('shop_name', $PHPShopSystem->getName());
    PHPShopParser::set('ouid', $result['order']);
    PHPShopParser::set('orderId', $result['order']);
    PHPShopParser::set('date', date("d-m-y"));
    PHPShopParser::set('adr_name', '');
    PHPShopParser::set('mail', $user['mail']);
    PHPShopParser::set('bonus_minus', 0);
    PHPShopParser::set('bonus_plus', 0);
    PHPShopParser::set('company', $PHPShopSystem->getParam('name'));
    PHPShopParser::set('tel', $tel);
    PHPShopParser::set('user_name', PHPShopString::utf8_win1251($user['name']));
    PHPShopParser::set('payment', 'Счет в банк');

    // Отсылаем письмо покупателю
    $title = "Ваш заказ " . $result['order'] . " успешно оформлен";
    $PHPShopMail = new PHPShopMail($user['mail'], $PHPShopSystem->getEmail(), $title, '', true, true);
    $content = ParseTemplateReturn('../../phpshop/lib/templates/order/usermail.tpl', true);
    $PHPShopMail->sendMailNow($content);

    // Отсылаем письмо администратору
    PHPShopParser::set('shop_admin', "http://" . $_SERVER['SERVER_NAME'] . "/phpshop/admpanel/");
    PHPShopParser::set('time', date("d-m-y H:i a"));
    PHPShopParser::set('ip', $_SERVER['REMOTE_ADDR']);
    $title_adm = "Поступил заказ " . $result['order'] . "/" . date("d-m-y");
    $PHPShopMail = new PHPShopMail($PHPShopSystem->getEmail(), $PHPShopSystem->getEmail(), $title_adm, '', true, true, array('replyto' => $user['mail']));
    $content_adm = ParseTemplateReturn('../../phpshop/lib/templates/order/adminmail.tpl', true);
    $PHPShopMail->sendMailNow($content_adm);
}