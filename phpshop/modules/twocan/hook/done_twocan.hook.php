<?php

/**
 * Функция хук, регистрация заказа в платежном шлюзе 2can&ibox, переадресация на платежную форму
 * @param object $obj объект функции
 * @param array $value данные о заказе
 * @param string $rout место внедрения хука
 */
function send_twocan_hook($obj, $value, $rout) {
    
    if ($rout === 'END' and (int) $value['order_metod'] === 10028) {
        global $PHPShopSystem;
         
        // Настройки модуля
        include_once(dirname(__FILE__) . '/mod_option.hook.php');
        $PHPShopTwocanArray = new PHPShopTwocanArray();
        $option = $PHPShopTwocanArray->getArray();
        
        // Номер заказа
        $uid = $PHPShopOrderFunction->objRow['uid'];

        $bonus_minus = $PHPShopOrderFunction->objRow['bonus_minus'];

       
        
        $orderNum = $value['ouid'];


         
        
        $orders = unserialize($obj->order);
        
        // Контроль оплаты от статуса заказа
        if (empty($option['status'])) {    
        // Содержимое корзины
       
            $total = 0;
            
            foreach ($orders['Cart']['cart'] as $key => $arItem) {

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
                $amount = $price *  $arItem['num'];

            
                $total = $total + $amount;
                
            }

            // Доставка
            if ($obj->delivery > 0) {                

                $delivery_price = (int) $obj->delivery;
                
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
                    'return_url' => 'http://' . $_SERVER['HTTP_HOST'] . '/success/?uid=' .  $value['ouid'],
                ]            
            ];    

            if( $option['exptimeout']) $twocanrequest["options"]["expiration_timeout"] = $option['exptimeout'];
        
            
                
            $result = $PHPShopTwocanArray->createOrder($twocanrequest, $option );
            
            if(in_array($result['result_code'],[200, 201])){
                $PHPShopTwocanArray->log($result, $value['ouid'], 'Заказ создан', 'Создание заказа', $result['result']['orders'][0]['id']);           

                $PHPShopTwocanArray->setOrder($value['ouid'], $result['result']['orders'][0]['id'], $result['result']['orders'][0]['amount'],$result['result']['orders'][0]['status'],0,0);

                header('Location: ' . $result['redirect_url']);
            }else{
                $PHPShopTwocanArray->log($result, $value['ouid'], 'Ошибка создания заказа', 'Создание заказа');
                $obj->set('mesageText', 'Ошибка создания заказа');
                $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['order_forma_mesage']);           
            }
        }else{
            $obj->set('mesageText', $option['title_sub']);

            $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['order_forma_mesage']);

            
        }
        $obj->set('orderMesage', $forma);
       
        
    }
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



$addHandler = array('send_to_order' => 'send_twocan_hook');
?>