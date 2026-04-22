<?php

PHPShopObj::loadClass("array");
/**
 * Класс получения настроек модуля
 */
class PHPShopVtbArray extends PHPShopArray
{

    function __construct()
    {
        $this->objType = 3;
        $this->objBase = $GLOBALS['SysValue']['base']['vtb']['vtb_system'];
        parent::__construct("login", "password", "dev_mode", "status", "title_sub", "taxationSystem","api_url");
    }

    /**
     * Запись лога
     * @param string $message содержание запроса в ту или иную сторону
     * @param string $order_id номер заказа
     * @param string $status статус оплаты
     */
    function log($message, $order_id, $status, $type)
    {

        $PHPShopOrm = new PHPShopOrm("phpshop_modules_vtb_log");
        $log = array(
            'message_new' => serialize($message),
            'order_id_new' => $order_id,
            'status_new' => $status,
            'type_new' => $type,
            'date_new' => time()
        );
        $PHPShopOrm->insert($log);
    }
}