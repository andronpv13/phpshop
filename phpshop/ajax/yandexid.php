<?php

/**
 * яндекс ID
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

// —истемные настройки
$PHPShopSystem = new PHPShopSystem();

$PHPShopNav = new PHPShopNav();

$PHPShopRecaptchaElement = new PHPShopRecaptchaElement();

$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'shop'));

$api = 'https://login.yandex.ru/info?';

if (!empty($_POST['access_token'])) {

    $ch = curl_init();
    $header = [
        'Content-Type: application/json',
        'Authorization: OAuth ' . $_POST['access_token']
    ];

    curl_setopt($ch, CURLOPT_URL, $api);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    $result = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($result, true);

    if (is_array($json) and ! empty($json['default_email'])) {

        // —оздаЄм нового пользовател€, или авторизуем старого
        PHPShopObj::importCore('users');
        $PHPShopUsers = new PHPShopUsers();
        $PHPShopUsers->stop_redirect = true;
        $userId = $PHPShopUsers->add_user_from_order($json['default_email'], PHPShopString::utf8_win1251($json['real_name']), $json['default_phone']['number']);

        if (!empty($userId)) {
            
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
            $data = $PHPShopOrm->getOne(['*'],['id'=>'='.(int) $userId]);

            setcookie("UserLogin", trim(trim($data['login'])), time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);
            setcookie("UserPassword", base64_decode($data['password']), time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);
            setcookie("UserChecked", 1, time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);

            echo json_encode(['user' => $data['login'], 'success' => true]);
        }
    }
}
else {
    echo '<head><script src="https://yastatic.net/s3/passport-sdk/autofill/v1/sdk-suggest-token-with-polyfills-latest.js"></script></head><body><script>YaSendSuggestToken("https://'.$_SERVER['SERVER_NAME'].'")
    </script>
</body>';
}