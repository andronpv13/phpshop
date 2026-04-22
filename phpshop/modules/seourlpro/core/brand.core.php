<?php

include_once("phpshop/core/shop.core.php");

class PHPShopBrand extends PHPShopShopCore {

    function __construct() {

        // Отладка
        $this->debug = false;

        $this->path = '/brand';

        // Список экшенов
        $this->action = array("nav" => "index");

        parent::__construct();
    }

    function index() {

        $PHPShopSeourlOption = new PHPShopSeourlOption();
        $seourl_option = $PHPShopSeourlOption->getArray();
        if ($seourl_option["seo_brands_enabled"] == 2) {
            if ($this->PHPShopNav->objNav['nav'] == '')
                $this->index_content();
            else
                $this->brand();
        } else
            $this->setError404();
    }

    // Выбор брендов по алфавиту
    function index_content() {

        $PHPShopOrmSort = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);

        // Мультибаза
        $servers = '';
        if (defined("HostID"))
            $servers .= " and servers REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $servers .= ' and (servers ="" or servers REGEXP "i1000i")';

        // Массив имен характеристик
        $PHPShopOrm = new PHPShopOrm();
        $result = $PHPShopOrm->query("select * from " . $GLOBALS['SysValue']['base']['sort_categories'] . " where (brand='1' and goodoption!='1') " . $servers . " order by num");
        while (@$row = mysqli_fetch_assoc($result)) {
            $arrayVendor[$row['id']] = $row;
        }

        $sortValue = $brands = $charList = $brandsList = null;

        if (!empty($arrayVendor) and is_array($arrayVendor))
            foreach ($arrayVendor as $key => $value) {
                if (is_numeric($key))
                    $sortValue .= ' category=' . $key . ' OR';
            }
        $sortValue = substr($sortValue, 0, strlen($sortValue) - 2);

        if (!empty($sortValue)) {

            // Массив значений характеристик
            $PHPShopOrm = new PHPShopOrm();
            $result = $PHPShopOrm->query("select * from " . $GLOBALS['SysValue']['base']['sort'] . " where $sortValue order by num");
            while (@$row = mysqli_fetch_array($result)) {
                $arrForSort[$row['name']] = $row['id'];
                $arrParentCat[$row['id']] = $row['category'];
                $arrSeo[$row['name']] = $row['sort_seo_name'];
            }
        }
        if (count($arrParentCat)) {
            ksort($arrForSort);
            $arrForSort = array_merge($arrForSort, array("" => "noId"));

            foreach ($arrForSort as $value => $key) {
                $charOld = $char;
                $char = substr(strtoupper($value), 0, 1);

                if ($charOld != $char) {
                    if (!empty($char))
                        $charList .= '   ' . PHPShopText::a("#" . $char, PHPShopText::b($char), $char);
                    if (!empty($charOld)) {
                        $this->set('brandChar', $charOld);
                        $this->set('brands', $brands);
                        $brands = '';
                        $brandsList .= PHPShopParser::file($GLOBALS['SysValue']['templates']['seourlpro']['selection_one'], true, false, true);
                    }
                }

                if (empty($arrSeo[$value])) {
                    $seoLink = $GLOBALS['PHPShopSeoPro']->setLatin($value);
                    $PHPShopOrmSort->update(array("sort_seo_name_new" => "$seoLink"), array('id' => '=' . $key));
                    $brands .= PHPShopText::li($value, $GLOBALS['PHPShopSeoPro']->setLatin($value) . '.html');
                } else
                    $brands .= PHPShopText::li($value, $GLOBALS['SysValue']['dir']['dir'].'/brand/' . $arrSeo[$value] . '.html');
            }
        } else
            return $this->setError404();

        $title = __('Бренды');
        $this->set('pageTitle', $title);
        $this->title = $title . " - " . $this->PHPShopSystem->getParam('title');
        $this->description = $title . ", " . $this->PHPShopSystem->getParam('title');
        $this->set('pageContent', PHPShopText::p($charList, false, 'brands-list-content') . $brandsList);

        // Навигация хлебные крошки
        $this->navigation(0, $title);


        // Подключаем шаблон
        $this->parseTemplate($this->getValue('templates.page_page_list'));
    }

    /**
     * SEO навигация брендов
     */
    function brand() {

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__, null, 'START'))
            return true;

        // Редирект без .html
        if (strstr($_SERVER['REQUEST_URI'], ".html") and $GLOBALS['PHPShopSeoPro']->getSettings()['html_enabled'] == 2) {
            header('Location: ' . $this->getValue('dir.dir') . str_replace('.html', '', $_SERVER['REQUEST_URI']), true, 301);
            return true;
        }

        // Валюта
        $this->set('productValutaName', $this->currency());

        // Количество ячеек
        if (empty($this->cell))
            $this->cell = $this->calculateCell("selection", $this->PHPShopSystem->getValue('num_row_adm'));


        if (!empty($_GET['gridChange']))
            switch ($_GET['gridChange']) {
                case 1:
                    $this->set('gridSetAactive', 'active');
                    break;
                case 2:
                    $this->set('gridSetBactive', 'active');
                    break;
                default: $this->set('gridSetBactive', 'active');
            }


        if (!empty($_GET['s']))
            switch ($_GET['s']) {
                case 1:
                    $this->set('sSetAactive', 'active');
                    break;
                case 2:
                    $this->set('sSetBactive', 'active');
                    break;
                default: $this->set('sSetCactive', 'active');
            }


        if (!empty($_GET['f']))
            switch ($_GET['f']) {
                case 1:
                    $this->set('fSetAactive', 'active');
                    $this->set('fSetAchecked', 'checked="checked"');
                    $this->set('fSetAselected', 'selected');
                    break;
                case 2:
                    $this->set('fSetBactive', 'active');
                    $this->set('fSetBchecked', 'checked="checked"');
                    $this->set('fSetBselected', 'selected');
                    break;
                default:
                    $this->set('fSetCactive', 'active');
                    $this->set('fSetCchecked', 'checked="checked"');
                    $this->set('fSetCselected', 'selected');
            }

        // Мультибаза
        $servers = '';
        if (defined("HostID"))
            $servers .= " and servers REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $servers .= ' and (servers ="" or servers REGEXP "i1000i")';

        // Массив имен характеристик
        $PHPShopOrm = new PHPShopOrm();
        $result = $PHPShopOrm->query("select * from " . $GLOBALS['SysValue']['base']['sort_categories'] . " where (brand='1' and goodoption!='1') " . $servers . " order by num");
        $categories = [];
        while (@$row = mysqli_fetch_assoc($result)) {
            $categories[] = $row['id'];
        }

        $PHPShopNav = new PHPShopNav();
        $seo_name = explode(".", $PHPShopNav->getNav());
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);
        $PHPShopOrm->debug=$this->debug;
        $PHPShopOrm->mysql_error = false;
        $PHPShopOrm->Option['where']=" or ";

        if (strtolower($seo_name[0]) !== $seo_name[0]) {
            return $this->setError404();
        }

        $vendor = $PHPShopOrm->getOne(array("*"), array('sort_seo_name' => "='" . PHPShopSecurity::TotalClean($seo_name[0]) . "'",'name' => "='" . PHPShopSecurity::TotalClean($seo_name[0]) . "'"));


        // Нет данных, 404 ошибка
        if (!is_array($vendor))
            return $this->setError404();

        if (isset($vendor['id']))
            $vendorArray = array($vendor);
        else
            $vendorArray = $vendor;

        $v = array();
        foreach ($vendorArray as $value) {
            if (in_array($value['category'], $categories)) {
                $v[$value['category']] = $value['id'];
            }
        }
        // Нет данных, 404 ошибка
        if (count($v) === 0)
            return $this->setError404();

        // Фильтр сортировки
        $order = $this->query_filter($this, $v);

        // Сложный запрос
        $this->PHPShopOrm->sql = $order;
        $this->PHPShopOrm->debug = $this->debug;
        $this->PHPShopOrm->mysql_error = false;
        $this->PHPShopOrm->comment = __CLASS__ . '.' . __FUNCTION__;
        $this->dataArray = $this->PHPShopOrm->select();
        $this->PHPShopOrm->clean();

        // Пагинатор
        if (is_array($this->dataArray))
            $count = count($this->dataArray);

        if ($count)
            $this->setPaginator($count, $order);

        // Добавляем в дизайн ячейки с товарами
        $grid = $this->product_grid($this->dataArray, $this->cell);
        if (empty($grid))
            $grid = PHPShopText::h4($this->lang('empty_product_list'));
        $this->add($grid, true);

        // Описание значения характеристики
        $PHPShopOrm = new PHPShopOrm();
        $PHPShopOrm->mysql_error = false;
        $result = $PHPShopOrm->query('SELECT a.*, b.content, b.title FROM ' . $this->getValue("base.sort") . ' AS a JOIN ' . $this->getValue("base.page") . ' AS b ON a.page = b.link where a.id = ' . intval($vendor["id"]) . ' limit 1');
        $row = mysqli_fetch_array($result);

        if (is_array($row)) {

            // Описание
            $this->set('sortDes', stripslashes($row['content']));
        } else {
            // Описание и имя характеристики. Первое, которое заполнено в массиве характеристик (если их больше 1).
            foreach ($vendorArray as $brand) {
                if (!empty($brand['description'])) {
                    $this->set('sortDes', stripslashes($brand['description']));
                    $brandTitle = $brand['name'];
                    break;
                }
            }
        }

        // Если описание из страницы или его нигде нет в характеристиках - берем имя с первого бренда.
        if (empty($brandTitle)) {
            $firstBrand = array_shift($vendorArray);
            $brandTitle = $firstBrand['name'];
        }

        // Название
        $this->set('sortName', $brandTitle);


        // Заголовок
        if (empty($vendor['title']))
            $this->title = __('Бренд') . " - " . $brandTitle . " - " . $this->PHPShopSystem->getParam('title');
        else {
            $this->title = str_replace(['@System@', '@valueTitle@'], [$this->PHPShopSystem->getParam('title'), $brandTitle], $vendor['title']);
        }

        if (empty($vendor['meta_description']))
            $this->description = $brandTitle . ', ' . $this->PHPShopSystem->getParam('descrip');
        else
            $this->description = str_replace(['@System@', '@valueTitle@'], [$this->PHPShopSystem->getParam('descrip'), $brandTitle], $vendor['meta_description']);


        $this->keywords = $brandTitle;

        // Навигация хлебные крошки
        $this->navigation(0, $brandTitle, ['url' => './', 'name' => __('Бренды')]);

        $this->parseTemplate($this->getValue('templates.product_selection_list'));
    }

    /**
     * Cортировка товаров по бренду
     */
    function query_filter($obj = false, $v = false) {
        global $SysValue;

        $s = intval(@$_REQUEST['s']);
        $f = intval(@$_REQUEST['f']);

        if (!empty($_REQUEST['p']))
            $p = intval($_REQUEST['p']);
        else
            $p = 1;

        $num_row = $obj->num_row;
        $num_ot = 0;
        $q = 0;
        $sort = $sortQuery = null;

        // Сортировка по характеристикам
        if (is_array($v)) {
            $sort .= ' and (';
            foreach ($v as $key => $value) {

                // Обычный отбор []
                if (PHPShopSecurity::true_num($key) and PHPShopSecurity::true_num($value)) {
                    $hash = $key . "-" . $value;
                    $sort .= " vendor REGEXP 'i" . $hash . "i' or";
                    $sortQuery .= "&v[$key]=$value";
                }
            }
            $sort = substr($sort, 0, strlen($sort) - 2);
            $sort .= ")";
        }

        // Сортировка принудительная пользователем
        switch ($f) {
            case(1): $order_direction = "";
                break;
            case(2): $order_direction = " desc";
                break;
            default: $order_direction = " desc";
                break;
        }
        switch ($s) {
            case(1): $order = array('order' => 'name' . $order_direction);
                break;
            case(2): $order = array('order' => 'price' . $order_direction);
                break;
            case(3): $order = array('order' => 'num, items' . $order_direction);
                break;
            default: $order = array('order' => 'num, items' . $order_direction);
        }

        // Преобзазуем массив уловия сортировки в строку
        foreach ($order as $key => $val)
            $string = $key . ' by ' . $val;

        // Все страницы
        if ($p == "all") {
            $sql = "select * from " . $SysValue['base']['products'] . " where enabled='1' and parent_enabled='0' $sort  $string";
        } else
            while ($q < $p) {

                $sql = "select * from " . $SysValue['base']['products'] . " where enabled='1' and parent_enabled='0' $sort  $string LIMIT $num_ot, $num_row";
                $q++;
                $num_ot = $num_ot + $num_row;
            }

        $obj->selection_order = array(
            'sortQuery' => $sortQuery,
            'sortV' => $sort
        );

        // Возвращаем SQL запрос
        return $sql;
    }

    /**
     * Генерация пагинатора
     */
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

        if (is_array($this->selection_order)) {
            $SQL = " where enabled='1' and parent_enabled='0' " . $this->selection_order['sortV'];
        } else
            $SQL = null;


        // Всего страниц
        $this->PHPShopOrm->comment = __CLASS__ . '.' . __FUNCTION__;
        $this->PHPShopOrm->clean();
        $result = $this->PHPShopOrm->query("select COUNT('id') as count from " . $this->objBase . $SQL);
        $row = mysqli_fetch_array($result);
        $this->num_page = $row['count'];

        $i = 1;
        $navigat = null;

        $num = ceil($this->num_page / $this->num_row);

        if (empty($_GET['p']))
            $_GET['p'] = 1;
        $this->page = $_GET['p'];

        if ($num > 1) {
            if ($this->page >= $num) {
                $p_to = $i - 1;
                $p_do = $this->page - 1;
            } else {
                $p_to = $this->page + 1;
                $p_do = 1;
            }

            while ($i <= $num) {

                if ($i > 1) {
                    $p_start = $this->num_row * ($i - 1);
                    $p_end = $p_start + $this->num_row;
                } else {
                    $p_start = $i;
                    $p_end = $this->num_row;
                }


                $this->set("paginPageRangeStart", $p_start);
                $this->set("paginPageRangeEnd", $p_end);
                $this->set("paginPageNumber", $i);


                if ($i != $this->page) {
                    $this->set("paginLink", "?f=" . $_REQUEST['f'] . "&s=" . $_REQUEST['s'] . $this->selection_order['sortQuery'] . "&p=" . $i);
                    if ($i == 1) {
                        $navigat .= parseTemplateReturn($template_location . "paginator/paginator_one_link.tpl", $template_location_bool);
                    } else {
                        if ($i > ($this->page - $this->nav_len) and $i < ($this->page + $this->nav_len)) {
                            $navigat .= parseTemplateReturn($template_location . "paginator/paginator_one_link.tpl", $template_location_bool);
                        } else if ($i - ($this->page + $this->nav_len) < 3 and ( ($this->page - $this->nav_len) - $i) < 3) {
                            $navigat .= parseTemplateReturn($template_location . "paginator/paginator_one_more.tpl", $template_location_bool);
                        }
                    }
                } else
                    $navigat .= parseTemplateReturn($template_location . "paginator/paginator_one_selected.tpl", $template_location_bool);

                $i++;
            }


            $nav = $this->getValue('lang.page_now') . ': ';

            $this->set("previousLink", "?f=" . $_REQUEST['f'] . "&s=" . $_REQUEST['s'] . "&p=" . $p_do);

            $this->set("nextLink", "?f=" . $_REQUEST['f'] . "&s=" . $_REQUEST['s'] . "&p=" . $p_to);

            $nav .= $navigat;


            $this->set("pageNow", $this->getValue('lang.page_now'));
            $this->set("navBack", $this->lang('nav_back'));
            $this->set("navNext", $this->lang('nav_forw'));
            $this->set("navigation", $navigat);

            // Назначаем переменную шаблонизатора
            $nav = parseTemplateReturn($template_location . "paginator/paginator_main.tpl", $template_location_bool);
            $this->set('productPageNav', $nav);
        }
    }

}

?>