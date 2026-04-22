<?php

session_start();

// Включение
$enabled = true;

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
PHPShopObj::loadClass("product");
PHPShopObj::loadClass("valuta");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopSystem = new PHPShopSystem();
$_SESSION['lang'] = $PHPShopSystem->getSerilizeParam("admoption.lang_adm");
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'admin'));

// Авторизация
if ($_GET['s'] == md5($PHPShopBase->SysValue['connect']['host'] . $PHPShopBase->SysValue['connect']['dbase'] . $PHPShopBase->SysValue['connect']['user_db'] . $PHPShopBase->SysValue['connect']['pass_db']))
    $enabled = true;

if (empty($enabled))
    exit("Ошибка авторизации!");


$count = 0;

// Настройки модуля
include_once dirname(__FILE__) . '/../class/CDEKFulfillment.php';
$CDEKFulfillment = new CDEKFulfillment();

function loadCDEKWarehouse($path) {
    global $CDEKFulfillment,$count;
    
    $data = $CDEKFulfillment->getProductList($path);
    if (is_array($data['_embedded']['product_offer'])) {
        foreach ($data['_embedded']['product_offer'] as $product) {
            if (is_array($product['items']))
                foreach ($product['items'] as $items) {

                    // Кол-во товара на складе СДЕК
                    if ($items['state'] == 'normal' and $items['warehouse'] == $CDEKFulfillment->option['warehouse_id']) {

                        $PHPShopProduct = new PHPShopProduct((int) $product['id'], 'export_cdek_id');

                        if (!empty($PHPShopProduct->getName())) {

                            $warehouse = (int) $PHPShopProduct->getParam('items' . $CDEKFulfillment->option['warehouse_cdek']);

                            // Добавить на склад
                            if ($warehouse < $items['count']) {
                                $PHPShopProduct->addToWarehouse($items['count'] - $warehouse, $parent = 0, $CDEKFulfillment->option['warehouse_cdek']);
                                $count++;
                            }
                            // Списать со склада
                            elseif ($warehouse > $items['count']) {
                                $PHPShopProduct->removeFromWarehouse($warehouse - $items['count'], $parent = 0, $CDEKFulfillment->option['warehouse_cdek']);
                                $count++;
                            }
                        }
                    }
                }
        }
    }

    if(!empty($data['_links']['next']['href']))
            loadCDEKWarehouse($data['_links']['next']['href']);
}

loadCDEKWarehouse('https://cdek.orderadmin.ru/api/products/offer?page=1');

echo "Остатки обновлены у " . $count . " товаров";
