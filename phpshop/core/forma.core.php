<?php

/**
 * Обработчик формы сообщения с сайта
 * @author PHPShop Software
 * @version 1.4
 * @package PHPShopCore
 */
class PHPShopForma extends PHPShopCore {

    private $ajax = false;

    /**
     * Конструктор
     */
    function __construct() {
        $this->debug = false;

        if (isset($_POST['ajax'])) {
            $this->ajax = true;
        }

        // список экшенов
        $this->action = array("post" => "content", "post" => "name", "nav" => "index");
        parent::__construct();
    }

    /**
     * Экшен по умолчанию, вывод формы связи
     */
    function index() {

        // Мета
        $title = __('Форма связи');
        $this->title = $title . ' - ' . $this->PHPShopSystem->getValue("name");

        // Определяем переменные
        $this->set('pageTitle', $title);

        // Навигация хлебные крошки
        $this->navigation(null, $title);

        // Перехват модуля
        $this->setHook(__CLASS__, __FUNCTION__);

        // Подключаем шаблон
        $this->addToTemplate("forma/page_forma_list.tpl");

        $this->parseTemplate($this->getValue('templates.page_page_list'));
    }

    /**
     * Экшен отправка формы при получении $_POST[name]
     */
    function name() {
        $this->content();
    }

    /**
     * Проверка ботов
     * @param array $option параметры проверки [url|captcha|referer]
     * @return boolean
     */
    function security($option = array('url' => false, 'captcha' => true, 'referer' => true)) {
        global $PHPShopRecaptchaElement;

        return $PHPShopRecaptchaElement->security($option);
    }

    /**
     * Экшен отправка формы при получении $_POST[content]
     */
    function content() {

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__, $_POST))
            return true;

        if ($this->ajax) {
            $_POST['tema'] = PHPShopString::utf8_win1251($_POST['tema']);
            $_POST['name'] = PHPShopString::utf8_win1251($_POST['name']);
            $_POST['content'] = PHPShopString::utf8_win1251($_POST['content']);
            $_POST['content'] .= ' 
                
' . __('Источник') . ': ' . $_SERVER['HTTP_REFERER'].'
IP: '.$_SERVER['REMOTE_ADDR'];
            $_POST['mail'] = PHPShopString::utf8_win1251($_POST['mail']);
        }

        // Безопасность
        if ($this->security()) {
            $this->lead();
            $this->send();

            if ($this->ajax) {
                echo json_encode([
                    'message' => PHPShopString::win_utf8($this->get('Error'))
                ]);
                exit;
            }
        } else {
            if ($this->ajax) {
                echo json_encode([
                    'message' => PHPShopString::win_utf8(__("Ошибка ключа, повторите попытку ввода ключа"))
                ]);
                exit;
            }
            $this->set('Error', __("Ошибка ключа, повторите попытку ввода ключа"));
        }

        $this->index();
    }

    /**
     * Добавление лида
     */
    function lead() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['notes']);
        $insert = array('date_new' => time(), 'message_new' => $_POST['tema'], 'name_new' => $_POST['name'], 'mail_new' => $_POST['mail'], 'tel_new' => $_POST['tel'], 'content_new' => PHPShopSecurity::TotalClean($_POST['content']));
        $PHPShopOrm->insert($insert);
    }

    /**
     * Генерация сообщения
     */
    function send() {

        // Подключаем библиотеку отправки почты
        PHPShopObj::loadClass("mail");

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__, $_POST))
            return true;

        if (!empty($_POST['tema']) and ! empty($_POST['name']) and ! empty($_POST['content'])) {
            $subject = $_POST['tema'] . " - " . $this->PHPShopSystem->getValue('name');

            $this->set('user_content', $_POST['content']);
            $this->set('user_name', $_POST['name']);
            $this->set('user_tel', $_POST['tel']);
            $this->set('date', date("d-m-y H:s a"));
            $this->set('user_mail', $_POST['mail']);

            $PHPShopMail = new PHPShopMail($this->PHPShopSystem->getEmail(), $this->PHPShopSystem->getEmail(), $subject, null, true, true, array('replyto' => $_POST['mail']));

            $content = ParseTemplateReturn('./phpshop/lib/templates/users/mail_admin_forma.tpl', true);
            $PHPShopMail->sendMailNow($content);

            $this->set('Error', __("Спасибо!
Мы свяжемся с вами с ближайшее время."));
        } else
            $this->set('Error', __("Не заполнены обязательные поля"));
    }

}

?>