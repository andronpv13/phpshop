<?php

//include_once 'phpshop/class/xml.class.php';

class Avangard
{
    const API_URL = 'https://www.avangard.ru/iacq/post';
    const REVERSE_ORDER_URL = 'https://www.avangard.ru/iacq/h2h/reverse_order';
    const PAYMENT_METHOD = 10013;
    const STATUS_SUCCESS = 3;
    const STATUS_REVERSE = 6;
    const LOG_STATUS_NEW_ORDER = 1;
    const LOG_STATUS_PAID = 2;
    const LOG_STATUS_REVERSE = 3;

    private $amount = 0;
    private $order_number;
    private $language = 'ru';

    function __construct()
    {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['avangard']['avangard_system']);

        /*
         * Опции модуля
         */
        $this->option = $PHPShopOrm->select();
    }

    /**
     * @param $message
     * @param $order_id
     * @param $status
     * @param $type
     */
    public function log($message, $order_id, $status, $type)
    {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['avangard']['avangard_log']);

        $PHPShopOrm->insert(
            array(
                'message_new'  => serialize($message),
                'order_id_new' => $order_id,
                'status_new'   => $status,
                'type_new'     => $type,
                'date_new'     => time()
            )
        );
    }

    /**
     * Состояние заказа
     * @param string $order_id номер заказа
     * @param int $statusCode код статуса
     * @param string $ticket
     */
    public function orderState($order, $status_code)
    {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['avangard']['avangard_order_status']);

        if($status_code == self::LOG_STATUS_NEW_ORDER) {
            $PHPShopOrm->insert(
                array(
                    'order_new'       => $order,
                    'status_code_new' => $status_code,
                    'date_new'        => time()
                )
            );
        } else {
            $PHPShopOrm->update(array('status_code_new' => $status_code), array('`order`' => '="' . $order . '"'));
        }
    }

    /**
     * Генерация формы
     *
     * @return string
     */
    public function getForm($qr = false)
    {
        $payment_forma = PHPShopText::setInput('hidden', 'shop_id', $this->option['shop_id'], false);
        $payment_forma .= PHPShopText::setInput('hidden', 'amount', $this->amount, false);
        $payment_forma .= PHPShopText::setInput('hidden', 'order_number', $this->order_number, false);
        $payment_forma .= "<input type='hidden' value='" . $this->getOrderDescription() . "' name='order_description' id='order_description'>";
        $payment_forma .= PHPShopText::setInput('hidden', 'language', $this->language, false);
        $payment_forma .= PHPShopText::setInput('hidden', 'back_url', $this->getBackURL(), false);
        $payment_forma .= PHPShopText::setInput('hidden', 'back_url_ok', $this->getSuccessBackURL(), false);
        $payment_forma .= PHPShopText::setInput('hidden', 'back_url_fail', $this->getFailBackUrl(), false);
        $payment_forma .= PHPShopText::setInput('hidden', 'signature', $this->getSignature(), false);
        $payment_forma .= '<p>' . $this->getOffer() . '</p>';

        if($qr){
            $payment_forma .= PHPShopText::setInput('hidden', 'is_qr', 1, false);
            $payment_forma .=PHPShopText::setInput('submit', 'send', $this->option['title_payment'] .' по QR', $float = "left;", 250);
        } else {
            $payment_forma .=PHPShopText::setInput('submit', 'send', $this->option['title_payment'], $float = "left;", 250);
        }
        
        return $payment_forma;
    }

    /**
     * Генерация поля signature
     *
     * @param bool $check
     * @return string
     */
    public function getSignature($check = false)
    {
        $check ? $sign = $this->option['av_sign'] : $sign = $this->option['shop_sign'];

        return strtoupper(md5(strtoupper(md5($sign) . md5($this->option['shop_id'] . $this->order_number . $this->amount))));
    }

    /**
     * @param $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;    
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param $orderNumber
     */
    public function setOrderNumber($orderNumber)
    {
        $this->order_number = $orderNumber;
    }

    /**
     * @return string
     */
    public function getOrderNumber()
    {
        return $this->order_number;
    }

    /**
     * @return string
     */
    public function getApiURL()
    {
        return self::API_URL;
    }

    /**
     * @return string
     */
    public function getOrderDescription()
    {
        global $PHPShopSystem;

        return PHPShopString::win_utf8($PHPShopSystem->getName() . ' оплата заказа №' . $this->getOrderNumber());
    }

    /**
     * @return string
     */
    public function getBackURL()
    {
        return self::getProtocol() . $_SERVER['SERVER_NAME'];
    }

    /**
     * @return string
     */
    public function getSuccessBackURL()
    {
        return self::getProtocol() . $_SERVER['SERVER_NAME'] . '/success/?status=success&uid=' . $this->order_number;
    }

    /**
     * @return string
     */
    public function getFailBackUrl()
    {
        return self::getProtocol() . $_SERVER['SERVER_NAME'] . '/success/?status=fail';
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
                    <input type="checkbox" value="on" name="offer" class="req" required="required">
                    Нажимая на кнопку, вы подтверждаете, что ознакомились с 
                    <a href="/page/' . $page['link']. '.html" target="_blank" class="avangard-link">Офертой.</a>
               </label><style>.avangard-link {color: #4a7eb7;} .avangard-link:hover, .avangard-link:focus  {text-decoration: underline;}</style>';
    }

    /**
     * @param $orderNumber
     */
    public function reverseOrder($orderNumber)
    {
        $xml = '<?xml version="1.0" encoding="windows-1251"?>' . "\n";
        $xml .= '<reverse_order><ticket>' . $this->getTicket($orderNumber) . '</ticket>' . "\n";
        $xml .= '<shop_id>' . $this->option['shop_id'] . '</shop_id>'  . "\n";
        $xml .= '<shop_passwd>' . $this->option['password'] . '</shop_passwd></reverse_order>';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::REVERSE_ORDER_URL . '?xml=' . urlencode($xml));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = xml2array(curl_exec($ch), false, false);

        $data['response_code'] == 0 ? $statusCode = self::LOG_STATUS_REVERSE : $statusCode = self::LOG_STATUS_PAID;
        $data['request'] = array('xml' => $xml);
        $this->log($data, $orderNumber, $data['response_message'], 'Отмена заказа');
        $this->orderState($orderNumber, $statusCode);
    }

    /**
     * @return string
     */
    public static function getProtocol()
    {
        return !empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS']) ? 'https://' : 'http://';
    }

    /**
     * @param $orderNumber
     * @return bool
     */
    public static function isPaid($orderNumber)
    {
        global $PHPShopModules;

        $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.avangard.avangard_order_status"));

        $log = $PHPShopOrm->select(array('*'), array("`order`=" => "'$orderNumber'", "`status_code`=" => self::LOG_STATUS_PAID), false, array('limit' => 1));

        return isset($log['id']) && $log['id'] > 0;
    }

    /**
     * @param $orderNumber
     * @return bool
     */
    public static function isReverse($orderNumber)
    {
        global $PHPShopModules;

        $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.avangard.avangard_order_status"));

        $log = $PHPShopOrm->select(array('*'), array("`order`=" => "'$orderNumber'", "`status_code`=" => self::LOG_STATUS_REVERSE), false, array('limit' => 1));

        return isset($log['id']) && $log['id'] > 0;
    }

    /**
     * @param $orderNumber
     * @return array
     */
    public static function getLogs($orderNumber)
    {
        global $PHPShopModules;

        $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.avangard.avangard_log"));

        $log = $PHPShopOrm->select(array('*'), array("order_id=" => "'$orderNumber'"), array('order' => 'date DESC'));

        $logArray = array();
        if(!empty($log['id'])) {
            $logArray = array($log);
        } else {
            $logArray = $log;
        }

        return $logArray;
    }

    /**
     * @param $orderNumber
     * @return bool
     */
    private function getTicket($orderNumber)
    {
        global $PHPShopModules;

        $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.avangard.avangard_log"));

        $log = $PHPShopOrm->select(array('*'), array("`order_id`=" => "'$orderNumber'", "`status`=" => '"Заказ оплачен"'), false, array('limit' => 1));

        if(!$log) {
            return false;
        }

        $text = unserialize($log['message']);

        return $text['ticket'];
    }
}