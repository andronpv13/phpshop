<?php

/**
 * Элемент формы обратного звонка
 */
class AddToTemplateOneclickElements extends PHPShopElements {

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
        global $OneclickOption;

        // Память настроек
        if (!$OneclickOption) {
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['oneclick']['oneclick_system']);
            $PHPShopOrm->debug = $this->debug;
            $OneclickOption = $this->option = $PHPShopOrm->select();
        } else
            $this->option = $OneclickOption;
    }

    /**
     * Вывод формы
     */
    function display() {

        if ($this->option['display'] == 0)
            return true;

        $PHPShopRecaptchaElement = new PHPShopRecaptchaElement();

        if ($this->option['captcha'] == 1)
            $this->set('oneclick_captcha', $PHPShopRecaptchaElement->captcha('oneclick'));

        $forma = PHPShopParser::file($GLOBALS['SysValue']['templates']['oneclick']['oneclick_forma'], true, false, true);
        $this->set('leftMenuContent', $forma);
        $this->set('leftMenuName', 'Быстрый заказ');

        // Подключаем шаблон
        if (empty($this->option['windows']))
            $dis = $this->parseTemplate($this->getValue('templates.left_menu'));
        else {
            if (empty($this->option['enabled']))
                $dis = PHPShopParser::file($GLOBALS['SysValue']['templates']['oneclick']['oneclick_window_forma'], true, false, true);
            else {
                $this->set('leftMenuContent', PHPShopParser::file($GLOBALS['SysValue']['templates']['oneclick']['oneclick_window_forma'], true, false, true));
                $dis = $this->parseTemplate($this->getValue('templates.left_menu'));
            }
        }


        // Назначаем переменную шаблона
        switch ($this->option['enabled']) {

            case 1:
                $this->set('leftMenu', $dis, true);
                break;

            case 2:
                $this->set('rightMenu', $dis, true);
                break;

            default: $this->set('oneclick', $dis);
        }
    }

}

function product_grid_mod_oneclickelem_hook($obj, $row) {
    $AddToTemplateOneclickElement = new AddToTemplateOneclickElements();

    if (empty($obj->PHPShopSystem->getSerilizeParam('admoption.cart_minimum')) or (int) $obj->PHPShopSystem->getSerilizeParam('admoption.cart_minimum') <= $obj->price($row, false, false)) {

        if ((int) $AddToTemplateOneclickElement->option['only_available'] === 1 && (int) $row['sklad'] === 1) {
            $AddToTemplateOneclickElement->set('oneclick', '');
        } elseif ((int) $AddToTemplateOneclickElement->option['only_available'] === 2 && (int) $row['sklad'] === 0) {
            $AddToTemplateOneclickElement->set('oneclick', '');
        } else {
            $AddToTemplateOneclickElement->display();
        }
    }
}

$addHandler = array
    (
    'product_grid' => 'product_grid_mod_oneclickelem_hook',
);
?>