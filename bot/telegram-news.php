<?php

/**
 * Telegram RSS Bot
 * @package PHPShopRest
 * @author PHPShop Software
 * @version 1.1
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
PHPShopObj::loadClass("date");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'shop'));

// ¬ход€щие данные
$body = file_get_contents('php://input');
$chat = json_decode($body, true);

if (is_array($chat)) {

    $bot = new PHPShopTelegramBot();
    
    if ($bot->check_notification() and !empty($bot->news_enabled))
        $bot->add_news($chat['message']);

    exit('ok');
}