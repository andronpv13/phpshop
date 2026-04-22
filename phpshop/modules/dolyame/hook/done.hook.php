<?php

function send_to_order_mod_dolyame_hook($obj, $value, $rout) {

    if ($value['order_metod'] == 10025) {

        if ($rout == 'MIDDLE') {
            $obj->cart_clean_enabled = false;
        }

        if ($rout == 'END') {

            require "./phpshop/modules/dolyame/class/Dolyame.php";
            $Dolyame = new Dolyame();

            // Статус для оплаты
            if ($Dolyame->order_status == 0) {

                $cart = $obj->PHPShopCart->getArray();
                foreach ($cart as $v) {

                    $products[] = [
                        'name' => iconv("windows-1251", "utf-8", htmlspecialchars($v['name'], ENT_COMPAT, 'cp1251', true)),
                        'quantity' => $v['num'],
                        'price' => number_format($v['price'], 2, '.', ''),
                        'sku' => $v['uid'],
                    ];
                }

                $client_info = [
                    'first_name' => iconv("windows-1251", "utf-8", htmlspecialchars($obj->get('user_name'), ENT_COMPAT, 'cp1251', true)),
                    //'phone' => $obj->get('tel'),
                    'email' => $_POST['mail'],
                ];


                // Новая заявка
                $result = $Dolyame->create($products, $client_info, $obj->orderId, $obj->ouid);

                if (!empty($result['link'])) {
                    $obj->PHPShopCart->clean();

                    $obj->set('dolyame_link', $result['link']);
                    $form = ParseTemplateReturn($GLOBALS['SysValue']['templates']['dolyame']['dolyame_cart'], true);

                    $obj->set('orderMesage', $form);
                } else
                    $obj->set('orderMesage', __('Ошибка оплаты Долями'));
            }
            else {
                $obj->PHPShopCart->clean();
                $mesageText=PHPShopText::notice($obj->PHPShopPayment->getValue('message_header'),false, '14px');
                $mesageText.=$obj->PHPShopPayment->getValue('message');
                $obj->set('mesageText', $mesageText);
                $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['order_forma_mesage']);
                $obj->set('orderMesage', $forma);
            }
            
        }
    }
}

$addHandler = array
    (
    'send_to_order' => 'send_to_order_mod_dolyame_hook'
);
?>