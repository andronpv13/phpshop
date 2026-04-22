<?php

/**
 * Обработчик 404 ошибки
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopCore
 */
class PHPShop404 extends PHPShopCore {

    /**
     * Конструктор
     */
    function __construct() {
        parent::__construct();
    }

    function index() {
        $this->title='404';
        header("HTTP/1.0 404 Not Found");
        header("Status: 404 Not Found");

        // Перехват модуля
        $this->setHook(__CLASS__,__FUNCTION__);

        $this->parseTemplate($this->getValue('templates.error_page_forma'));
    }
}
?>