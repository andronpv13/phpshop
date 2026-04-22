<?php

/**
 * Библиотека RSS для Google Merchant
 * @author PHPShop Software
 * @version 1.4
 * @package PHPShopClass
 */
class PHPShopRssGoogle {

    var $xml = null;

    /**
     * вывод характеристик
     * @var bool 
     */
    var $vendor = false;

    /**
     * вывод параметров
     * @var bool 
     */
    var $param = false;

    /**
     * массив брендов
     * @var array 
     */
    var $brand_array = array();

    /**
     * массив параметров
     * @var array 
     */
    var $param_array = array();

    /**
     * массив значений тег/имя характеристики
     * @var array 
     */
    var $vendor_name = array('vendor' => 'Бренд');

    /**
     * память событий модулей
     * @var bool 
     */
    var $memory = true;
    var $ssl = 'http://';

    /**
     * Конструктор
     */
    function __construct() {
        global $PHPShopModules, $PHPShopSystem;

        $this->PHPShopSystem = $PHPShopSystem;
        $PHPShopValuta = new PHPShopValutaArray();
        $this->PHPShopValuta = $PHPShopValuta->getArray();

        // Модули
        $this->PHPShopModules = &$PHPShopModules;

        // Промоакции
        $this->PHPShopPromotions = new PHPShopPromotions();

        // Процент накрутки
        $this->percent = $this->PHPShopSystem->getValue('percent');

        // Валюта по умолчанию
        $this->defvaluta = $this->PHPShopSystem->getValue('dengi');
        $this->defvalutaiso = $this->PHPShopValuta[$this->defvaluta]['iso'];
        $this->defvalutacode = $this->PHPShopValuta[$this->defvaluta]['code'];

        // Кол-во знаков после запятой в цене
        $this->format = $this->PHPShopSystem->getSerilizeParam('admoption.price_znak');

        // SSL
        if (isset($_GET['ssl']))
            $this->ssl = 'https://';
        else if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS']))
            $this->ssl = 'https://';

        $this->setHook(__CLASS__, __FUNCTION__);
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
        if ($this->PHPShopModules)
            return $this->PHPShopModules->setHookHandler($class_name, $function_name, array(&$this), $data, $rout);
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
     * Проверка прав каталога режима Multibase
     * @return string
     */
    function queryMultibase() {

        // Мультибаза
        if (defined("HostID") or defined("HostMain")) {

            // Не выводить скрытые каталоги
            $where['skin_enabled '] = "!='1'";

            if (defined("HostID"))
                $where['servers'] = " REGEXP 'i" . HostID . "i'";
            elseif (defined("HostMain"))
                $where['skin_enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
            $PHPShopOrm->debug = $this->debug;
            $this->categories = array_column($PHPShopOrm->getList(['id'], $where, false, ['limit' => 1000], __CLASS__, __FUNCTION__), 'id');

            if (count($this->categories) > 0) {
                $dop_cats = '';
                foreach ($this->categories as $category) {
                    $dop_cats .= ' OR dop_cat LIKE \'%#' . $category . '#%\' ';
                }
                $categories_str = implode("','", $this->categories);

                return " (category IN ('$categories_str') " . $dop_cats . " ) and ";
            }
        }
    }

    /**
     * Данные по товарам. Оптимизировано.
     * @return array массив товаров
     */
    function product() {
        $Products = array();

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);

        if (isset($_GET['getall']))
            $where = null;
        else {
            if (isset($GLOBALS['SysValue']['base']['marketplaces']['marketplaces_system'])) {
                $where = "google_merchant='1' and";
            } else {
                $where = null;
            }
        }

        // Мультибаза
        $queryMultibase = $this->queryMultibase();
        if (!empty($queryMultibase))
            $where .= ' ' . $queryMultibase;

        // IDs
        if (is_array($this->where)) {
            $where = 'id  IN (' . implode(',', $this->where) . ') and ';
        }

        $result = $PHPShopOrm->query("select * from " . $GLOBALS['SysValue']['base']['products'] . " where $where enabled='1' and parent_enabled='0' and price>0");
        while ($row = mysqli_fetch_array($result)) {
            $id = $row['id'];
            $name = trim(strip_tags($row['name']));
            $category = $row['category'];
            $uid = $row['uid'];
            $price = $row['price'];
            $oldprice = $row['price_n'];

            // Промоакции
            $promotions = $this->PHPShopPromotions->getPrice($row);
            if (is_array($promotions)) {
                $price = $promotions['price'];
                $oldprice = $promotions['price_n'];
            }

            if ($row['sklad'] == 0)
                $enabled = "in stock";
            else
                $enabled = "out of stock";

            if (empty($row['description']))
                $row['description'] = $row['content'];
            $description = '<![CDATA[' . trim(strip_tags($row['description'], '<p><h3><ul><li><br>')) . ']]>';
            $content = '<![CDATA[' . $row['content'] . ']]>';
            $baseinputvaluta = $row['baseinputvaluta'];

            if ($baseinputvaluta) {
                //Если валюта отличается от базовой
                if ($baseinputvaluta !== $this->defvaluta) {
                    $vkurs = $this->PHPShopValuta[$baseinputvaluta]['kurs'];

                    // Если курс нулевой или валюта удалена
                    if (empty($vkurs))
                        $vkurs = 1;

                    // Приводим цену в базовую валюту
                    $price = $price / $vkurs;
                    $oldprice = $oldprice / $vkurs;
                }
            }

            $price = ($price + (($price * $this->percent) / 100));
            $price = round($price, $this->format);
            $oldprice = round($oldprice, $this->format);

            $array = array(
                "id" => $id,
                "category" => $category,
                "name" => $name,
                "picture" => $row['pic_big'],
                "price" => $price,
                "price2" => round($row['price2'], (int) $this->format),
                "price3" => round($row['price3'], (int) $this->format),
                "price4" => round($row['price4'], (int) $this->format),
                "price5" => round($row['price5'], (int) $this->format),
                "oldprice" => $oldprice,
                "weight" => $row['weight'],
                "enabled" => $enabled,
                "yml_bid_array" => unserialize($row['yml_bid_array']),
                "uid" => $uid,
                "description" => $description,
                "content" => $content,
                "prod_seo_name" => $row['prod_seo_name'],
                "fee" => $row['fee'],
                "cpa" => $row['cpa'],
                "manufacturer_warranty" => $row['manufacturer_warranty'],
                "sales_notes" => $row['sales_notes'],
                "country_of_origin" => $row['country_of_origin'],
                "adult" => $row['adult'],
                "rec" => $row['odnotip'],
                "delivery" => $row['delivery'],
                "pickup" => $row['pickup'],
                "store" => $row['store'],
                "vendor_code" => $row['vendor_code'],
                "vendor_name" => $row['vendor_name'],
                "condition" => $row['yandex_condition'],
                "barcode" => $row['barcode'],
                "price_google" => round($row['price_google'], (int) $this->format),
            );

            // Параметр сортировки
            if (!empty($this->vendor))
                $array['vendor_array'] = unserialize($row['vendor_array']);

            $Products[$id] = $array;
        }
        return $Products;
    }

    /**
     * Заголовок 
     */
    function setHeader() {
        $this->xml .= '<?xml version="1.0"?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
<channel>
<title>' . $this->PHPShopSystem->getName() . '</title>
<link>' . $this->ssl . $_SERVER['SERVER_NAME'] . '</link>
<description>' . $this->PHPShopSystem->getValue('company') . '</description>
<platform>PHPShop</platform>
<version>' . $GLOBALS['SysValue']['upload']['version'] . '</version>';
    }

    /**
     * Очистка спецсимволов
     */
    function cleanStr($string) {
        $string = html_entity_decode($string, ENT_QUOTES, 'windows-1251');
        return str_replace('&#43;', '+', $string);
    }

    /**
     * Товары 
     */
    function setProducts() {
        $vendor = null;
        $this->xml .= null;
        $product = $this->product($vendor = true);

        // Учет модуля SEOURLPRO
        if (!empty($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'])) {
            $seourlpro_enabled = true;
            $html_enabled = (new PHPShopOrm($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system']))->getOne(['html_enabled'])['html_enabled'];
        } else
            $html_enabled = 1;


        if ($html_enabled == 2)
            $html = null;
        else
            $html = '.html';


        // Передавать параметр
        if (isset($_GET['from']))
            $from = '?from=xml';
        else
            $from = null;


        foreach ($product as $val) {

            $bid_str = null;
            $vendor = $param = null;

            // Стандартный урл
            $url = '/shop/UID_' . $val['id'];

            // SEOURLPRO
            if (!empty($seourlpro_enabled)) {
                if (empty($val['prod_seo_name']))
                    $url = '/id/' . str_replace("_", "-", PHPShopString::toLatin($val['name'])) . '-' . $val['id'];
                else
                    $url = '/id/' . $val['prod_seo_name'] . '-' . $val['id'];
            }

            $xml = '
      <item> 
      <title><![CDATA[' . $this->cleanStr($val['name']) . ']]></title> 
      <link>' . $this->ssl . $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir'] . $url . $html . $from . '</link> 
      <description>' . $this->cleanStr($val['description']) . '</description>
      <g:image_link>' . $this->ssl . $_SERVER['SERVER_NAME'] . $val['picture'] . '</g:image_link> 
      <g:price>' . $val['price'] . ' ' . $this->defvalutaiso . '</g:price> 
      <g:availability>' . $val['enabled'] . '</g:availability>
      <g:id>' . $val['id'] . '</g:id>
      </item>';


            // Перехват модуля, занесение в память наличия модуля для оптимизации
            if ($this->memory_get(__CLASS__ . '.' . __FUNCTION__, true)) {
                $hook = $this->setHook(__CLASS__, __FUNCTION__, array('xml' => $xml, 'val' => $val));
                if ($hook) {
                    $this->xml .= $hook;
                } else {
                    $this->xml .= $xml;
                    $this->memory_set(__CLASS__ . '.' . __FUNCTION__, 0);
                }
            } else
                $this->xml .= $xml;
        }
    }

    /**
     * Подвал 
     */
    function serFooter() {
        $this->xml .= '</channel></rss>';
    }

    /**
     * Компиляция документа, вывод результата 
     */
    function compile() {
        $this->setHeader();
        $this->setProducts();
        $this->serFooter();
        return PHPShopString::win_utf8($this->xml);
    }

}
