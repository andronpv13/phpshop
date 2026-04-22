<?php

session_start();
$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass(array("base", "system", "yandexcloud", "orm", "date", "security", "string", "parser", "lang"));

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopBase->chekAdmin();

$PHPShopSystem = new PHPShopSystem();

$YandexGPT = new YandexGPT();
include($_classPath . '/lib/parsedown/Parsedown.php');
$html = false;
switch ($_POST['role']) {
    case "catalog_descrip":
        $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_catalog_description_role');
        break;

    case "catalog_title":
        $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_catalog_title_role');
        break;

    case "catalog_content":
        $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_catalog_content_role');
        $html = true;
        break;

    case "product_descrip":
        $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_product_descrip_role');
        break;

    case "product_title":
        $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_product_title_role');
        break;

    case "product_content":
        $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_product_content_role');
        $html = true;
        break;

    case "product_description":
        $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_product_description_role');
        $html = true;
        break;

    case "product_comment":
        $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_product_comment_role');
        break;

    case "news_content":
        $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_news_content_role');
        $html = true;
        break;

    case "news_description":
        $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_news_description_role');
        break;

    case "news_sendmail":
        $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_news_sendmail_role');
        $html = true;
        break;

    case "page_content":
        $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_page_content_role');
        $html = true;
        break;

    case "page_description":
        $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_page_description_role');
        break;

    case "page_descrip":
        $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_page_descrip_role');
        break;

    case "page_title":
        $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_page_title_role');
        break;

    case "gbook_review":
        $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_gbook_review_role');
        break;

    case "gbook_answer":
        $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_gbook_answer_role');
        $html = true;
        break;

    case "site_title":
        $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_site_title_role');
        break;

    case "site_descrip":
        $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_site_descrip_role');
        break;

    default :
        $system = PHPShopString::utf8_win1251($_POST['role']);
        if (!empty($_POST['html']))
            $html = $_POST['html'];
}

$message = $_POST['text'];
$result = $YandexGPT->text(PHPShopString::utf8_win1251(strip_tags($message)), $system, $PHPShopSystem->getSerilizeParam('ai.yandexgpt_temperature'), (int) $_POST['length']);

if (empty($html)) {
    $text = str_replace(['*', '\n', '\r'], ['', '', ''], $result['result']['alternatives'][0]['message']['text']);
    $text = preg_replace("/\r|\n/", ' ', $text);
} else
    $text = $YandexGPT->html($result['result']['alternatives'][0]['message']['text']);

header("Content-Type: application/json;charset=UTF-8");
if (!empty($text)) {
    echo json_encode([
        'text' => $text,
        'success' => true,
        'role' => $_POST['role']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'role' => $_POST['role']
    ]);
}