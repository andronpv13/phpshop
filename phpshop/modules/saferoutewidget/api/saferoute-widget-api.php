<?php

session_start();

$_classPath = "../../../";
include_once($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
PHPShopObj::loadClass('modules');
PHPShopObj::loadClass('orm');
PHPShopObj::loadClass('system');
PHPShopObj::loadClass('security');
PHPShopObj::loadClass('order');

$orm = new PHPShopOrm('phpshop_modules_saferoutewidget_system');

$settings = $orm->select();

include_once "SafeRouteWidgetApi.php";

$widgetApi = new SafeRouteWidgetApi();
$widgetApi->setToken($settings['key']);
$widgetApi->setShopId($settings['shop_id']);

$request = ($_SERVER['REQUEST_METHOD'] === 'POST')
    ? json_decode(file_get_contents('php://input'), true)
    : $_REQUEST;

$widgetApi->setMethod($_SERVER['REQUEST_METHOD']);
$widgetApi->setData(isset($request['data']) ? $request['data'] : array());

header('Content-Type: text/html; charset=UTF-8');
echo $widgetApi->submit($request['url']);