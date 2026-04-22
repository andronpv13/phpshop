<?php

function send_to_order_mod_tinkoff_hook($obj, $value, $rout)
{
    if ($rout == 'END' && $value['order_metod'] == 10032) {
        include_once $GLOBALS['SysValue']['class']['tinkoff'];
        $tinkoff = new Tinkoff();

        // Контроль оплаты от статуса заказа
        if (empty($tinkoff->settings['status'])) {

            $orders = unserialize($obj->order);

            $obj->tinkoff_total = $obj->total * 100;
            $obj->tinkoff_cart = $orders['Cart']['cart'];
            $obj->tinkoff_delivery_nds = $obj->PHPShopDelivery->objRow['ofd_nds'];

            $request = $tinkoff->getPaymentUrl($obj, $value);

            if ($request['url']) {
                if((int) $tinkoff->settings['force_payment'] === 1) {
                    header('Location: ' . $request['url']);
                } else {
                    $obj->set('payment_forma', PHPShopText::button(__('Оплатить через Т-Банк'), "window.location.replace('" . $request['url'] . "')", 'paybutton btn btn-default'));
                    $form = ParseTemplateReturn($GLOBALS['SysValue']['templates']['tinkoff']['tinkoff_payment_form'], true);
                }
            }
            else
                $obj->set('payment_forma', $request['error']);

            // Очищаем корзину
            unset($_SESSION['cart']);

            $obj->set('orderMesage', $form);
        } else {

            $clean_cart = "
               <script>
                   if(window.document.getElementById('num')){
                       window.document.getElementById('num').innerHTML='0';
                       window.document.getElementById('sum').innerHTML='0';
                   }
               </script>";
            $obj->set('mesageText', $tinkoff->settings['title_end'] . $clean_cart);
            $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['order_forma_mesage']);

            $obj->set('orderMesage', $forma);
        }
    }
}

$addHandler = array
(
    'send_to_order' => 'send_to_order_mod_tinkoff_hook'
);

?>