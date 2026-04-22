<?php

include_once dirname(__DIR__) . '/class/Idram.php';

function userorderpaymentlink_mod_idram_hook($obj, $PHPShopOrderFunction)
{
    // Контроль оплаты от статуса заказа
    if (Idram::isIdramPaymentMethod($PHPShopOrderFunction->order_metod_id)) {
        $Idram = new Idram();

        if ($PHPShopOrderFunction->getParam('statusi') == $Idram->options['status'] or empty($Idram->options['status'])) {
            $total = number_format($PHPShopOrderFunction->getTotal(), 2, '.', '');

            $Idram->log(
                ['form' => $Idram->createPaymentForm($PHPShopOrderFunction->objRow['uid'], $PHPShopOrderFunction->objRow['id'], $total)],
                $PHPShopOrderFunction->objRow['uid'],
                'Форма подготовлена для отправки',
                'Регистрация заказа'
            );

            $obj->set('payment_forma', PHPShopText::form($Idram->createPaymentForm($PHPShopOrderFunction->objRow['uid'], $PHPShopOrderFunction->objRow['id'], $total), 'idrampay', 'post', Idram::PAYMENT_FORM_ACTION, '_blank'));

           return ParseTemplateReturn($GLOBALS['SysValue']['templates']['idram']['idram_payment_forma'], true);

        }
        return ' Заказ обрабатывается менеджером';
    }
}

$addHandler = ['userorderpaymentlink' => 'userorderpaymentlink_mod_idram_hook'];
?>