<?php

/**
 * VK Reviews Bot
 * @package PHPShopRest
 * @author PHPShop Software
 * @version 1.0
 */
$_classPath = '../phpshop/';
include($_classPath . 'class/obj.class.php');
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("bot");
PHPShopObj::loadClass("string");
PHPShopObj::loadClass("lang");
PHPShopObj::loadClass("security");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'shop'));

// ¬ход€щие данные
$body = file_get_contents('php://input');
$chat = json_decode($body, true);

if (is_array($chat)) {

    $bot = new PHPShopVKBot();

    if ($chat['secret'] == $bot->reviews_secret) {

        if ($chat['type'] == 'confirmation') {
            echo $bot->reviews_confirmation;
        } else {
            $bot->add_reviews($chat);
            exit('ok');
        }
    }
}
?>