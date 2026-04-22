<?php

/**
 * Родительский класс создания элементов
 * Примеры использования размещены в папке phpshop/inc/
 * @author PHPShop Software
 * @version 1.2
 * @package PHPShopClass
 */
class PHPShopElements {

    /**
     * @var string имя БД
     */
    var $objBase;
    var $objPath;

    /**
     * @var bool режим отладки
     */
    var $debug = false;
    var $template_debug = false;

    /**
     * @var bool кэширование
     */
    var $cache = false;

    /**
     * @var bool форматирование полей кэша
     */
    var $cache_format = array();

    /**
     * @var bool использование памяти
     */
    var $memory = false;

    /**
     * @var array массив экшенов
     */
    var $action = array();

    /**
     * @var string префикс имен функций экшенов
     */
    var $action_prefix = null;

    /**
     * @var array массив запрещенных разделов
     */
    var $disp_format = array();

    /**
     * @var string результат работы парсера
     */
    var $Disp;

    /**
     * Конструктор
     */
    function __construct() {
        global $PHPShopSystem, $PHPShopNav, $PHPShopModules, $PHPShopCache;

        if ($this->objBase) {
            $this->PHPShopOrm = new PHPShopOrm($this->objBase);

            $this->PHPShopOrm->cache_format = $this->cache_format;
            $this->PHPShopOrm->cache = $this->cache;
            $this->PHPShopOrm->debug = $this->debug;
        }
        $this->SysValue = &$GLOBALS['SysValue'];
        $this->PHPShopSystem = &$PHPShopSystem;
        $this->PHPShopNav = &$PHPShopNav;
        $this->LoadItems = &$GLOBALS['LoadItems'];
        $this->PHPShopModules = &$PHPShopModules;
        $this->webp = $this->PHPShopSystem->getSerilizeParam('admoption.image_webp');

        // Кэш
        $this->PHPShopCache = $PHPShopCache;
    }

    function __call($name, $arguments) {
        if ($name == __CLASS__) {
            self::__construct();
        }
    }

    /**
     * Добавление в переменную вывода через парсер
     * @param string $template имя шаблона для парсинга
     */
    function addToTemplate($template) {
        $this->Disp .= ParseTemplateReturn($template);
    }

    /**
     * Добавление в переменную вывода
     * @param sting $content контент
     */
    function add($content) {
        $this->Disp .= $content;
    }

    /**
     * Парсинг шаблона
     * @param string $template имя шаблона
     * @param bool $mod использование шаблона в модуле
     * @return string
     */
    function parseTemplate($template, $mod = false) {
        return ParseTemplateReturn($template, $mod, $this->template_debug);
    }

    /**
     * Создание системной переменной для парсинга
     * @param string $name имя
     * @param mixed $value значение
     * @param bool $flag [1] - добавить, [0] - переписать
     */
    function set($name, $value, $flag = false) {
        if ($flag)
            @$this->SysValue['other'][$name] .= $value;
        else
            $this->SysValue['other'][$name] = $value;
    }

    /**
     * Выдача системной переменной
     * @param string $name
     * @return string
     */
    function get($name) {
        if (isset($this->SysValue['other'][$name]))
            return $this->SysValue['other'][$name];
    }

    /**
     * Выдача системной переменной
     * @param string $param раздел.имя переменной
     * @return mixed
     */
    function getValue($param) {
        $param = explode(".", $param);
        if (is_array($param) and isset($this->SysValue[$param[0]][$param[1]]))
            return $this->SysValue[$param[0]][$param[1]];
    }

    /**
     * Изменение системной переменной
     * @param string $param раздел.имя переменной
     * @param mixed $value значение параметра
     */
    function setValue($param, $value) {
        $param = explode(".", $param);
        if ($param[0] == "var")
            $param[0] = "other";
        $this->SysValue[$param[0]][$param[1]] = $value;
    }

    /**
     * Выдача переменной из кэша
     * @param string $param раздел.имя переменной
     * @return string
     */
    function getValueCache($param) {
        return $this->LoadItems[$param];
    }

    /**
     * Инициализация переменной по результату выполнения функции
     * @param string $method_name имя функции
     * @param bool $flag добавление данных в переменную
     */
    function init($method_name, $flag = false) {

        if (!in_array($this->SysValue['nav']['path'], $this->disp_format)) {

            // Кэш
            if ($this->PHPShopCache->mod == 2 and $this->PHPShopCache->valid_element($method_name)) {
                $cache_key = md5(str_replace("www.", "", getenv('SERVER_NAME')) . $method_name);
                $cache = $this->PHPShopCache->get($cache_key);
                if (!empty($cache)) {
                    $this->set($method_name, $cache);
                    return true;
                }
            }

            // Если переменная не определена модулем
            if (!empty($flag) and $this->isAction($method_name))
                $this->set($method_name, call_user_func(array(&$this, $method_name)), true);

            elseif (empty($this->SysValue['other'][$method_name])) {
                if ($this->isAction($method_name))
                    $this->set($method_name, call_user_func(array(&$this, $method_name)));
                elseif ($this->isAction("index"))
                    $this->set($method_name, call_user_func(array(&$this, 'index')));
                else
                    $this->setError("index", "method does not exist");
            }

            // Кэш
            if (($this->PHPShopCache->mod == 2) and $this->PHPShopCache->valid_element($method_name) and ! empty($this->get($method_name)))
                $this->PHPShopCache->set($cache_key, $this->get($method_name), false, $method_name);
        }
    }

    /**
     * Проверка экшена
     * @param string $method_name имя метода
     * @return bool
     */
    function isAction($method_name) {
        if (method_exists($this, $method_name))
            return true;
    }

    /**
     * Сообщение об ошибке
     * @param string $name имя функции
     * @param string $action сообщение
     */
    function setError($name, $action) {
        echo '<p><span style="color:red">Ошибка обработчика события: </span> <strong>' . __CLASS__ . '->' . $name . '()</strong>
	 <br><em>' . $action . '</em></p>';
    }

    /**
     * Вывод языкового параметра по ключу [config.ini]
     * @param string $str ключ языкового массива
     * @return string
     */
    function lang($str) {
        if ($this->SysValue['lang'][$str])
            return $this->SysValue['lang'][$str];
        else
            return $str;
    }

    /**
     * Запись в память
     * @param string $param имя параметра [catalog.param]
     * @param mixed $value значение
     */
    function memory_set($param, $value) {
        if (!empty($this->memory)) {
            $param = explode(".", $param);
            $_SESSION['Memory'][__CLASS__][$param[0]][$param[1]] = $value;
            $_SESSION['Memory'][__CLASS__]['time'] = time();
        }
    }

    /**
     * Выборка из памяти
     * @param string $param имя параметра [catalog.param]
     * @param bool $check сравнить с нулем
     * @return
     */
    function memory_get($param, $check = false) {
        $this->memory_clean();
        if (!empty($this->memory)) {
            $param = explode(".", $param);
            if (isset($_SESSION['Memory'][__CLASS__][$param[0]][$param[1]])) {
                if (!empty($check)) {
                    if (!empty($_SESSION['Memory'][__CLASS__][$param[0]][$param[1]]))
                        return true;
                } else
                    return $_SESSION['Memory'][__CLASS__][$param[0]][$param[1]];
            }
            elseif (!empty($check))
                return true;
        } else
            return false;
    }

    /**
     * Чистка памяти по времени
     * @param bool $clean_now принудительная чистка
     */
    function memory_clean($clean_now = false) {
        if (!empty($_SESSION['Memory'])) {
            if (!empty($clean_now))
                unset($_SESSION['Memory'][__CLASS__]);
            elseif (@$_SESSION['Memory'][__CLASS__]['time'] < (time() - 60 * 10))
                unset($_SESSION['Memory'][__CLASS__]);
        }
    }

    /**
     * Назначение экшена обработки переменных POST и GET
     */
    function setAction($action) {

        if (!empty($action))
            $this->action = $action;

        if (is_array($this->action)) {
            foreach ($this->action as $k => $v) {

                switch ($k) {

                    // Экшен POST
                    case("post"):

                        // Если несколько экшенов
                        if (is_array($v)) {
                            foreach ($v as $function)
                                if (!empty($_POST[$function]) and $this->isAction($function))
                                    return call_user_func(array(&$this, $this->action_prefix . $function));
                        } else {
                            // Если один экшен
                            if (!empty($_POST[$v]) and $this->isAction($v))
                                return call_user_func(array(&$this, $this->action_prefix . $v));
                        }
                        break;

                    // Экшен GET
                    case("get"):

                        // Если несколько экшенов
                        if (is_array($v)) {
                            foreach ($v as $function)
                                if (!empty($_GET[$function]) and $this->isAction($function))
                                    return call_user_func(array(&$this, $this->action_prefix . $function));
                        } else {
                            // Если один экшен
                            if (!empty($_GET[$v]) and $this->isAction($v))
                                return call_user_func(array(&$this, $this->action_prefix . $v));
                        }

                        break;

                    // Экшен NAME
                    case("name"):

                        // Если несколько экшенов
                        if (is_array($v)) {
                            foreach ($v as $function)
                                if ($this->PHPShopNav->getName() == $function and $this->isAction($function))
                                    return call_user_func(array(&$this, $this->action_prefix . $function));
                        } else {
                            // Если один экшен
                            if ($this->PHPShopNav->getName() == $v and $this->isAction($v))
                                return call_user_func(array(&$this, $this->action_prefix . $v));
                        }

                        break;


                    // Экшен NAV
                    case("nav"):

                        // Если несколько экшенов
                        if (is_array($v)) {
                            foreach ($v as $function) {
                                if ($this->PHPShopNav->getNav() == $function and $this->isAction($function)) {
                                    return call_user_func(array(&$this, $this->action_prefix . $function));
                                    $call_user_func = true;
                                }
                            }
                        } else {
                            // Если один экшен
                            if ($this->PHPShopNav->getNav() == $v and $this->isAction($v))
                                return call_user_func(array(&$this, $this->action_prefix . $v));
                        }
                        break;
                }
            }
        } else
            $this->setError("action", "экшены объявлена неверно");
    }

    /**
     * Назначение перехвата события выполнения модулем
     * @param string $class_name имя класса
     * @param string $function_name имя метода
     * @param mixed $data данные для обработки
     * @param string $rout позиция вызова к функции [END | START | MIDDLE], по умолчанию END
     * @return bool
     */
    function setHook($class_name, $function_name, $data = false, $rout = false) {
        if (!empty($this->PHPShopModules))
            return $this->PHPShopModules->setHookHandler($class_name, $function_name, array(&$this), $data, $rout);
    }

    /**
     * Назначение HTML переменных верстки
     * @param string $class_name имя класса
     */
    function setHtmlOption($class_name) {

        if (!empty($GLOBALS['SysValue']['html'][strtolower($class_name)])) {

            $html = $GLOBALS['SysValue']['html'][strtolower($class_name)];

            // Назначение сетки
            if (strstr($html, '-')) {
                $option = explode("-", $html);
                $html = $option[0];
                $this->cell = $option[1];
            }

            $this->cell_type = $html;
            //$this->cell=1;
            $this->product_grid = null;
        }
    }

    /**
     * Подключение функций из файлов ядра
     * @param string $class_name имя класса
     * @param string $function_name имя функции
     * @param array $function_row массив дополнительны данных из функции
     * @param string $path имя раздела
     * @return mixed
     */
    function doLoadFunction($class_name, $function_name, $function_row = false, $path = false) {

        if (empty($path))
            $path = $GLOBALS['SysValue']['nav']['path'];

        $function_path = './phpshop/core/' . $path . '.core/' . $function_name . '.php';
        if (is_file($function_path)) {
            include_once($function_path);
            if (function_exists($function_name)) {
                return call_user_func_array($function_name, array(&$this, $function_row));
            }
        }
    }

    /**
     * Поддержка webp
     * @param string $image имя файла
     * @return string
     */
    function setImage($image) {
        global $_classPath;

        if (!empty($image)) {

            // Преобразование webp -> jpg для iOS < 14
            if (PHPShopSecurity::getExt($image) == 'webp') {
                if (defined('isMobil') and defined('isIOS')) {

                    if (!class_exists('PHPThumb'))
                        include_once($_classPath . 'lib/thumb/phpthumb.php');

                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $image)) {
                        $thumb = new PHPThumb($_SERVER['DOCUMENT_ROOT'] . $image);
                        $thumb->setFormat('STRING');
                        $image = 'data:image/jpg;base64, ' . base64_encode($thumb->getImageAsString('webp'));
                    }
                }
            }
            // Преобразование в webp
            elseif ($this->webp) {

                if (!class_exists('PHPThumb'))
                    include_once($_classPath . 'lib/thumb/phpthumb.php');

                if (file_exists($_SERVER['DOCUMENT_ROOT'] . $image)) {
                    $thumb = new PHPThumb($_SERVER['DOCUMENT_ROOT'] . $image);
                    $thumb->setFormat('WEBP');
                    $image = 'data:image/webp;base64, ' . base64_encode($thumb->getImageAsString(PHPShopSecurity::getExt($image)));
                }
            }
        }

        return $image;
    }

}

?>