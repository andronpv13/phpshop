<?php

/**
 * Библиотека подключения к БД
 * @author PHPShop Software
 * @version 2.2
 * @package PHPShopClass
 * @param string $iniPath путь до конфигурационного файла config.ini
 * @param bool $connectdb подключение к MySQL
 * @param bool $error блокировка ошибок
 */
class PHPShopBase {

    /**
     * путь до конфигурационного файла config.ini
     * @var string 
     */
    var $iniPath;

    /**
     * массив данных настроек конфигурационного файла config.ini
     * @var array 
     */
    var $SysValue;

    /**
     * Кодировка MySQL (русская cp1251)
     * @var string
     */
    var $codBase = "cp1251";

    /**
     * Настройки локали сервера (русская cp1251)
     * @var string 
     */
    var $locale = 'ru_RU.cp1251';

    /**
     * режим отладки
     * @var bool 
     */
    var $debug = true;

    /**
     * текст ошибки подключения
     * @var string
     */
    var $mysql_error = null;

    /**
     * Подключения к БД
     * @param string $iniPath путь до конфигурационного файла config.ini
     * @param bool $connectdb подключение к БД
     * @param bool $error блокировка ошибок PHP
     */
    function __construct($iniPath, $connectdb = true, $error = true) {

        //$error = false;

        // Отладка ядра
        $this->setPHPCoreReporting($error);

        $this->iniPath = $iniPath;
        $this->SysValue = parse_ini_file_true($this->iniPath, 1);

        // Кодировка
        if ($this->getParam("connect.charset") != "") {
            $this->codBase = $this->getParam("connect.charset");
            if ($this->codBase == 'utf8')
                $this->codBase = 'utf-8';
        }

        // UTF-8 Fix
        $this->fixUTF();

        define('parser_function_allowed', $this->SysValue['function']['allowed']);
        define('parser_function_deny', $this->SysValue['function']['deny']);
        define('parser_function_guard', $this->SysValue['function']['guard']);

        // Переключение БД
        $this->selectBase();

        $GLOBALS['SysValue'] = &$this->SysValue;
        $GLOBALS['dir']['dir'] = $GLOBALS['SysValue']['dir']['dir'];
        if ($connectdb)
            $this->link_db = $this->connect($connectdb);
    }

    /**
     * Переключение БД
     */
    function selectBase() {

        if (!empty($_GET['base'])) {
            if (is_array($this->SysValue['connect_' . $_GET['base']])) {
                $_SESSION['base'] = $_GET['base'];
                unset($_SESSION['cart']);
            } elseif ($_GET['base'] == 'default')
                unset($_SESSION['base']);
        }

        if (!empty($_SESSION['base'])) {
            $this->SysValue['connect']['dbase'] = $this->SysValue['connect_' . $_SESSION['base']]['dbase'];
            $this->SysValue['connect']['user_db'] = $this->SysValue['connect_' . $_SESSION['base']]['user_db'];
            $this->SysValue['connect']['dbase'] = $this->SysValue['connect_' . $_SESSION['base']]['dbase'];
            $this->SysValue['connect']['pass_db'] = $this->SysValue['connect_' . $_SESSION['base']]['pass_db'];
        }
    }

    /**
     * Выдача системных параметров конфига
     * @return array
     */
    function getSysValue() {
        return $this->SysValue;
    }

    /**
     * Выдача системных параметров конфига
     * <code>
     * // example
     * $PHPShopBase= new PHPShopBase('./inc/config.ini');
     * $PHPShopBase->getParam('base.table_name');
     * </code>
     * @param mixed $param имя параметра
     * @return string
     */
    function getParam($param) {
        $param = explode(".", $param);
        if (count($param) > 2)
            return $this->SysValue[$param[0]][$param[1]][$param[2]];
        elseif (!empty($this->SysValue[$param[0]][$param[1]]))
            return $this->SysValue[$param[0]][$param[1]];
    }

    /**
     * Добавить параметр
     * <code>
     * // example
     * $PHPShopBase= new PHPShopBase('./inc/config.ini');
     * $PHPShopBase->setParam('base.table_name','mybase');
     * </code>
     * @param string $param имя параметра
     * @param mixed $value значение параметра
     */
    function setParam($param, $value) {
        $param = explode(".", $param);
        if ($param[0] == "var")
            $param[0] = "other";
        $GLOBALS['SysValue'][$param[0]][$param[1]] = $value;
    }

    /**
     * Вывод сообщения об ошибке
     * @param int $e номер внутренней ошибки
     * @param string $message текст сообщения
     * @param string $error текст ошибки
     */
    static function errorConnect($e = false, $message = "Нет соединения с базой", $error = false) {

        $message = '<strong>' . $message . '</strong><br><em>Ошибка: ' . $error . '</em>';

        if (is_dir($_SERVER['DOCUMENT_ROOT'] . '/install/') and $e != 105) {
            header('Location: /install/');
        }
        if (function_exists('ParseTemplateReturn')) {
            $GLOBALS['SysValue']['other']['message'] = $message;
            $GLOBALS['SysValue']['other']['title'] = $e;
            $error = ParseTemplateReturn('phpshop/lib/templates/error/error.tpl', true);

            if ($GLOBALS['PHPShopBase']->codBase == 'utf-8')
                $error = iconv("windows-1251", "utf-8", $error);

            exit($error);
        } elseif (class_exists('PHPShopObj')) {
            PHPShopObj::loadClass('parser');
            PHPShopObj::loadClass('lang');
            PHPShopParser::set('message', $message);
            PHPShopParser::set('title', $e);
            $error = PHPShopParser::file($_SERVER['DOCUMENT_ROOT'] . '/phpshop/lib/templates/error/error.tpl');

            if ($GLOBALS['PHPShopBase']->codBase == 'utf-8')
                $error = iconv("windows-1251", "utf-8", $error);

            exit($error);
        } else
            exit($message);
    }

    /**
     * Соединение с БД MySQL
     * @param bool $connectdb подключение / проверка подключения
     */
    function connect($connectdb = true) {
        global $link_db;

        $port = $this->getParam("connect.port");
        if (empty($port))
            $port = 3306;

        try {
            $link_db = mysqli_connect($this->getParam("connect.host"), $this->getParam("connect.user_db"), $this->getParam("connect.pass_db"), null, $port);

            try {
                mysqli_select_db($link_db, $this->getParam("connect.dbase")) or $this->mysql_error .= mysqli_error($link_db);

                if ($this->codBase != "utf-8")
                    mysqli_query($link_db, "SET NAMES '" . $this->codBase . "'");

                mysqli_query($link_db, "SET SESSION sql_mode=''");
                mysqli_report(MYSQLI_REPORT_OFF);
            } catch (Exception $ex) {
                $this->mysql_error .= mysqli_error($link_db);
            }
        } catch (Exception $e) {
            $this->mysql_error = mysqli_connect_error();
        }

        if ($connectdb and ! empty($this->mysql_error))
            $this->errorConnect(101, "Нет соединения с базой", $this->mysql_error);
        else if (empty($this->mysql_error))
            return $link_db;
    }

    /**
     * Проверка прав администратора
     */
    function chekAdmin() {

        // Portable PHP password hashing framework.
        require_once dirname(__FILE__) . '/../lib/phpass/passwordhash.php';

        PHPShopObj::loadClass('admrule');
        $this->Rule = new PHPShopAdminRule();
    }

    /**
     * Выдача кол-ва строк в таблице
     * @param string $from_base имя таблицы
     * @param string $query SQL запрос
     * @return int
     */
    function getNumRows($from_base, $query) {
        $sql = "select COUNT('id') as count from " . $this->SysValue['base'][$from_base] . " " . $query;
        $result = mysqli_query($this->link_db, $sql);
        $row = @mysqli_fetch_array($result);
        $num = $row['count'];
        return intval($num);
    }

    /**
     * Настройка локали сервера 
     */
    function setLocale() {
        if (function_exists('setlocale') and ! empty($this->locale))
            setlocale(LC_ALL, $this->locale);
    }

    /**
     * UTF-8 Fix
     */
    function fixUTF() {

        //  UTF-8 Default Charset Fix
        if (stristr(ini_get("default_charset"), "utf") and function_exists('ini_set') and $this->codBase != "utf-8") {
            ini_set("default_charset", $this->codBase);
        }
    }

    /**
     *  Настройка уровня оповещения отладчика
     */
    function setPHPCoreReporting($error) {
        if (function_exists('error_reporting')) {
            if (empty($error)) {
                error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
                if (function_exists('ini_set')) {
                    ini_set('allow_call_time_pass_reference', 1);
                }
            } else
                error_reporting(0);

            // Short Open Tag 
            if (ini_get("short_open_tag") == 0) {
                ini_set('short_open_tag', 1);
            }
        }
    }

    /**
     * Проверка мультибазы для внешних файлов
     * @param string $path путь до папки с лицензией
     */
    function checkMultibase($path = '../') {
        global $PHPShopSystem;

        $this->LicenseParse = @parse_ini_file_true($path . 'license/' . PHPShopFile::searchFile($path . 'license/', 'getLicense', true), 1);

        if (is_array($this->LicenseParse) and strstr($this->LicenseParse['License']['HardwareLocked'], 'Showcase')) {

            if (getenv('SERVER_NAME') == $this->LicenseParse['License']['DomenLocked'] or getenv('SERVER_NAME') == 'www.' . $this->LicenseParse['License']['DomenLocked']) {
                define("HostMain", true);
            } else {
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['servers']);
                $PHPShopOrm->debug = false;
                $data = $PHPShopOrm->select(array('id', 'name', 'company', 'logo', 'price'), array('enabled' => "='1'", 'host' => '="' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . '"'), false, array('limit' => 1));

                if (is_array($data)) {
                    define("HostID", intval($data['id']));

                    if (!empty($data['price']))
                        define("HostPrice", $data['price']);

                    if ($PHPShopSystem) {

                        if (!empty($data['company']))
                            $PHPShopSystem->setParam('company', $data['company']);

                        if (!empty($data['name']))
                            $PHPShopSystem->setParam('name', $data['name']);

                        if (!empty($data['logo']))
                            $PHPShopSystem->setParam('logo', $data['logo']);
                    }
                }
            }
        }
    }

    /**
     * Авторизация ссылок печатных документов
     */
    public function checkFile($s, $id) {

        $host = $GLOBALS['SysValue']['connect']['host'];
        $dbname = $GLOBALS['SysValue']['connect']['dbase'];
        $uname = $GLOBALS['SysValue']['connect']['user_db'];
        $upass = $GLOBALS['SysValue']['connect']['pass_db'];

        if ($s == md5($host . $dbname . $uname . $upass . $id))
            return true;
    }

}

/**
 * Безопасная обработка INI файлов
 * @param string $file INI файл
 * @param bool $process_sections [true/false] режим многомерного массива
 * @return array
 */
function parse_ini_file_true($file, $process_sections) {
    if (function_exists('parse_ini_file'))
        return @parse_ini_file($file, $process_sections);
    elseif (is_file($file))
        return parse_ini_string(@file_get_contents($file), $process_sections);
}

?>