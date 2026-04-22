<?php

/**
 * Библиотека AvitoXML
 * @author PHPShop Software
 * @version 1.3
 * @package PHPShopClass
 */
class AvitoXML {

    var $xml = null;
    private $categories = [];

    /**
     * память событий модулей
     * @var bool
     */
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

        // Исходное изображение
        if ($this->PHPShopSystem->ifSerilizeParam('admoption.image_save_source'))
            $this->image_source = true;
        else
            $this->image_source = false;

        // Настройки
        $this->options = (new PHPShopOrm('phpshop_modules_avito_system'))->getOne();

        // Цены
        $this->fee_type = $this->options['fee_type'];
        $this->fee = $this->options['fee'];
        $this->price = $this->options['price'];

        // Характеристики
        $PHPShopOrm = new PHPShopOrm();
        $PHPShopOrm->sql = 'SELECT a.attribute_avitoapi,a.name as param, b.id, b.name FROM ' . $GLOBALS['SysValue']['base']['sort_categories'] . ' AS a LEFT JOIN ' . $GLOBALS['SysValue']['base']['sort'] . ' AS b ON a.id = b.category where a.attribute_avitoapi !="" limit 50000';
        $param = $PHPShopOrm->select();
        if (is_array($param)) {
            $this->param = true;
            foreach ($param as $par) {
                $this->param_array[$par['id']] = $par;
            }
        }

        $this->description_template = $this->options['stop_words'];
        $this->export_items_enabled = $this->options['export_items_enabled'];
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


        $image_url = $this->options['image_url'];
        if (empty($image_url))
            $image_url = $_SERVER['SERVER_NAME'];

        // Главное изображение
        $pic_main_b = str_replace(".", "_big.", $pic_main);
        if (!$this->image_source or ! file_exists($_SERVER['DOCUMENT_ROOT'] . $pic_main_b))
            $pic_main_b = $pic_main;

        if (!empty($pic_main_b)) {
            if (!strstr($pic_main_b, 'https'))
                $pic_main_b = 'https://' . $image_url . $pic_main_b;

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
                    $name_b = 'https://' . $image_url . $name_b;

                $images[] = $name_b;
            }
        }

        // Карта проезда
        $map_url = $this->options['map_url'];

        if (!empty($map_url)) {
            if (strstr($map_url, ','))
                $map_img = explode(',', $map_url);
            else
                $map_img[] = $map_url;

            if (is_array($map_img))
                foreach ($map_img as $map)
                    $images[] = trim($map);
        }

        if (is_array($images))
            foreach ($images as $image) {

                // Видео
                if (in_array(pathinfo($image, PATHINFO_EXTENSION), ['mp4', 'mov']))
                    $xml .= '<VideoURL>' . $image . '</VideoURL>';
                // Изображение
                else
                    $xml .= '<Image url="' . $image . '"/>';
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

        $where['category_avitoapi'] = '!=""';

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
                    $Catalog[$row['id']]['category_avitoapi'] = $row['category_avitoapi'];
                }
            }

        $this->Catalog = $Catalog;
        return $Catalog;
    }

    /**
     * @param array $product
     * @return float
     */
    private function getProductPrice($price, $baseinputvaluta) {

        $PHPShopValuta = new PHPShopValutaArray();
        $currencies = $PHPShopValuta->getArray();
        $defvaluta = $this->PHPShopSystem->getValue('dengi');
        $percent = $this->PHPShopSystem->getValue('percent');
        $format = $this->PHPShopSystem->getSerilizeParam('admoption.price_znak');

        //Если валюта отличается от базовой
        if ($baseinputvaluta !== $defvaluta) {
            $vkurs = $currencies[$baseinputvaluta]['kurs'];

            // Если курс нулевой или валюта удалена
            if (empty($vkurs))
                $vkurs = 1;

            // Приводим цену в базовую валюту
            $price = $price / $vkurs;
        }

        return round($price + (($price * $percent) / 100), (int) $format);
    }

    /**
     * Данные по товарам. Оптимизировано.
     * @return array массив товаров
     */
    function product() {

        $Products = array();
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);

        $where = " export_avito='1' and";

        // Мультибаза
        $queryMultibase = $this->queryMultibase();
        if (!empty($queryMultibase))
            $where .= ' ' . $queryMultibase;
        
        // Наличие
        if(!empty($this->export_items_enabled))
            $where.=" items>0 and sklad='0' and ";

        $result = $PHPShopOrm->query("select * from " . $GLOBALS['SysValue']['base']['products'] . " where " . $where . " enabled='1' and parent_enabled='0'");
        if ($result)
            while ($row = mysqli_fetch_array($result)) {

                // Пропуск неопределенных товаров
                if (in_array($row['category'], array(1000001, 1000004, 0)))
                    continue;

                $id = $row['id'];
                $name = trim(strip_tags($row['name']));
                $category = $row['category'];
                $uid = $row['uid'];

                // price columns
                if (!empty($row['price_avito'])) {
                    $price = $row['price_avito'];
                } elseif (!empty($row['price' . (int) $this->price])) {
                    $price = $row['price' . (int) $this->price];
                } else
                    $price = $row['price'];

                $price = $this->getProductPrice($price, $row['baseinputvaluta']);

                // Наценка
                if ($this->fee > 0) {
                    if ($this->fee_type == 1) {
                        $price = $price - ($price * $this->fee / 100);
                    } else {
                        $price = $price + ($price * $this->fee / 100);
                    }
                }

                // Округление
                $price = round($price, -1);

                $content = $this->replaceDescriptionVariables($row);

                // Чистка тегов
                $content = '<![CDATA[' . trim(strip_tags($content, '<p><strong><i><ul><li><br><em><ol>')) . ']]>';

                // Замена символов
                if (!empty($this->description_template))
                    $content = str_replace(explode(',', $this->description_template), '', $content);

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
                    "weight" => $row['weight'],
                    "length" => $row['length'],
                    "width" => $row['width'],
                    "height" => $row['height'],
                    "uid" => $uid,
                    "content" => $content,
                    "prod_seo_name" => $row['prod_seo_name'],
                    "vendor_code" => $row['vendor_code'],
                    "vendor_name" => $row['vendor_name'],
                    "items" => $row['items'],
                    "price_avito" => round($row['price_avito'], (int) $this->format),
                    "baseinputvaluta" => $row['baseinputvaluta'],
                    "sklad" => $row['sklad'],
                    "name_avito" => $row['name_avito'],
                    "export_avito_id" => $row['export_avito_id'],
                    "vendor_array" => unserialize($row['vendor_array'])
                );

                $Products[] = $array;
            }
        return $Products;
    }

    /**
     * Заголовок
     */
    function setHeader() {
        $this->xml .= '<?xml version="1.0" encoding="UTF-8"?>
        <Ads formatVersion="3" target="Avito.ru">';
    }

    /**
     * Категории
     */
    function setCategories() {
        $this->category();
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


        $this->xml .= null;
        $product = $this->product();

        // Категори Авито
        $CategoryAvitoArray = new PHPShopCategoryAvitoArray();

        foreach ($product as $val) {

            $AdType_enabled = $Condition_enabled = false;

            // Ключ обновления
            if ($this->options['type'] == 1)
                $id = $val['id'];
            else
                $id = $val['uid'];

            // Имя товара
            if (!empty($val['name_avito']))
                $val['name'] = $val['name_avito'];

            $xml = '<Ad>
    <Address>' . PHPShopString::win_utf8($this->options['address']) . '</Address>
    <Latitude>' . $this->options['latitude'] . '</Latitude>
    <Longitude>' . $this->options['longitude'] . '</Longitude>
    <Id>' . $id . '</Id>
    <AvitoId>' . $val['export_avito_id'] . '</AvitoId>
    <ManagerName>' . PHPShopString::win_utf8($this->options['manager']) . '</ManagerName>
    <ContactPhone>' . PHPShopString::win_utf8($this->options['phone']) . '</ContactPhone>
    <Title>' . PHPShopString::win_utf8($this->cleanStr($val['name'])) . '</Title>
    <Description>' . PHPShopString::win_utf8($val['content']) . '</Description>
    <Price>' . $val['price'] . '</Price>';

            // Доступность
            if ($val['sklad'] == 1)
                $availability = PHPShopString::win_utf8('Под заказ');
            else
                $availability = PHPShopString::win_utf8('В наличии');

            $xml .= '<Availability>' . $availability . '</Availability>';

            // Категория и тип
            $slug = $this->Catalog[$val['category']]['category_avitoapi'];
            $parent = $CategoryAvitoArray->getParam($slug . '.parent_to');
            $category_parent = $CategoryAvitoArray->getParam($parent . '.parent_to');
            $ProductType = $CategoryAvitoArray->getParam($parent . '.name');
            $GoodsSubType = $CategoryAvitoArray->getParam($slug . '.name');
            $GoodsType = $CategoryAvitoArray->getParam($category_parent . '.name');
            $Category = $CategoryAvitoArray->getParam($CategoryAvitoArray->getParam($category_parent . '.parent_to') . '.name');

            $CategoryAvitoArray->getParam($parent . '.parent_to');

            if (empty($Category)) {
                $Category = $ProductType;
                $GoodsType = $GoodsSubType;
                $GoodsSubType = $ProductType = null;
            }
            
            // Коррекция для категории Транспорт
            if($category_parent == "zapchasti_i_aksessuary"){
               $Category=$GoodsType;
               $GoodsType=$ProductType;
               $GoodsSubType = $ProductType = null;
            }

            $xml .= '<Category>' . PHPShopString::win_utf8($Category) . '</Category>';
            $xml .= '<GoodsType>' . PHPShopString::win_utf8($GoodsType) . '</GoodsType>';
            $xml .= '<ProductType>' . PHPShopString::win_utf8($ProductType) . '</ProductType>';
            $xml .= '<GoodsSubType>' . PHPShopString::win_utf8($GoodsSubType) . '</GoodsSubType>';


            // Изображение
            $xml .= '<Images>' . $this->getImages($val['id'], $val['picture']) . '</Images>';

            // Характеристики
            if (is_array($val['vendor_array'])) {
                foreach ($val['vendor_array'] as $v) {

                    if ($this->param_array[$v[0]]['param'] != "")
                        $xml .= '<' . PHPShopString::win_utf8(str_replace('&', '&amp;', $this->param_array[$v[0]]['attribute_avitoapi'])) . '>' . PHPShopString::win_utf8(str_replace('&', '&amp;', $this->param_array[$v[0]]['name'])) . '</' . PHPShopString::win_utf8(str_replace('&', '&amp;', $this->param_array[$v[0]]['attribute_avitoapi'])) . '>';

                    if ($this->param_array[$v[0]]['attribute_avitoapi'] == 'AdType')
                        $AdType_enabled = true;

                    if ($this->param_array[$v[0]]['attribute_avitoapi'] == 'Condition')
                        $Condition_enabled = true;
                }
            }

            if (empty($AdType_enabled))
                $xml .= '<AdType>' . PHPShopString::win_utf8('Товар приобретен на продажу') . '</AdType>';

            if (empty($Condition_enabled))
                $xml .= '<Condition>' . PHPShopString::win_utf8('Новое') . '</Condition>';

            $xml .= '</Ad>';
            $this->xml .= $xml;
        }
    }

    /**
     * Подвал
     */
    function serFooter() {

        $this->xml .= '</Ads>';
    }

    /**
     * Компиляция документа, вывод результата
     */
    function compile() {

        $this->setHeader();
        $this->setCategories();
        $this->setProducts();
        $this->serFooter();

        return $this->xml;
    }

    /**
     * Характеристики для шаблона описания
     */
    private function sort_table($product) {

        $category = new PHPShopCategory((int) $product['category']);

        $sort = $category->unserializeParam('sort');
        $vendor_array = unserialize($product['vendor_array']);
        $dis = $sortCat = $sortValue = null;
        $arrayVendorValue = [];

        if (is_array($sort))
            foreach ($sort as $v) {
                $sortCat .= (int) $v . ',';
            }

        if (!empty($sortCat)) {

            // Массив имен характеристик
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
            $arrayVendor = array_column($PHPShopOrm->getList(['*'], ['id' => sprintf(' IN (%s 0)', $sortCat)], ['order' => 'num']), null, 'id');

            if (is_array($vendor_array))
                foreach ($vendor_array as $v) {
                    foreach ($v as $value)
                        if (is_numeric($value))
                            $sortValue .= (int) $value . ',';
                }

            if (!empty($sortValue)) {

                // Массив значений характеристик
                $PHPShopOrm = new PHPShopOrm();
                $result = $PHPShopOrm->query("select * from " . $GLOBALS['SysValue']['base']['sort'] . " where id IN ( $sortValue 0) order by num");
                while (@$row = mysqli_fetch_array($result)) {
                    $arrayVendorValue[$row['category']]['name'][$row['id']] = $row['name'];
                    $arrayVendorValue[$row['category']]['id'][] = $row['id'];
                }

                if (is_array($arrayVendor))
                    foreach ($arrayVendor as $idCategory => $value) {

                        if (!empty($arrayVendorValue[$idCategory]['name'])) {
                            if (!empty($value['name'])) {
                                $arr = array();
                                foreach ($arrayVendorValue[$idCategory]['id'] as $valueId) {
                                    $arr[] = $arrayVendorValue[$idCategory]['name'][(int) $valueId];
                                }

                                $sortValueName = implode(', ', $arr);

                                $dis .= PHPShopText::li($value['name'] . ': ' . $sortValueName, null, '');
                            }
                        }
                    }

                return PHPShopText::ul($dis, '');
            }
        }
    }

    /**
     *  Шаблон генерации описания
     */
    private function replaceDescriptionVariables($product) {
        $template = $this->options['preview_description_template'];

        if (empty(trim($template))) {
            return $product['content'];
        }

        if (stripos($template, '@Content@') !== false) {
            $template = str_replace('@Content@', $product['content'], $template);
        }
        if (stripos($template, '@Description@') !== false) {
            $template = str_replace('@Description@', $product['description'], $template);
        }
        if (stripos($template, '@Attributes@') !== false) {
            $template = str_replace('@Attributes@', $this->sort_table($product), $template);
        }
        if (stripos($template, '@Catalog@') !== false) {
            $template = str_replace('@Catalog@', $this->categories[$product['category']]['site_title'], $template);
        }
        if (stripos($template, '@Product@') !== false) {
            $template = str_replace('@Product@', $product['name'], $template);
        }
        if (stripos($template, '@Article@') !== false) {
            $template = str_replace('@Article@', __('Артикул') . ': ' . $product['uid'], $template);
        }
        if (stripos($template, '@Subcatalog@') !== false) {
            if (count($this->categoriesForPath) === 0) {
                $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
                $this->categoriesForPath = array_column($orm->getList(['id', 'name', 'parent_to'], false, false, ['limit' => 100000]), null, 'id');
            }

            $this->path = [];
            $this->getNavigationPath($this->categories[$product['category']]['site_id']);

            $subcat = '';
            array_shift($this->path);

            foreach ($this->path as $subcategory) {
                $subcat .= $subcategory['name'] . ' - ';
            }

            $subcat = substr($subcat, 0, strlen($subcat) - 3);

            $template = str_replace('@Subcatalog@', $subcat, $template);
        }

        return $template;
    }

    private function getNavigationPath($id) {

        if (!empty($id)) {
            if (isset($this->categoriesForPath[$id])) {
                $this->path[] = $this->categoriesForPath[$id];
                if (!empty($this->categoriesForPath[$id]['parent_to']))
                    $this->getNavigationPath($this->categoriesForPath[$id]['parent_to']);
            }
        }
    }

}

class PHPShopCategoryAvitoArray extends PHPShopArray {

    function __construct($sql = false, $select = ["id", "name", "parent_to"]) {
        global $PHPShopModules;

        $this->objSQL = $sql;
        $GLOBALS['SysValue']['my']['array_limit'] = 1000000;
        $this->cache = false;
        $this->debug = false;
        $this->ignor = false;
        $this->order = ['order' => 'name'];
        $this->objBase = $PHPShopModules->getParam("base.avito.avitoapi_categories");
        parent::__construct(...$select);
    }

}
