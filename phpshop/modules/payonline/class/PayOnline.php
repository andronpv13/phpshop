<?php

/**
 * Class PayOnline
 */
class PayOnline {

    const PAYMENT_ID = 10033;
    const CURRENCY = 'RUB';
    const FORM_ACTION = 'https://secure.payonlinesystem.com/ru/payment/';

    private $orderId;
    private $amount;

    public function __construct()
    {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['payonline']['payonline_system']);

        /*
         * Опции модуля
         */
        $this->option = $PHPShopOrm->select();
    }

    /**
     * Генерация формы
     *
     * @return string
     */
    public function getForm()
    {
        $payment_forma = PHPShopText::setInput('hidden', 'OrderId', $this->getOrderId(), false);
        $payment_forma .= PHPShopText::setInput('hidden', 'Amount', $this->getAmount(), false);
        $payment_forma .= PHPShopText::setInput('hidden', 'MerchantId', $this->option['merchant_id'], false);
        $payment_forma .= PHPShopText::setInput('hidden', 'Currency', self::CURRENCY, false);
        $payment_forma .= PHPShopText::setInput('hidden', 'SecurityKey', $this->generateSecurityKey(), false);
        $payment_forma .= PHPShopText::setInput('hidden', 'FailUrl', $this->getFailUrl(), false);
        $payment_forma .= PHPShopText::setInput('hidden', 'ReturnUrl', $this->getSuccessUrl(), false);
        $payment_forma .= '<p>' . $this->getOffer() . '</p>';

        $payment_forma .=PHPShopText::setInput('submit', 'send', $this->option['title_payment'], $float = "left; margin-left:10px;", 250);

        return $payment_forma;
    }

    /**
     * @param $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    private function generateSecurityKey()
    {
        return md5('MerchantId=' . $this->option['merchant_id'] . '&OrderId=' . $this->getOrderId() . '&Amount=' . $this->getAmount() . '&Currency=' . self::CURRENCY . '&PrivateSecurityKey=' . $this->option['key']);
    }

    /**
     * @return string
     */
    private function getFailUrl()
    {
        if(!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }

        return $protocol . $_SERVER['SERVER_NAME'] . '/success/?status=fail';
    }

    /**
     * @return string
     */
    private function getSuccessUrl()
    {
        if(!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }

        return $protocol . $_SERVER['SERVER_NAME'] . '/success/?status=success&Order_ID=' . $this->getOrderId();
    }

    /**
     * @return string
     */
    private function getOrderDescription()
    {
        global $PHPShopSystem;

        return PHPShopString::win_utf8($PHPShopSystem->getName() . ' оплата заказа ' . $this->getOrderId());
    }

    /**
     * @return string
     */
    public function getOffer()
    {
        if(!$this->option['page_id']) {
            return '';
        }

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page']);
        $page = $PHPShopOrm->select(array('link'), array('id=' => '"' . $this->option['page_id'] . '"'));

        return '<label style="padding-top: 10px;">
                    <input type="checkbox" value="on" name="offer" class="req" required="required" checked="">
                    Нажимая на кнопку, вы подтверждаете, что ознакомились с 
                    <a href="/page/' . $page['link']. '.html" target="_blank" class="payonline-link">Публичной офертой.</a>
               </label><style>.payonline-link {color: #4a7eb7;} .payonline-link:hover, .payonline-link:focus  {text-decoration: underline;}</style>';
    }

    /**
     * Запись лога
     * @param array $message содержание запроса в ту или иную сторону
     * @param string $order_id номер заказа
     * @param string $status статус оплаты
     */
    public function log($message, $order_id, $status, $type)
    {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['payonline']['payonline_log']);

        $log = array(
            'message_new'  => serialize($message),
            'order_id_new' => $order_id,
            'status_new'   => $status,
            'type_new'     => $type,
            'date_new'     => time()
        );

        $PHPShopOrm->insert($log);
    }

    public function fiskalize($order, $transactionId, $paymentType, $total)
    {
        $PHPShopSystem = new PHPShopSystem();
        $ndsEnabled = $PHPShopSystem->getParam('nds_enabled');
        $nds = $PHPShopSystem->getParam('nds');
        $cart = unserialize($order['orders']);
        $delivery = new PHPShopDelivery($cart['Person']['dostavka_metod']);

        $goods = [];
        foreach ($cart['Cart']['cart'] as $product) {
            if ((float) $cart['Person']['discount'] > 0 && empty($product['promo_price']))
                $price = ($product['price'] - ($product['price'] * (float) $cart['Person']['discount'] / 100));
            else
                $price = $product['price'];

            $goods[] =  [
                'description' => PHPShopString::win_utf8($product['name']),
                'quantity'    => number_format((string) $product['num'], 2, ".", ""),
                'amount'      => number_format((string) $price, 2, ".", ""),
                'tax'         => !empty($ndsEnabled) ? 'vat' . $nds : 'none'
            ];
        }
        if ((float) $cart['Cart']['dostavka'] > 0) {
            $goods[] = [
                'description' => PHPShopString::win_utf8('Доставка'),
                'quantity'    => '1.00',
                'amount'      =>  number_format((string) $cart['Cart']['dostavka'], 2, ".", ""),
                'tax'         => !empty($ndsEnabled) ? 'vat' . $delivery->getParam('ofd_nds') : 'none'
            ];
        }
        $params = [
            'operation' => 'Benefit',
            'transactionId' => $transactionId,
            'paymentSystemType' => $paymentType,
            'totalAmount' => $total,
            'goods' => $goods,
            'email' => $cart['Person']['mail']
        ];

        $body = json_encode($params);
        $key = md5('RequestBody='.$body.'&MerchantId='.$this->option['merchant_id'].'&PrivateSecurityKey='.$this->option['key']);
        $url = 'https://secure.payonlinesystem.com/Services/Fiscal/Request.ashx?MerchantId='.$this->option['merchant_id'].'&SecurityKey='.$key;


        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($body)
        ]);

        $result = curl_exec($ch);

        $this->log(json_decode($result), $this->getOrderId(), 'Фискализация', 'Результат фискализации');
    }
}