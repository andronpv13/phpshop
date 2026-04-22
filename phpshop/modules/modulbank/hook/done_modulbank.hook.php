<?php

/**
 * Функция хук, регистрация заказа в платежном шлюзе
 * @param object $obj объект функции
 * @param array $value данные о заказе
 * @param string $rout место внедрения хука
 */
function send_modulbank_hook($obj, $value, $rout) {
    global $PHPShopSystem;

    if ($rout === 'END' and $value['order_metod'] == 10012) {

        include_once 'phpshop/modules/modulbank/class/ModulBank.php';

        $Modulbank = new ModulBank();
        $orders = unserialize($obj->order);

        // НДС
        $tax = $Modulbank->getNds();

        // Контроль оплаты от статуса заказа
        if (empty($Modulbank->option['status'])) {

            $Modulbank->parameters['order_id'] = $value['ouid'];
            $Modulbank->parameters['receipt_contact'] = $_POST['mail'];
            $Modulbank->parameters['description'] = PHPShopString::win_utf8(str_replace('"', '', $PHPShopSystem->getName() . __(' оплата заказа ') . $Modulbank->parameters['order_id']));

            // Содержимое корзины
            $total = 0;

            foreach ($orders['Cart']['cart'] as $key => $arItem) {

                // Скидка
                $price = $obj->discount > 0 ? $price = number_format($arItem['price'] - ($arItem['price'] * $obj->discount / 100), 2, '.', '') : $price = number_format($arItem['price'], 2, '.', '');

                // НДС
                $vat_code = (new PHPShopProduct($arItem['id']))->getParam('modulbank_vat_code');
                if (!empty($vat_code))
                    $vat_code = 'vat' . $vat_code;
                else
                    $vat_code = $tax;

                $aItem[] = array(
                    "name" => PHPShopString::win_utf8(str_replace(array('&#43;'), array(''), $arItem['name'])),
                    "quantity" => $arItem['num'],
                    "price" => $price,
                    "sno" => $Modulbank->option['taxationSystem'],
                    "payment_object" => 'commodity',
                    "payment_method" => 'full_prepayment',
                    'vat' => $vat_code
                );
                $total = number_format($total + (int) $arItem['num'] * $price, 2, '.', '');
            }

            // Доставка
            if ($obj->delivery > 0) {

                switch ($obj->PHPShopDelivery->getParam('ofd_nds')) {
                    case 0:
                        $tax_delivery = 'vat0';
                        break;
                    case 5:
                        $tax_delivery = 'vat5';
                        break;
                    case 10:
                        $tax_delivery = 'vat10';
                        break;
                    case 18:
                        $tax_delivery = 'vat18';
                        break;
                    case 20:
                        $tax_delivery = 'vat20';
                        break;
                    case 22:
                        $tax_delivery = 'vat22';
                        break;
                    default: $tax_delivery = $tax;
                }

                $aItem[] = array(
                    "name" => PHPShopString::win_utf8('Доставка'),
                    "quantity" => 1,
                    "price" => (float) number_format($obj->delivery, 2, '.', ''),
                    "sno" => $Modulbank->option['taxationSystem'],
                    "payment_object" => 'service',
                    "payment_method" => 'full_prepayment',
                    'vat' => $tax_delivery
                );
                $total = number_format($total + $obj->delivery, 2, '.', '');
            }

            $Modulbank->parameters['receipt_items'] = json_encode($aItem);
            $Modulbank->parameters['amount'] = number_format($total, 2, '.', '');

            $payment_form = $Modulbank->getForm();

            $Modulbank->log($Modulbank->parameters, $Modulbank->parameters['order_id'], __('Форма подготовлена для отправки'), __('Регистрация заказа'));

            $obj->set('payment_forma', PHPShopText::form($payment_form, 'modulbankpay', 'post', 'https://pay.modulbank.ru/pay', '_blank'));

            $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['modulbank']['modulbank_payment_forma'], true);
        } else {

            $clean_cart = "
            <script>
                if(window.document.getElementById('num')){
                    window.document.getElementById('num').innerHTML='0';
                    window.document.getElementById('sum').innerHTML='0';
                }
            </script>";
            $obj->set('mesageText', $Modulbank->option['title_sub'] . $clean_cart);
            $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['order_forma_mesage']);

            // Очищаем корзину
            unset($_SESSION['cart']);
        }

        $obj->set('orderMesage', $forma);
    }
}

$addHandler = array('send_to_order' => 'send_modulbank_hook');
?>