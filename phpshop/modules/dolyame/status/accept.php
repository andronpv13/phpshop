<?php
$postdata = file_get_contents("php://input");

if (!empty($postdata)) {
    $data = json_decode($postdata, true);
    
    if (is_array($data) && isset($data['status']) && isset($data['id'])) {
        $_classPath = $_SERVER['DOCUMENT_ROOT'] . "/phpshop/";
        include($_classPath . "class/obj.class.php");

        PHPShopObj::loadClass("base");
        $PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);

        PHPShopObj::loadClass("orm");
        PHPShopObj::loadClass("system");
        PHPShopObj::loadClass("text");
        PHPShopObj::loadClass("string");

        require "../class/Dolyame.php";
        $Dolyame = new Dolyame();
        
        if(!$Dolyame->check_notification())
            exit('Login Error!');

        // Лог
        $Dolyame->log($data, $data['id'], $data['id'] . '/webhook/'.$data['status'], $data['status']);

        switch ($data['status']) {

            // Принят
            case "wait_for_commit":

                // Проверка заказа
                $info = $Dolyame->info($data['id']);

                if ($info['status'] == "wait_for_commit") {
                    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
                    $row = $PHPShopOrm->getOne(['*'], ['id' => '=' . (int) $data['id']]);
                    
                    if (is_array($row) and $Dolyame->order_status == $row['statusi']) {

                        $order = unserialize($row['orders']);
                        $cart = $order['Cart']['cart'];

                        foreach ($cart as $v) {

                            $products[] = [
                                'name' => iconv("windows-1251", "utf-8", htmlspecialchars($v['name'], ENT_COMPAT, 'cp1251', true)),
                                'quantity' => $v['num'],
                                'price' => number_format($v['price'], 2, '.', ''),
                                'sku' => $v['uid'],
                            ];
                        }

                        // Подтвержение
                        $commit = $Dolyame->commit($products, $data['id']);
                    }
                }

                break;

            // Оплачен первый платеж
            case "completed":

                // Проверка заказа
                $info = $Dolyame->info($data['id']);

                if ($info['status'] == "committed" or $info['status'] == "completed") {
                    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
                    $row = $PHPShopOrm->update(['statusi_new' => $Dolyame->order_status_payment], ['id' => '=' . (int) $data['id']]);
                }

                break;
        }
    }
}