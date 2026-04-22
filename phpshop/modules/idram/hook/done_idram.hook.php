<?php

include_once dirname(__DIR__) . '/class/Idram.php';

function send_to_order_mod_idram_hook($obj, $value, $rout) {

    if ($rout === 'END' && Idram::isIdramPaymentMethod($value['order_metod'])) {

        $Idram = new Idram();

        // Контроль оплаты от статуса заказа
        if (empty($Idram->options['status'])) {

            $total = number_format($obj->total, 2, '.', '');

            $orm = new PHPShopOrm('phpshop_orders');
            $order = $orm->getOne(['id'], ['uid' => "='" . $obj->ouid . "'"]);

            $Idram->log(
                ['form' => $Idram->createPaymentForm($obj->ouid, $order['id'], $total)],
                $obj->ouid,
                'Форма подготовлена для отправки',
                'Регистрация заказа'
            );

            $obj->set('payment_forma', PHPShopText::form($Idram->createPaymentForm($obj->ouid, $order['id'], $total), 'idrampay', 'post', Idram::PAYMENT_FORM_ACTION, '_blank'));
            $obj->set('payment_info', $Idram->options['payment_description']);
            $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['idram']['idram_payment_forma'], true);
        } else {
            $obj->set('mesageText', $Idram->options['payment_status']);
            $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['order_forma_mesage']);
        }

        $obj->set('orderMesage', $forma);
    }
}

$addHandler = ['send_to_order' => 'send_to_order_mod_idram_hook'];
?>