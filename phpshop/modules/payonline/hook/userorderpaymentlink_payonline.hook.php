<?php

/**
 * Функция хук, вывод кнопки оплаты в ЛК и регистрация регистрация заказа в платежном шлюзе
 * @param object $obj объект функции
 * @param array $PHPShopOrderFunction данные о заказе
 */
function userorderpaymentlink_mod_payonline_hook($obj, $PHPShopOrderFunction)
{
    include_once 'phpshop/modules/payonline/class/PayOnline.php';

    $PayOnline = new PayOnline();

    // Контроль оплаты от статуса заказа
    if ($PHPShopOrderFunction->order_metod_id == PayOnline::PAYMENT_ID)
        if ($PHPShopOrderFunction->getParam('statusi') == $PayOnline->option['status'] or empty($PayOnline->option['status'])) {
            $PayOnline->setAmount(number_format($PHPShopOrderFunction->getTotal(), 2, '.', ''));
            $PayOnline->setOrderId($PHPShopOrderFunction->objRow['uid']);

            $PayOnline->log(array('form' => $PayOnline->getForm()), $PayOnline->getOrderId(), 'Форма подготовлена для отправки', 'Регистрация заказа');

            $obj->set('payment_forma', PHPShopText::form($PayOnline->getForm(), 'payonlinepay', 'post', PayOnline::FORM_ACTION, '_blank'));

            $return = ParseTemplateReturn($GLOBALS['SysValue']['templates']['payonline']['payonline_payment_forma'], true);

        } elseif ($PHPShopOrderFunction->getSerilizeParam('orders.Person.order_metod') == PayOnline::PAYMENT_ID)
            $return = ' Заказ обрабатывается менеджером';

    return $return;
}

$addHandler = array('userorderpaymentlink' => 'userorderpaymentlink_mod_payonline_hook');
?>