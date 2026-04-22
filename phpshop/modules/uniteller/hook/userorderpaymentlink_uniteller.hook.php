<?php

/**
 * Функция хук, вывод кнопки оплаты в ЛК и регистрация регистрация заказа в платежном шлюзе
 * @param object $obj объект функции
 * @param array $PHPShopOrderFunction данные о заказе
 */
function userorderpaymentlink_mod_uniteller_hook($obj, $PHPShopOrderFunction)
{
    global $PHPShopSystem;

    include_once 'phpshop/modules/uniteller/class/Uniteller.php';
    $Uniteller = new Uniteller();

    // Контроль оплаты от статуса заказа
    if ($PHPShopOrderFunction->order_metod_id == 10022)
        if ($PHPShopOrderFunction->getParam('statusi') == $Uniteller->option['status'] or empty($Uniteller->option['status'])) {

            $tax = $Uniteller->getNds();

            $Uniteller->parameters['Subtotal_P'] = number_format($PHPShopOrderFunction->getTotal(), 2, '.', '');
            $Uniteller->parameters['Order_IDP']  = $PHPShopOrderFunction->objRow['uid'];
            $Uniteller->parameters['Email']      = $PHPShopOrderFunction->getMail();
            $Uniteller->parameters['Comment']    = PHPShopString::win_utf8($PHPShopSystem->getName() . ' оплата заказа ' . $Uniteller->parameters['Subtotal_P']);

            $order = $PHPShopOrderFunction->unserializeParam('orders');

            // Содержимое корзины
            foreach ($order['Cart']['cart'] as $key => $arItem) {

                // Скидка
                if($order['Person']['discount'] > 0 && empty($arItem['promo_price']))
                    $price = number_format($arItem['price'] - ($arItem['price'] * $order['Person']['discount'] / 100), 2, '.', '');
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
            if (!empty($order['Cart']['dostavka'])) {

                PHPShopObj::loadClass('delivery');
                $PHPShopDelivery = new PHPShopDelivery($order['Person']['dostavka_metod']);

                $tax_delivery = $PHPShopDelivery->getParam('ofd_nds');

                if (empty($tax_delivery))
                    $tax_delivery = $tax;

                $Uniteller->parameters['Receipt']['lines'][] = array(
                    'name'           => PHPShopString::win_utf8('Доставка'),
                    'price'          => (float) number_format($order['Cart']['dostavka'], 2, '.', ''),
                    'qty'            => 1,
                    'sum'            => (float) number_format($order['Cart']['dostavka'], 2, '.', ''),
                    'vat'            => $tax_delivery,
                    'payattr'        => 1,
                    'lineattr'       => 4
                );

            }

            $Uniteller->parameters['Receipt']['customer']['email'] = $_POST['mail'];
            $Uniteller->parameters['Receipt']['total'] = number_format($PHPShopOrderFunction->getTotal(), 0, '.', '');
            $Uniteller->parameters['Receipt']['payments'][0]['amount'] = number_format($PHPShopOrderFunction->getTotal(), 0, '.', '');
            $Uniteller->parameters['Receipt']['taxmode'] = $Uniteller->option['taxationSystem'];

            $payment_form = $Uniteller->getForm();

            $Uniteller->log($Uniteller->parameters, $Uniteller->parameters['Order_IDP'], 'Форма подготовлена для отправки', 'Регистрация заказа');

            $obj->set('payment_forma', PHPShopText::form($payment_form, 'unitellerpay', 'post', $Uniteller::$FORM_ACTION, '_blank'));

            $return = ParseTemplateReturn($GLOBALS['SysValue']['templates']['uniteller']['uniteller_payment_forma'], true);

        } elseif ($PHPShopOrderFunction->getSerilizeParam('orders.Person.order_metod') == 10012)
            $return = ' Заказ обрабатывается менеджером';

    return $return;
}

$addHandler = array('userorderpaymentlink' => 'userorderpaymentlink_mod_uniteller_hook');
?>