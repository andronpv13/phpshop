<?php

if (!defined("OBJENABLED"))
    exit(header('Location: /?error=OBJENABLED'));

class PHPShopRulepartner extends PHPShopCore {

    /**
     * Конструктор
     */
    function __construct() {

        // Имя Бд
        $this->objBase = $GLOBALS['SysValue']['base']['partner']['partner_system'];
        $this->debug = false;
        parent::__construct();

        // Навигация хлебные крошки
        $this->navigation(null, __('Партнерская программа'));
    }

    function index() {

        $PHPShopOrm = new PHPShopOrm($this->objBase);
        $data = $PHPShopOrm->select(array('percent,rule'));

        // Определяем переменные
        $this->set('partnerPercent', $data['percent']);
        $this->set('pageContent', Parser($data['rule']));
        $this->set('pageTitle', $GLOBALS['SysValue']['lang']['partner_rule_title']);

        // Мета
        $this->title = __("Кабинет партнера").' - '.__('Напоминание пароля')." - " . $this->PHPShopSystem->getValue("name");

        // Подключаем шаблон
        $this->parseTemplate($this->getValue('templates.page_page_list'));
    }

}

?>
