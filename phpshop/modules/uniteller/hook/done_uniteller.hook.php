<?php

/**
 * Функция хук, регистрация заказа в платежном шлюзе
 * @param object $obj объект функции
 * @param array $value данные о заказе
 * @param string $rout место внедрения хука
 */
function send_uniteller_hook($obj, $value, $rout) {
    global $PHPShopSystem;

    if ($rout === 'MIDDLE' and $value['order_metod'] == 10022) {

        include_once 'phpshop/modules/uniteller/class/Uniteller.php';

        $Uniteller = new Uniteller();

        $aCart = $obj->PHPShopCart->getArray();

        // НДС
        $tax = $Uniteller->getNds();

        // Контроль оплаты от статуса заказа
        if (empty($Uniteller->option['status'])) {

            $Uniteller->parameters['Subtotal_P']      = number_format($obj->get('total'), 2, '.', '');
            $Uniteller->parameters['Order_IDP']       = $value['ouid'];
            $Uniteller->parameters['Email']           = $_POST['mail'];
            $Uniteller->parameters['Comment']         = PHPShopString::win_utf8($PHPShopSystem->getName() . ' оплата заказа ' . $Uniteller->parameters['Order_IDP']);

            // Содержимое корзины
            foreach ($aCart as $key => $arItem) {

                // Скидка
                if($obj->discount > 0 && empty($arItem['promo_price']))
                    $price = number_format($arItem['price']  - ($arItem['price']  * $obj->discount  / 100), 2, '.', '');
                else
                    $price = number_format($arItem['price'], 2, '.', '');

                $Uniteller->parameters['Receipt']['lines'][] = array(
                    'name'           => PHPShopString::win_utf8($arItem['name']),
                    'price'          => $price,
                    'qty'            => $arItem['num'],
                    'sum'            => $price * (int) $arItem['num'],
                    'vat'            => $tax,
                    'payattr'        => 1,
                    'lineattr'       => 1
                );
            }

            // Доставка
            if ($obj->delivery > 0) {

                $tax_delivery = $obj->PHPShopDelivery->getParam('ofd_nds');

                if (empty($tax_delivery))
                    $tax_delivery = $tax;

                $Uniteller->parameters['Receipt']['lines'][] = array(
                    'name'           => PHPShopString::win_utf8('Доставка'),
                    'price'          => (float) number_format($obj->delivery, 2, '.', ''),
                    'qty'            => 1,
                    'sum'            => (float) number_format($obj->delivery, 2, '.', ''),
                    'vat'            => $tax_delivery,
                    'payattr'        => 1,
                    'lineattr'       => 4
                );
            }

            $Uniteller->parameters['Receipt']['customer']['email'] = $_POST['mail'];
            $Uniteller->parameters['Receipt']['total'] = number_format($obj->get('total'), 0, '.', '');
            $Uniteller->parameters['Receipt']['payments'][0]['amount'] = number_format($obj->get('total'), 0, '.', '');
            $Uniteller->parameters['Receipt']['taxmode'] = $Uniteller->option['taxationSystem'];

            $payment_form = $Uniteller->getForm();

            $Uniteller->log($Uniteller->parameters, $Uniteller->parameters['Order_IDP'], 'Форма подготовлена для отправки', 'Регистрация заказа');

            $obj->set('payment_forma', PHPShopText::form($payment_form, 'unitellerpay', 'post', $Uniteller::$FORM_ACTION, '_blank'));

            $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['uniteller']['uniteller_payment_forma'], true);
        }else{

            $clean_cart = "
            <script>
                if(window.document.getElementById('num')){
                    window.document.getElementById('num').innerHTML='0';
                    window.document.getElementById('sum').innerHTML='0';
                }
            </script>";
            $obj->set('mesageText', $Uniteller->option['title_sub'] . $clean_cart);
            $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['order_forma_mesage']);

            // Очищаем корзину
            unset($_SESSION['cart']);
        }

        $obj->set('orderMesage', $forma);
        }
}

$addHandler = array('send_to_order' => 'send_uniteller_hook');
?>