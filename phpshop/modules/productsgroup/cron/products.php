<?php

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

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopSystem = new PHPShopSystem();

// Авторизация
if ($_GET['s'] == md5($PHPShopBase->SysValue['connect']['host'] . $PHPShopBase->SysValue['connect']['dbase'] . $PHPShopBase->SysValue['connect']['user_db'] . $PHPShopBase->SysValue['connect']['pass_db']))
    $enabled = true;

if (empty($enabled))
    exit("Ошибка авторизации!");


// Настройки модуля
PHPShopObj::loadClass("modules");

$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
$data = $PHPShopOrm->getList(['id,productsgroup_products,productsgroup_products_keys'], ['productsgroup_check' => "='1'", 'enabled' => "='1'"], ['order' => 'datas desc'], ['limit' => 1000]);

if (is_array($data)) {
    $count = 0;
    foreach ($data as $prod) {

        $sql_where = $products_keys = '';
        $price_all = 0;
        $productsgroup_products = unserialize($prod['productsgroup_products']);

        foreach ($productsgroup_products as $item) {

            if ($item['id'] > 0) {

                if ($sql_where != '')
                    $sql_where .= ' OR id=' . $item['id'];
                else
                    $sql_where = ' WHERE id=' . $item['id'];

                $products_num[$item['id']] = $item['num'];
                $products_keys .= '#' . $item['id'] . '#';
            }
        }

        $sql = 'SELECT * FROM ' . $GLOBALS['SysValue']['base']['products'] . ' ' . $sql_where;
        $query = mysqli_query($link_db, $sql);
        $products = mysqli_fetch_array($query);
        do {
            $price_all = $price_all + ($products['price'] * intval($products_num[$products['id']]));
        } while ($products = mysqli_fetch_array($query));

        $PHPShopOrm->update(['price_new' => $price_all, 'productsgroup_products_keys_new' => $products_keys], ['id' => '=' . $prod['id']]);
        $count++;
    }

    echo "Цены обновлены у " . $count . " групп товаров";
}
?>