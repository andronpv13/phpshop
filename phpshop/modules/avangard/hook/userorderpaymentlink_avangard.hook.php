<?php

/**
 * Функция хук, вывод кнопки оплаты в ЛК и регистрация регистрация заказа в платежном шлюзе
 * @param object $obj объект функции
 * @param array $PHPShopOrderFunction данные о заказе
 */
function userorderpaymentlink_mod_avangard_hook($obj, $PHPShopOrderFunction)
{
    include_once 'phpshop/modules/avangard/class/Avangard.php';

    $Avangard = new Avangard();

    // Контроль оплаты от статуса заказа
    if ($PHPShopOrderFunction->order_metod_id == Avangard::PAYMENT_METHOD)
        if ($PHPShopOrderFunction->getParam('statusi') == $Avangard->option['status_id'] or empty($Avangard->option['status_id'])) {

            $Avangard->setAmount($PHPShopOrderFunction->getTotal() * 100);
            $Avangard->setOrderNumber($PHPShopOrderFunction->objRow['uid']);
            $payment_form = $Avangard->getForm();

            $Avangard->log($payment_form, $Avangard->getOrderNumber(), 'Форма подготовлена для отправки', 'Регистрация заказа');
            $Avangard->orderState($Avangard->getOrderNumber(), Avangard::LOG_STATUS_NEW_ORDER);
            
            $return = PHPShopText::form($payment_form, 'avangardpay', 'post', $Avangard->getApiURL(), '_blank');
            
            if($Avangard->option['qr'] == 1){
                $payment_form_qr = $Avangard->getForm(true);
                $return .= PHPShopText::form($payment_form_qr, 'avangardpay', 'post', $Avangard->getApiURL(), '_blank');
            }
        } elseif ($PHPShopOrderFunction->getSerilizeParam('orders.Person.order_metod') == Avangard::PAYMENT_METHOD)
            $return = ' Заказ обрабатывается менеджером';

    return $return;
}

$addHandler = array('userorderpaymentlink' => 'userorderpaymentlink_mod_avangard_hook');
?>