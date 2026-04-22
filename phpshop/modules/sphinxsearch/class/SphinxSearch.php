<?php

/**
 * Библиотека работы с поиском Sphinx
 * @author PHPShop Software
 * @version 1.2
 * @package PHPShopModules
 * @todo https://pushorigin.ru/sphinx/attrs
 * @todo http://chakrygin.ru/2013/07/sphinx-search.html
 */
class SphinxSearch {

    static $options;

    function __construct() {
        $this->link_db = $this->connect();
    }

    public function connect() {
        global $PHPShopBase;

        $port = self::getOption('port');
        $host = self::getOption('host');

        if (!empty($port) and ! empty($host)) {

            $link_db = @mysqli_connect($host, null, null, null, $port);
            if ($link_db) {
                mysqli_query($link_db, "SET NAMES '" . $PHPShopBase->codBase . "'");
                mysqli_query($link_db, "SET SESSION sql_mode=''");
                mysqli_report(MYSQLI_REPORT_OFF);

                return $link_db;
            }
        }
    }

    /**
     * Учет категорий витрин
     * @return array
     */
    private function getServerCategories() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);

        if (defined("HostID")) {
            return array_column($PHPShopOrm->getList(['id'], ['skin_enabled' => '!= "1"', 'servers' => " REGEXP 'i" . HostID . "i'"]), 'id'
            );
        }
        return array_column($PHPShopOrm->getList(['id'], ['skin_enabled' => '!= "1"', 'servers' => ' ="" or servers REGEXP "i1000i"']), 'id');
    }

    public static function getCategoriesWrapperTemplate() {
        if ((int) self::getOption('find_in_categories') === 2) {
            return $GLOBALS['SysValue']['templates']['sphinxsearch']['search_categories_list_wrapper'];
        }

        return $GLOBALS['SysValue']['templates']['sphinxsearch']['search_categories_wrapper'];
    }

    public static function getCategoriesTemplate() {
        if ((int) self::getOption('find_in_categories') === 2) {
            return $GLOBALS['SysValue']['templates']['sphinxsearch']['search_categories_list'];
        }

        return $GLOBALS['SysValue']['templates']['sphinxsearch']['search_categories'];
    }

    public function search($query_str, $pole, $category = 0, $from = 0, $size = 15) {
        global $PHPShopSystem;

        $categories = [];
        if ((defined('HostID') or defined('HostMain')) && $category === 0) {
            $categories = $this->getServerCategories();
        }
        if ($category > 0) {
            $categories = [$category];
        }


        // Сортировка
        if ((int) self::getOption('available_sort') == 1) {
            $sort = 'items DESC,';
        } else
            $sort = null;

        // Разделение слов
        if (strstr($query_str, '-')) {
            $query_array = explode("-", $query_str);
        } else
            $query_array[] = $query_str;

        $query = null;
        foreach ($query_array as $w) {

            // Область поиска
            if ($pole === 1) {
                if (self::getOption('search_uid_first') == 1)
                    $query .= '(@uid ' . $w . ')|(@name ' . $w . ')|';
                else
                    $query .= '(@name ' . $w . ')|(@uid ' . $w . ')|';
            }
            else {
                if (self::getOption('search_uid_first') == 1)
                    $query .= '(@uid ' . $w . ')|(@name ' . $w . ')|(@content ' . $w . ')|';
                else
                    $query .= '(@name ' . $w . ')|(@uid ' . $w . ')|(@content ' . $w . ')|';
            }
        }

        $query = substr($query, 0, (strlen($query) - 1));

        $result = $this->query($query, $sort, $from, $size, array_values($categories));

        if ($result['product'])
            $ids = array_column($result['product'], 'id');

        if (count($ids) === 0) {

            $isYandexSearchCloud = (bool) self::getOption('yandexsearch');
            $isYandexSearchToken = (bool) $PHPShopSystem->getSerilizeParam('ai.yandexsearch_token');

            if (empty($isYandexSearchToken))
                $isYandexSearchCloud = false;
            else
                PHPShopObj::loadClass('yandexcloud');

            if ($isYandexSearchCloud and ! empty($query)) {
                $ids = $this->getYandexSearchCloud($query);
            }

            if (count($ids) === 0)
                return ['products' => [], 'total' => 0];
        }

        $dop_cats = '';
        foreach ($this->getServerCategories() as $cat) {
            $dop_cats .= ' OR dop_cat LIKE \'%#' . $cat . '#%\' ';
        }
        $categories_str = implode("','", $this->getServerCategories());
        $catt = "(category IN ('$categories_str') " . $dop_cats . " ) ";

        $products = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))
                ->getList(['*'], [
            'id' => sprintf(" IN ('%s')", implode("','", $ids)) . ' and ' . $catt
                ], ['order' => sprintf(" FIELD(id, '%s')", implode("','", $ids))]
        );

        if ($result['product'])
            $categories = array_column($result['product'], 'category');

        return [
            'products' => $products,
            'total' => $result['total'],
            'categories' => $result['categories'],
            'count' => $result['count']
        ];
    }

    public function setPaginator($page, $total, $pageSize, $obj, $query, $category, $pole) {

        $i = 1;
        $navigat = null;
        $pageCount = (int) ceil($total / $pageSize);
        $page > 1 ? $previousPage = $page - 1 : $previousPage = 1;
        $page === $pageCount ? $nextPage = $page : $nextPage = $page + 1;

        if ($pageCount > 1) {
            while ($i <= $pageCount) {

                if ($i > 1) {
                    $p_start = $pageSize * ($i - 1);
                    $p_end = $p_start + $pageSize;
                } else {
                    $p_start = $i;
                    $p_end = $pageSize;
                }

                $obj->set("paginPageRangeStart", $p_start);
                $obj->set("paginPageRangeEnd", $p_end);
                $obj->set("paginPageNumber", $i);

                if ($i != $page) {
                    if ($i == 1) {
                        $obj->set("paginLink", "?words=" . $query . "&pole=" . $pole . "&p=" . $i . "&cat=" . $category);
                        $navigat .= parseTemplateReturn("paginator/paginator_one_link.tpl");
                    } else {
                        if ($i > ($page - 3) and $i < ($page + 3)) {
                            $obj->set("paginLink", "?words=" . $query . "&pole=" . $pole . "&p=" . $i . "&cat=" . $category);
                            $navigat .= parseTemplateReturn("paginator/paginator_one_link.tpl");
                        } else if ($i - ($page + 3) < 3 and ( ($page - 3) - $i) < 3) {
                            $navigat .= parseTemplateReturn("paginator/paginator_one_more.tpl");
                        }
                    }
                } else {
                    $obj->set("paginLink", "?words=" . $query . "&pole=" . $pole . "&p=" . $page . "&cat=" . $category);
                    $navigat .= parseTemplateReturn("paginator/paginator_one_selected.tpl");
                }

                $i++;
            }

            $obj->set("previousLink", "?words=" . $query . "&pole=" . $pole . "&p=" . $previousPage . "&cat=" . $category);
            $obj->set("nextLink", "?words=" . $query . "&pole=" . $pole . "&p=" . $nextPage . "&cat=" . $category);
            $obj->set("pageNow", $obj->getValue('lang.page_now'));
            $obj->set("navBack", $obj->lang('nav_back'));
            $obj->set("navNext", $obj->lang('nav_forw'));
            $obj->set("navigation", $navigat);

            // Назначаем переменную шаблонизатора
            $obj->set('searchPageNav', parseTemplateReturn("paginator/paginator_main.tpl"));
        }
    }

    // Синонимы
    public function synonyms($query) {

        $synonyms = self::getOption('synonyms');
        $synonyms = trim($synonyms);

        if (!empty($synonyms)) {

            if (strpos($synonyms, "\r\n")) {
                $eol = "\r\n";
            } elseif (strpos($synonyms, "\n")) {
                $eol = "\n";
            } else {
                $eol = "\r";
            }

            $synonyms = explode($eol, $synonyms);

            if (is_array($synonyms)) {
                foreach ($synonyms as $synonym) {

                    $words = explode(",", $synonym);

                    if (is_array($words) and !stristr($query, $words[0] . '-' . $words[1]) and !stristr($query, $words[1] . '-' . $words[0])) {
                        $synonym_array[$words[0]] = $words[0] . '-' . $words[1];
                        $synonym_array[$words[1]] = $words[1] . '-' . $words[0];
                    }
                }

                if (is_array($synonym_array)) {
                    foreach ($synonym_array as $search => $replace) {
                               $query = str_ireplace($search, $replace, $query);
                    }
                }
            }
        }

        return $query;
    }

    public function query($query, $sort, $from, $size, $categories) {


        if ($this->link_db)
            $result = mysqli_query($this->link_db, "SELECT * FROM productsIndex WHERE MATCH('" . PHPShopString::win_utf8($query) . "') ORDER BY $sort WEIGHT() DESC LIMIT 1000");

        $i = 0;
        $total = 0;
        $count = [];

        if ($result)
            while ($row = mysqli_fetch_array($result)) {

                if ($i >= $from and $i < ($from + $size)) {

                    if (is_array($categories) and ! in_array($row['category'], $categories))
                        continue;

                    $product[] = [
                        'id' => $row['id'],
                        'name' => $row['name'],
                        'category' => $row['category']
                    ];
                }

                $count[$row['category']] ++;
                $category[] = $row['category'];

                $i++;
                $total++;
            }

        return ['product' => $product, 'total' => $total, 'count' => $count, 'categories' => $category];
    }

    public function query_categories($query) {

        if ($this->link_db)
            $result = mysqli_query($this->link_db, "SELECT * FROM categoriesIndex WHERE MATCH('" . PHPShopString::win_utf8($query) . "') ORDER BY WEIGHT() DESC LIMIT " . (int) self::getOption('ajax_search_categories_cnt'));

        if ($result)
            while ($row = mysqli_fetch_array($result)) {
                $categories[] = [
                    'name' => PHPShopString::utf8_win1251($row['name']),
                    'id' => $row['id']];
            }

        return $categories;
    }

    function getYandexSearchCloud($search) {

        $YandexSearch = new YandexSearch();
        $site = $_SERVER['SERVER_NAME'];
        //$site = 'myphpshop.ru';
        // Учет модуля SEOURLPRO
        if (!empty($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'])) {
            $site .= '/id/';
        } else {
            $site .= '/shop/';
        }

        $result = $YandexSearch->search(PHPShopString::win_utf8($search) . ' site:' . $site);

        if (is_array($result)) {
            $ids = array();
            foreach ($result as $document) {

                $id_seo = preg_replace('#^.*/id/.*-(.*)\.html$#', '$1', $document['url']);
                $id = preg_replace('#^.*/shop/UID_(.*)\.html$#', '$1', $document['url']);

                if (!empty($id_seo))
                    $ids[] = $id_seo;
                elseif (!empty($id))
                    $ids[] = $id;
            }

            return $ids;
        }
    }

    /**
     *  Поиск по именам категорий
     */
    function searchCategories($query, $obj) {

        $data = $this->query_categories($query);
        return $obj->product_grid($data, 1, 'search/search_ajax_catalog_forma.tpl', $obj->line);
    }

    public function searchAjax($query, $obj, $limit = 5) {

        // Убираем дублирование в другой раскладке
        $wordsArr = explode(' ', urldecode(PHPShopSecurity::true_search($query)));
        $query = implode(' ', array_slice($wordsArr, 0, ceil(count($wordsArr) / 2)));

        $categories = [];
        if (defined('HostID') or defined('HostMain')) {
            $categories = $this->getServerCategories();
        }

        // Поиск по каталогам
        $grid = $this->searchCategories($query, $obj);

        // Сортировка
        if ((int) self::getOption('available_sort') == 1) {
            $sort = 'items DESC,';
        } else
            $sort = null;

        // Область поиска
        if (self::getOption('search_uid_first') == 1)
            $query = '@uid ' . $query . '|@name ' . $query;
        else
            $query = '@name ' . $query . '|@uid ' . $query;


        $result = $this->query($query, $sort, 0, self::getOption('ajax_search_products_cnt'), $categories);


        if ($result['product'])
            $productsIds = array_column($result['product'], 'id');


        if (count($productsIds) === 0 and empty($grid)) {
            exit;
        }

        if (count($productsIds) > 0) {
            $products = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))
                    ->getList(
                    ['*'], [
                'id' => sprintf(" IN ('%s')", implode("','", $productsIds)),
                'category' => sprintf(' IN (%s)', implode(',', $this->getServerCategories()))
                    ], ['order' => sprintf(" FIELD(id, '%s')", implode("','", $productsIds))]
            );

            $grid .= $obj->product_grid($products, 1, 'search/search_ajax_product_forma.tpl', $obj->line);
        }

        if (!empty($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'])) {
            $grid = $GLOBALS['PHPShopSeoPro']->AjaxCompile($grid);
        }

        return PHPShopParser::replacedir($obj->separator . $grid);
    }

    public static function getOption($key) {
        if (!is_array(self::$options)) {
            self::$options = (new PHPShopOrm('phpshop_modules_sphinxsearch_system'))->select();
        }

        if (isset(self::$options[$key])) {
            return self::$options[$key];
        }

        return null;
    }

    public static function getOptions() {
        if (!is_array(self::$options)) {
            self::$options = (new PHPShopOrm('phpshop_modules_sphinxsearch_system'))->select();
        }

        return self::$options;
    }

    public static function getCategoriesByIds($categoryIds) {
        $where = [
            'id' => sprintf(' IN (%s)', implode(',', $categoryIds)),
            'skin_enabled ' => "!='1'"
        ];
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['skin_enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        return array_column((new PHPShopOrm('phpshop_categories'))
                        ->getList(
                                ['id', 'name', 'icon'], $where, ['order' => sprintf(' FIELD(id, %s)', implode(',', $categoryIds))]
                        ), null, 'id'
        );
    }

    public static function getWordForm($num, $form_for_1, $form_for_2, $form_for_5) {
        $num = abs((int) $num) % 100; // берем число по модулю и сбрасываем сотни (делим на 100, а остаток присваиваем переменной $num)
        $num_x = $num % 10; // сбрасываем десятки и записываем в новую переменную
        if ($num > 10 && $num < 20) // если число принадлежит отрезку [11;19]
            return $form_for_5;
        if ($num_x > 1 && $num_x < 5) // иначе если число оканчивается на 2,3,4
            return $form_for_2;
        if ($num_x == 1) // иначе если оканчивается на 1
            return $form_for_1;

        return $form_for_5;
    }

}
