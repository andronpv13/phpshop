<?php
/**
 * Обработчик отписки от рассылки
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopCore
 */
class PHPShopUnsubscribe extends PHPShopCore {

    /**
     * Конструктор
     */
    function __construct() {

        // Отладка
        $this->debug = false;

        $this->title = "Отказ от рассылки";

        $this->description = "Отказ от рассылки";

        // список экшенов
        $this->action = array('nav' => 'index');
        parent::__construct();
    }

    /**
     * Отписка
     */
    function index() {
        if (isset($_REQUEST['id']) && isset($_REQUEST['hash'])) {

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
            $user = $PHPShopOrm->select(array('id', 'mail', 'password'), array('id=' => intval($_REQUEST['id'])));

            if ($user['id']) {
                $hash = md5($user['mail'] . $user['password']);

                if ($hash === $_REQUEST['hash']) {
                    $PHPShopOrm->update(array('sendmail_new' => 0), array('id=' => $user['id']));
                    $this->set('content', PHPShopText::alert(__("Вы успешно отказались от новостной рассылки"), 'success'));
                }
                else
                    $this->set('content', PHPShopText::alert(__("Пользователь не найден")));

            }
            else
                $this->set('content', PHPShopText::alert(__("Пользователь не найден")));

            // Подключаем шаблон
            $this->parseTemplate($this->getValue('templates.unsubscribe_message'), true);
        }
        else
            $this->setError404();
    }

}

?>