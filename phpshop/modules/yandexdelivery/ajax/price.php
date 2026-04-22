<?php

/**
 * Доставка YandexDelivery
 * @package PHPShopAjaxElements
 */
session_start();
$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
include_once($_classPath . "modules/yandexdelivery/class/YandexDelivery.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("lang");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);

$YandexDelivery = new YandexDelivery();
$data = new YandexDeliveryOrderData();
$dimensions = $YandexDelivery->getDimensions($_SESSION['cart']);

if (!empty($_POST['weight']))
    $data->weight = (int) $_POST['weight'];
else $data->weight = (int) $YandexDelivery->options['weight'];

if (!empty($dimensions['length']))
    $data->length = (int) $dimensions['length'];
else $data->length = (int) $YandexDelivery->options['length'];

if (!empty($dimensions['height']))
    $data->height = (int) $dimensions['height'];
else $data->height = (int) $YandexDelivery->options['height'];

if (!empty($dimensions['width']))
    $data->width = (int) $dimensions['width'];
else $data->width = (int)$YandexDelivery->options['width'];

$data->delivery_variant_id = $_POST['delivery_variant_id'];
$YandexDelivery->options['paid'] = 1;
echo $YandexDelivery->getApproxDeliveryPrice($data);
