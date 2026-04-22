<?php

include_once dirname(__FILE__) . '/tinkoffMerchantAPI.class.php';

/**
 * Оплата через Т-Банк
 * @author PHPShop Software
 * @version 1.4
 * @todo https://www.tbank.ru/kassa/dev/payments/
 * @todo https://developer.tbank.ru/eacq/api/send-closing-receipt
 */
class Tinkoff {

    public $currency = 'RUB';
    public $customerEmail = '';
    public $settings = array();

    const PAYMENT_ID = 10032;
    const RECEIPT_URL = "https://securepay.tinkoff.ru/cashbox/SendClosingReceipt";

    public function __construct() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['tinkoff']['tinkoff_system']);
        $this->settings = $PHPShopOrm->select();

        if ($this->settings['payment_mode'] == 1)
            $this->payment_mode = 'full_prepayment';
        else
            $this->payment_mode = 'full_payment';
    }

    static public $tinkoffVats = array(
        'none' => 'none',
        '0' => 'vat0',
        '5' => 'vat5',
        '10' => 'vat10',
        '18' => 'vat18',
        '20' => 'vat20',
        '22' => 'vat22',
    );

    public function log($message, $order_id, $status, $type) {

        $PHPShopOrm = new PHPShopOrm("phpshop_modules_tinkoff_log");
        $log = array(
            'message_new' => serialize($message),
            'order_id_new' => $order_id,
            'status_new' => $status,
            'type_new' => $type,
            'date_new' => time()
        );
        $PHPShopOrm->insert($log);
    }

    public function getPaymentUrl($obj, $value) {
        $this->customerEmail = $value['mail'];

        $requestData = array(
            'OrderId' => $obj->ouid,
            'Amount' => $obj->tinkoff_total,
            'TerminalKey' => $this->settings['terminal'],
            'DATA' => array(
                'Email' => $this->customerEmail,
                'Connection_type' => 'phpshop'
            ),
        );

        if ($this->settings['enabled_taxation']) {
            $requestData['Receipt'] = $this->getReceipt($obj);

            if (count($requestData['Receipt']['Items']) > 99) {
                return array('error' => 'Превышено допустимое количество позиций в чеке');
            }
        }

        $tinkoff = new TinkoffMerchantAPI($this->settings['terminal'], $this->settings['secret_key'], $this->settings['gateway']);
        $request = $tinkoff->buildQuery('Init', $requestData);
        $request = json_decode($request);

        $this->log(['request' => $requestData, 'result' => $request], $obj->ouid, 'Заказ зарегистрирован', 'Init');

        return isset($request->PaymentURL) ? array('url' => $request->PaymentURL) : array('error' => 'Запрос совершился неудачей');
    }

    /**
     *  Второй чек
     */
    public function createClosingReceipt($obj, $email, $PaymentId) {

        $this->customerEmail = $email;
        $this->payment_mode = 'full_payment';

        $requestData = array(
            'PaymentId' => $PaymentId,
            'TerminalKey' => $this->settings['terminal'],
        );

        $requestData['Receipt'] = $this->getReceipt($obj);
        $requestData['Receipt']["FfdVersion"] = "1.05";
        $requestData['Receipt']["Payments"]['AdvancePayment']=$obj->tinkoff_total;

        $tinkoff = new TinkoffMerchantAPI($this->settings['terminal'], $this->settings['secret_key'], self::RECEIPT_URL);
        $request = $tinkoff->buildQuery(null, $requestData);
        $request = json_decode($request,true);

        if (!empty($request['ErrorCode']))
            $this->log(['request' => $requestData, 'result' => $request], $obj->ouid, 'Запрос совершился неудачей', 'ClosingReceipt');
        else
            $this->log(['request' => $requestData, 'result' => $request], $obj->ouid, 'Второй чек отправлен', 'ClosingReceipt');

    }

    function getReceipt($obj) {
        global $PHPShopSystem;
        $receiptItems = array();

        foreach ($obj->tinkoff_cart as $product) {

            // Скидка
            if ($obj->discount > 0 && empty($product['promo_price']))
                $price = $product['price'] - ($product['price'] * $obj->discount / 100);
            else
                $price = $product['price'];

            // Ограничение 128 символов
            $product['name'] = substr($product['name'], 0, 128);

            $receiptItems[] = array(
                'Name' => mb_convert_encoding($product['name'], "UTF-8", "Windows-1251"),
                "Price" => $price * 100,
                "Quantity" => $product['num'],
                "Amount" => $price * $product['num'] * 100,
                "PaymentMethod" => $this->payment_mode,
                "PaymentObject" => "commodity",
                "Tax" => self::getTinkoffVat($PHPShopSystem->objRow['nds']),
            );
        }

        if ($obj->delivery > 0) {
            $receiptItems[] = array(
                'Name' => mb_convert_encoding('Доставка', "UTF-8", "Windows-1251"),
                "Price" => $obj->delivery * 100,
                "Quantity" => 1,
                "Amount" => $obj->delivery * 100,
                "PaymentMethod" => $this->payment_mode,
                "PaymentObject" => "service",
                "Tax" => self::getTinkoffVat($obj->tinkoff_delivery_nds),
            );
        }

        $receipt = array(
            'Email' => $this->customerEmail,
            'Taxation' => $this->settings['taxation'],
            'Items' => $receiptItems,
        );

        return $receipt;
    }

    /**
     * @param $rate
     * @return mixed
     */
    public static function getTinkoffVat($rate) {
        global $PHPShopSystem;

        if ($PHPShopSystem->getParam('nds_enabled')) {
            return self::$tinkoffVats[$rate] ? self::$tinkoffVats[$rate] : self::$tinkoffVats['none'];
        }

        return self::$tinkoffVats['none'];
    }

}
