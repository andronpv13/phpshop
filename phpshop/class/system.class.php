<?php

if (!defined("OBJENABLED"))
    require_once(dirname(__FILE__) . "/obj.class.php");

/**
 * Системные настройки
 * @author PHPShop Software
 * @version 1.7
 * @package PHPShopObj
 */
class PHPShopSystem extends PHPShopObj {

    var $timezone = null;
    var $company = null;

    /**
     * Конструктор
     */
    function __construct() {
        $this->objID = 1;
        $this->install = false;
        $this->cache = false;
        $this->objBase = $GLOBALS['SysValue']['base']['system'];
        parent::__construct();

        // Временная зона
        $this->setTimeZone();
    }

    /**
     * Настройка временной зоны сервера 
     */
    function setTimeZone() {
        $this->timezone = $this->getSerilizeParam("admoption.timezone");
        if (function_exists('date_default_timezone_set') and ! empty($this->timezone))
            date_default_timezone_set($this->timezone);
    }

    /**
     * Вывод имени сайта
     * @return string
     */
    function getName() {
        return parent::getParam("name");
    }

    /**
     * Реквизиты юридических лиц
     * @param int $company
     */
    function setCompany($company) {
        if (!empty($company)) {
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['company']);
            $data = $PHPShopOrm->getOne(array('*'), array('id' => '=' . (int) $company, 'enabled' => "='1'"));
            if (is_array($data)) {
                $this->company['bank'] = unserialize($data['bank']);
                $this->company['bank']['org_name'] = $data['name'];
                parent::setParam('nds', $this->company['bank']['nds']);
                parent::setParam('company', $data['name']);
            }
        }
    }

    /**
     * Вывод сериализованного значения [param.val]
     * @param string $param
     * @return string
     */
    function getSerilizeParam($param) {

        $param = explode(".", $param);

        if (!empty($this->company) && isset($this->company[$param[0]]))
            $val = $this->company[$param[0]];
        else
            $val = parent::unserializeParam($param[0]);

        if(!empty($val[$param[1]]))
        return $val[$param[1]];
    }

    /**
     * Добавить или изменить сериализованный параметр [param.val]
     * @param string $param имя параметра 
     * @param mixed $value значение параметра
     */
    function setSerilizeParam($param, $value) {
        $param = explode(".", $param);
        if (is_array($param)) {
            $val = parent::unserializeParam($param[0]);
            $val[$param[1]] = $value;
            $this->objRow[$param[0]] = serialize($val);
        }
    }

    /**
     * Сравнение сериализованного значения [param.val]
     * @param string $param имя переменной
     * @param string $value значение переменной
     * @return bool
     */
    function ifSerilizeParam($param, $value = false) {
        if (empty($value))
            $value = 1;
        if ($this->getSerilizeParam($param) == $value)
            return true;
    }
    
    /**
     * Статус нового заказа по умолчанию
     * @return int
     */
    function getNewStatusOrder() {
        return (int) $this->getSerilizeParam('admoption.status_new_order');
    }

    /**
     * Вывод ИД валюты по умолчанию на сайте
     * @return int
     */
    function getDefaultValutaId() {
        return parent::getParam("dengi");
    }

    /**
     * Вывод курса валюты по умолчанию
     * @return float
     */
    function getDefaultOrderValutaId() {
        return parent::getParam("kurs");
    }

    /**
     * Вывод курса валюты по умолчанию
     * @param bool $order валюта в заказе (true)
     * @return float
     */
    function getDefaultValutaKurs($order = false) {
        if (!class_exists("phpshopvaluta"))
            parent::loadClass("phpshopvaluta");
        if ($order) {
            // Проверка валюты витрины
            if (defined("HostID"))
                $valuta_id = $_SESSION['valuta'];
            else
                $valuta_id = $this->getDefaultOrderValutaId();
        } else
            $valuta_id = $this->getDefaultValutaId();
        $PV = new PHPShopValuta($valuta_id);

        return $PV->getKurs();
    }

    /**
     * Вывод ISO валюты по умолчанию
     * @param bool $order валюта в заказе (true)
     * @return string
     */
    function getDefaultValutaIso($order = false) {
        if (!class_exists("phpshopvaluta"))
            parent::loadClass("valuta");
        if ($order) {

            // Проверка валюты витрины
            if (defined("HostID"))
                $valuta_id = $_SESSION['valuta'];
            else
                $valuta_id = $this->getDefaultOrderValutaId();
        } else
            $valuta_id = $this->getDefaultValutaId();
        $PV = new PHPShopValuta($valuta_id);

        return $PV->getIso();
    }

    /**
     * Вывод кода валюты по умолчанию
     * @param bool $order валюта в заказе только с курсом для заказа (true)
     * @return string
     */
    function getDefaultValutaCode($order = false) {
        if (!class_exists("phpshopvaluta"))
            parent::loadClass("valuta");

        if ($order) {
            // Проверка валюты витрины
            if (defined("HostID"))
                $valuta_id = $_SESSION['valuta'];
            else
                $valuta_id = $this->getDefaultOrderValutaId();
        }
        elseif (isset($_SESSION['valuta']))
            $valuta_id = $_SESSION['valuta'];
        else
            $valuta_id = $this->getDefaultValutaId();

        $PV = new PHPShopValuta($valuta_id);
        return $PV->getCode();
    }

    /**
     * Вывод иконки валюты рубля
     * @return string
     */
    function getValutaIcon($order = false) {

        if ($this->getDefaultValutaIso($order) == 'RUR' or $this->getDefaultValutaIso($order) == "RUB")
            return '<span class=rubznak>p</span>';
        else
            return $this->getDefaultValutaCode($order);
    }

    /**
     * Вывод логотипа сайта
     * @param bool $print логотип для бланков
     * @return string
     */
    function getLogo($print = false) {
        $logo = parent::getParam("logo");

        if (!empty($print)) {
            $bank_logo = $this->getSerilizeParam("bank.org_logo");
            if (!empty($bank_logo))
                $logo = $bank_logo;
        }
        return $logo;
    }

    /**
     * Вывод массива настроек системы из БД
     * @return array
     */
    function getArray() {
        $array = $this->objRow;
        foreach ($array as $key => $v)
            if (is_string($key))
                $newArray[$key] = $v;
        return $newArray;
    }

    /**
     * Вывод e-mail администратора
     * @return string
     */
    function getEmail() {
        return parent::getParam('adminmail2');
    }

    /**
     * Настройка почты SMTP
     * @param array $add дополнительнеы параметры
     * @return array
     */
    function getMailOption($add = false) {

        if ($this->ifSerilizeParam('admoption.mail_smtp_enabled', 1)) {

            if ($this->ifSerilizeParam('admoption.mail_smtp_debug', 1))
                $mail_debug = 2;
            else
                $mail_debug = 0;

            $option = array(
                'smtp' => true,
                'host' => $this->getSerilizeParam('admoption.mail_smtp_host'),
                'port' => $this->getSerilizeParam('admoption.mail_smtp_port'),
                'debug' => $mail_debug,
                'auth' => $this->getSerilizeParam('admoption.mail_smtp_auth'),
                'user' => $this->getSerilizeParam('admoption.mail_smtp_user'),
                'password' => $this->getSerilizeParam('admoption.mail_smtp_pass'),
                'replyto' => $this->getSerilizeParam('admoption.mail_smtp_replyto')
            );
        } else
            $option = null;

        // Дополнительнеы параметры
        if (is_array($add)) {
            foreach ($add as $k => $v)
                $option[$k] = $v;
        }


        return $option;
    }

    public function getPriceColumn() {
        $column = 'price'; // цена по умолчанию

        // Колонка цены по статусу пользователя
        if (!empty($_SESSION['UsersStatus'])) {
            if (empty($_SESSION['UsersStatusPice'])) {
                // Выборка из базы нужной колонки цены для автор. пользователя
                $PHPShopUser = new PHPShopUserStatus($_SESSION['UsersStatus']);
                $GetUsersStatusPrice = $PHPShopUser->getPrice();
                $_SESSION['UsersStatusPice'] = $GetUsersStatusPrice;
            }
            else
                $GetUsersStatusPrice = $_SESSION['UsersStatusPice'];

            if ($GetUsersStatusPrice > 1) {
                return "price" . $GetUsersStatusPrice;
            }
        }

        // Колонка цены витрины
        if (defined("HostPrice") && HostPrice > 1) {
            return $column . HostPrice;
        }

        if (defined("HostID") && HostID > 0) {
            $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['servers']);
            $showcase = $orm->getOne(array('*'), array('id' => sprintf('="%s"', HostID)));

            if (!empty($showcase['price']) && (int) $showcase['price'] > 1) {
                define("HostPrice", $showcase['price']);
                return $column . HostPrice;
            }
        }

        return $column;
    }

    public function isDisplayWarehouse()
    {
        // Отображение склада отключено
        if ((int) $this->getSerilizeParam('admoption.sklad_enabled') === 0) {
            return false;
        }

        // Показывать только авторизованным пользователям
        if ((int) $this->getSerilizeParam('admoption.user_items_activate') === 1 and empty($_SESSION['UsersId'])) {
            return false;
        }

        // Отображение склада скрыто в статусе пользователя
        if (
            !empty($_SESSION['UsersStatus']) &&
            (int) $_SESSION['UsersStatus'] > 0 &&
            !(new PHPShopUserStatus((int) $_SESSION['UsersStatus']))->isDisplayWarehouse()
        ) {
            return false;
        }

        return true;
    }
}

/**
 * Массив данных по юридическим лицам
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopArray
 */
class PHPShopCompanyArray extends PHPShopArray {

    function __construct() {
        $this->objBase = $GLOBALS['SysValue']['base']['company'];
        $this->objSQL = array('enabled' => "='1'");
        parent::__construct('id', "name", 'bank');
    }

}

?>