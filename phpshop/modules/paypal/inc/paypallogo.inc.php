<?php

/**
 * Обязательнео лого PayPal
 */
class AddToTemplatePaypalLogo extends PHPShopElements {

    var $debug = false;

    /**
     * Конструктор
     */
    function __construct() {
        parent::__construct();
        $this->option();
    }

    /**
     * Настройки
     */
    function option() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['paypal']['paypal_system']);
        $PHPShopOrm->debug = $this->debug;
        $this->option = $PHPShopOrm->select();
    }

    /**
     * Вывод формы
     */
    function display() {

        $forma = parseTemplateReturn($GLOBALS['SysValue']['templates']['paypal']['paypal_logo'], true);
        $this->set('leftMenuContent', $forma);
        $this->set('leftMenuName', $this->option['title']);

        // Подключаем шаблон
        $dis = $this->parseTemplate($this->getValue('templates.left_menu'));


        // Назначаем переменную шаблона
        //if ($this->option['operator'] == 1)
            switch ($this->option['logo_enabled']) {

                case 1:
                    $this->set('leftMenu', $dis, true);
                    break;

                case 2:
                    $this->set('rightMenu', $dis, true);
                    break;

                default: $this->set('paypallogo', $dis);
            }
    }

}

// Добавляем в шаблон элемент
$AddToTemplatePaypalLogo = new AddToTemplatePaypalLogo();
$AddToTemplatePaypalLogo->display();
?>