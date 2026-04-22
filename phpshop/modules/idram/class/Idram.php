<?php

class Idram
{
    const PAYMENT_FORM_ACTION = 'https://money.idram.am/payment.aspx';
    const IDRAM_PAYMENT_ID = 10046;

    public $options = [];

    public function __construct()
    {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['idram']['idram_system']);
        $this->options = $PHPShopOrm->select();
    }

    public function createPaymentForm($orderNumber, $orderId, $total)
    {
        $system = new PHPShopSystem();
        $iso = $system->getDefaultValutaIso();
        if($iso !== 'AMD') {
            $currency = (new PHPShopOrm('phpshop_valuta'))->getOne(['*'], ['iso' => '="AMD"']);
            if(isset($currency['id'])) {
                $total = (float) $total * (float) $currency['kurs'];
            }
        }

        $description = PHPShopString::win_utf8($system->getName() . ' оплата заказа ' . $orderNumber);

        $form = PHPShopText::setInput('hidden', 'EDP_LANGUAGE', $this->options['language'], false);
        $form .= PHPShopText::setInput('hidden', 'EDP_REC_ACCOUNT', $this->options['idram_id'], false);
        $form .= PHPShopText::setInput('hidden', 'EDP_DESCRIPTION', $description, false);
        $form .= PHPShopText::setInput('hidden', 'EDP_AMOUNT', $total, false);
        $form .= PHPShopText::setInput('hidden', 'EDP_BILL_NO', $orderNumber, false);
        $form .= PHPShopText::setInput('hidden', 'EDP_ORDER_ID', (int) $orderId, false);
        $form .=PHPShopText::setInput('submit', 'send', $this->options['title'], $float = "none; margin-left:10px;text-align: center;", 250);

        return $form;
    }

    public static function isIdramPaymentMethod($paymentId)
    {
        return (int) $paymentId === self::IDRAM_PAYMENT_ID;
    }

    public static function getOrderStatuses($currentStatus)
    {
        $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
        $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
        $order_status_value[] = [__('Новый заказ'), 0, $currentStatus];

        if (is_array($OrderStatusArray))
            foreach ($OrderStatusArray as $order_status) {
                $order_status_value[] = [
                    $order_status['name'],
                    $order_status['id'],
                    $currentStatus
                ];
            }

        return $order_status_value;
    }

    public static function getAvailableLanguages($currentLang)
    {
        return [
            ['RU', 'RU', $currentLang],
            ['EN', 'EN', $currentLang],
            ['AM', 'AM', $currentLang],
        ];
    }

    public function log($message, $order_id, $status, $type)
    {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['idram']['idram_log']);

        $log = [
            'message_new'  => serialize($message),
            'order_id_new' => $order_id,
            'status_new'   => $status,
            'type_new'     => $type,
            'date_new'     => time()
        ];

        $PHPShopOrm->insert($log);
    }
}