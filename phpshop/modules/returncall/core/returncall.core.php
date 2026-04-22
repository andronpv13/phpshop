<?php

class PHPShopReturncall extends PHPShopCore {
    
    var $empty_index_action = false;

    /**
     * Конструктор
     */
    function __construct() {

        // Имя Бд
        $this->objBase = $GLOBALS['SysValue']['base']['returncall']['returncall_jurnal'];

        // Отладка
        $this->debug = false;

        // Настройка
        $this->system();

        // Список экшенов
        $this->action = array(
            'post' => 'returncall_mod_send',
            'name' => 'done',
            'nav' => 'index'
        );


        parent::__construct();

        // Хлебные крошки
        $this->navigation(null, __('Обратный звонок'));

        // Мета
        $this->title = $this->system['title'] . " - " . $this->PHPShopSystem->getValue("name");
        $this->description = $this->system['title'] . " " . $this->PHPShopSystem->getValue("name");
        $this->keywords = $this->system['title'] . ", " . $this->PHPShopSystem->getValue("name");
    }

    /**
     * Настройка
     */
    function system() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['returncall']['returncall_system']);
        $this->system = $PHPShopOrm->select();
    }

    /**
     * Сообщение об удачной заявке
     */
    function done() {
        
        // Мета
        $this->title = $this->description = $this->system['title'] . __(' - Отправлено'). " - " . $this->PHPShopSystem->getValue("name");
        
        $message = $this->system['title_end'];
        if (empty($message))
            $message = $GLOBALS['SysValue']['lang']['returncall_done'];
        $this->set('pageTitle', $this->system['title']);
        $this->set('retuncallDone', $message);
        $message = PHPShopParser::file($GLOBALS['SysValue']['templates']['returncall']['returncall_done'], true, false, true);
        $this->set('pageContent', $message);
        $this->parseTemplate($this->getValue('templates.page_page_list'));
    }

    /**
     * Экшен по умолчанию, вывод формы звонка
     */
    function index($message = false) {

        // Статус отправки
        if ($message)
            $this->set('pageTitle', $message);
        else
            $message = $this->system['title'];

        if ($this->system['captcha_enabled'] == 1) {
            $PHPShopRecaptchaElement = new PHPShopRecaptchaElement();
            $this->set('returncall_captcha', $PHPShopRecaptchaElement->captcha('returncall', 'normal'));
        }

        // Подключаем шаблон
        $this->set('pageTitle', $message);
        $this->set('pageContent', PHPShopParser::file($GLOBALS['SysValue']['templates']['returncall']['returncall_forma'], true, false, true));
        $this->parseTemplate($this->getValue('templates.page_page_list'));
    }

    /**
     * Проверка ботов
     * @param array $option параметры проверки [url|captcha|referer]
     * @return boolean
     */
    function security() {
        global $PHPShopRecaptchaElement;

        return $PHPShopRecaptchaElement->security(['url' => 'returncall_mod_message', 'captcha' => true, 'referer' => true]);
    }

    /**
     * Экшен записи при получении $_POST[returncall_mod_send]
     */
    function returncall_mod_send() {

        if ($this->security() and PHPShopSecurity::true_param($_POST['returncall_mod_name'], $_POST['returncall_mod_tel'])) {
            $this->write();

            if(isset($_POST['ajax'])) {
                $message = $this->system['title_end'];
                if (empty($message))
                    $message = $GLOBALS['SysValue']['lang']['returncall_done'];

                echo json_encode([
                    'message' => PHPShopString::win_utf8($message),
                    'success' => true
                ]);
                exit;
            }

            header('Location: ./done.html');
            exit();
        } else {
            $message = $GLOBALS['SysValue']['lang']['returncall_error'];

            if(isset($_POST['ajax'])) {
                echo json_encode([
                    'message' => PHPShopString::win_utf8($message),
                    'success' => false
                ]);
                exit;
            }
        }

        $this->index($message);
    }

    /**
     * Запись в базу
     */
    function write() {

        // Подключаем библиотеку отправки почты
        PHPShopObj::loadClass("mail");

        if(isset($_POST['ajax'])) {
            $_POST['returncall_mod_name']    = PHPShopString::utf8_win1251($_POST['returncall_mod_name']);
            $_POST['returncall_mod_message'] = PHPShopString::utf8_win1251($_POST['returncall_mod_message']);
            $_POST['returncall_mod_time_start'] = PHPShopString::utf8_win1251($_POST['returncall_mod_time_start']);
            $_POST['returncall_mod_time_end'] = PHPShopString::utf8_win1251($_POST['returncall_mod_time_end']);
        }

        $insert = array();
        $insert['name_new'] = PHPShopSecurity::TotalClean($_POST['returncall_mod_name'], 2);
        $insert['tel_new'] = PHPShopSecurity::TotalClean($_POST['returncall_mod_tel'], 2);
        $insert['date_new'] = time();
        $insert['time_start_new'] = PHPShopSecurity::TotalClean($_POST['returncall_mod_time_start'], 2);
        $insert['time_end_new'] = PHPShopSecurity::TotalClean($_POST['returncall_mod_time_end'], 2);
        $insert['message_new'] = PHPShopSecurity::TotalClean($_POST['returncall_mod_message'], 2);
        $insert['ip_new'] = $_SERVER['REMOTE_ADDR'];
        $insert['status_new'] = 0;
        $insert['mail_new'] = PHPShopSecurity::TotalClean($_POST['returncall_mod_mail'], 2);

        // Запись в базу
        $this->PHPShopOrm->insert($insert);

        $zag = $this->PHPShopSystem->getValue('name') . " - " . __('Обратный звонок') . " - " . PHPShopDate::dataV();

        if (!empty($insert['time_end_new']))
            $insert['time_start_new'] .= ' - ' . $insert['time_end_new'];

        $message = "{Доброго времени}!

<p>
{С сайта} " . $this->PHPShopSystem->getValue('name') . " {пришла заявка на обратный звонок}
</p>
{Имя}:                " . $insert['name_new'] . "<br>
{Телефон}:            " . $insert['tel_new'] . "<br>
E-mail:            " . $insert['mail_new'] . "<br>
{Время звонка}:       " . $insert['time_start_new'] . "<br>
{Сообщение}:          " . $insert['message_new'] . "<br>
{Дата}:               " . PHPShopDate::dataV($insert['date_new']) . "<br>
IP:                 " . $_SERVER['REMOTE_ADDR'] . "<br>
REFERER:            " . $_SERVER['HTTP_REFERER'];

        // Отсылаем письмо администратору
        $PHPShopMail = new PHPShopMail($this->PHPShopSystem->getValue('adminmail2'), $this->PHPShopSystem->getValue('adminmail2'), $zag, '', true, true);
        $this->set('message', Parser($message));
        $content_adm = ParseTemplateReturn('./phpshop/lib/templates/order/blank.tpl', true);
        $PHPShopMail->sendMailNow($content_adm);
    }

}

?>