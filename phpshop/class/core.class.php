<?php

/**
 * Родительский класс ядра
 * Примеры использования размещены в папке phpshop/core/
 * @author PHPShop Software
 * @version 2.0
 * @package PHPShopClass
 */
class PHPShopCore {

    /**
     * имя БД
     * @var string 
     */
    var $objBase;

    /**
     * Путь для навигации
     * @var string
     */
    var $objPath;

    /**
     * режим отладки
     * @var bool 
     */
    var $debug = false;
    var $template_debug = true;

    /**
     * вывод SQL ошибок
     * @var bool 
     */
    var $mysql_error = false;

    /**
     * результат работы парсера
     * @var string 
     */
    var $Disp, $ListInfoItems;

    /**
     * массив обработки POST, GET запросов
     * @var array 
     */
    var $action = array("nav" => "index");

    /**
     * префикс экшен функций (action_|a_), используется при большом количестве методов в классах
     * @var string 
     * 
     */
    var $action_prefix = null;

    /**
     * метатеги
     * @var string
     */
    var $title, $description, $keywords, $lastmodified;

    /**
     * ссылка в навигации от корня
     * @var string 
     */
    var $navigation_link, $navigation_array = null;

    /**
     * шаблон вывода
     * @var string 
     */
    var $template = 'templates.shop';

    /**
     * таблица массива навигации
     * @var string  
     */
    var $navigationBase = 'base.categories';
    var $arrayPath;

    /**
     * длина пагинации для форматирования длины строки
     * @var int 
     */
    var $nav_len = 3;
    var $cache = false;

    /**
     * очистка временных переменных шаблона 
     * @var bool 
     */
    var $garbage_enabled = false;

    /**
     * отключение защиты проверки пустого экшена
     * @var bool
     */
    var $empty_index_action = true;
    var $memory = true;
    var $max_item = 300;

    /**
     * Конструктор
     */
    function __construct() {
        global $PHPShopSystem, $PHPShopNav, $PHPShopModules, $PHPShopCache;

        if ($this->objBase) {
            $this->PHPShopOrm = new PHPShopOrm($this->objBase);
            $this->PHPShopOrm->debug = $this->debug;
            $this->PHPShopOrm->cache = $this->cache;
        }
        $this->SysValue = &$GLOBALS['SysValue'];

        $this->PHPShopSystem = $PHPShopSystem;
        $this->num_row = $this->PHPShopSystem->getParam('num_row');
        $this->PHPShopNav = $PHPShopNav;
        $this->PHPShopModules = &$PHPShopModules;
        $this->page = $this->PHPShopNav->getId();

        if (strlen($this->page) == 0)
            $this->page = 1;

        // Кэш
        $this->PHPShopCache = $PHPShopCache;

        // Определяем переменные
        $this->set('pageProduct', $this->SysValue['license']['product_name']);
    }

    function __call($name, $arguments) {
        if ($name == __CLASS__) {
            self::__construct();
        }
    }

    /**
     * Сравнение параметра из массива
     * @param string $paramName имя переменной
     * @param string $paramValue значение переменной
     * @return bool
     */
    function ifValue($paramName, $paramValue = false) {
        if (empty($paramValue))
            $paramValue = 1;
        if ($this->objRow[$paramName] == $paramValue)
            return true;
    }

    /**
     * Расчет навигации хлебных крошек
     * @param int $id ИД позиции
     * @return array
     */
    function getNavigationPath($id) {
        global $PHPShopShopCatalogElement;
        
        $PHPShopOrm = new PHPShopOrm($this->getValue($this->navigationBase));
        $PHPShopOrm->debug = $this->debug;
        $PHPShopOrm->cache = $this->cache;

        if (!empty($id)) {
            $PHPShopOrm->comment = "Навигация";
            $v = $PHPShopOrm->select(array('name,id,parent_to'), array('id' => '=' . $id), false, array('limit' => 1));
            if (is_array($v)) {
                $this->navigation_array[] = array('id' => $v['id'], 'name' => $v['name'], 'parent_to' => $v['parent_to']);
                if (!empty($v['parent_to'])) {

                    // Перевод подкаталогов в родительские для витрин если один родитель
                    if (is_array($PHPShopShopCatalogElement->tree_array_host)) {
                        foreach ($PHPShopShopCatalogElement->tree_array_host[0]['sub'] as $k => $cat) {
                            if ($k == $v['id']) {
                                 $v['parent_to']=0;
                            }
                        }
                    }

                    $this->getNavigationPath($v['parent_to']);
                }
            }
        }
    }

    /**
     * Навигация хлебных крошек
     * @param int $id текущий ИД родителя
     * @param string $name имя раздела
     * @param array $title массив родителя [url,name]
     */
    function navigation($id, $name, $title = false) {
        global $SysValue;

        $dis = null;
        // Шаблоны разделителя навигации
        $elementTemplate = $SysValue['dir']['templates'] . chr(47) . $_SESSION['skin'] . chr(47) . $this->getValue('templates.breadcrumbs_splitter');
        $lastElemTemplate = $SysValue['dir']['templates'] . chr(47) . $_SESSION['skin'] . chr(47) . $this->getValue('templates.breadcrumbs_splitter_last');

        if ((bool) PHPShopParser::check($this->getValue('templates.breadcrumbs_splitter'), 'breadcrumbElemTitle') === false) {
            $elementTemplate = './phpshop/lib/templates/breadcrumbs/breadcrumbs_splitter.tpl';
        }
        $lastTemplatePath = $this->getValue('templates.breadcrumbs_splitter_last');
        if (empty($lastTemplatePath) || PHPShopParser::checkFile($lastElemTemplate, true) === false) {
            $lastElemTemplate = './phpshop/lib/templates/breadcrumbs/breadcrumbs_last.tpl';
        }
        $home = ParseTemplateReturn($this->getValue('templates.breadcrumbs_home'), false, $this->template_debug);

        if (empty($home))
            $home = PHPShopText::a('/', __('Главная'));

        // Реверсивное построение массива категорий
        $this->getNavigationPath($id);

        if (is_array($this->navigation_array))
            $arrayPath = array_reverse($this->navigation_array);

        $currentIndex = 2;
        $i = 0;
        if (!empty($arrayPath) and is_array($arrayPath)) {
            foreach ($arrayPath as $v) {
                // назначаем thisCat, чтобы в метках сохранить ИД дерева октрытых категорий в разделе shop.
                if ($this->PHPShopNav->getPath() == "shop")
                    $this->set('thisCat' . $i++, $v['id']);

                if ($name != $v['name']) {
                    $this->set('breadcrumbElemLink', '/' . $this->PHPShopNav->getPath() . '/CID_' . $v['id'] . '.html');
                    $this->set('breadcrumbElemTitle', $v['name']);
                    $this->set('breadcrumbElemIndex', $currentIndex++);
                    $dis .= ParseTemplateReturn($elementTemplate, true, $this->template_debug);
                }
            }
        }

        // назначаем thisCat, чтобы в метках сохранить ИД дерева октрытых категорий в разделе shop.
        if (!empty($this->PHPShopNav) and $this->PHPShopNav->getPath() == "shop")
            $this->set('thisCat' . $i++, $this->PHPShopNav->getId());

        // Указан массив родителя
        if (empty($dis) and is_array($title)) {
            $this->set('breadcrumbElemLink', $title['url']);
            $this->set('breadcrumbElemTitle', $title['name']);
            $this->set('breadcrumbElemIndex', $currentIndex++);

            $home .= ParseTemplateReturn($elementTemplate, true, $this->template_debug);
        }

        $this->set('breadcrumbElemTitle', $name);

        if (empty($name))
            $lastElemTemplate = null;

        $dis = $home . $dis . ParseTemplateReturn($lastElemTemplate, true, $this->template_debug);

        $this->set('breadCrumbs', $dis);

        // Навигация для javascript в shop.tpl
        $this->set('pageNameId', $id);
    }

    /**
     * Генерация даты изменения документа
     */
    function header() {
        if ($this->getValue("cache.last_modified") == "true" and empty($_SESSION['cart'])) {

            // Некоторые сервера требуют обзательных заголовков 200
            //header("HTTP/1.1 200");
            //header("Status: 200");
            @header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0");
            @header("Pragma: no-cache");

            if (!empty($this->lastmodified)) {
                $updateDate = @gmdate("D, d M Y H:i:s", $this->lastmodified);
            } else {
                $updateDate = gmdate("D, d M Y H:i:s", (date("U") - 21600));
            }

            @header("Last-Modified: " . $updateDate . " GMT");
        }

        // HSTS
        if (empty($this->PHPShopSystem) or ! $this->PHPShopSystem instanceof PHPShopSystem) {
            $this->PHPShopSystem = new PHPShopSystem();
        }
        if ($this->PHPShopSystem->ifSerilizeParam('admoption.hsts', 1))
            @header("Strict-Transport-Security:max-age=63072000");

        @header("X-Powered-By: PHPShop");
    }

    /**
     * Генерация заголовков документа
     */
    function meta() {

        if (!empty($this->PHPShopSystem)) {

            if (!empty($this->title))
                $this->set('pageTitl', strip_tags($this->title));
            else
                $this->set('pageTitl', $this->PHPShopSystem->getValue("title"));

            if (!empty($this->description))
                $this->set('pageDesc', str_replace('"', "", strip_tags($this->description)));
            else
                $this->set('pageDesc', $this->PHPShopSystem->getValue("descrip"));

            if (!empty($this->keywords))
                $this->set('pageKeyw', str_replace('"', "", strip_tags($this->keywords)));
            else
                $this->set('pageKeyw', $this->PHPShopSystem->getValue("keywords"));
        }


        // Навигация хлебные крошки если не заполнено
        if ($this->get('breadCrumbs') == '') {
            if (strstr($this->title, '-'))
                $title = explode("-", $this->title);
            else
                $title[0] = $this->title;
            $this->navigation(false, $title[0]);
        }
    }

    /**
     * Загрузка экшенов
     */
    function loadAction() {
        $this->setAction();
        $this->Compile();
    }

    /**
     * Выдача списка данных
     * @param array $select имена колонок БД для выборки
     * @param array $where параметры условий запроса
     * @param array $order параметры сортировки данных при выдаче
     * @return array
     */
    function getListInfoItem($select = false, $where = false, $order = false, $class_name = false, $function_name = false, $sql = false) {
        $this->ListInfoItems = null;
        $this->where = $where;

        // Обработка номера страницы
        if (!PHPShopSecurity::true_num($this->page))
            return $this->setError404();

        if (empty($this->page)) {
            $num_ot = 0;
            $num_do = $this->num_row;
        } else {
            $num_ot = $this->num_row * ($this->page - 1);
            $num_do = $this->num_row;
        }

        $option = array('limit' => intval($num_ot) . ',' . intval($num_do));

        $this->set('productFound', $this->getValue('lang.found_of_products'));
        $this->set('productNumOnPage', $this->getValue('lang.row_on_page'));
        $this->set('productPage', $this->getValue('lang.page_now'));

        $this->PHPShopOrm->comment = __CLASS__ . '.' . __FUNCTION__;

        if (!empty($sql)) {
            $this->PHPShopOrm->sql = 'select *, (price_n - price) as discount from ' . $this->objBase . ' where ' . $sql . ' limit ' . $option['limit'];
        }

        return $this->PHPShopOrm->select($select, $where, $order, $option, $class_name, $function_name);
    }

    function setPaginator($count = null, $sql = null) {

        // проверяем наличие шаблонов пагинации в папке шаблона
        // если отсутствуют, то используем шаблоны из lib
        $type = $this->memory_get(__CLASS__ . '.' . __FUNCTION__);
        if (!$type) {
            if (!PHPShopParser::checkFile("paginator/paginator_one_link.tpl")) {
                $type = "lib";
            } else {
                $type = "templates";
            }

            $this->memory_set(__CLASS__ . '.' . __FUNCTION__, $type);
        }

        if ($type == "lib") {
            $template_location = "./phpshop/lib/templates/";
            $template_location_bool = true;
        }

        // Кол-во данных
        $this->count = $count;
        $SQL = null;

        // Выборка по параметрам WHERE
        $nWhere = 1;
        if (is_array($this->where)) {
            $SQL .= ' where ';
            foreach ($this->where as $pole => $value) {
                $SQL .= $pole . $value;
                if ($nWhere < count($this->where))
                    $SQL .= $this->PHPShopOrm->Option['where'];
                $nWhere++;
            }
        }

        // Всего страниц
        $this->PHPShopOrm->comment = __CLASS__ . '.' . __FUNCTION__;
        $result = $this->PHPShopOrm->query("select COUNT('id') as count from " . $this->objBase . $SQL);
        $row = mysqli_fetch_array($result);
        $this->num_page = $row['count'];

        $i = 1;

        // Кол-во страниц в навигации
        $num = ceil($this->num_page / $this->num_row);
        $this->max_page = $num;

        // 404 ошибка при ошибочной пагинации
        if ($this->page > $this->num_page) {
            return $this->setError404();
        }

        if ($num > 1) {
            if ($this->page >= $num) {
                $p_to = $i - 1;
                $p_do = $this->page - 1;
            } else {
                $p_to = $this->page + 1;
                $p_do = 1;
            }

            $this->set("paginPageCount", $num);

            while ($i <= $num) {
                if ($i > 1) {
                    $p_start = $this->num_row * ($i - 1) + 1;
                    $p_end = $p_start + $this->num_row;
                } else {
                    $p_start = $i;
                    $p_end = $this->num_row;
                }

                $this->set("paginPageRangeStart", $p_start);
                $this->set("paginPageRangeEnd", $p_end);
                $this->set("paginPageNumber", $i);

                if ($i != $this->page) {
                    if ($i == 1) {
                        $this->set("paginLink", substr($this->objPath, 0, strlen($this->objPath) - 1) . '.html' . $sort);
                        $navigat .= parseTemplateReturn($template_location . "paginator/paginator_one_link.tpl", $template_location_bool);
                    } else {
                        if ($i > ($this->page - $this->nav_len) and $i < ($this->page + $this->nav_len)) {
                            $this->set("paginLink", $this->objPath . $i . '.html' . $sort);
                            $navigat .= parseTemplateReturn($template_location . "paginator/paginator_one_link.tpl", $template_location_bool);
                        } else if ($i - ($this->page + $this->nav_len) < 3 and ( ($this->page - $this->nav_len) - $i) < 3) {
                            $navigat .= parseTemplateReturn($template_location . "paginator/paginator_one_more.tpl", $template_location_bool);
                        }
                    }
                } else
                    $navigat .= parseTemplateReturn($template_location . "paginator/paginator_one_selected.tpl", $template_location_bool);

                $i++;
            }

            $this->set("pageNow", $this->getValue('lang.page_now'));
            $this->set("navBack", $this->lang('nav_back'));
            $this->set("navNext", $this->lang('nav_forw'));
            $this->set("navigation", $navigat);

            // Убираем дубль первой страницы CID_X_1.html
            if ($p_do == 1)
                $this->set("previousLink", substr($this->objPath, 0, strlen($this->objPath) - 1) . '.html' . $sort);
            else
                $this->set("previousLink", $this->objPath . ($p_do) . '.html' . $sort);


            // Убираем дубль первой страницы CID_X_0.html
            if ($p_to == 0)
                $this->set("nextLink", substr($this->objPath, 0, strlen($this->objPath) - 1) . '.html' . $sort);
            else
                $this->set("nextLink", $this->objPath . ($p_to) . '.html' . $sort);

            // Назначаем переменную шаблонизатора
            $nav = parseTemplateReturn($template_location . "paginator/paginator_main.tpl", $template_location_bool);
            $this->set('productPageNav', $nav);

            // Перехват модуля в конце функции
            $this->setHook(__CLASS__, __FUNCTION__, $nav, 'END');
        }
    }

    /**
     * Выдача подробного описания
     * @param array $select имена колонок БД для выборки
     * @param array $where параметры условий запроса
     * @param array $order параметры сортировки данных при выдаче
     * @return array
     */
    function getFullInfoItem($select, $where, $class_name = false, $function_name = false) {
        $result = $this->PHPShopOrm->select($select, $where, false, array('limit' => '1'), $class_name, $function_name);
        return $result;
    }

    /**
     * Добавление данных в вывод парсера
     * @param string $template шаблон для парсинга
     * @param bool $mod работа в модуле
     * @param array масив замены в шаблоне
     */
    function addToTemplate($template, $mod = false, $replace = null) {
        if ($mod)
            $template_file = $template;
        else
            $template_file = $this->getValue('dir.templates') . chr(47) . $_SESSION['skin'] . chr(47) . $template;
        if (is_file($template_file)) {
            $dis = ParseTemplateReturn($template, $mod, $this->template_debug);

            // Замена в шаблоне
            if (is_array($replace)) {
                foreach ($replace as $key => $val)
                    $dis = str_replace($key, $val, $dis);
            }

            $this->ListInfoItems .= $dis;

            $this->set('pageContent', $this->ListInfoItems);
        } else
            $this->setError("addToTemplate", $template_file);
    }

    /**
     * Добавление данных
     * @param string $content содержание
     * @param bool $list [1] - добавление в список данных, [0] - добавление в общую переменную вывода
     */
    function add($content, $list = false) {
        if ($list)
            $this->ListInfoItems .= $content;
        else
            $this->Disp .= $content;
    }

    /**
     * Парсинг шаблона и добавление в общую переменную вывода
     * @param string $template имя шаблона
     * @param bool $mod работа в модуле
     * @param array $replace масив замены в шаблоне
     */
    function parseTemplate($template, $mod = false, $replace = null) {
        $this->set('productPageDis', $this->ListInfoItems);
        $dis = ParseTemplateReturn($template, $mod, $this->template_debug);

        // Замена в шаблоне
        if (is_array($replace)) {
            foreach ($replace as $key => $val)
                $dis = str_replace($key, $val, $dis);
        }

        $this->Disp = $dis;
    }

    /**
     * Сообщение об ошибке
     * @param string $name имя функции
     * @param string $action сообщение
     */
    function setError($name, $action) {
        echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
            <div class="alert alert-danger alert-dismissible" id="debug-message" role="alert" style="margin:10px">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong><span class="glyphicon glyphicon-alert"></span> ' . $name . '()</strong> ' . $action . '
</div>';
    }

    /**
     * Компиляция парсинга
     */
    function Compile() {

        // Переменная вывода
        $this->set('DispShop', $this->Disp, false);

        $hook = $this->setHook(__CLASS__, __FUNCTION__, false, 'START');
        if ($hook)
            return $hook;

        // Мета
        $this->meta();

        // Дата модификации
        $this->header();

        // Запись файла локализации
        writeLangFile();

        /**
         * Перехват модуля 
         * Если больше не получилось никуда внедриться, то можно перехватить буфер и поменять str_replace. 
         * Буфер $obj->Disp или $obj->get('DispShop');
         */
        $hook = $this->setHook(__CLASS__, __FUNCTION__, false, 'END');
        if ($hook) {
            return $hook;
        } else {
            // Вывод в шаблон
            ParseTemplate($this->getValue($this->template));
        }

        // Очистка временных переменных шаблонов [off]
        $this->garbage();
    }

    /**
     * Создание переменной шаблонизатора для парсинга
     * @param mixid $name имя [массив]
     * @param mixed $value значение
     * @param bool $flag [1] - добавить, [0] - переписать
     */
    function set($var, $value, $flag = false) {

        if (!is_array($var))
            $name_array[] = $var;
        else
            $name_array = $var;

        foreach ($name_array as $name) {

            if ($flag and ! empty($this->SysValue['other'][$name]))
                $this->SysValue['other'][$name] .= $value;
            else
                $this->SysValue['other'][$name] = $value;
        }
    }

    /**
     * Выдача переменной шаблонизатора
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

        if (count($param) > 2 and ! empty($this->SysValue[$param[0]][$param[1]][$param[2]]))
            return $this->SysValue[$param[0]][$param[1]][$param[2]];

        if (!empty($this->SysValue[$param[0]][$param[1]]))
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
     * Назначение экшена обработки переменных POST и GET
     */
    function setAction() {

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
                            if (empty($call_user_func)) {
                                if ($this->isAction('index')) {

                                    // Защита от битых адресов /page/page/page/****
                                    if ($this->PHPShopNav->getNav() and ! $this->empty_index_action)
                                        $this->setError404();
                                    else
                                        call_user_func(array(&$this, $this->action_prefix . 'index'));
                                } else
                                    $this->setError($this->action_prefix . "index", "метод не существует");
                            }
                        } else {
                            // Если один экшен
                            if (@$this->PHPShopNav and @ $this->PHPShopNav->getNav() == $v and $this->isAction($v))
                                return call_user_func(array(&$this, $this->action_prefix . $v));
                            elseif ($this->isAction('index')) {

                                // Защита от битых адресов /page/page/page/****
                                if (@$this->PHPShopNav->getNav() and ! $this->empty_index_action)
                                    $this->setError404();
                                else
                                    call_user_func(array(&$this, $this->action_prefix . 'index'));
                            }
                            elseif (!empty($this->PHPShopNav))
                                $this->setError($this->action_prefix . "phpshop" . $this->PHPShopNav->getPath() . "->index", "метод не существует");
                        }

                        break;
                }
            }
        } else
            $this->setError("action", "экшены объявлена неверно");
    }

    /**
     * Проверка экшена
     * @param string $method_name имя метода
     * @return bool
     */
    function isAction($method_name) {
        if (method_exists($this, $this->action_prefix . $method_name))
            return true;
    }

    /**
     * Ожидание экшена
     * @param string $method_name  имя метода
     */
    function waitAction($method_name) {
        if (!empty($_REQUEST[$method_name]) and $this->isAction($method_name))
            call_user_func(array(&$this, $this->action_prefix . $method_name));
    }

    /**
     * Генерация ошибки 404
     */
    function setError404() {

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__))
            return true;

        // Титл
        $this->title = __("Ошибка") . " 404  - " . $this->PHPShopSystem->getValue("name");

        // Заголовок ошибки
        @header("HTTP/1.0 404 Not Found");
        @header("Status: 404 Not Found");

        // Подключаем шаблон
        $this->parseTemplate($this->getValue('templates.error_page_forma'));
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
            return true;
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
        else
            return false;
    }

    /**
     * Назначение HTML переменных верстки
     * @param string $class_name имя класса
     */
    function setHtmlOption($class_name) {
        if (!empty($GLOBALS['SysValue']['html'][strtolower($class_name)]))
            $this->cell_type = $GLOBALS['SysValue']['html'][strtolower($class_name)];
    }

    /**
     * Сообщение
     * @param string $title заголовок
     * @param string $content содержание
     * @return string
     */
    function message($title, $content) {
        $message = PHPShopText::b(PHPShopText::notice($title, false, '14px')) . PHPShopText::br();
        $message .= PHPShopText::message($content, false, '12px', 'black');
        return $message;
    }

    /**
     * Очистка временных переменных
     */
    function garbage() {
        if ($this->garbage_enabled) {
            timer('start', 'Garbage');
            unset($this->SysValue['other']);
            timer('end', 'Garbage');
        }
    }

}

?>