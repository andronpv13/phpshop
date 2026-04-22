<?php

function send_to_order_mod_cloudpayment_hook($obj, $value, $rout) {
    global $PHPShopSystem;

    if ($rout == 'MIDDLE' and $value['order_metod'] == 10014) {

        $aCart = $obj->PHPShopCart->getArray();

        // Настройки модуля
        include_once(dirname(__FILE__) . '/mod_option.hook.php');
        $PHPShopcloudpaymentArray = new PHPShopcloudpaymentArray();
        $option = $PHPShopcloudpaymentArray->getArray();

        // Валюта
        $currency = $PHPShopSystem->getDefaultValutaIso();

        // Контроль оплаты от статуса заказа
        if (empty($option['status'])) {
            // Номер счета
            $mrh_ouid = explode("-", $value['ouid']);
            $inv_id = $mrh_ouid[0] . "-" . $mrh_ouid[1];

            // Сумма покупки
            $out_summ = $obj->get('total');

            // НДС
            if ($PHPShopSystem->getParam('nds_enabled') == '')
                $tax = $tax_delivery = 0;
            else
                $tax = $PHPShopSystem->getParam('nds');

            foreach ($aCart as $key => $arItem) {

                // Скидка
                if ($obj->discount > 0 && empty($arItem['promo_price']))
                    $price = $arItem['price'] - ($arItem['price'] * $obj->discount / 100);
                else
                    $price = $arItem['price'];

                $amount = floatval($price) * floatval($arItem['num']);

                $aItem[] = array(
                    "label" => PHPShopString::win_utf8($arItem['name']),
                    "price" => floatval($price),
                    "quantity" => $arItem['num'],
                    "amount" => $amount,
                    "vat" => $tax,
                    "method" => 1,
                    "object" => 1
                );
            }

            // Доставка
            if ($obj->delivery > 0) {

                $tax_delivery = $obj->PHPShopDelivery->getParam('ofd_nds');

                if (empty($tax_delivery))
                    $tax_delivery = $tax;

                $cartSum = $obj->PHPShopCart->getSum();

                $delivery_price = floatval($out_summ) - floatval($cartSum);

                $aItem[] = array(
                    "label" => PHPShopString::win_utf8('Доставка'),
                    "price" => $delivery_price,
                    "quantity" => 1,
                    "amount" => $delivery_price,
                    "vat" => intval($tax_delivery),
                    "method" => 1,
                    "object" => 4
                );
            }

            $kassa_array = array(
                "cloudPayments" => (
                array(
                    "customerReceipt" => array(
                        "Items" => $aItem,
                        "taxationSystem" => intval($option['taxationSystem']),
                        "email" => $_POST["mail"],
                        "phone" => $_POST["tel_new"]
                    )
                )
                ),
                "cmsData" => [
                    "cmsName" => "PHPShop",
                    "cmsModule" => "phpshop-1.1"
                ]
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
            accountId: "' . $_POST["mail"] . '",
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
        } else {
            $obj->set('mesageText', $option['title_end']);
            $data = ParseTemplateReturn($GLOBALS['SysValue']['templates']['order_forma_mesage']);
        }

        $obj->set('orderMesage', $data);
    }
}

$addHandler = array
    (
    'send_to_order' => 'send_to_order_mod_cloudpayment_hook'
);
?>