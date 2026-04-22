<?php

include_once dirname(dirname(__DIR__)) . '/class/Avito.php';

/**
 * Базовый класс для генерации XML для Авито.
 * Class BaseAvitoXml
 */
class BaseAvitoXml {

    /** @var string */
    protected $xml;

    /** @var Avito */
    protected $Avito;

    /** @var PHPShopSystem */
    private $PHPShopSystem;
    private $categories = [];
    private $xmlPriceId;
    private $categoriesForPath = [];
    private $path = [];

    public function __construct() {

        $this->xmlPriceId = $xmlPriceId;
        $this->PHPShopSystem = new PHPShopSystem();

        $this->Avito = new Avito();
        
        $this->fee_type = $this->Avito->options['fee_type'];
        $this->fee = $this->Avito->options['fee'];
        $this->price = $this->Avito->options['price'];

        // Пароль
        if (!empty(Avito::getOption('password')))
            if ($_GET['pas'] != Avito::getOption('password'))
                exit('Login error!');
    }

    public function setAds() {
        $this->xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $this->xml .= '<Ads formatVersion="3" target="Avito.ru">';

        $products = $this->getProducts($_GET['getall']);

        foreach ($products as $product) {
            $this->xml .= static::getXml($product);
        }

        $this->xml .= '</Ads>';
    }

    /**
     * Компиляция документа, вывод результата
     */
    public function compile() {

        $this->loadCategories();

        $this->setAds();

        echo $this->xml;
    }

    public function getProducts($getAll = false) {

        // Исходное изображение
        $image_source = $this->PHPShopSystem->ifSerilizeParam('admoption.image_save_source');
        $result = array();

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);


        $where = array(
            'enabled' => '="1"',
            'parent_enabled' => '="0"',
            'export_avito' => '="1"',
            'items' => ' > 0',
        );

        if ($getAll) {
            unset($where['export_avito']);
            unset($where['items']);
        }

        $products = $PHPShopOrm->getList(array('*'), $where);

        foreach ($products as $product) {

            $product['description'] = $this->replaceDescriptionVariables($product);
            $product['description'] = '<![CDATA[' . PHPShopString::win_utf8(trim(strip_tags($product['description'], '<p><br><strong><em><ul><ol><li>'))) . ']]>';

            $product['name'] = '<![CDATA[' . PHPShopString::win_utf8(trim(strip_tags($product['name']))) . ']]>';
            if (!empty($product['name_avito'])) {
                $product['name'] = '<![CDATA[' . PHPShopString::win_utf8(trim(strip_tags($product['name_avito']))) . ']]>';
            }

            $PHPShopOrm = new PHPShopOrm('phpshop_foto');
            $images = $PHPShopOrm->getList(array('*'), array('parent' => '=' . $product['id']), array('order' => 'num'));
            if (count($images) === 0) {
                $images[] = array('name' => $product['pic_big']);
            }

            // Изображения
            foreach ($images as $key => $image) {
                if (!empty($image['name']) and !strstr('http', $image['name'])) {

                    if (!empty($image_source))
                        $images[$key]['name'] = str_replace(".", "_big.", $image['name']);

                    $image_url = Avito::getOption('image_url');
                    if (empty($image_url))
                        $image_url = $_SERVER['SERVER_NAME'];

                    $images[$key]['name'] = $this->ssl . $image_url . $image['name'];
                }
            }

            // Склад
            if ($product['items'] < 1)
                $product['items'] = 0;

            // price columns
            if (!empty($product['price_avito'])) {
                $price = $product['price_avito'];
            } elseif (!empty($product['price' . (int) $this->price])) {
                $price = $product['price' . (int) $this->price];
            }
            else $price = $product['price'];

            $price = $this->getProductPrice($price, $product['baseinputvaluta']);

            if ($this->fee > 0) {
                if ($this->fee_type == 1) {
                    $price = $price - ($price * $this->fee / 100);
                } else {
                    $price = $price + ($price * $this->fee / 100);
                }
            }

            $result[$product['id']] = array(
                "id" => $product['id'],
                "uid" => $product['uid'],
                "category" => PHPShopString::win_utf8($this->categories[$product['category']]['category']),
                "type" => PHPShopString::win_utf8($this->categories[$product['category']]['type']),
                "subtype" => PHPShopString::win_utf8($this->categories[$product['category']]['subtype']),
                "subtype_id" => $this->categories[$product['category']]['subtype_avito'],
                "name" => str_replace(array('&#43;', '&#43'), '+', $product['name']),
                "images" => $images,
                "price" => $price,
                "description" => $product['description'],
                "prod_seo_name" => $product['prod_seo_name'],
                "condition" => PHPShopString::win_utf8($product['condition_avito']),
                "status" => $product['ad_status_avito'],
                "listing_fee" => $product['listing_fee_avito'],
                "category_avito" => $this->categories[$product['category']]['category_avito'],
                "oem" => $product['oem_avito'],
                "tiers" => $product['tiers_avito'],
                "items" => $product['items'],
                "building_avito" => $product['building_avito'],
                "type_avito" => $this->categories[$product['category']]['type_avito']
            );
        }

        return $result;
    }

    public static function getAddress() {
        if (!empty(Avito::getOption('address'))) {
            return Avito::getOption('address');
        }

        $PHPShopSystem = new PHPShopSystem();

        return $PHPShopSystem->getSerilizeParam('bank.org_adres');
    }

    /**
     * @param array $product
     * @return float
     */
    private function getProductPrice($price, $baseinputvaluta) {
        $PHPShopPromotions = new PHPShopPromotions();
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
     * Заполнение свойства categories массивом вида id категории => название категории в Авито.
     */
    private function loadCategories() {
        $orm = new PHPShopOrm('phpshop_modules_avito_categories');

        if (is_array($this->xmlPriceId)) {
            $categories = array_column($orm->getList(['id'], ['xml_price_id' => sprintf(' IN (%s)', implode(',', $this->xmlPriceId))]), 'id');
        } else {
            $categories = array_column($orm->getList(['id'], ['xml_price_id' => sprintf('="%s"', $this->xmlPriceId)]), 'id');
        }

        $where = [
            'skin_enabled' => "!='1'",
            'category_avito' => sprintf(' IN (%s)', implode(',', $categories))
        ];

        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['skin_enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
        $categories = $PHPShopOrm->getList(array('id', 'category_avito', 'type_avito', 'subtype_avito', 'name'), $where);

        foreach ($categories as $category) {
            $avitoCategory = $this->Avito->getCategoryById((int) $category['category_avito']);
            if (!empty($avitoCategory)) {
                $this->categories[$category['id']] = array(
                    'category' => $avitoCategory,
                    'type' => $this->Avito->getAvitoType($category['type_avito']),
                    'subtype' => $this->Avito->getAvitoSubType($category['subtype_avito']),
                    'subtype_avito' => $category['subtype_avito'],
                    'category_avito' => (int) $category['category_avito'],
                    'site_title' => $category['name'],
                    'site_id' => (int) $category['id'],
                    'type_avito' => (int) $category['type_avito']
                );
            }
        }
    }

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

    private function replaceDescriptionVariables($product) {
        $template = Avito::getOption('preview_description_template');

        if (empty(trim($template))) {
            return '';
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
