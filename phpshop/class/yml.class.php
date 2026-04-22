<?php

/**
 * Библиотека YML
 * @author PHPShop Software
 * @version 2.0
 * @package PHPShopClass
 */
class PHPShopYml {

    var $xml = null;
    private $categories = [];

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
    var $option = false;

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
    var $image_source = false;

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

        // Ведущий товар в подтипах можно положить в корзину
        $this->parent_price_enabled = $this->PHPShopSystem->getSerilizeParam('admoption.parent_price_enabled');

        // CRM
        $this->option = $this->PHPShopSystem->ifSerilizeParam('1c_option.update_option');

        // SSL
        if (isset($_GET['ssl']))
            $this->ssl = 'https://';
        else if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS']))
            $this->ssl = 'https://';

        // Исходное изображение
        if (isset($_GET['image_source']) and $this->PHPShopSystem->ifSerilizeParam('admoption.image_save_source'))
            $this->image_source = true;
        else
            $this->image_source = false;

        // Колонка цен
        $this->price = $this->PHPShopSystem->getPriceColumn();
        if ($_GET['price'] > 1)
            $this->price = 'price' . intval($_GET['price']);

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
     * Изображения товара
     * @param array $product_row
     * @return string
     */
    public function getImages($id, $pic_main) {
        $xml = null;

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
        $data = $PHPShopOrm->select(['*'], ['parent' => '=' . (int) $id, 'name' => '!="' . $pic_main . '"'], ['order' => 'num'], ['limit' => 15]);

        // Главное изображение
        $pic_main_b = str_replace(".", "_big.", $pic_main);
        if (!$this->image_source or ! file_exists($_SERVER['DOCUMENT_ROOT'] . $pic_main_b))
            $pic_main_b = $pic_main;

        if (!empty($pic_main_b)) {
            if (!strstr($pic_main_b, 'https'))
                $pic_main_b = 'https://' . $_SERVER['SERVER_NAME'] . $pic_main_b;

            $images[] = $pic_main_b;
        }

        if (is_array($data)) {
            foreach ($data as $row) {

                $name = $row['name'];
                $name_b = str_replace(".", "_big.", $name);

                // Подбор исходного изображения
                if (!$this->image_source or ! file_exists($_SERVER['DOCUMENT_ROOT'] . $name_b))
                    $name_b = $name;

                if (!strstr($name_b, 'https'))
                    $name_b = 'https://' . $_SERVER['SERVER_NAME'] . $name_b;

                $images[] = $name_b;
            }
        }

        if (is_array($images))
            foreach ($images as $image) {

                // Видео
                if (in_array(pathinfo($image, PATHINFO_EXTENSION), ['mp4', 'mov']))
                    $xml .= '<video>' . $image . '</video>';
                // Изображение
                else
                    $xml .= '<picture>' . $image . '</picture>';
            }

        return $xml;
    }

    /**
     * Данные по каталогам
     * @return array массив каталогов
     */
    function category() {
        $Catalog = [];
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);

        // Не выводить скрытые каталоги
        if (isset($_GET['getall']) or isset($_GET['retailcrm']))
            $where = null;

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['skin_enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $data = $PHPShopOrm->select(array('*'), $where, false, array('limit' => 10000));
        if (is_array($data))
            foreach ($data as $row) {
                if ($row['id'] != $row['parent_to']) {
                    $Catalog[$row['id']]['id'] = $row['id'];
                    $Catalog[$row['id']]['name'] = $row['name'];
                    $Catalog[$row['id']]['parent_to'] = $row['parent_to'];
                    $Catalog[$row['id']]['market_category_id'] = $row['category_yandexcart'];
                }
            }

        $this->Catalog = $Catalog;
        return $Catalog;
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
            if (isset($_GET['marketplace']) && $_GET['marketplace'] === 'yandexmarket' && isset($GLOBALS['SysValue']['base']['marketplaces']['marketplaces_system'])) {
                $where = "cdek='1' and";
            } elseif (isset($_GET['marketplace']) && $_GET['marketplace'] === 'aliexpress' && isset($GLOBALS['SysValue']['base']['marketplaces']['marketplaces_system'])) {
                $where = "aliexpress='1' and";
            } elseif (isset($_GET['marketplace']) && $_GET['marketplace'] === 'sbermarket' && isset($GLOBALS['SysValue']['base']['marketplaces']['marketplaces_system'])) {
                $where = "sbermarket='1' and";
            } elseif (isset($_GET['marketplace']) && $_GET['marketplace'] === 'ozon' && isset($GLOBALS['SysValue']['base']['ozonseller']['ozonseller_system'])) {
                $where = "export_ozon='1' and";
            } elseif (isset($_GET['marketplace']) && $_GET['marketplace'] === 'vk' && isset($GLOBALS['SysValue']['base']['vkseller']['vkseller_system'])) {
                $where = "export_vk='1' and";
            } elseif (isset($_GET['marketplace']) && $_GET['marketplace'] === 'megamarket' && isset($GLOBALS['SysValue']['base']['megamarket']['megamarket_system'])) {
                $where = "export_megamarket='1' and";
            }
            // Модуль Яндекс.Маркет
            elseif (isset($_GET['campaign']) && isset($GLOBALS['SysValue']['base']['yandexcart']['yandexcart_system'])) {
                $where = "yml_" . (int) $_GET['campaign'] . "='1' and";
            } else {
                $where = "yml='1' and";
            }
        }

        if (isset($_GET['available'])) {
            $where .= " sklad='0' and";
        }

        // Мультибаза
        $queryMultibase = $this->queryMultibase();
        if (!empty($queryMultibase))
            $where .= ' ' . $queryMultibase;

        $wherePrice = 'and price>0';
        if ($_GET['search']) {
            $wherePrice = '';
        }

        // IDs
        if (is_array($this->where)) {
            $where = 'id  IN (' . implode(',', $this->where) . ') and ';
        }

        $result = $PHPShopOrm->query("select * from " . $GLOBALS['SysValue']['base']['products'] . " where $where enabled='1' and parent_enabled='0' $wherePrice");
        if ($result)
            while ($row = mysqli_fetch_array($result)) {

                // Пропуск неопределенных товаров
                if (in_array($row['category'], array(1000001, 1000004, 0)))
                    continue;

                $id = $row['id'];
                $name = trim(strip_tags($row['name']));

                // Основная категория
                $category = $row['category'];
                // Товар с доп. каталога, основного каталога в выводе нет.
                if (count($this->categories) > 0) {
                    if (in_array($category, $this->categories) === false) {
                        foreach (explode('#', $row['dop_cat']) as $dopCat) {
                            if (!empty($dopCat) && in_array($dopCat, $this->categories)) {
                                $category = $dopCat;
                                break;
                            }
                        }
                    }
                }

                $uid = $row['uid'];

                $price = $row[$this->price];
                $oldprice = $row['price_n'];

                $promotions = $this->PHPShopPromotions->getPrice($row);
                if (is_array($promotions)) {
                    $price = $promotions['price'];
                    $oldprice = $promotions['price_n'];
                }

                if (empty($row['description']))
                    $row['description'] = $row['content'];

                // Чистка тегов
                if (empty($_GET['striptag'])) {
                    $description = '<![CDATA[' . trim(strip_tags($row['description'], '<p><h3><ul><li><br>')) . ']]>';
                    $content = '<![CDATA[' . $row['content'] . ']]>';
                } else {
                    $description = strip_tags($row['description']);
                    $content = strip_tags($row['content']);
                }


                $baseinputvaluta = $row['baseinputvaluta'];

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


                $price = ($price + (($price * $this->percent) / 100));
                $price = round($price, intval($this->format));
                $oldprice = round($oldprice, intval($this->format));

                $array = array(
                    "id" => $id,
                    "category" => $category,
                    "name" => str_replace(array('&#43;', '&#43'), '+', $name),
                    "picture" => htmlspecialchars($row['pic_big']),
                    "price" => $price,
                    "price2" => round($row['price2'], (int) $this->format),
                    "price3" => round($row['price3'], (int) $this->format),
                    "price4" => round($row['price4'], (int) $this->format),
                    "price5" => round($row['price5'], (int) $this->format),
                    "oldprice" => $oldprice,
                    "weight" => $row['weight'],
                    "length" => $row['length'],
                    "width" => $row['width'],
                    "height" => $row['height'],
                    "uid" => $uid,
                    "vkurs" => $vkurs,
                    "description" => $description,
                    "raw_description" => $row['description'],
                    "content" => $content,
                    "raw_content" => $row['content'],
                    "prod_seo_name" => $row['prod_seo_name'],
                    "manufacturer_warranty" => $row['manufacturer_warranty'],
                    "sales_notes" => $row['sales_notes'],
                    "country_of_origin" => $row['country_of_origin'],
                    "adult" => $row['adult'],
                    "delivery" => $row['delivery'],
                    "pickup" => $row['pickup'],
                    "store" => $row['store'],
                    "yandex_min_quantity" => $row['yandex_min_quantity'],
                    "yandex_step_quantity" => $row['yandex_step_quantity'],
                    "vendor_code" => $row['vendor_code'],
                    "vendor_name" => $row['vendor_name'],
                    "manufacturer" => $row['manufacturer'],
                    "condition" => $row['yandex_condition'],
                    "condition_reason" => $row['yandex_condition_reason'],
                    "quality" => $row['yandex_quality'],
                    "items" => $row['items'],
                    "gift" => $row['gift'],
                    "gift_check" => $row['gift_check'],
                    "gift_items" => $row['gift_items'],
                    "barcode" => $row['barcode'],
                    "model" => $row['model'],
                    "market_sku" => $row['market_sku'],
                    "cpa" => $row['cpa'],
                    "price_yandex_dbs" => round($row['price_yandex_dbs'], (int) $this->format),
                    "price_sbermarket" => round($row['price_sbermarket'], (int) $this->format),
                    "price_cdek" => round($row['price_cdek'], (int) $this->format),
                    "price_aliexpress" => round($row['price_aliexpress'], (int) $this->format),
                    "price_ozon" => round($row['price_ozon'], (int) $this->format),
                    "yandex_service_life_days" => $row['yandex_service_life_days'],
                    "price_vk" => round($row['price_vk'], (int) $this->format),
                    "price_yandex" => round($row['price_yandex'], (int) $this->format),
                    "price_yandex_2" => round($row['price_yandex_2'], (int) $this->format),
                    "price_yandex_3" => round($row['price_yandex_3'], (int) $this->format),
                    "baseinputvaluta" => $row['baseinputvaluta'],
                    "items1" => $row['items1'],
                    "items2" => $row['items2'],
                    "items3" => $row['items3'],
                );

                // Категория Яндекс
                $market_category_id = $this->Catalog[$category]['market_category_id'];
                if (!empty($market_category_id))
                    $array['market_category_id'] = $market_category_id;

                // Параметр сортировки
                if (!empty($this->vendor))
                    $array['vendor_array'] = unserialize($row['vendor_array']);

                // Цвет-размер
                if ($_GET['search']) {
                    $row['parent'] = null;
                    $array['parent'] = null;
                }
                if (!empty($row['parent'])) {
                    $parent = @explode(",", $row['parent']);

                    $Parents = $this->parent($parent, $array);
                    if (is_array($Parents)) {
                        $array['parent'] = 1;
                        $Products = array_merge($Products, $Parents);
                    }
                }

                $Products[] = $array;
            }
        return $Products;
    }

    /**
     * Данные по товарам подтипам.
     * @return array массив товаров
     */
    function parent($parent, $parent_array) {

    $PHPShopOrm = new  PHPShopOrm($GLOBALS['SysValue']['base']['products']);

    // Подтипы из 1С
    if ($this->option)
        $result = $PHPShopOrm->query("select * from " . $GLOBALS['SysValue']['base']['products'] . " where uid IN (\"" . @implode('","', $parent) . "\") and enabled='1' and parent_enabled='1' and sklad='0' and price>0");
    else
        $result = $PHPShopOrm->query("select * from " . $GLOBALS['SysValue']['base']['products'] . " where id IN (\"" . @implode('","', $parent) . "\") and enabled='1' and parent_enabled='1' and sklad='0' and price>0");

    while ($row = mysqli_fetch_array($result)) {
        $id = $row['id'];
        $name = trim(strip_tags($row['name']));
        $uid = $row['uid'];
        $price = $row[$this->price];
        $oldprice = $row['price_n'];

        // Промоакции
        if ($this->PHPShopPromotions)
            $promotions = $this->PHPShopPromotions->getPrice($row);
        if (is_array($promotions)) {
            $price = $promotions['price'];
            $oldprice = $promotions['price_n'];
        }

        $baseinputvaluta = $row['baseinputvaluta'];

        if ($baseinputvaluta) {

            //Если валюта отличается от базовой
            if ($baseinputvaluta !== $this->defvaluta) {

                // Приводим цену в базовую валюту
                $price = $price / $parent_array['vkurs'];
                $oldprice = $oldprice / $parent_array['vkurs'];
            }
        }

        $price = ($price + (($price * $this->percent) / 100));
        $price = round($price, intval($this->format));
        $oldprice = round($oldprice, intval($this->format));

        // Изображение
        if (empty($row['pic_big']))
            $row['pic_big'] = $parent_array['picture'];

        $array = array(
            "id" => $id,
            "group_id" => $parent_array['id'],
            "parent_name" => $parent_array['name'],
            "size" => $row['parent'],
            "color" => $row['parent2'],
            "category" => $parent_array['category'],
            "name" => str_replace(array('&#43;', '&#43'), '+', $name),
            "picture" => htmlspecialchars($row['pic_big']),
            "price" => $price,
            "price2" => round($row['price2'], (int) $this->format),
            "price3" => round($row['price3'], (int) $this->format),
            "price4" => round($row['price4'], (int) $this->format),
            "price5" => round($row['price5'], (int) $this->format),
            "oldprice" => $oldprice,
            "weight" => $row['weight'],
            "length" => $row['length'],
            "width" => $row['width'],
            "height" => $row['height'],
            "uid" => $uid,
            "description" => $parent_array['description'],
            "raw_description" => $parent_array['raw_description'],
            "raw_content" => $parent_array['raw_content'],
            "prod_seo_name" => $parent_array['prod_seo_name'],
            "fee" => $parent_array['fee'],
            "cpa" => $parent_array['cpa'],
            "manufacturer_warranty" => $parent_array['manufacturer_warranty'],
            "sales_notes" => $parent_array['sales_notes'],
            "country_of_origin" => $parent_array['country_of_origin'],
            "adult" => $parent_array['adult'],
            "delivery" => $parent_array['delivery'],
            "pickup" => $parent_array['pickup'],
            "store" => $parent_array['store'],
            "manufacturer" => $parent_array['manufacturer'],
            "yandex_min_quantity" => $parent_array['yandex_min_quantity'],
            "yandex_step_quantity" => $parent_array['yandex_step_quantity'],
            "vendor_array" => $parent_array['vendor_array'],
            "items" => $row['items'],
            "gift" => $row['gift'],
            "gift_check" => $row['gift_check'],
            "gift_items" => $row['gift_items'],
            "barcode" => $row['barcode'],
            "model" => $row['model'],
            "market_sku" => $row['market_sku'],
            "price_yandex_dbs" => round($row['price_yandex_dbs'], (int) $this->format),
            "price_sbermarket" => round($row['price_sbermarket'], (int) $this->format),
            "price_cdek" => round($row['price_cdek'], (int) $this->format),
            "price_aliexpress" => round($row['price_aliexpress'], (int) $this->format),
            "price_vk" => round($row['price_vk'], (int) $this->format),
            "price_yandex" => round($row['price_yandex'], (int) $this->format),
            "price_yandex_2" => round($row['price_yandex_2'], (int) $this->format),
            "price_yandex_3" => round($row['price_yandex_3'], (int) $this->format),
            "baseinputvaluta" => $row['baseinputvaluta'],
            "items1" => $row['items1'],
            "items2" => $row['items2'],
            "items3" => $row['items3'],
        );

        $Products[$id] = $array;
    }
    return $Products;
}

/**
 * Заголовок
 */
function setHeader() {
    $this->xml .= '<?xml version="1.0" encoding="' . $this->encoding . '"?>
<yml_catalog date="' . date(DATE_RFC3339) . '">
<shop>
<name>' . $this->PHPShopSystem->getName() . '</name>
<company>' . $this->PHPShopSystem->getValue('company') . '</company>
<url>' . $this->ssl . $_SERVER['SERVER_NAME'] . '</url>
<platform>PHPShop</platform>
<version>' . $GLOBALS['SysValue']['upload']['version'] . '</version>';
}

/**
 * Валюты
 */
function setCurrencies() {
    $this->xml .= '<currencies>';
    $this->xml .= '<currency id="' . $this->PHPShopValuta[$this->PHPShopSystem->getValue('dengi')]['iso'] . '" rate="1"/>';
    $this->xml .= '</currencies>';
}

/**
 * Категории
 */
function setCategories() {

    // Перехват модуля
    $hook = $this->setHook(__CLASS__, __FUNCTION__);
    if ($hook) {
        return $hook;
    }

    $this->xml .= '<categories>';
    $category = $this->category();
    foreach ($category as $val) {
        if (empty($val['parent_to']))
            $this->xml .= '<category id="' . $val['id'] . '">' . $this->cleanStr($val['name']) . '</category>
';
        else
            $this->xml .= '<category id="' . $val['id'] . '" parentId="' . $val['parent_to'] . '">' . $this->cleanStr($val['name']) . '</category>
';
    }

    $this->xml .= '</categories>';
}

/**
 * Доставка
 */
function setDelivery() {

    $xml = '<delivery-options/>';

    // Перехват модуля, занесение в память наличия модуля для оптимизации
    if ($this->memory_get(__CLASS__ . '.' . __FUNCTION__, true)) {
        $hook = $this->setHook(__CLASS__, __FUNCTION__, array('xml' => $xml));
        if ($hook) {
            $this->xml .= $hook;
        } else {
            $this->xml .= $xml;
            $this->memory_set(__CLASS__ . '.' . __FUNCTION__, 0);
        }
    } else
        $this->xml .= $xml;
}

/**
 * Очистка спецсимволов
 */
function cleanStr($string) {
    $string = html_entity_decode($string, ENT_QUOTES, 'windows-1251');
    $string = str_replace('&#43;', '+', $string);
    $string = str_replace(array('"', '&', '>', '<', "'"), array('&quot;', '&amp;', '&gt;', '&lt;', '&apos;'), $string);
    return $string;
}

/**
 * Товары
 */
function setProducts() {
    global $csv_export_count;

    $vendor = null;
    $this->xml .= '<offers>';
    $product = $this->product($vendor = true);
    $csv_export_count = 0;

    // Учет модуля SEOURL
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
        $from = '?from=yml';
    else
        $from = null;


    foreach ($product as $val) {

        $csv_export_count++;

        $vendor = $param = null;
        $id = $val['id'];

        // Стандартный урл
        $url = '/shop/UID_' . $val['id'];

        // SEOURLPRO
        if (!empty($seourlpro_enabled)) {
            if (empty($val['prod_seo_name']))
                $url = '/id/' . str_replace("_", "-", PHPShopString::toLatin($val['name'])) . '-' . $val['id'];
            else
                $url = '/id/' . $val['prod_seo_name'] . '-' . $val['id'];
        }

        // Подтип
        if (!empty($val['group_id'])) {
            $val['id'] = $id;
            $group_id = ' group_id="' . $val['group_id'] . '"';
            $group_postfix = '?option=' . $id;

            if (!empty($seourlpro_enabled)) {

                if (!empty($val['prod_seo_name']))
                    $url = '/id/' . $val['prod_seo_name'] . '-' . $val['group_id'];
                else
                    $url = '/id/' . str_replace("_", "-", PHPShopString::toLatin($val['parent_name'])) . '-' . $val['group_id'];
            } else
                $url = '/shop/UID_' . $val['group_id'];
        }
        // Родитель
        elseif (!empty($val['parent']))
            $group_postfix = '?option=' . $id;
        else
            $group_id = $group_postfix = null;

        // Ведущий товар в подтипах можно положить в корзину
        if ($this->parent_price_enabled == 0 and ! empty($val['parent']))
            continue;

        // Изображение
        $picture = $this->getImages($val['id'], $val['picture']);

        if (isset($_GET['getall'])) {
            $val['description'] = $val['content'];
        }

        $name = '<name>' . $this->cleanStr($val['name']) . '</name>';
        $type = '';
        if (!empty($val['model']) && !empty($val['vendor_name']) && !isset($_GET['cdek'])) {
            $name = '<typePrefix>' . $this->cleanStr($val['name']) . '</typePrefix>';
            $type = ' type="vendor.model"';
        }

        $retailQuantity = '';
        if (isset($_GET['retailcrm'])) {
            $retailQuantity = sprintf(' quantity="%s"', $val['items']);
        }

        if ($val['sklad'] == 1)
            $available = 'false';
        else
            $available = 'true';

        $xml = '
<offer id="' . $val['id'] . '" available="' . $available . '" ' . $group_id . $type . $retailQuantity . '>
 <url>' . $this->ssl . $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir'] . $url . $html . $group_postfix . '</url>
      <price>' . $val['price'] . '</price>';

        // Старая цена
        if ($val['price_n'] > $val['price'])
            $xml .= '<oldprice>' . $val['price_n'] . '</oldprice>';

        // weight
        if (!empty($val['weight']))
            $xml .= '<weight>' . round($val['weight'] / 1000, 3) . '</weight>';

        // Габариты
        if (!empty($val['length']) && !empty($val['width']) && !empty($val['height']))
            $xml .= '<dimensions>' . sprintf('%s/%s/%s', number_format((int)$val['length'], 2, '.', ''), number_format((int)$val['width'], 2, '.', ''), number_format((int)$val['height'], 2, '.', '')
                    ) . '</dimensions>';

        $xml .= '<currencyId>' . $this->defvalutaiso . '</currencyId>
      <categoryId>' . $val['category'] . '</categoryId>
      ' . $picture . '
      ' . $name . '
      <description>' . $val['description'] . '</description>
</offer>';

        $hook = $this->setHook(__CLASS__, __FUNCTION__, array('xml' => $xml, 'val' => $val));
        if ($hook) {
            $this->xml .= $hook;
        } else {
            $this->xml .= $xml;
        }
    }
    if (!empty($this->xml))
        $this->xml .= '
        </offers>
        ';
}

/**
 * Подвал
 */
function serFooter() {

    // Перехват модуля
    $hook = $this->setHook(__CLASS__, __FUNCTION__);
    if ($hook) {
        $this->xml .= $hook;
    }


    $this->xml .= '</shop>
       </yml_catalog>';
}

/**
 * Компиляция документа, вывод результата
 */
function compile() {
    global $PHPShopBase;

    if (isset($_GET['utf']) or $PHPShopBase->codBase == 'utf-8') {
        $this->encoding = 'utf-8';
        $this->charset = 'utf-8';
    } else {
        $this->charset = 'cp1251';
        $this->encoding = 'windows-1251';
    }

    $this->setHeader();
    $this->setCurrencies();
    $this->setCategories();

    if (!isset($_GET['getall']))
        $this->setDelivery();

    $this->setProducts();
    $this->serFooter();

    if (isset($_GET['utf']) or $PHPShopBase->codBase == 'utf-8') {
        $this->xml = PHPShopString::win_utf8($this->xml);
        $this->charset = 'utf-8';
    } else
        $this->charset = 'cp1251';

    return $this->xml;
}

}
