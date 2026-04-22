<?php

function changeStatusOrder($data)
{

    // SMS оповещение пользователю о смене статуса заказа
    if ($data['statusi'] != $_POST['statusi_new']) {

        // —татусы заказов
        $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
        $GetOrderStatusArray = $PHPShopOrderStatusArray->getArray();

        // Ќастройки модул€
        include_once(dirname(__FILE__) . '/mod_option.php');
        $PHPShopModule = new PHPShopModule();

        // получение шаблона
        $statusOrderTpl = $PHPShopModule->getTplStatusOrder();

        if ($PHPShopModule->getCascadeEnabled()) {
            $statusOrderTpl = $PHPShopModule->getTplStatusOrderViber();
        }


        $PHPShopSystem = new PHPShopSystem();
        $nameShop = $PHPShopSystem->objRow['name'];

        // массив данных дл€ вставки при парсинге строки
        $datainsert = array(
            '@NameShop@' => $nameShop,
            '@OrderNum@' => $data['uid'],
            '@OrderStatus@' => $_POST['statusi_new'] ? $GetOrderStatusArray[$_POST['statusi_new']]['name'] : 'ѕодтвержден'
        );

        // телефон на который отправл€етс€ сообщение
        $phone = array($PHPShopModule->true_num($data['tel']));

        // сообщение
        $msg = $PHPShopModule->parseString($statusOrderTpl, $datainsert);


        if ($PHPShopModule->getCascadeEnabled()) {
            $PHPShopModule->sendSms($phone, $msg, 'change_status_order_template_viber');
        } else {
            $PHPShopModule->sendSms($phone, $msg);
        }



    }

}

$addHandler = array(
    'actionStart' => false,
    'actionDelete' => false,
    'actionUpdate' => 'changeStatusOrder'
);

?>