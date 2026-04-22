<?php

class Uniteller
{
    public static $FORM_ACTION = 'https://fpay.uniteller.ru/v2/pay';

    function __construct() {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['uniteller']['uniteller_system']);

        /*
         * Опции модуля
         */
        $this->option = $PHPShopOrm->select();

        /*
         * Данные для передачи
         */
        $this->parameters = array(
            'Shop_IDP'       => $this->option['shop_idp'],
            'URL_RETURN_NO'  => 'http://' . $_SERVER['SERVER_NAME'] . '/success/?status=fail',
            'URL_RETURN_OK'  => 'http://' . $_SERVER['SERVER_NAME'] . '/success/?status=success',
            'Receipt'        => array(
                'payments' => array(
                    array(
                    'kind' => 1,
                    'type' => 0
                    )
                )
            )
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

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['uniteller']['uniteller_log']);

        foreach($message['Receipt']['lines'] as $key => $val)
            $message['Receipt']['lines'][$key]['name'] = PHPShopString::utf8_win1251($message['Receipt']['lines'][$key]['name']);

        $message['Comment'] = PHPShopString::utf8_win1251($message['Comment']);

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
                $tax = 0;
            else
                $tax = $PHPShopSystem->getParam('nds');
        } else
            $tax = -1;

        return $tax;
    }


    /**
     * Генерация формы
     *
     * @return string
     */
    public function getForm()
    {
        $payment_forma = PHPShopText::setInput('hidden', 'Shop_IDP', $this->parameters['Shop_IDP'], false);
        $payment_forma .= PHPShopText::setInput('hidden', 'Order_IDP', $this->parameters['Order_IDP'], false);
        $payment_forma .= PHPShopText::setInput('hidden', 'Subtotal_P', $this->parameters['Subtotal_P'], false);
        $payment_forma .= PHPShopText::setInput('hidden', 'Comment', $this->parameters['Comment'], false);
        $payment_forma .= PHPShopText::setInput('hidden', 'Email', $this->parameters['Email'], false);
        $payment_forma .= PHPShopText::setInput('hidden', 'Receipt', base64_encode(json_encode($this->parameters['Receipt'])), false);
        $payment_forma .= PHPShopText::setInput('hidden', 'URL_RETURN_NO', $this->parameters['URL_RETURN_NO'], false);
        $payment_forma .= PHPShopText::setInput('hidden', 'URL_RETURN_OK', $this->parameters['URL_RETURN_OK'], false);
        $payment_forma .= PHPShopText::setInput('hidden', 'ReceiptSignature', $this->get_ReceiptSignature(), false);
        $payment_forma .= PHPShopText::setInput('hidden', 'Signature', $this->get_signature(), false);

        $payment_forma .=PHPShopText::setInput('submit', 'send', $this->option['title_payment'], $float = "left; margin-left:10px;", 250);

        return $payment_forma;
    }

    /**
     * Генерация поля ReceiptSignature
     *
     * @return string
     */
    public function get_ReceiptSignature() {

        return strtoupper(
            hash('sha256', (
                hash('sha256', $this->parameters['Shop_IDP']) . '&' .
                hash('sha256', $this->parameters['Order_IDP']) .  '&' .
                hash('sha256', $this->parameters['Subtotal_P']) . '&' .
                hash('sha256', base64_encode(json_encode($this->parameters['Receipt']))) . '&' .
                hash('sha256', $this->option['password']))
            )
        );
    }

    /**
     * Генерация поля signature
     *
     * @return string
     */
    public function get_signature() {

        return strtoupper(
            md5(
                md5($this->parameters['Shop_IDP']) . '&' .
                md5($this->parameters['Order_IDP']) .  '&' .
                md5($this->parameters['Subtotal_P']) . '&' .
                md5('') . '&' .
                md5('') . '&' .
                md5('') . '&' .
                md5('') . '&' .
                md5('') . '&' .
                md5('') . '&' .
                md5('') . '&' .
                md5($this->option['password'])
            )
        );
    }
}