<?php

include_once dirname(__FILE__) . '/../class/YandexKassa.php';

/**
 * Обработчик оплаты яндекс касса
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopCore
 */
class PHPShopYandexkassa extends PHPShopCore {

    /**
     * Конструктор
     */
    function __construct() {
        // Список экшенов
        parent::__construct();
    }

    /**
     * Экшен по умолчанию
     */
    function index() {
        if (!isset($_REQUEST['order']) || empty($_REQUEST['order'])) {
            $this->setError404();
        }

        try {
            $YandexKassa = new YandexKassa();
            $logOrder = $YandexKassa->getLogDataByOrderId((int) base64_decode($_REQUEST['order']));
            $order = $YandexKassa->getOrderStatus($logOrder['yandex_id']);

            if (isset($order['paid']) && $order['paid']) {
                $forma = $GLOBALS['SysValue']['templates']['yandexkassa']['yandexmoney_success_forma'];
            } else {
                $forma = $GLOBALS['SysValue']['templates']['yandexkassa']['yandexmoney_fail_forma'];
            }
        } catch (\Exception $exception) {
            $forma = $GLOBALS['SysValue']['templates']['yandexkassa']['yandexmoney_fail_forma'];
        }

        if ($GLOBALS['PHPShopBase']->codBase == 'utf-8')
            $forma = PHPShopString::win_utf8($forma, true);

        $this->parseTemplate($forma, true);
    }

}
