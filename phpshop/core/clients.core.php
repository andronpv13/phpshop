<?php

// Импортируем роутер личного кабинета
PHPShopObj::importCore('users');

/**
 * Обработчик проверки заказа пользователем
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopCore
 */
class PHPShopClients extends PHPShopUsers {
    /**
     * @var int срок активности в месяцах проверки, после блокируется
     */
    var $order_live=3;

    /**
     * Конструктор
     */
    function __construct() {

        // Отладка
        $this->debug=false;
        parent::__construct();

        // Список экшенов
        $this->action=array('get'=>array('order'),'post'=>array('order'),'nav'=>'index');
    }

    /**
     * Экшен по умочанию
     */
    function index() {

        $this->set('formaContent',ParseTemplateReturn($this->getValue('templates.clients_forma')));
        $this->set('formaTitle',__('On-line проверка состояния заказа'));

        // Перехват модуля
        $this->setHook(__CLASS__,__FUNCTION__);

        $this->ParseTemplate($this->getValue('templates.clients_page_list'));
    }

    /**
     * Проверка заполнения полей
     * @return bool
     */
    function true_user() {

        // Перехват модуля
        $hook=$this->setHook(__CLASS__,__FUNCTION__);
        if($hook) return $hook;

        if(PHPShopSecurity::true_order($_REQUEST['order']) and PHPShopSecurity::true_email($_REQUEST['mail'])) {
            return true;
        }
    }

    /**
     * Экшен вывода данных по заказу
     */
    function order() {

        // Проверка прохождения авторизации
        if($this->true_user()) {

            // Подключение функции вывода данных по заказу
            $this->doLoadFunction(__CLASS__,'action_order_info',$tip=2,'users');

            $this->set('formaTitle',__('On-line проверка состояния заказа'));

            // Перехват модуля
            $this->setHook(__CLASS__,__FUNCTION__);
            
            $this->ParseTemplate($this->getValue('templates.users_page_list'));
        }
        else {
            // Форма ввода заказа
            $this->index();
        }
    }
}
?>