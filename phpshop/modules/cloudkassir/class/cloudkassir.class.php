<?php

/**
 * Библиотека онлайнкассы CloudPayments
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopClass
 * @subpackage RestApi
 */
class CloudPaymentsRest {

    public $log = null;
    public $orderId = null;

    /**
     * Режим отладки
     * @var bool 
     */
    protected $debug = false;

    /**
     * Конструктор
     * @param string $OrderId № заказа
     */
    public function __construct($OrderId) {
        $this->orderId = $OrderId;
        $this->option();
    }

    /**
     * Настройки модуля
     */
    public function option() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['cloudkassir']['cloudkassir_system']);
        $this->option = $PHPShopOrm->select();
    }

    /**
     * Запрос REST
     * @param array $data данные
     * @param string $rout роутер
     * @param bool $post использовать POST, иначе GET
     * @return string
     */
    public function request($data) {
        $option = $this->option;
        $data_string = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.cloudpayments.ru/kkt/receipt");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string)
        ));
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "$option[publicid]:$option[apisecret]");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        $result = curl_exec($ch); // run the whole process

        curl_close($ch);
        $response = explode("\r\n\r\n", $result);
        $success = json_decode($response[1], true);

        return $success;

    }

    /**
     * Действие
     * @param array $data массив данных для чека
     * @param string $operation операция [sell|sell_refund]
     */
    public function setOparation($data, $operation = 'sell') {

        $this->log(__METHOD__ . '::' . $operation . ' IN', $data);
        $result = $this->request($data);
        $this->log(__METHOD__ . '::' . $operation . ' OUT', $result);
        return $result;
    }

    /**
     * Лог
     * @param string $path URL запроса
     * @param array $data данные
     */
    public function log($path, $data) {

        $this->log[$path] = $data;

        // Вывод отладки
        if ($this->debug) {
            echo '<pre>';
            print_r($this->log);
            echo '</pre>';
        } 
    }

}

?>