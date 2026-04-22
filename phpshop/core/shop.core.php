<?php

/**
 * Обработчик товаров
 * @author PHPShop Software
 * @version 2.9
 * @package PHPShopShopCore
 */
class PHPShopShop extends PHPShopShopCore {

    /**
     * Режим отладки
     */
    var $debug = false;

    /**
     * Режим кэширования записей БД, рекомендуется для этого файла true
     */
    var $cache = true;

    /**
     * Имена полей БД, удаляемых из кэша для оптимизации памяти, рекомендуется  array('content','yml_bid_array')
     * @var array
     */
    var $cache_format = array('content', 'yml_bid_array');

    /**
     * Максимальный лимит вывода товаров/каталогов на странице для оптимизации памяти, рекомендуется не более 100
     */
    var $max_item = 200;

    /**
     * Имя функции шаблона вывода фильтров характеристик товара
     */
    var $sort_template, $cat_template = null;
    var $ed_izm = null;
    var $multi_currency_search = false;
    var $parent_title = 'Размер';
    var $parent_color = 'Цвет';
    var $category;
    var $category_array = array();
    var $selected_filter = [];
    var $add_main_product_to_parent = false;
    var $navigation_last = false;

    /**
     * Конструктор
     */
    function __construct() {
        global $PHPShopAnalitica;

        // Размещение
        $this->path = '/' . $GLOBALS['SysValue']['nav']['path'];

        // Список экшенов
        $this->action = array("nav" => array("CID", "UID"));
        parent::__construct();

        $this->PHPShopOrm->cache_format = $this->cache_format;

        $this->page = $this->PHPShopNav->getPage();
        if (strlen($this->page) == 0)
            $this->page = 1;

        // Сортировка по цене среди мультивалютных товаров
        $this->multi_currency_search = $this->PHPShopSystem->getSerilizeParam('admoption.multi_currency_search');

        $this->PHPShopAnalitica = $PHPShopAnalitica;
    }

    /**
     * Форма ячеек с товарами
     * @return string
     */
    function setCell($d1, $d2 = null, $d3 = null, $d4 = null, $d5 = null, $d6 = null, $d7 = null) {

        // Перехват модуля, занесение в память наличия модуля для оптимизации
        if ($this->memory_get(__CLASS__ . '.' . __FUNCTION__, true)) {
            $Arg = func_get_args();
            $hook = $this->setHook(__CLASS__, __FUNCTION__, $Arg);
            if ($hook) {
                return $hook;
            } else
                $this->memory_set(__CLASS__ . '.' . __FUNCTION__, 0);
        }

        return parent::setCell($d1, $d2, $d3, $d4, $d5, $d6, $d7);
    }

    /**
     * Выделение текущего каталога в меню
     */
    function setActiveMenu() {

        $this->set('thisCat', $this->PHPShopCategory->getParam('parent_to'));


        // Верхний уловень каталога
        $cat = $this->get('thisCat');
        if (empty($cat))
            $this->set('thisCat', intval($this->PHPShopNav->getId()));

        // Если 3х вложенность каталога
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
        $PHPShopOrm->cache = $this->cache;
        $PHPShopOrm->debug = $this->debug;
        $PHPShopOrm->cache_format = array('content', 'description');
        $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($this->get('thisCat'))), false, array('limit' => 1));
        $ParentTest = (int) $data['parent_to'];
        $this->set('thisCatName', $data['name']);
        $this->set('parentCatName', $data['name']);

        if (!empty($ParentTest)) {
            $this->set('thisCat', $ParentTest);
            $this->set('thisPodCat', $this->PHPShopCategory->getParam('parent_to'));
        }

        if ($this->PHPShopCategory->getParam('parent_to') == 0) {
            $this->set('elementCatalogBackHide', 'hide');
        } else
            $this->set('elementCatalogBackHide', 'visible-xs');


        // Перехват модуля
        $this->setHook(__CLASS__, __FUNCTION__, $data);
    }

    /**
     * Прикрепленные файлы товара
     * @param array $files
     */
    function file($row) {

        $files = unserialize($row['files']);
        if ($this->PHPShopSystem->getSerilizeParam('admoption.digital_product_enabled') != 1) {
            if (is_array($files)) {
                $this->set('productFiles', '');
                foreach ($files as $cfile) {
                    $this->set('productFiles', '<p><span class="glyphicon glyphicon-paperclip fa fa-paperclip"></span> ', true);
                    $this->set('productFiles', PHPShopText::a($cfile['path'], urldecode($cfile['name']), urldecode($cfile['name']), false, false, '_blank'), true);
                    $this->set('productFiles', '</p>', true);
                }
            } else {
                $this->set('productFiles', __("Нет файлов"));
                $this->set('productFilesStart', PHPShopText::comment());
                $this->set('productFilesEnd', PHPShopText::comment('>'));
            }
        } else
            $this->set('productFiles', __("Файлы будут доступны только после оплаты"));

        // Перехват модуля
        $this->setHook(__CLASS__, __FUNCTION__, $row);
    }

    /**
     * Прикрепленные статьи товара
     * @param string $pages
     */
    function article($row) {

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__, $row, 'START'))
            return true;

        $dis = $data = null;
        if (strstr($row['page'], ','))
            $pages = explode(",", $row['page']);
        else
            $pages = array($row['page']);

        if (!empty($pages) and is_array($pages)) {
            foreach ($pages as $val) {
                if ($val) {
                    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page']);
                    $data = $PHPShopOrm->select(array('name'), array('link' => "='" . $val . "'", 'enabled' => '="1"'));

                    if (is_array($data)) {
                        $this->set('pageLink', $val);
                        $this->set('pageName', $data['name']);

                        // Перехват модуля
                        $this->setHook(__CLASS__, __FUNCTION__, $data, 'MIDDLE');

                        // Подключаем шаблон
                        $dis .= ParseTemplateReturn($this->getValue('templates.product_pagetema_forma'));
                    }
                }
            }
            if (!empty($dis)) {
                $this->set('temaContent', $dis);
                $this->set('temaTitle', __('Статьи по теме'));

                // Вставляем результат в шаблон
                $this->set('pagetemaDisp', ParseTemplateReturn($this->getValue('templates.product_pagetema_list')));
            }
        }

        if (!$this->get('pagetemaDisp')) {
            $this->set('pagetemaDispStart', PHPShopText::comment());
            $this->set('pagetemaDispEnd', PHPShopText::comment('>'));
        }

        // Перехват модуля
        $this->setHook(__CLASS__, __FUNCTION__, $data, 'END');
    }

    /**
     * Вывод средней оценки к товару из отзывов пользователей
     * Функция вынесена в отдельный файл commentRate.php
     * @return mixed
     */
    function comment_rate($row, $type = "") {

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__, array("row" => $row, "type" => "$type")))
            return true;
        $this->doLoadFunction(__CLASS__, __FUNCTION__, array("row" => $row, "type" => "$type"));
    }

    /**
     * Вывод галлереи изображений
     * Функция вынесена в отдельный файл image_gallery.php
     * @return mixed
     */
    function image_gallery($row) {

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__, $row))
            return true;

        $this->doLoadFunction(__CLASS__, __FUNCTION__, $row);
    }

    /**
     * Вывод опций товаров
     * Функция вынесена в отдельный файл option_select.php
     * @return mixed
     */
    function option_select($row) {

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__, $row))
            return true;

        $this->doLoadFunction(__CLASS__, __FUNCTION__, $row);
    }

    /**
     * Экшен выборки подробной информации при наличии переменной навигации UID
     */
    function UID() {
        global $cache_key;

        $this->ajaxTemplate = 'product/main_product_forma_full_ajax.tpl';

        // Перехват модуля в начале функции
        if ($this->setHook(__CLASS__, __FUNCTION__, null, 'START'))
            return true;

        // Безопасность
        if (!PHPShopSecurity::true_num($this->PHPShopNav->getId()))
            return $this->setError404();

        // Выборка данных
        if (isset($_REQUEST['ajax']))
            $where = ['id' => "=" . $this->PHPShopNav->getId()];
        else
            $where = ['id' => "=" . $this->PHPShopNav->getId(), 'parent_enabled' => "='0'"];

        $row = parent::getFullInfoItem(['*'], $where, __CLASS__, __FUNCTION__);

        // Показывать отключенные товары по прямым ссылкам для поисковиков вместо 404 ошибки
        if (empty($row['enabled'])) {
            if ($this->PHPShopSystem->getSerilizeParam('admoption.safe_links') == 1)
                $row['sklad'] = 1;
            else
                unset($row);
        }

        // 404 ошибка
        if (empty($row['id']) or $this->PHPShopSystem->getParam("shop_type") == 2)
            return $this->setError404();

        // Категория
        $this->category = $row['category'];
        $this->PHPShopCategory = new PHPShopCategory($this->category);
        $this->category_name = $this->PHPShopCategory->getName();

        // 404 ошибка мультибазы
        if ($this->errorMultibase($this->category, $row['dop_cat']))
            return $this->setError404();

        // Единица измерения
        if (empty($row['ed_izm']))
            $ed_izm = $this->ed_izm;
        else
            $ed_izm = $row['ed_izm'];

        // Прикрепленные файлы
        $this->file($row);

        // Вес
        $this->set('productWeight', $row['weight']);

        // Большая картинка для schema.org
        $this->set('productImg', $row['pic_big']);

        // Фотогалерея
        $this->image_gallery($row);

        // Таблица характеристик
        $this->sort_table($row);

        // Опции товара
        $this->option_select($row);

        // Рейтинг
        if (empty($row['rate'])) {
            $rate = 5;
            $rate_count = 1;
        } else {
            $rate = $row['rate'];
            $rate_count = $row['rate_count'];
        }
        $this->set('productRatingValue', $rate);
        $this->set('productRatingCount', $rate_count);

        // Оценки из отзывов к товару.
        $this->comment_rate($row);

        // Имя товара
        $this->set('productName', $row['name']);
        $this->set('productNameClean', str_replace(['"', "'"], '', strip_tags($row['name'])));

        // Краткое описание
        $this->set('productContent', $row['description']);

        // Артикул
        $this->set('productArt', $row['uid']);
        if (!empty($row['uid']) and PHPShopParser::checkFile('product/main_product_forma_full_productArt.tpl')) {
            $this->set('productArt', ParseTemplateReturn('product/main_product_forma_full_productArt.tpl'));
        }

        // Опции склада
        $this->checkStore($row);

        $this->set('productSaleReady', $this->lang('productSaleReady'));
        $this->set('productDes', Parser($row['content']));
        $this->set('productPriceMoney', $this->dengi);
        $this->set('productBack', $this->lang('product_back'));
        $this->set('productSale', $this->lang('product_sale'));
        $this->set('productSelect', $this->lang('product_select'));
        //$this->set('productValutaName', $this->currency());
        $this->set('productUid', $row['id']);
        $this->set('productId', $row['id']);
        $this->set('productBestPrice', $this->lang('productBestPrice'));

        // Статьи по теме
        $this->article($row);

        // Подтипы
        $this->parent($row);

        // Перехват модуля в середине функции
        $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');

        // Подключаем шаблон
        // Ajax 
        if (isset($_REQUEST['ajax'])) {

            // JSON для Аналитики
            if ($_REQUEST['ajax'] == 'json') {

                $json = json_encode([
                    'id' => $row['id'],
                    'name' => PHPShopString::win_utf8($row['name'], true),
                    'uid' => PHPShopString::win_utf8($row['uid']),
                    'category' => PHPShopString::win_utf8($this->category_name),
                    'price' => str_replace(' ', '', $this->get('productPrice')),
                    'success' => 1
                ]);

                // Кэш
                $this->PHPShopCache->set($cache_key, gzcompress($json));

                header("Content-Type: application/json");
                exit($json);

                // Быстрый просмотр   
            } else {
                $disp = ParseTemplateReturn($this->ajaxTemplate);
                if (!empty($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system']))
                    $disp = $GLOBALS['PHPShopSeoPro']->AjaxCompile($disp);

                $productview = PHPShopParser::replacedir($disp);

                // Кэш
                $this->PHPShopCache->set($cache_key, gzcompress($productview));

                header('Content-type: text/html; charset=' . $GLOBALS['PHPShopLang']->charset);
                exit($productview);
            }
        } else
            $this->add(ParseTemplateReturn($this->getValue('templates.main_product_forma_full')), true);

        // Однотипные товары
        $this->odnotip($row);

        // Данные родительской категории
        $cat = $this->PHPShopCategory->getValue('parent_to');
        if (!empty($cat)) {
            $parent_category_row = $this->select(array('id,name,parent_to'), array('id' => '=' . $cat), false, array('limit' => 1), __FUNCTION__, array('base' => $this->getValue('base.categories'), 'cache' => 'true'));
        } else {
            $cat = 0;
            $parent_category_row = array(
                'name' => __('Каталог'),
                'id' => 0
            );
        }

        $this->set('catalogCat', $parent_category_row['name']);
        $this->set('catalogId', $parent_category_row['id']);
        $this->set('catalogUId', $cat);
        $this->set('pcatalogId', $this->category);
        $this->set('productName', $row['name']);
        $this->set('productNameClean', str_replace(['"', "'"], '', strip_tags($row['name'])));
        $this->set('catalogCategory', $this->PHPShopCategory->getName());

        // Выделение текущего каталога в меню
        $this->setActiveMenu();

        // Навигация хлебных крошек
        if (!empty($this->navigation_last))
            $this->navigation($this->category, $row['name']);
        else
            $this->navigation($this->category, null);

        // Мета заголовки
        $this->set_meta(array($row, $this->PHPShopCategory->getArray(), $parent_category_row));
        $this->lastmodified = $row['datas'];

        // Перехват модуля в конце функции
        $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');

        // Аналитика
        $row['category'] = $this->category_name;
        $this->PHPShopAnalitica->init(__FUNCTION__, $row);

        // Подключаем шаблон
        $this->parseTemplate($this->getValue('templates.product_page_full'));
    }

    /**
     * Мета-теги
     * @param array $row данные
     */
    function set_meta($row) {

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__, $row))
            return true;

        $this->doLoadFunction(__CLASS__, __FUNCTION__, $row);
    }

    /**
     * Однотипные товары
     * @param array $row массив данных
     */
    function odnotip($row) {
        global $PHPShopProductIconElements;

        $this->odnotip_setka_num = null;
        $this->line = false;
        $this->template_odnotip = 'main_spec_forma_icon';

        // Перехват модуля в начале функции
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $row, 'START');
        if ($hook)
            return true;

        $disp = null;
        $odnotipList = null;

        if (!empty($row['odnotip']))
            $odnotipList = ' id IN (' . $row['odnotip'] . ') ';

        // Режим проверки остатков на складе
        if ($this->PHPShopSystem->getSerilizeParam('admoption.sklad_status') == 2)
            $chek_items = ' and items>0';
        else
            $chek_items = null;


        if (!empty($odnotipList)) {

            // Вставка в центральную часть
            if (PHPShopParser::check($this->getValue('templates.main_product_odnotip_list'), 'productOdnotipList')) {
                if (empty($this->odnotip_setka_num))
                    $this->odnotip_setka_num = $this->PHPShopSystem->getParam('num_vitrina');
                $productOdnotipList = true;
                $this->template_odnotip = 'main_product_forma_' . $this->odnotip_setka_num;
            } else {
                $productOdnotipList = false;
                if (empty($this->odnotip_setka_num))
                    $this->odnotip_setka_num = 1;
                $this->template_odnotip = 'main_spec_forma_icon';
            }

            $PHPShopOrm = new PHPShopOrm();
            $PHPShopOrm->mysql_error = false;
            $PHPShopOrm->debug = $this->debug;
            $result = $PHPShopOrm->query("select * from " . $this->objBase . " where " . $odnotipList . " " . $chek_items . " and  enabled='1' and parent_enabled='0' order BY FIELD (id, " . $row['odnotip'] . ")");

            if ($result)
                while ($row = mysqli_fetch_assoc($result))
                    $data[] = $row;
            
            // Перехват модуля в середине функции
            $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');

            // Сетка товаров
            if (!empty($data) and is_array($data))
                $disp = $PHPShopProductIconElements->seamply_forma($data, $this->odnotip_setka_num, $this->template_odnotip, $this->line);
        }


        if (!empty($disp)) {

            $this->set('productOdnotipList', $disp);
            $this->set('productOdnotip', __('Рекомендуемые товары'));

            $odnotipDisp = ParseTemplateReturn($this->getValue('templates.main_product_odnotip_list'));
            $this->set('odnotipDisp', $odnotipDisp);
        }

        // Перехват модуля в конце функции
        $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');
    }

    /**
     * Вывод подтипов товаров
     * @param array $row массив значений
     */
    function parent($row){

    // Перехват модуля в начале функции
    if($this->setHook(__CLASS__, __FUNCTION__, $row, 'START'))
        return true;

    $this->select_value = array();

    // ИД главного товара
    $this->parent_id = $row['id'];
    $row['parent'] = PHPShopSecurity::CleanOut($row['parent']);

    if (!empty($row['parent'])) {
        $parent = @explode(",", $row['parent']);

        // Учет склада
        $sklad_status = $this->PHPShopSystem->getSerilizeParam('admoption.sklad_status');

        // Убираем добавление в корзину главного товара
        $this->set('ComStartCart', '<!--');
        $this->set('ComEndCart', '-->');

        // Название опции
        PHPShopObj::loadClass('sort');
        $PHPShopParentNameArray = new PHPShopParentNameArray(array('id' => '=' . $this->PHPShopCategory->getParam('parent_title')));
        $parent_title = $PHPShopParentNameArray->getParam($this->PHPShopCategory->getParam('parent_title') . ".name");
        $parent_color = $PHPShopParentNameArray->getParam($this->PHPShopCategory->getParam('parent_title') . ".color");
        if (!empty($parent_title))
            $this->parent_title = $parent_title;
        else
            $this->parent_title = __($this->parent_title);

        if (!empty($parent_color))
            $this->parent_color = $parent_color;
        else
            $this->parent_color = __($this->parent_color);

        // Подтипы из 1С
        if ($this->PHPShopSystem->ifSerilizeParam('1c_option.update_option'))
            $Product = $this->select(array('*'), array('uid' => ' IN ("' . @implode('","', $parent) . '")', 'enabled' => "='1'", 'sklad' => "!='1'"), array('order' => 'num,length(parent),parent'), array('limit' => 300), __FUNCTION__, false, false);
        else
            $Product = $this->select(array('*'), array('id' => ' IN ("' . @implode('","', $parent) . '")', 'enabled' => "='1'", 'sklad' => "!='1'"), array('order' => 'num,length(parent),parent'), array('limit' => 300), __FUNCTION__, false, false);

        // Цена главного товара
        if (is_array($Product) and ! empty($row['price']) and empty($row['priceSklad']) and ( !empty($row['items']) or ( empty($row['items']) and $sklad_status == 1))) {

            // Главный товар в списке подтипов
            if ($this->add_main_product_to_parent)
                $this->select_value[] = array($row['name'] . " -  (" . $this->price($row) . "  " . $this->currency . ')', $row['id'], $row['items'], $row);
        }

        // Выпадающий список товаров
        if (is_array($Product))
            foreach ($Product as $p) {
                if (!empty($p)) {

                    // Если товар на складе
                    if (empty($p['priceSklad']) and ( !empty($p['items']) or ( empty($p['items']) and $sklad_status == 1))) {
                        $price = $this->price($p);

                        // Перехват модуля в середине функции, занесение в память наличия модуля для оптимизации
                        if ($this->memory_get(__CLASS__ . '.' . __FUNCTION__, true)) {

                            $hook = $this->setHook(__CLASS__, __FUNCTION__, $p, 'MIDDLE');
                            if ($hook) {
                                $this->select_value[] = $hook;
                            } else {
                                $this->memory_set(__CLASS__ . '.' . __FUNCTION__, 0);
                                $this->select_value[] = array($p['name'] . ' -  (' . $price . ' ' . $this->currency . ')', $p['id'], $p['items'], $p);
                            }
                        } else
                            $this->select_value[] = array($p['name'] . ' -  (' . $price . ' ' . $this->currency . ')', $p['id'], $p['items'], $p);
                    }
                }
            }

        $this->set('productPrice', $this->price($row));
        $productPriceNew = $this->price($row, true);
        if ((float) $productPriceNew > 0) {
            $this->set(array('productPriceOld', 'productPriceRub'), PHPShopText::strike($productPriceNew . " " . $this->currency, $this->format));
        }

        if (count($this->select_value) > 0) {
            $this->set('parentList', PHPShopText::select('parentId', $this->select_value, "; max-width:300px;"));
            $this->set('productParentList', ParseTemplateReturn("product/product_odnotip_product_parent.tpl"));
            $this->set('optionsDisp', null);
        } else
            $this->set('elementCartHide', 'hide hidden d-none');


        // Перехват модуля в конце функции
        $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');
    }
}

/**
 * Экшен выборки подробной информации при наличии переменной навигации CID
 */
function CID() {

    if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
        return true;

    // ID категории
    $this->category = PHPShopSecurity::TotalClean($this->PHPShopNav->getId(), 1);
    $this->PHPShopCategory = new PHPShopCategory(intval($this->category));
    $this->category_name = $this->PHPShopCategory->getName();

    // Запрос на подкаталоги
    $parent_category_row = $this->select(array('*'), array('parent_to' => '=' . $this->category . "  or dop_cat LIKE '%#" . intval($this->category) . "#%'"), false, array('limit' => 1), __FUNCTION__, array('base' => $this->getValue('base.categories')));


    // Перехват модуля
    $this->setHook(__CLASS__, __FUNCTION__, $parent_category_row, 'MIDDLE');

    // Вывод подкаталогов
    if (!empty($parent_category_row['id'])) {

        $depth = $this->PHPShopSystem->getSerilizeParam('admoption.catlist_depth');
        if (empty($depth)) {
            $depth = 2;
        }

        $this->category_array = array_column($this->PHPShopCategory->getChildrenCategories($depth, ['id', 'parent_to']), 'id');

        // Ввывода товаров из подкаталогов
        if (is_array($this->category_array) and $this->PHPShopSystem->ifSerilizeParam('admoption.catlist_enabled') and PHPShopParser::check($this->getValue('templates.product_page_list'), 'ProductCatalogContent')) {
            $this->CID_Product(null, true);
        } else {
            // Вывод только каталогов
            if ($this->page > 1)
                return $this->setError404();
            else
                $this->CID_Category();
        }
    } // Вывод товаров
    else {
        $this->CID_Product();
    }
}

/**
 * Генерация SQL запроса со сложными фильтрами и условиями
 * Функция вынесена в отдельный файл query_filter.php
 * @return mixed
 */
function query_filter($where = false, $v = false) {

    // Перехват модуля
    $hook = $this->setHook(__CLASS__, __FUNCTION__);
    if (!empty($hook))
        return $hook;

    return $this->doLoadFunction(__CLASS__, __FUNCTION__);
}

/**
 * Вывод таблицы характеристик товара
 * Функция вынесена в отдельный файл sort_table.php
 * @param array $row массив значений
 * @return mixed
 */
function sort_table($row) {

    // Перехват модуля
    if ($this->setHook(__CLASS__, __FUNCTION__, $row))
        return true;

    $this->doLoadFunction(__CLASS__, __FUNCTION__, $row);
}

/**
 * Вывод списка товаров
 * @param integer $category ИД категории
 * @param boolean $mode формат вывода данных раздела подкаталоги и данные основного раздела\данные категории с товаром
 */
function CID_Product($category = null, $mode = false) {
    global $cache_key;

    if (!empty($category))
        $this->category = intval($category);

    // Путь для навигации
    $this->objPath = './CID_' . $this->category . '_';

    // Количество ячеек для вывода товара
    $this->cell = $cell = $this->calculateCell($this->category, $this->PHPShopCategory->getParam('num_row'));

    // Перехват модуля в начале
    if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
        return true;

    // 404 если каталога не существует или мультибаза
    if (empty($this->category_name) or $this->errorMultibase($this->category) or $this->PHPShopCategory->getParam('skin_enabled') == 1 or $this->PHPShopSystem->getParam("shop_type") == 2)
        return $this->setError404();

    $this->set('catalogName', $this->category_name);

    // Фильтр сортировки
    $order = $this->query_filter();

    // Кол-во товаров на странице
    $num_cow = $this->PHPShopCategory->getParam('num_cow');

    if (!empty($num_cow))
        $this->num_row = $num_cow;
    else if ($this->PHPShopSystem->getValue('num_row') > 0)
        $this->num_row = $this->PHPShopSystem->getValue('num_row');
    else // если 0 делаем по формуле кол-во колонок * 2 строки.
        $this->num_row = (6 - $cell) * $cell;

    // Коррекция кол-ва товаров на странице
    if ((int) $this->cell > 0) {
        $check_cell = $this->num_row % $this->cell;
        if ($this->num_row % $this->cell !== 0)
            $this->num_row = $this->num_row - $check_cell;
    }

    $this->dataArray = parent::getListInfoItem(false, false, false, __CLASS__, __FUNCTION__, $order['sql']);

    if (!is_array($this->dataArray)) {

        if ($this->page > 1) {
            $this->category_name = null;
            return $this->setError404();
        }

        if (isset($_POST['ajax'])) {
            if (isset($_POST['json'])) {
                header('Content-type: application/json; charset=UTF-8');

                $json = json_encode([
                    'products' => PHPShopString::win_utf8(PHPShopText::h4($this->lang('empty_product_list'), 'empty_product_list')),
                    'pagination' => PHPShopString::win_utf8($this->get('productPageNav')),
                ]);

                // Кэш
                $this->PHPShopCache->set($cache_key, gzcompress($json));
                exit($json);
            }
            header('Content-type: text/html; charset=' . $GLOBALS['PHPShopLang']->charset);

            exit(PHPShopText::h4($this->lang('empty_product_list'), 'empty_product_list'));
        }

        if ($this->PHPShopSystem->ifSerilizeParam('admoption.filter_cache_enabled'))
            $this->update_cache('filter');
    }

    // Пагинатор
    if (is_array($this->dataArray))
        $count = count($this->dataArray);
    else
        $count = 0;
    $this->setPaginator($count, $order['sql']);

    if (empty($count))
        $this->set('empty_product_list', true);

    if ($this->PHPShopSystem->getSerilizeParam('admoption.filter_cache_enabled') == 1 && $this->PHPShopSystem->getSerilizeParam('admoption.filter_products_count') == 1)
        $this->update_cache('count_products');


    $this->set('pcatalogId', $this->category);

    // Добавляем в дизайн ячейки с товарами
    $grid = $this->product_grid($this->dataArray, $cell);

    // Ajax Paginator
    if (!empty($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'])) {
        $this->set('page_prefix', '-');
        $seourlpro = true;
    } elseif (!empty($GLOBALS['SysValue']['base']['seourl']['seourl_system'])) {
        $this->set('seomod', $GLOBALS['seourl_pref'] . PHPShopString::toLatin($this->category_name));
        $this->set('page_prefix', '_');
    } else
        $this->set('page_prefix', '_');

    $this->set('page_postfix', $_SERVER['QUERY_STRING']);
    $this->set('max_page', $this->max_page);
    if (isset($_POST['ajax'])) {

        // Поддержка модуля SeoUrlPro
        if (!empty($seourlpro))
            $grid = $GLOBALS['PHPShopSeoPro']->AjaxCompile($grid);

        if (isset($_POST['json'])) {
            header('Content-type: application/json; charset=UTF-8');

            $json = json_encode([
                'products' => PHPShopString::win_utf8(PHPShopParser::replacedir($this->separator . $grid)),
                'pagination' => PHPShopString::win_utf8($this->get('productPageNav')),
                'filter' => $this->update_filter($order['sql']),
                'logic' => (int) $this->PHPShopSystem->getSerilizeParam('admoption.filter_logic'),
                    /* 'sql' => $order['sql'] */
            ]);

            // Кэш
            $this->PHPShopCache->set($cache_key, gzcompress($json));
            exit($json);
        }
        header('Content-type: text/html; charset=' . $GLOBALS['PHPShopLang']->charset);
        exit(PHPShopParser::replacedir($this->separator . $grid));
    }

    if ((empty($grid) and empty($mode)) or ( empty($grid) and ! empty($_GET['v']))) {
        if (isset($_POST['json'])) {
            header('Content-type: application/json; charset=UTF-8');
            exit(json_encode([
                'products' => PHPShopString::win_utf8(PHPShopText::h4($this->lang('empty_product_list'), 'empty_product_list')),
                'pagination' => PHPShopString::win_utf8($this->get('productPageNav'))
            ]));
        }
        $grid = PHPShopText::h4($this->lang('empty_product_list'), 'empty_product_list');
        $this->set('hideSort', 'hide');
    }
    $this->add($grid, true);

    // Фильтр товаров
    PHPShopObj::loadClass('sort');
    $PHPShopSort = new PHPShopSort($this->category, $this->PHPShopCategory->getParam('sort'), true, $this->sort_template, isset($_GET['v']) ? $_GET['v'] : false, true, true, true, $this->cat_template);

    // Ajax Filter
    if (isset($_REQUEST['ajaxfilter'])) {

        header('Content-type: text/html; charset=' . $GLOBALS['PHPShopLang']->charset);
        $ajaxfilter = $PHPShopSort->display();

        // Кэш
        $this->PHPShopCache->set($cache_key, gzcompress($ajaxfilter));

        exit($PHPShopSort->display());
    }

    $this->set('vendorDisp', $PHPShopSort->display());
    $this->set('vendorCatDisp', $PHPShopSort->categories());

    if ($this->category_array) {
        $dop_cats = '';
        foreach ($this->category_array as $dopCat) {
            $dop_cats .= ' OR dop_cat LIKE \'%#' . $dopCat . '#%\' ';
        }
        $categories_str = implode(",", $this->category_array);
        $where = array('(category' => ' IN (' . $categories_str . ') ' . $dop_cats . ')', 'enabled' => "='1'", 'parent_enabled' => "='0'", $this->PHPShopSystem->getPriceColumn() => '>1');
    } else
        $where = array('(category' => '=' . intval($this->category) . ' OR dop_cat LIKE \'%#' . $this->category . '#%\')', 'enabled' => "='1'", 'parent_enabled' => "='0'", $this->PHPShopSystem->getPriceColumn() => '>1');


    $group = null;

    // Максимальная и минимальная цена для всех товаров
    if (strlen($this->PHPShopCategory->getParam('sort')) > 5) {

        if ($this->multi_currency_search)
            $search_where = array('min(price_search) as min', 'max(price_search) as max');
        else
            $search_where = array('max(' . $this->PHPShopSystem->getPriceColumn() . ') as max', 'min(' . $this->PHPShopSystem->getPriceColumn() . ') as min');

        $data = $this->select($search_where, $where, $group);
        $data['max'] = intval($data['max']);
        $data['min'] = intval($data['min']);


        // Проверка промоакций
        $promotion = (new PHPShopPromotions())->promotion_get_discount(['category' => $this->category]);

        if (!empty($promotion['action'])) {

            // %
            if (!empty($promotion['percent'])) {

                // Повышение
                if ($promotion['status'] == 1) {
                    $data['max'] += $data['max'] * $promotion['percent'];
                    $data['min'] += $data['min'] * $promotion['percent'];
                }
                // Понижение
                else {
                    $data['max'] -= $data['max'] * $promotion['percent'];
                    $data['min'] -= $data['min'] * $promotion['percent'];
                }
            }
            // Сумма
            elseif (!empty($promotion['sum'])) {

                // Повышение
                if ($promotion['status'] == 1) {
                    $data['max'] += $promotion['sum'];
                    $data['min'] += $promotion['sum'];
                }
                // Понижение
                else {
                    $data['max'] -= $promotion['sum'];
                    $data['min'] -= $promotion['sum'];
                }
            }
        }

        // Наценка для поиска по цене
        if (!empty((int) $this->PHPShopSystem->getParam('percent'))) {
            $data['max'] = ($data['max'] + (($data['max'] * intval($this->PHPShopSystem->getParam('percent'))) / 100));
            $data['min'] = ($data['min'] + (($data['min'] * intval($this->PHPShopSystem->getParam('percent'))) / 100));
        }

        $this->price_max = intval($data['max']) + 6;
        $this->price_min = intval($data['min']);

        if ($this->price_min == $this->price_max)
            $this->price_min = intval($this->price_max / 2);

        $this->set('price_max', intval($this->price_max));
        $this->set('price_min', intval($this->price_min));
    }

    // Фильтр сортировки по складам
    if ($this->PHPShopSystem->ifSerilizeParam('admoption.sklad_sort_enabled')) {
        if (is_array($this->warehouse)) {


            $this->warehouse[0] = __('Все склады');

            $warehouse_sort = null;
            foreach ($this->warehouse as $warehouse_id => $warehouse_name) {
                $this->set('warehouse_id', $warehouse_id);
                $this->set('warehouse_name', $warehouse_name);

                if ($_GET['w'] == $warehouse_id)
                    $this->set('warehouse_active', 'active');
                else
                    $this->set('warehouse_active', '');


                $warehouse_sort .= parseTemplateReturn('filter/warehouse.tpl');
            }

            $this->set('warehouse_sort', $warehouse_sort);
        }
    }

    // Перехват модуля в конце функции
    $this->setHook(__CLASS__, __FUNCTION__, $this->dataArray, 'END');

    // Подключаем функцию вывода подкаталогов, или информации о каталоге
    if ($mode) {
        $this->set('ProductCatalogContent', $this->CID_Category(true));
    } else
        $this->set('ProductCatalogContent', $this->catalog_content());

    // Аналитика
    $this->PHPShopAnalitica->init(__FUNCTION__, array('name' => $this->category_name, 'pic_small' => $this->PHPShopCategory->getParam('icon')));

    // Подключаем шаблон
    if (!empty($grid))
        $this->parseTemplate($this->getValue('templates.product_page_list'));
    else if ($mode)
        $this->parseTemplate($this->getValue('templates.catalog_info_forma'));
}

/**
 * Данные каталога с товаром
 */
function catalog_content() {
    global $PHPShopShopCatalogElement;

    // Родительская категория
    $cat = $this->PHPShopCategory->getParam('parent_to');

    // Данные родительской категории
    if (!empty($cat)) {
        $parent_category_row = $this->select(array('id,name,parent_to'), array('id' => '=' . $cat), false, array('limit' => 1), __FUNCTION__, array('base' => $this->getValue('base.categories'), 'cache' => 'true'));
    } else {
        $cat = $this->category;
        $parent_category_row = array();
    }

    // Перевод подкаталогов в родительские для витрин если один родитель
    if (is_array($PHPShopShopCatalogElement->tree_array_host)) {
        foreach ($PHPShopShopCatalogElement->tree_array_host[0]['sub'] as $k => $v) {
            if ($k == $this->category) {
                $cat = 0;
            }
        }
    }


    if (!empty($parent_category_row['name']))
        $this->set('catalogCat', $parent_category_row['name']);

    $this->set('catalogCategory', $this->PHPShopCategory->getName());
    $this->set('productId', $this->category);
    $this->set('catalogUId', $cat);
    
    // Выделение текущего каталог в меню
    $this->setActiveMenu();

    // Мета заголовки
    $this->set_meta(array($this->PHPShopCategory->getArray(), $parent_category_row, $this->selected_filter));

    // Дублирующая навигация
    $this->other_cat_navigation($cat);

    // Навигация хлебных крошек для новых шаблонов
    $this->navigation($cat, $this->PHPShopCategory->getName());

    // Описание каталога
    $this->set('catalogContent', Parser($this->PHPShopCategory->getContent()));

    // Перехват модуля 
    $this->setHook(__CLASS__, __FUNCTION__, $parent_category_row);

    // Возвращаем шаблон
    $dis = ParseTemplateReturn($this->getValue('templates.product_catalog_content'));

    return $dis;
}

/**
 * Альтернативная навигация категорий с списке товаров
 * @param Int $parent ИД родителя категории
 */
function other_cat_navigation($parent) {

    // Перехват модуля в начале функции
    $this->setHook(__CLASS__, __FUNCTION__, $parent, 'START');

    // Имя родителя
    $dis = PHPShopText::h1($this->get('catalogCat'));

    $dataArray = array();

    // Использование глобального кэша
    if (!empty($GLOBALS['Cache'][$GLOBALS['SysValue']['base']['categories']]) and is_array($GLOBALS['Cache'][$GLOBALS['SysValue']['base']['categories']]))
        foreach ($GLOBALS['Cache'][$GLOBALS['SysValue']['base']['categories']] as $val) {
            if ($val['parent_to'] == $parent and $val['skin_enabled'] != 1)
                $dataArray[] = $val;
        }

    if (count($dataArray) > 1) {
        foreach ($dataArray as $row) {

            if ($row['id'] == $this->category)
                $class = 'activ_catalog';
            else
                $class = null;

            $dis .= PHPShopText::a('/shop/CID_' . $row['id'] . '.html', $row['name'], false, false, false, false, $class);
            $dis .= ' | ';
        }
    } // Выборка данных из БД при отсутствии данных в кэше
    else {
        $PHPShopOrm = new PHPShopOrm($this->getValue('base.categories'));
        $PHPShopOrm->debug = $this->debug;
        $PHPShopOrm->cache = false;
        $dataArray = $PHPShopOrm->select(array('*'), array('parent_to' => '=' . $parent, 'skin_enabled' => " != '1'"), array('order' => 'num'), array('limit' => 100));
        if (is_array($dataArray))
            foreach ($dataArray as $row) {

                if ($row['id'] == $this->category)
                    $class = 'activ_catalog';
                else
                    $class = null;

                $dis .= PHPShopText::a('/shop/CID_' . $row['id'] . '.html', $row['name'], false, false, false, false, $class);
                $dis .= ' | ';
            }
    }

    // Перехват модуля в конце функции
    $hook = $this->setHook(__CLASS__, __FUNCTION__, $parent, 'END');
    if ($hook)
        return true;


    $this->set('DispCatNav', substr($dis, 0, strlen($dis) - 2));
}

/**
 * Вывод списка категорий
 * @param $mode boolean режим вывода. Подключение шаблона или возврат работы в переменную
 */
function CID_Category($mode = false) {

    // шаблон вывода категорий с иконками
    $this->cid_cat_with_foto_template = 'catalog/cid_category.tpl';

    // Перехват модуля в начале функции
    $hook = $this->setHook(__CLASS__, __FUNCTION__, false, 'START');
    if ($hook)
        return true;

    // ID категории
    $this->category = PHPShopSecurity::TotalClean($this->PHPShopNav->getId(), 1);
    $this->PHPShopCategory = new PHPShopCategory($this->category);

    // Скрытый каталог
    if ($this->PHPShopCategory->getParam('skin_enabled') == 1 or $this->errorMultibase($this->category) or $this->PHPShopSystem->getParam("shop_type") == 2)
        return $this->setError404();

    // Название категории
    $this->category_name = $this->PHPShopCategory->getName();

    // Условия выборки
    $where['skin_enabled'] = "!='1'";
    $where['skin_enabled'] .= ' and (parent_to =' . $this->category . " or dop_cat LIKE '%#" . $this->category . "#%')";

    // Мультибаза
    if (defined("HostID"))
        $where['servers'] = " REGEXP 'i" . HostID . "i'";
    elseif (defined("HostMain"))
        $where['skin_enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

    // Сортировка каталога
    switch ($this->PHPShopCategory->getValue('order_to')) {
        case(1):
            $order_direction = "";
            break;
        case(2):
            $order_direction = " desc";
            break;
        default:
            $order_direction = "";
            break;
    }
    switch ($this->PHPShopCategory->getValue('order_by')) {
        case(1):
            $order = array('order' => 'num, name' . $order_direction);
            break;
        case(2):
            $order = array('order' => 'num, name' . $order_direction);
            break;
        case(3):
            $order = array('order' => 'num' . $order_direction);
            break;
        default:
            $order = array('order' => 'num' . $order_direction);
            break;
    }

    // Выборка данных
    $PHPShopOrm = new PHPShopOrm($this->getValue('base.categories'));
    $PHPShopOrm->debug = $this->debug;
    $PHPShopOrm->cache = $this->cache;
    $dis = null;
    $dataArray = $PHPShopOrm->select(array('*'), $where, $order, array('limit' => $this->max_item));
    if (is_array($dataArray))
        if (PHPShopParser::checkFile($this->cid_cat_with_foto_template)) {
            foreach ($dataArray as $row) {
                if (empty($row['icon']))
                    $row['icon'] = $this->no_photo;
                $this->set('podcatalogIcon', $this->setImage($row['icon']));
                $this->set('podcatalogId', $row['id']);
                $this->set('podcatalogName', $row['name']);
                $this->set('podcatalogDesc', $row['content']);
                $this->set('podcatalogColor', (int) $row['color']);

                $dis .= ParseTemplateReturn($this->cid_cat_with_foto_template);
            }
            $disp = $dis;
        } else {
            foreach ($dataArray as $row) {
                $dis .= PHPShopText::li($row['name'], '/shop/CID_' . $row['id'] . '.html');
            }
            $disp = PHPShopText::ul($dis);
        }

    $this->set('catalogContent', Parser($this->PHPShopCategory->getContent()));
    $this->set('catalogName', $this->category_name);
    $this->set('thisCat', $this->PHPShopNav->getId());

    // Иконки подкаталогов
    if ($this->PHPShopCategory->getValue('podcatalog_view') == 0)
        $this->set('catalogList', $disp);

    // Данные родительской категории для meta
    $cat = $this->PHPShopCategory->getValue('parent_to');
    if (!empty($cat)) {
        $parent_category_row = $this->select(array('id,name,parent_to'), array('id' => '=' . $cat), false, array('limit' => 1), __FUNCTION__, array('base' => $this->getValue('base.categories'), 'cache' => 'true'));
    } else {
        $cat = 0;
        $parent_category_row = array(
            'name' => __('Каталог'),
            'id' => 0
        );
    }

    // Выделение текущего каталог в меню
    $this->setActiveMenu();

    // Мета заголовки
    $this->set_meta(array($this->PHPShopCategory->getArray(), $parent_category_row, $this->selected_filter));

    // Навигация хлебных крошек для новых шаблонов
    $this->navigation($this->PHPShopCategory->getParam('parent_to'), $this->category_name);

    // Перехват модуля в конце функции
    $this->setHook(__CLASS__, __FUNCTION__, $dataArray, 'END');

    // Подключаем шаблон
    if ($mode == true)
        return ParseTemplateReturn($this->getValue('templates.catalog_info_forma'));
    else
        $this->parseTemplate($this->getValue('templates.catalog_info_forma'));
}

/**
 * Экшен 404 ошибки по ссылке /shop/
 */
function index() {
    $this->setError404();
}

/**
 * Блокировкам пустых значений и пересчет количества фильтра
 */
function update_filter($where) {

    if ($this->PHPShopSystem->ifSerilizeParam("admoption.filter_cache_enabled")) {
        $this->PHPShopOrm->sql = 'select vendor_array from ' . $this->SysValue['base']['products'] . ' where ' . $where;
        $data = $this->PHPShopOrm->select();

        $sort = [];
        if (is_array($data)) {
            foreach ($data as $row) {
                $vendor_array = unserialize($row['vendor_array']);

                if (is_array($vendor_array)) {
                    foreach ($vendor_array as $k => $v) {
                        $sort[$k . '-' . $v[0]] ++;
                    }
                }
            }
        }

        return $sort;
    }
}

/*
 * Обновление кеша характеристик для фильтра
 */

function update_cache($type) {
    if (is_array($_REQUEST['v']) and count($_REQUEST['v']) == 1) {
        foreach ($_REQUEST['v'] as $sort_id => $sort_value) {

            if (count($sort_value) == 1) {
                $period = ($this->PHPShopSystem->getSerilizeParam('admoption.filter_cache_period')) * 86400;
                if (empty($period))
                    $period = 259200;

                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
                $sort_cache = $PHPShopOrm->select(array('sort_cache', 'sort_cache_created_at'), array('id=' => $this->category));

                if ((int) ($sort_cache['sort_cache_created_at'] + $period) > time()) {
                    $cache = unserialize($sort_cache['sort_cache']);
                    if ($type == 'filter') {

                        if (is_array($cache['filter_cache'][$sort_id]))
                            array_push($cache['filter_cache'][$sort_id], PHPShopSecurity::TotalClean($sort_value[0], 5));
                        else
                            $cache['filter_cache'][$sort_id] = array(PHPShopSecurity::TotalClean($sort_value[0], 5));

                        $PHPShopOrm->update(
                                array(
                            'sort_cache_new' => serialize($cache)
                                ), array(
                            'id=' => $this->category
                        ));
                    } elseif ($type == 'count_products') {
                        if (is_array($_REQUEST['v']))
                            foreach ($_REQUEST['v'] as $k => $v)
                                if (is_array($v))
                                    foreach ($v as $key => $val)
                                        $cache['products'][$k][intval($val)] = (int) $this->num_page;
                        $PHPShopOrm->update(
                                array(
                            'sort_cache_new' => serialize($cache)), array(
                            'id=' => $this->category
                        ));
                    }
                } else {

                    if ($type == 'filter') {
                        // Очистка массива
                        if (is_array($_REQUEST['v']))
                            foreach ($_REQUEST['v'] as $k => $v)
                                if (is_array($v))
                                    foreach ($v as $key => $val)
                                        $cache['filter_cache'][$k][$key] = intval($val);

                        $PHPShopOrm->update(
                                array(
                            'sort_cache_new' => serialize($cache),
                            'sort_cache_created_at_new' => time()), array(
                            'id=' => $this->category
                        ));
                    }elseif ($type == 'count_products') {

                        if (is_array($_REQUEST['v']))
                            foreach ($_REQUEST['v'] as $k => $v)
                                if (is_array($v))
                                    foreach ($v as $key => $val)
                                        $cache['products'][$k][intval($val)] = (int) $this->num_page;
                        $PHPShopOrm->update(
                                array(
                            'sort_cache_new' => serialize($cache),
                            'sort_cache_created_at_new' => time()), array(
                            'id=' => $this->category
                        ));
                    }
                }
            }
        }
    }
}

}

?>