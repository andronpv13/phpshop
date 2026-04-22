<?php

/**
 *  Удаление товаров из акций
 */
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
PHPShopObj::loadClass("string");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopSystem = new PHPShopSystem();

// Авторизация
if ($_GET['s'] == md5($PHPShopBase->SysValue['connect']['host'] . $PHPShopBase->SysValue['connect']['dbase'] . $PHPShopBase->SysValue['connect']['user_db'] . $PHPShopBase->SysValue['connect']['pass_db']))
    $enabled = true;

if (empty($enabled))
    exit("Ошибка авторизации!");


// Настройки модуля
PHPShopObj::loadClass("modules");
$PHPShopModules = new PHPShopModules($_classPath . "modules/");

include_once dirname(__FILE__) . '/../class/OzonSeller.php';
$OzonSeller = new OzonSeller();
$actions = $OzonSeller->getActions()['result'];

$count = 0;
$max = 100;
if (is_array($actions))
    foreach ($actions as $action) {

        // Есть товары в акции
        if (!empty($action['participating_products_count'])) {

            $products = $OzonSeller->getActionsProduct($action['id'])['result']['products'];

            if (is_array($products)) {
                foreach ($products as $product) {

                    if ($count < $max)
                        $ids[] = $product['id'];

                    $count++;
                }
            }

            $result = $OzonSeller->deactivationActionsProduct($action['id'], $ids);

            if (is_array($result['result']['product_ids'])) {
                echo "Удалено " . count($result['result']['product_ids']) . " товаров из акции " . PHPShopString::utf8_win1251($action['title']);
            }
        }
    }