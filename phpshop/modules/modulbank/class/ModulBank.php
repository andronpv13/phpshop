<?php

class ModulBank
{
    protected static $API_URL = 'https://pay.modulbank.ru/pay';

    function __construct() {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['modulbank']['modulbank_system']);

        /*
         * Опции модуля
         */
        $this->option = $PHPShopOrm->select();

        /*
         * Данные для передачи
         */
        $this->parameters = array(
            'merchant'       => $this->option['merchant'],
            'testing'        => $this->option['dev_mode'],
            'fail_url'       => 'https://' . $_SERVER['HTTP_HOST'] . '/success/?status=fail',
            'callback_url'        => 'https://' . $_SERVER['HTTP_HOST'] . '/modulbank/',
            'callback_on_failure' => true,
            'unix_timestamp' => time()
        );
    }

    /**
     * Запись лога
     * @param array $message содержание запроса в ту или иную сторону
     * @param string $order_id номер заказа
     * @param string $status статус оплаты
     */
    public function log($message, $order_id, $status, $type)
    {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['modulbank']['modulbank_log']);

        $message['receipt_items'] = json_decode($message['receipt_items'], true);

        foreach($message['receipt_items'] as $key => $val)
            $message['receipt_items'][$key]['name'] = PHPShopString::utf8_win1251($message['receipt_items'][$key]['name']);

        $message['description']   = PHPShopString::utf8_win1251($message['description']);

        $log = array(
            'message_new'  => serialize($message),
            'order_id_new' => $order_id,
            'status_new'   => $status,
            'type_new'     => $type,
            'date_new'     => time()
        );

        $PHPShopOrm->insert($log);
    }

    /**
     * Возвращает величину НДС
     *
     * @return string
     */
    public function getNds()
    {
        global $PHPShopSystem;

        if ($PHPShopSystem->getParam('nds_enabled') == 1){
            if ($PHPShopSystem->getParam('nds') == 0)
                $tax = 'vat0';
             elseif ($PHPShopSystem->getParam('nds') == 5)
                $tax = 'vat5';
            elseif ($PHPShopSystem->getParam('nds') == 10)
                $tax = 'vat10';
            elseif ($PHPShopSystem->getParam('nds') == 18)
                $tax = 'vat18';
            elseif ($PHPShopSystem->getParam('nds') == 20)
                $tax = 'vat20';
            elseif ($PHPShopSystem->getParam('nds') == 22)
                $tax = 'vat22';
        } else
            $tax = 'none';

        return $tax;
    }

    /**
     * Генерация формы
     *
     * @return string
     */
    public function getForm()
    {
        $this->parameters['success_url'] = 'https://' . $_SERVER['HTTP_HOST'] . '/success/?status=success&uid=' . $this->parameters['order_id'];

        $payment_forma = PHPShopText::setInput('hidden', 'merchant', $this->parameters['merchant'], false);
        $payment_forma .= PHPShopText::setInput('hidden', 'amount', $this->parameters['amount'], false);
        $payment_forma .= PHPShopText::setInput('hidden', 'order_id', $this->parameters['order_id'], false);
        $payment_forma .= PHPShopText::setInput('hidden', 'description', $this->parameters['description'], false);
        $payment_forma .= PHPShopText::setInput('hidden', 'success_url', $this->parameters['success_url'], false);
        $payment_forma .= PHPShopText::setInput('hidden', 'testing', $this->parameters['testing'], false);
        $payment_forma .= PHPShopText::setInput('hidden', 'fail_url', $this->parameters['fail_url'], false);
        $payment_forma .= PHPShopText::setInput('hidden', 'callback_url', $this->parameters['callback_url'], false);
        $payment_forma .= PHPShopText::setInput('hidden', 'callback_on_failure', $this->parameters['callback_on_failure'], false);
        $payment_forma .= PHPShopText::setInput('hidden', 'receipt_contact', $this->parameters['receipt_contact'], false);
        $payment_forma .= "<input type='hidden' value='" . $this->parameters['receipt_items'] . "' name='receipt_items'>";
        $payment_forma .= PHPShopText::setInput('hidden', 'unix_timestamp', $this->parameters['unix_timestamp'], false);
        $payment_forma .= PHPShopText::setInput('hidden', 'signature', $this->get_signature(), false);

        $payment_forma .=PHPShopText::setInput('submit', 'send', $this->option['title_payment'], $float = "left; margin-left:10px;", 250);

        return $payment_forma;
    }

    /**
     * Генерация поля signature
     *
     * @return string
     */
    public function get_signature() {
        $keys = array_keys($this->parameters);
        sort($keys);
        $chunks = array();

        foreach ($keys as $k) {
            $v = (string) $this->parameters[$k];
            if (($v !== '') && ($k != 'signature')) {
                $chunks[] = $k . '=' . base64_encode($v);
            }
        }

        return sha1($this->option['key'] . strtolower(sha1($this->option['key'] . implode('&', $chunks))));
    }
}