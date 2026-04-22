<?php

/**
 * Функция хук, вывод кнопки оплаты в ЛК и регистрация регистрация заказа в платежном шлюзе
 * @param object $obj объект функции
 * @param array $PHPShopOrderFunction данные о заказе
 */
function userorderpaymentlink_mod_twocan_hook($obj, $PHPShopOrderFunction) {
  
    global $PHPShopSystem,$PHPShopModules;

    // Настройки модуля
    include_once(dirname(__FILE__) . '/mod_option.hook.php');
    $PHPShopTwocanArray = new PHPShopTwocanArray();
    $option = $PHPShopTwocanArray->getArray();
    $uid = $PHPShopOrderFunction->objRow['uid'];
    // Оплата
    if ($_REQUEST["paynow"] == "Y") {

        // Номер заказа
        
        $bonus_minus = $PHPShopOrderFunction->objRow['bonus_minus'];

        // Префикс
        

        $orderNum = $uid;


        $order = $PHPShopOrderFunction->unserializeParam('orders');

        // Содержимое корзины
        $i = 0;
        
        $total = 0;
        foreach ($order['Cart']['cart'] as $key => $arItem) {

            // Скидка
            if ($order['Person']['discount'] > 0 && empty($arItem['promo_price']))
                $price = ($arItem['price'] - ($arItem['price'] * $order['Person']['discount'] / 100));
            else
                $price = $arItem['price'] ;

            // Бонусы
            if ($bonus_minus > 0 and $i == 0) {
                $price = $price - $bonus_minus ;
            }

            $price = round($price);
            $amount = $price * $arItem['num'];

            $i++;
            $total = $total + $amount;
        }

        // Доставка
        if (!empty($order['Cart']['dostavka'])) {

            PHPShopObj::loadClass('delivery');
            $PHPShopDelivery = new PHPShopDelivery($order['Person']['dostavka_metod']);

            $delivery_price = (int) $order['Cart']['dostavka'];

            $total = $total + $delivery_price;
        }
        $twocanrequest = [
            "merchant_order_id" => preg_replace('/[^0-9.]+/', '', $orderNum),
            "description" => mb_convert_encoding('Заказ № ' . $orderNum, "UTF-8", "Windows-1251"),
            "location" => [
                 "ip" => getUserIP()
            ],
            "amount" => number_format($total, 2, '.', ''),
            "custom_fields" => [
                "real_merchant_order_id" => mb_convert_encoding($orderNum, "UTF-8", "Windows-1251")
            ],
            "options" => [
                "template" => $option['template'],
                "auto_charge" => $option['autocharge'],
                "terminal" => $option['terminal'],                
                'return_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/success/?uid=' .  $uid,
            ]            
        ];  
   

        if( $option['exptimeout']) $twocanrequest["options"]["expiration_timeout"] = $option['exptimeout'];
       
        


        $result = $PHPShopTwocanArray->createOrder($twocanrequest, $option );
       
         
        if(in_array($result['result_code'],[200, 201])){
            $PHPShopTwocanArray->log($result, $uid, 'Заказ создан', 'Создание заказа', $result['result']['orders'][0]['id'],$result['result']['orders'][0]['status'] );           
            $PHPShopTwocanArray->setOrder($uid,$result['result']['orders'][0]['id'], $result['result']['orders'][0]['amount'],$result['result']['orders'][0]['status'],0,0);
            header('Location: ' . $result['redirect_url']);
            
        }else{
            $PHPShopTwocanArray->log($result, $uid, 'Ошибка создания заказа', 'Создание заказа');
            $obj->set('mesageText', 'Ошибка создания заказа');
            return 'Ошибка создания заказа';   
        }
       
    }
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.twocan.twocan_orders"));
    $order = $PHPShopOrm->getOne(array('*'), array("id=" => "'$uid'"));
    $return = "2can&ibox";
    // Контроль оплаты от статуса заказа
    if ($PHPShopOrderFunction->order_metod_id == 10028){
        if (($PHPShopOrderFunction->getParam('statusi') == $option['status'] or empty($option['status'])) and $PHPShopOrderFunction->getParam('paid') != '1') {            
            $order_uid = $PHPShopOrderFunction->objRow['uid'];
            $return = PHPShopText::a("/users/order.html?order_info=$order_uid&paynow=Y#Order", 'Оплатить сейчас', 'Оплатить сейчас', false, false, '_blank', 'btn btn-success pull-right');
        } elseif ($PHPShopOrderFunction->getSerilizeParam('orders.Person.order_metod') == 10028 && $order['status'] == 'authorized'){
            $return .= '. ' . $option['title_sub'];
        } elseif ($PHPShopOrderFunction->getSerilizeParam('orders.Person.order_metod') == 10028 && $order['status'] == 'charged'){
            $return .= '. Заказ оплачен';
        }elseif($PHPShopOrderFunction->getSerilizeParam('orders.Person.order_metod') == 10028 && $order['status'] == 'refunded' && ($order['charged'] == $order['refunded'])){
            $return .= '. Произведен возврат';
        }elseif($PHPShopOrderFunction->getSerilizeParam('orders.Person.order_metod') == 10028 && $order['status'] == 'refunded' && ($order['charged'] > $order['refunded'])){
            $return .= '. Произведен частичный возврат';
        }
    }
    return $return;
}





function getUserIP()
{
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
              $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
              $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}

$addHandler = array('userorderpaymentlink' => 'userorderpaymentlink_mod_twocan_hook');
?>