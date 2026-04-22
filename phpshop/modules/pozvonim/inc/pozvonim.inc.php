<?php

if (!defined("OBJENABLED")) {
    exit(header('Location: /?error=OBJENABLED'));
}

/**
 * Элемент формы обратного звонка
 */
class AddToTemplatepozvonimElement extends PHPShopElements
{

    var $debug = false;

    /**
     * Конструктор
     */
    function __construct()
    {
        parent::__construct();
        $this->option();
    }

    /**
     * Настройки
     */
    function option()
    {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['pozvonim']['pozvonim_system']);
        $PHPShopOrm->debug = $this->debug;
        $this->option = $PHPShopOrm->select();
    }

    /**
     * Вывод
     */
    function display()
    {
        if ($this->option['key']) {
            // Подключаем шаблон
            $dis = '<script crossorigin="anonymous" async type="text/javascript" src="//api.pozvonim.com/widget/callback/v3/'
                   . $this->option['key']
                   . '/connect" id="check-code-pozvonim" charset="UTF-8"></script>';
            // Назначаем переменную шаблона
            $this->set('leftMenu', $dis);
            $this->set('pozvonim', $dis);
        }
    }

}

// Добавляем в шаблон элемент
if ($PHPShopNav->notPath('pozvonim')) {
    $AddToTemplatepozvonimElement = new AddToTemplatepozvonimElement();
    $AddToTemplatepozvonimElement->display();
}
?>