<?php

function retailSend($data) {
    global $_classPath;

    if ($_POST['retail_status_new'] == 2) {

        require_once $_classPath . "modules/retailcrm/class/Tools.php";
        require_once $_classPath . "modules/retailcrm/class/phpclient/Validation.php";

        retailOrder($_POST['rowID'], 'cart');
    }
}

function addRetailTab($data) {
    global $PHPShopGUI;

    if ($data['retail_status'] == 1) {

        $Tab1 = $PHPShopGUI->setCheckbox("retail_status_new", 2, "Отправить заказ в RetailCRM", 0);
        $PHPShopGUI->addTab(array("RetailCRM", $Tab1, false, 101));
    }
}

function retailOrder($id, $type) {
    global $PHPShopModules;
    $productsOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $products = $productsOrm->select(array('*'), array('enabled' => "='1'"), false, array('limit' => 1000000));
    $tmpProduct = array();
    foreach ($products as $product) {
        $tmpProduct[$product["id"]] = $product['uid'];
    }


    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.retailcrm.retailcrm_system"));
    $data = $PHPShopOrm->select();

    $value = Tools::iconvArray(unserialize($data['value']));

    //ini_set('memory_limit', '-1');
    $corders = array();

    if ($type == 'cart' && !is_null($id)) {
        $orderOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $order = $orderOrm->select(array('*'), array("id" => "='" . (int) $id . "'"), false);

        $order["status"] = unserialize($order["status"]);
        $order["orders"] = unserialize($order["orders"]);

        $order = Tools::iconvArray($order);

        $persone = isset($order["orders"]["Person"]) ? $order["orders"]["Person"] : array();

        $tmp = array(
            "number" => $order["uid"],
            "externalId" => $order["id"],
            "createdAt" => date("Y-m-d H:i:s", $order["datas"]),
            "discount" => isset($order["orders"]["Person"]["discount"]) ? $order["orders"]["Person"]["discount"] : 0,
            "phone" => str_replace(['(', ')', ' ', '+', '-', '&#43;'], '', $order['tel']),
            "email" => isset($order["orders"]["Person"]["mail"]) ? $order["orders"]["Person"]["mail"] : "",
            "customerComment" => $order["status"]["maneger"],
            "contragentType" => !empty($persone["org_name"]) || !empty($persone["org_inn"]) || !empty($persone["org_kpp"]) ? "legal-entity" : "individual",
            "orderType" => !empty($persone["org_name"]) || !empty($persone["org_inn"]) || !empty($persone["org_kpp"]) ? "eshop-legal" : "eshop-individual",
            "legalName" => isset($persone["org_name"]) ? $persone["org_name"] : "",
            "INN" => isset($persone["org_inn"]) ? $persone["org_inn"] : "",
            "KPP" => isset($persone["org_kpp"]) ? $persone["org_kpp"] : "",
            "orderMethod" => "shopping-cart",
            "delivery" => array(
                "address" => array(
                    "region" => isset($order["state"]) ? $order["state"] : "",
                    "city" => isset($order["city"]) ? $order["city"] : "",
                    "street" => isset($order["street"]) ? $order["street"] : "",
                    "building" => !empty($persone["building"]) ? $persone["building"] : $persone["corpus"],
                    "flat" => !empty($order["flat"]) ? $order["flat"] : $persone["appartment"],
                    "intercomCode" => isset($persone["domofon"]) ? $persone["domofon"] : "",
                    "floor" => is_int($persone["floor"]) ? $persone["floor"] : "",
                    "block" => is_int($persone["entrance"]) ? $persone["entrance"] : "",
                    "house" => isset($order["house"]) ? $order["house"] : "",
                    "metro" => isset($persone["metro"]) ? $persone["metro"] : "",
                    "notes" => ($persone["elevator"] > 0) ? "Этаж: " . $persone["elevator"] : "",
                )
            ),
        );

        if (empty($order['fio']))
            $fio = $order['orders']['Person']['name_person'];
        else
            $fio = $order['fio'];
        $fioArr = explode(' ', $fio);
        $tmp['lastName'] = $fioArr[0];
        if (isset($fioArr[1])) {
            $tmp['firstName'] = $fioArr[1];
        }
        if (isset($fioArr[2])) {
            $tmp['patronymic'] = $fioArr[2];
        }

        if (!empty($order["orders"]["Person"]["order_metod"]) && isset($value["payment"][$order["orders"]["Person"]["order_metod"]])) {
            $tmp["paymentType"] = $value["payment"][$order["orders"]["Person"]["order_metod"]];
        }
        if ($order["statusi"] == 0) {
            $tmp["status"] = $value["status"]["new"];
        } elseif (!empty($order["statusi"]) && isset($value["status"][$order["statusi"]])) {
            $tmp["status"] = $value["status"][$order["statusi"]];
        }

        if (isset($order["orders"]["Cart"]["cart"]) && count($order["orders"]["Cart"]["cart"]) > 0) {
            foreach ($order["orders"]["Cart"]["cart"] as $item) {

                $item["retail_product_id"] = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['retail_product_id'], ['id' => '=' . $item["id"]])["retail_product_id"];

                // Есть externalId
                if (!empty($item["retail_product_id"])) {
                    $tmp["items"][] = array(
                        "productId" => $item["retail_product_id"],
                        "initialPrice" => $item["price"],
                        "productName" => $item["name"],
                        "quantity" => $item["num"]
                    );
                } else {
                    $tmp["items"][] = array(
                        "productId" => $item["id"],
                        "initialPrice" => $item["price"],
                        "productName" => $item["name"],
                        "quantity" => $item["num"],
                        "xmlId" => isset($tmpProduct[$item["id"]]) ? $tmpProduct[$item["id"]] : "",
                    );
                }
            }
        }

        if (!empty($order["orders"]["Person"]["dostavka_metod"]) && isset($value["delivery"][$order["orders"]["Person"]["dostavka_metod"]])) {
            $tmp["delivery"]["code"] = $value["delivery"][$order["orders"]["Person"]["dostavka_metod"]];
        }



        if (!empty($order["user"])) {

            // Внешний код
            $PHPShopOrmUser = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
            $retail_user_id = $PHPShopOrmUser->getOne(['retail_user_id'], ['id' => '=' . (int) $order["user"]])['retail_user_id'];

            if (!empty($retail_user_id))
                $tmp["customerId"] = $retail_user_id;
            else
                $tmp["customerId"] = $order["user"];
            
        } else {
            $tmp["customerId"] = uniqid(time());
            if (!empty($persone["org_name"]) || !empty($persone["org_inn"]) || !empty($persone["org_kpp"])) {
                $tmp["contragentType"] = "legal-entity";
                $tmp["orderType"] = "eshop-legal";
            }
            $tmp = array_merge($tmp, Tools::explodeFio($persone["name_person"]));
            $tmp["legalName"] = isset($persone["org_name"]) ? $persone["org_name"] : "";
            $tmp["INN"] = isset($persone["org_inn"]) ? $persone["org_inn"] : "";
            $tmp["KPP"] = isset($persone["org_kpp"]) ? $persone["org_kpp"] : "";
        }
        $corders = Tools::clearArray($tmp);
    }
    $valid = new Validation();
    $order = array($valid->orderCheck($corders));
    $api = new ApiHelper($value["url"], $value["key"]);
    $api->processOrders($order);
}

$addHandler = array(
    'actionStart' => 'addRetailTab',
    'actionDelete' => false,
    'actionUpdate' => 'retailSend',
);
?>