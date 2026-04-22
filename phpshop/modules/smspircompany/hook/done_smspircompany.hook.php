<?php

function write_hook($obj, $row, $rout) {

    if ($rout == "END") {

        $commonSum = $obj->total;
    
        PHPShopObj::loadClass('parser');
        
        // Настройки модуля
        include_once(dirname(__DIR__) . '/admpanel/mod_option.php');
        
        $PHPShopModule = new PHPShopModule();

        $cart = unserialize($row['orders_new']);
        
        // получение шаблона
        $orderTpl      = $PHPShopModule->getTplOrder();
        $orderTplAdmin = $PHPShopModule->getTplAdminOrder();

        if ($PHPShopModule->getCascadeEnabled()) {
            $orderTpl      = $PHPShopModule->getTplOrderViber();
            $orderTplAdmin = $PHPShopModule->getTplAdminOrderViber();
        }

        $PHPShopSystem = new PHPShopSystem();
        $nameShop = $PHPShopSystem->objRow['name'];
        $PHPShopValuta = new PHPShopValuta($PHPShopSystem->objRow['dengi']);
        $currency = $PHPShopValuta->getCode();


        foreach($cart['Cart']['cart'] as $k=>$v){
          $productName[] = $v['name'] . ' - ' . $v['price'] . ' ' . $currency .', ' . $v['num'] . ' шт.';
        }
        
        $product = implode(','."\r\n", $productName);
        
        PHPShopParser::set('OrderDate', date('d.m.Y', $row['datas_new']));
        PHPShopParser::set('ProductNum', $cart['Cart']['num']);
        PHPShopParser::set('ProductName', $product);
        PHPShopParser::set('OrderSum', $cart['Cart']['sum'] . ' ' . $currency);
        PHPShopParser::set('ProductDiscount', $cart['Person']['discount'] . '%');
        PHPShopParser::set('ProductDostavka', $cart['Cart']['dostavka'] . ' ' . $currency);
        PHPShopParser::set('OrderCommonSum', $commonSum . ' ' . $currency);
          
        $order = PHPShopParser::file(dirname(__DIR__).'/lib/templates/order/order.tpl', true);

        // массив данных для вставки при парсинге строки
        $datainsertUser = array(
          '@NameShop@'      => $nameShop, 
          '@OrderNum@'      => $row['ouid'],
          '@OrderStatus@'   => 'подтвержден',
          '@Order@'         => $order
         );

        // телефон на который отправляется сообщение
        $phone = array($PHPShopModule->true_num($row['tel_new']));

        // сообщение
        $msgToUser = $PHPShopModule->parseString($orderTpl, $datainsertUser);

        // отправка админу
        $PHPShopDelivery = new PHPShopDelivery($row['dostavka_metod']);
        
        // массив данных для вставки при парсинге строки
        $datainsertAdmin = array(
          '@NameShop@'        => $nameShop, 
          '@OrderNum@'        => $row['ouid'],
          '@UserFio@'         => $row['fio_new'],
          '@UserPhone@'       => $row['tel_new'],
          '@UserMail@'        => $row['mail'],
          '@UserDelivery@'    => $PHPShopDelivery->getCity(),
          '@UserCountry@'     => $row['country_new'],
          '@UserState@'       => $row['state_new'],
          '@UserCity@'        => $row['city_new'],
          '@UserIndex@'       => $row['index_new'],
          '@UserStreet@'      => $row['street_new'],
          '@UserHouse@'       => $row['house_new'],
          '@UserPorch@'       => $row['porch_new'],
          '@UserDoorPhone@'   => $row['door_phone_new'],
          '@UserFlat@'        => $row['flat_new'],
          '@UserDelivtime@'   => $row['delivtime_new'],
          '@UserDopInfo@'     => $row['dop_info'],
          '@CommonSumOrder@'  => $commonSum . ' ' . $currency,
          '@Order@'           => $order
        );
        $msgToAdmin = $PHPShopModule->parseString($orderTplAdmin, $datainsertAdmin);


        if ($PHPShopModule->getCascadeEnabled()) {
            // сообщение админу
            $PHPShopModule->sendSmsAdmin($msgToAdmin, 'order_template_admin_viber');

            // отправка покупателю
            $PHPShopModule->sendSms($phone,$msgToUser, 'order_template_viber');
        } else {
            // сообщение админу
            $PHPShopModule->sendSmsAdmin($msgToAdmin);

            // отправка покупателю
            $PHPShopModule->sendSms($phone,$msgToUser);
        }

    }
}

$addHandler = array
  (
    'write' => 'write_hook',
  );
?>