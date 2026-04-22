<?php

/**
 * Обработчик гостевой книги
 * @author PHPShop Software
 * @version 1.3
 * @package PHPShopCore
 */
class PHPShopGbook extends PHPShopCore {
    
    var $empty_index_action = true;

    /**
     * Конструктор
     */
    function __construct() {

        // Имя Бд
        $this->objBase = $GLOBALS['SysValue']['base']['gbook'];

        // Путь для навигации
        $this->objPath = "/gbook/gbook_";

        // Отладка
        $this->debug = false;

        // Список экшенов
        $this->action = array("post" => "send_gb", "nav" => "ID", "get" => "add_forma");
        parent::__construct();

        // кол-во отзывов на странице
        if (!$this->num_row)
            $this->num_row = 10;

        // Навигация хлебные крошки
        $this->navigation(false, __('Отзывы'));
    }

    /**
     * Экшен по умолчанию, вывод отзывов
     */
    function index() {

        // Сообщение о записи отзыва
        if (!empty($_GET['write']))
            $this->set('Error', __("Сообщение успешно добавлено. Отзыв будет размещен только после проверки модератором"));
        
        $where['flag']="='1'";

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['flag'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        // Выборка данных
        $this->dataArray = parent::getListInfoItem(array('*'), $where, array('order' => 'datas DESC'));

        if (is_array($this->dataArray))
            foreach ($this->dataArray as $row) {

                // Ссылка на автора
                if (!empty($row['mail']))
                    $d_mail = PHPShopText::a('mailto:' . $row['mail'], PHPShopText::b($row['name']), $row['name']);
                else
                    $d_mail = PHPShopText::b($row['name']);

                // Определяем переменые
                $this->set('gbookData', PHPShopDate::dataV($row['datas'], false));
                $this->set('gbookName', $row['name']);
                $this->set('gbookTema', $row['tema']);
                $this->set('gbookMail', $d_mail);
                $this->set('gbookOtsiv', $row['otsiv']);
                $this->set('gbookOtvet', $row['otvet']);
                $this->set('gbookId', $row['id']);

                // Перехват модуля
                $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');

                // Подключаем шаблон
                $this->addToTemplate($this->getValue('templates.main_gbook_forma'));
            }

        // Пагинатор
        $this->setPaginator();

        // Мета
        $this->title = __('Отзывы') . ' - ' . $this->PHPShopSystem->getValue("name");
        $this->description = __('Отзывы') . '  ' . $this->PHPShopSystem->getValue("name");
        $this->keywords = __('Отзывы') . ', ' . $this->PHPShopSystem->getValue("name");

        $page = $this->PHPShopNav->getId();
        if ($page > 1) {
            $this->description .= ' Часть ' . $page;
            $this->title .= ' - Страница ' . $page;
        }

        // Перехват модуля
        $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');

        // Подключаем шаблон
        $this->parseTemplate($this->getValue('templates.gbook_page_list'));

        // Ссылка на новый отзыв
        $this->add($this->attachLink());
    }

    /**
     * Экшен выборки подробной информации при наличии переменной навигации ID
     * @return string
     */
    function ID() {

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__, null, 'START'))
            return true;

        // Безопасность
        if (!PHPShopSecurity::true_num($this->PHPShopNav->getId()))
            return $this->setError404();
        
        $where['id'] = '='.$this->PHPShopNav->getId();

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['id'].= ' and (servers ="" or servers REGEXP "i1000i")';

        // Выборка данных
        $row = parent::getFullInfoItem(array('*'), $where);

        // 404
        if (!isset($row))
            return $this->setError404();

        // Ссылка на автора
        if (!empty($row['mail']))
            $d_mail = PHPShopText::a('mailto:' . $row['mail'], PHPShopText::b($row['name']));
        else
            $d_mail = PHPShopText::b($row['name']);

        // Определяем переменные
        $this->set('gbookData', PHPShopDate::dataV($row['datas'],false));
        $this->set('gbookName', $row['name']);
        $this->set('gbookTema', $row['tema']);
        $this->set('gbookMail', $d_mail);
        $this->set('gbookOtsiv', $row['otsiv']);
        $this->set('gbookOtvet', $row['otvet']);
        $this->set('gbookId', $row['id']);

        // Перехват модуля
        $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');

        // Подключаем шаблон
        $this->addToTemplate($this->getValue('templates.main_gbook_forma'));

        // Мета
        $this->title = $row['tema'] . " - " . $this->PHPShopSystem->getValue("name");
        $this->description = strip_tags($row['otsiv']);
        $this->lastmodified = PHPShopDate::GetUnixTime($row['datas']);

        // Генератор keywords
        include('./phpshop/lib/autokeyword/class.autokeyword.php');
        $this->keywords = callAutokeyword($row['otsiv']);

        // Перехват модуля
        $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');

        // Подключаем шаблон
        $this->parseTemplate($this->getValue('templates.gbook_page_list'));
    }

    /**
     * Ссылка на новый отзыв
     * @return string
     */
    function attachLink() {

        // Перехват модуля
        $hook = $this->setHook(__CLASS__, __FUNCTION__);
        if ($hook)
            return $hook;

        return PHPShopText::div(PHPShopText::a('/gbook/?add_forma=true', __('Оставить отзыв')), 'center', 'padding:20px', 'gbook_add');
    }

    /**
     * Новый отзыв
     */
    function add_forma() {

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__))
            return true;

        $this->parseTemplate($this->getValue('templates.gbook_forma_otsiv'));
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
     * Экшен записи отзыва при получении $_POST[send_gb]
     */
    function send_gb() {

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__, $_POST, 'START'))
            return true;

        if ($this->security()) {
            $this->write();
            header("Location: ../gbook/?write=ok");
        } else {
            $this->set('Error', __("Ошибка ключа, повторите попытку ввода ключа"));

            // Перехват модуля
            $this->setHook(__CLASS__, __FUNCTION__, $_POST, 'END');

            $this->parseTemplate($this->getValue('templates.gbook_forma_otsiv'));
        }
    }

    /**
     * Запись отзыва в базу
     */
    function write() {

        // Подключаем библиотеку отправки почты
        PHPShopObj::loadClass("mail");

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__, $_POST))
            return true;

        if (isset($_POST['send_gb'])) {
            if (!PHPShopSecurity::true_email($_POST['mail_new'])) {//проверка почты
                $_POST['mail_new'] = null;
            }
            if (PHPShopSecurity::true_param($_POST['name_new'], $_POST['otsiv_new'], $_POST['tema_new'])) {
                $name_new = PHPShopSecurity::TotalClean($_POST['name_new'], 2);
                $otsiv_new = PHPShopSecurity::TotalClean($_POST['otsiv_new'], 2);
                $tema_new = PHPShopSecurity::TotalClean($_POST['tema_new'], 2);
                $mail_new = PHPShopSecurity::TotalClean($_POST['mail_new'], 3);
                $date = date("U");
                $ip = $_SERVER['REMOTE_ADDR'];

                // Запись в базу
                $this->PHPShopOrm->insert(array('datas' => $date, 'name' => $name_new, 'mail' => $mail_new, 'tema' => $tema_new, 'otsiv' => $otsiv_new), $prefix = '');

                $subject = __("Уведомление о новом отзыве");

                // Пересенные для шаблона сообщения
                $this->set('gbook_name', $name_new);
                $this->set('gbook_mail', $mail_new);
                $this->set('gbook_title', $tema_new);
                $this->set('gbook_content', $otsiv_new);
                $this->set('gbook_ip', $ip);

                // Шаблон сообщения администратору
                $message = ParseTemplateReturn('phpshop/lib/templates/gbook/mail.tpl', true);

                new PHPShopMail($this->PHPShopSystem->getEmail(), $this->PHPShopSystem->getEmail(), $subject, $message, false, false);
            }
        }
    }

}

?>