<?php

/**
 * VK ID
 * @package PHPShopAjaxElements
 */
session_start();

$_classPath = "../";
include($_classPath . "class/obj.class.php");
include($_classPath . "inc/elements.inc.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("nav");
PHPShopObj::loadClass("lang");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("product");

// Системные настройки
$PHPShopSystem = new PHPShopSystem();

$PHPShopNav = new PHPShopNav();

$PHPShopRecaptchaElement = new PHPShopRecaptchaElement();

$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'shop'));
$client_id = $PHPShopSystem->getSerilizeParam('admoption.vk_id');

if (!empty($_GET['access_token']) and ! empty($client_id)) {

    $api = 'https://id.vk.ru/oauth2/user_info';

    $ch = curl_init();
    $header = [
        'Content-Type: application/x-www-form-urlencoded',
    ];

    $params = [
        'access_token' => $_GET['access_token'],
        'client_id' => $client_id,
    ];

    curl_setopt($ch, CURLOPT_URL, $api);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    if (!empty($params)) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params, '', '&'));
    }

    $result = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($result, true)['user'];

    if (is_array($json) and ! empty($json['email'])) {

        // Создаём нового пользователя, или авторизуем старого
        PHPShopObj::importCore('users');
        $PHPShopUsers = new PHPShopUsers();
        $PHPShopUsers->stop_redirect = true;

        $userId = $PHPShopUsers->add_user_from_order($json['email'], PHPShopString::utf8_win1251($json['first_name'] . ' ' . $json['last_name']), $json['phone']);

        if (!empty($userId)) {

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
            $data = $PHPShopOrm->getOne(['*'], ['id' => '=' . (int) $userId]);

            setcookie("UserLogin", trim(trim($data['login'])), time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);
            setcookie("UserPassword", base64_decode($data['password']), time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);
            setcookie("UserChecked", 1, time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);

            header('Location: https://' . $_SERVER['SERVER_NAME'] . urldecode($_GET['state']));
        }
    }
}