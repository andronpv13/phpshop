<?php

function userorderpaymentlink_mod_cloudpayments_hook($obj, $PHPShopOrderFunction) {
    global $PHPShopSystem;

    // Настройки модуля
    include_once(dirname(__FILE__) . '/mod_option.hook.php');
    $PHPShopcloudpaymentArray = new PHPShopcloudpaymentArray();
    $option = $PHPShopcloudpaymentArray->getArray();

    // Валюта
    $currency = $PHPShopSystem->getDefaultValutaIso();

    // Контроль оплаты от статуса заказа
    if ($PHPShopOrderFunction->order_metod_id == 10014)
        if ($PHPShopOrderFunction->getParam('statusi') == $option['status'] or empty($option['status'])) {

            // Номер счета
            $mrh_ouid = explode("-", $PHPShopOrderFunction->objRow['uid']);
            $inv_id = $mrh_ouid[0] . "-" .$mrh_ouid[1];

            // Сумма покупки
            $out_summ = $PHPShopOrderFunction->getTotal();

            $order = $PHPShopOrderFunction->unserializeParam('orders');

            // НДС
            if ($PHPShopSystem->getParam('nds_enabled') == '')
                $tax = $tax_delivery = 0;
            else
                $tax = $PHPShopSystem->getParam('nds');

            foreach ($order['Cart']['cart'] as $key => $arItem) {

                // Скидка
                if ($order['Person']['discount'] > 0 && empty($arItem['promo_price']))
                    $price = $arItem['price'] - ($arItem['price'] * $order['Person']['discount'] / 100);
                else
                    $price = $arItem['price'];

                $amount = floatval($price) * floatval($arItem['num']);

                $aItem[] = array(
                    "label"     => PHPShopString::win_utf8($arItem['name']),
                    "price"     => floatval($price),
                    "quantity"  => $arItem['num'],
                    "amount"    => $amount,
                    "vat"       => $tax,
                    "method"   => 1,
                    "object"   => 1
                );

            }

            // Доставка
            if ($obj->delivery > 0) {

                $tax_delivery = $obj->PHPShopDelivery->getParam('ofd_nds');

                if(empty($tax_delivery))
                    $tax_delivery = $tax;

                $cartSum = $PHPShopOrderFunction->getCartSumma();

                $delivery_price = floatval($out_summ - $cartSum);

                $aItem[] = array(
                    "label"     => PHPShopString::win_utf8('Доставка'),
                    "price"     => $delivery_price,
                    "quantity"  => 1,
                    "amount"    => $delivery_price,
                    "vat"       => intval($tax_delivery),
                    "method"   => 1,
                    "object"   => 4
                );
            }
            // Доставка
            if (!empty($order['Cart']['dostavka'])) {

                PHPShopObj::loadClass('delivery');
                $PHPShopDelivery = new PHPShopDelivery($order['Person']['dostavka_metod']);

                $cartSum = $PHPShopOrderFunction->getCartSumma();

                $delivery_price = floatval($out_summ) - floatval($cartSum);

                $tax_delivery = $PHPShopDelivery->getParam('ofd_nds');

                if(empty($tax_delivery))
                    $tax_delivery = $tax;

                $aItem[] = array(
                    "label"     => PHPShopString::win_utf8('Доставка'),
                    "price"     => $delivery_price,
                    "quantity"  => 1,
                    "amount"    => $delivery_price,
                    "vat"       => intval($tax_delivery)
                );
            }

            $kassa_array = array(
                "cloudPayments" => (
                    array(
                        "customerReceipt" => array(
                            "Items" => $aItem,
                            "taxationSystem" => intval($option['taxationSystem']),
                            "email" => $PHPShopOrderFunction->getMail()
                        )
                    )
                )
            );

            $json = json_encode($kassa_array);

            // Платежная форма
            $data = '<script src="https://widget.cloudpayments.ru/bundles/cloudpayments"></script>';
            $data .= '<script type="text/javascript">
            this.pay = function () {

        var widget = new cp.CloudPayments();
        widget.charge({ 
            publicId: "' . $option["publicId"] . '",  
            description: "' . $option["description"] . '", 
            amount: ' . $out_summ . ', 
            currency: "' . $currency . '", 
            invoiceId: "' . $inv_id . '", 
            accountId: "' . $PHPShopOrderFunction->getMail() . '", 
            data: ' . $json . ' 
        },
        function (options) { // success
             location="http://' . $_SERVER['HTTP_HOST'] . '/success/?result=success&inv_id=' . $mrh_ouid[0] . $mrh_ouid[1] . '";
        },
        function (reason, options) { // fail
            location="http://' . $_SERVER['HTTP_HOST'] . '/success/?result=fail";
        });
        };    
        </script>

        <button id="pay" class="btn btn-primary">' . $option["title"] . '</button>
        <script type="text/javascript">
    
        $("#pay").click(function(event){
            event.preventDefault();
            pay();
            return false;
        });
        </script>';

            // Очищаем корзину
            unset($_SESSION['cart']);

            $return = $data;
        } elseif ($PHPShopOrderFunction->getSerilizeParam('orders.Person.order_metod') == 10014)
            $return = ', Заказ обрабатывается менеджером';

    return $return;
}

$addHandler = array
    (
    'userorderpaymentlink' => 'userorderpaymentlink_mod_cloudpayments_hook'
);
?>