<?php

if (!defined("OBJENABLED")) {
    require_once(dirname(__FILE__) . "/array.class.php");
    require_once(dirname(__FILE__) . "/category.class.php");
}

/**
 * Сортировки и фильтры товаров
 * @author PHPShop Software
 * @version 2.0
 * @package PHPShopClass
 */
class PHPShopSort {

    /**
     * Отладка
     * @var bool
     */
    var $debug = false;
    var $disp;
    var $catdisp;

    /**
     * Вывод фильтра характеристик
     * @param int $category ИД категории характеристики
     * @param string $sort сериализованный массив характеристик
     * @param bool $direct опция учета направления сортировки
     * @param string $template Имя функции шаблона вывода
     * @param array $vendor массив данных характеристик у товара
     * @param bool $filter опция учета выборки с учетом флага фильтра в характеристики
     * @param bool $goodoption опция учета выборки с учетом отсутствия флага опции товара в характеристики
     * @param bool $cache_enabled опция использования кеша
     * @param string $cattemplate Имя функции шаблона вывода виртуальных каталогов
     * @param string $getall вывод всех данных
     */
    function __construct($category = null, $sort = null, $direct = true, $template = null, $vendor = false, $filter = true, $goodoption = true, $cache_enabled = true, $cattemplate = null, $getall = false) {
        global $PHPShopSystem;

        $sql_add = null;

        // Направление сортировки
        if ($direct)
            $this->direct();

        if (!empty($sort))
            $sort = unserialize($sort);
        elseif (!empty($category)) {
            $PHPShopCategory = new PHPShopCategory($category);
            $sort = $PHPShopCategory->unserializeParam('sort');
        }

        if (!empty($category)) {
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
            $cache = $PHPShopOrm->select(array('sort_cache'), array('id=' => $category));
            $this->sort_cache = unserialize($cache['sort_cache']);
        }

        // Вывод количества товара под характеристику
        if ($cache_enabled) {
            $this->filter_cache_enabled = $PHPShopSystem->ifSerilizeParam('admoption.filter_cache_enabled');
            $this->count_products = $PHPShopSystem->ifSerilizeParam('admoption.filter_products_count');
        }
        else
            $this->filter_cache_enabled = $this->count_products = false;

        // Учет фильтров
        if ($filter)
            $sql_add.=" and (`filtr`='1' or `virtual`='1') ";

        // Учет опций
        if (empty($goodoption))
            $sql_add.=" and goodoption!='1' ";

        // Список для выборки
        $sortList = null;
        if (is_array($sort) and count($sort)>0) {
            foreach ($sort as $value) {
                $sortList.=' id=' . trim($value) . ' OR';
            }
            $sortList = substr($sortList, 0, strlen($sortList) - 2);

            // Мультибаза
            if (defined("HostID"))
                $sql_add  .= " and servers REGEXP 'i" . HostID . "i'";
            elseif (defined("HostMain"))
                $sql_add  .= ' and (servers ="" or servers REGEXP "i1000i")';

            $PHPShopOrm = new PHPShopOrm();
            $PHPShopOrm->debug = $this->debug;
            $PHPShopOrm->comment = __CLASS__ . '.' . __FUNCTION__;
            $result = $PHPShopOrm->query("select * from " . $GLOBALS['SysValue']['base']['sort_categories'] . " where ($sortList) " . $sql_add . " order by num, name");
            while (@$row = mysqli_fetch_array($result)) {
                $id = $row['id'];
                $name = $row['name'];

                // Фильтры
                if (empty($row['virtual']) or !empty($getall))
                    $this->disp.=$this->value($id, $name, true, $template, $vendor, $row['description']);

                // Каталоги
                if (!empty($row['virtual']))
                    $this->catdisp.=$this->value($id, $name, true, $cattemplate,false,$row['sort_seo_name']);
            }
        }
    }

    /**
     * Направление сортировки товара в каталоге
     */
    function direct() {
        global $SysValue;

        // Направление сортировки пользователем
        switch (@$_GET['f']) {
            case(1):
                $SysValue['other']['productSortNext'] = 2;
                $SysValue['other']['productSortImg'] = 1;
                $SysValue['other']['productSortTo'] = 1;
                break;
            case(2):
                $SysValue['other']['productSortNext'] = 1;
                $SysValue['other']['productSortImg'] = 2;
                $SysValue['other']['productSortTo'] = 2;
                break;
            default:
                $SysValue['other']['productSortNext'] = 2;
                //$SysValue['other']['productSortImg']=1;
                $SysValue['other']['productSortTo'] = 1;
        }

        // Сортировка пользователем
        switch (@$_GET['s']) {
            case(1):
                $SysValue['other']['productSortA'] = "sortActiv";
                $SysValue['other']['productSort'] = 1;
                break;
            case(2):
                $SysValue['other']['productSortB'] = "sortActiv";
                $SysValue['other']['productSort'] = 2;
                break;
            case(3):
                $SysValue['other']['productSortC'] = "sortActiv";
                $SysValue['other']['productSort'] = 3;
                break;
            case(4):
                $SysValue['other']['productSortD'] = "sortActiv";
                $SysValue['other']['productSort'] = 4;
                break;
            default:
                $SysValue['other']['productSort'] = 1;
        }


        if (empty($_GET['v'])) {
            $SysValue['other']['productVendor'] = "";
        } else {
            $productVendor = null;
            if (is_array($_GET['v'])) {
                foreach ($_GET['v'] as $k => $v)
                    if (is_array($v))
                        foreach ($v as $vs)
                            $productVendor.='v[' . intval($k) . '][]=' . intval($vs) . '&';


                $productVendor = substr($productVendor, 0, strlen($productVendor) - 1);
            }
            $SysValue['other']['productVendor'] = $productVendor;
        }

        // Сортировка по цене
        $SysValue['other']['productRriceOT'] = PHPShopSecurity::TotalClean(@$_POST['priceOT'], 1);
        $SysValue['other']['productRriceDO'] = PHPShopSecurity::TotalClean(@$_POST['priceDO'], 1);
    }

    /**
     * Вывод значений характеристик
     * @param int $n ИД характеристики
     * @param string $title Название
     * @param bool $all Показывать опцию выбрать все
     * @param string $template Имя функции шаблона вывода
     * @param int $vendor ID характеристики родителя
     * @param string $help Подсказка
     * @return string
     */
    function value($n, $title, $all = false, $template = null, $vendor = false, $help = null) {
        global $SysValue;

        $disp = null;
        $i = 1;
        if (empty($vendor)) {
            if (empty($_POST['v']))
                $vendor = @$SysValue['nav']['query']['v'];
            else
                $vendor = $_POST['v'];
        }

        // Показать выбрать все
        if (!empty($all) and empty($template)) {
            $value[] = array($title, '', null);
        }

        $all_sel = 'selected';
        $PHPShopOrm = new PHPShopOrm();
        $PHPShopOrm->debug = $this->debug;
        $PHPShopOrm->comment = __CLASS__ . '.' . __FUNCTION__;
        $result = $PHPShopOrm->query("select * from " . $SysValue['base']['sort'] . " where category=" . intval($n) . " order by num,name");

        while ($row = mysqli_fetch_array($result)) {
            $id = $row['id'];

            $name = substr($row['name'], 0, 100);
            $sel = null;
            if (is_array($vendor))
                foreach ($vendor as $v) {
                    if ($id == $v) {
                        $sel = "selected";
                        $all_sel = null;
                    }
                }

            if (!empty($this->sort_cache['filter_cache'][$n]) and is_array($this->sort_cache['filter_cache'][$n]) && $this->filter_cache_enabled) {
                if (!in_array($id, $this->sort_cache['filter_cache'][$n])) {
                    if (!empty($this->sort_cache['products'][$n][$id]) && $this->count_products) {
                        $value[$i] = array($name, $id, $sel, $this->sort_cache['products'][$n][$id], $row['icon'],$row['sort_seo_name']);
                        $i++;
                    } else {
                        $value[$i] = array($name, $id, $sel, null, $row['icon'],$row['sort_seo_name']);
                        $i++;
                    }
                }
            } else {
                if (!empty($this->sort_cache['products'][$n][$id]) && $this->count_products) {
                    $value[$i] = array($name, $id, $sel, $this->sort_cache['products'][$n][$id], $row['icon'],$row['sort_seo_name']);
                    $i++;
                } else {
                    $value[$i] = array($name, $id, $sel, null, $row['icon'],$row['sort_seo_name']);
                    $i++;
                }
            }
        }

        $SysValue['sort'][] = $n;

        if (empty($template) && !empty($value)) {
            $size = (strlen($title) + 7) * 6;
            $disp = PHPShopText::select('v[' . $n . ']', $value, $size, false, false, false, false, false, $n);
        } elseif (function_exists($template)) {
            $disp = call_user_func_array($template, array($value, $n, $title, $vendor, $help));
        }

        // Массив характеристик для использования в модулях
        $this->value_array = $value;

        return $disp;
    }

    /**
     * Виртуальные категории
     */
    function categories() {
        return $this->catdisp;
    }

    /**
     * Вывод блока сортировки по характеристикам
     */
    function display() {
        global $SysValue;

        $v_ids = null;
        if (!empty($this->disp)) {
            if (is_array($SysValue['sort']))
                foreach ($SysValue['sort'] as $value)
                    $v_ids.=$value . ",";
            $len = strlen($v_ids);
            $v_ids = substr($v_ids, 0, $len - 1);

            // Кнопка применить и сбросить фильтра
            $SysValue['other']['vendorSelectDisp'] = PHPShopText::button($SysValue['lang']['sort_apply'], $onclick = 'GetSortAll(' . $SysValue['nav']['id'] . ',' . $v_ids . ')', 'ok', 'vendorActionButton');
            $SysValue['other']['vendorSelectDisp'].=' ' . PHPShopText::button($SysValue['lang']['sort_reset'], $onclick = 'window.location.replace(\'?\')');
            $SysValue['other']['vendorDispTitle'] = PHPShopText::div(PHPShopText::b($SysValue['lang']['sort_title']));
        }

        return PHPShopText::td($this->disp);
    }

    /**
     * Вывод текущего содержимого
     * @return string 
     */
    function getContent() {
        return $this->disp;
    }

}

/**
 * Шаблон вывода характеристик
 * @param array $value массив значений $value[]=array('моя цифра 1',123,'selected');
 * @param int $n integer
 * @param string $title название характеристики
 * @param array $vendor массив значений характеристик товара
 * @return string 
 */
function sorttemplateexample($value, $n, $title, $vendor) {
    $disp = null;

    if (is_array($value)) {
        foreach ($value as $p) {
            if (is_array($vendor[$n])) {
                foreach ($vendor[$n] as $value) {

                    if ($value == $p[1])
                        $text = PHPShopText::b($p[0]);
                    else
                        $text = $p[0];

                    $disp.=PHPShopText::br() . PHPShopText::a('?v[' . $n . ']=' . $p[1], $text, $p[0], $color = false, $size = false, $target = false, $class = false);
                }
            }else {
                if ($vendor[$n] == $p[1])
                    $text = PHPShopText::b($p[0]);
                else
                    $text = $p[0];

                $disp.=PHPShopText::br() . PHPShopText::a('?v[' . $n . ']=' . $p[1], $text, $p[0], $color = false, $size = false, $target = false, $class = false);
            }
        }
    }
    return $disp;
}

/**
 * Массив с характеристиками товаров
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopArray
 */
class PHPShopSortArray extends PHPShopArray {

    /**
     * Конструктор
     * @param array $sql SQL условие выборки
     * @param bull $debug отладка
     */
    function __construct($sql = false, $debug = false) {
        $this->objSQL = $sql;
        $this->debug = $debug;
        $this->objBase = $GLOBALS['SysValue']['base']['sort'];
        parent::__construct('id', 'name', 'page');
    }

}

/**
 * Массив с характеристиками категорий товаров
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopArray
 */
class PHPShopSortCategoryArray extends PHPShopArray {

    /**
     * Конструктор
     * @param array $sql SQL условие выборки
     * @param bull $debug отладка
     */
    function __construct($sql = false, $debug = false) {
        $this->objSQL = $sql;
        $this->debug = $debug;
        $this->order = array('order' => 'num desc, name');
        $this->objBase = $GLOBALS['SysValue']['base']['sort_categories'];
        parent::__construct('id', 'name', 'category', 'filtr', 'page', 'optionname', 'goodoption');
    }

}

/**
 * Массив с названием опций товаров
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopArray
 */
class PHPShopParentNameArray extends PHPShopArray {

    /**
     * Конструктор
     * @param array $sql SQL условие выборки
     * @param bull $debug отладка
     */
    function __construct($sql = false, $debug = false) {
        $this->objSQL = $sql;
        $this->debug = $debug;
        $this->order = array('order' => 'name');
        $this->objBase = $GLOBALS['SysValue']['base']['parent_name'];
        parent::__construct('id', 'name','color');
    }

}

/**
 *  Вывод характеристик по имени
 *  @example $search=PHPShopSortSearch('Бренд'); $search->search($vendor_array);
 */
class PHPShopSortSearch {

    /**
     * Выборка характеристик по имени
     * @param string $name имя характеристики
     */
    function __construct($name) {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
        $PHPShopOrm->debug = false;
        $data = $PHPShopOrm->select(array('id'), array('name' => '="' . $name . '"'), false, array('limit' => 1));
        if (is_array($data)) {

            $this->sort_category = $data['id'];

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);
            $PHPShopOrm->debug = false;
            $data = $PHPShopOrm->select(array('id,name'), array('category' => '=' . $this->sort_category), false, array('limit' => 1000));
            if (is_array($data)) {
                foreach ($data as $val)
                    $this->sort_array[$val['id']] = $val['name'];
            }
        }
    }

    /**
     * Поиск в массиве характеристик товара нужной характеристики
     * @param array $row массив характеристик товара
     * @return string имя характеристики в тэге
     */
    function search($row) {
        if (is_array($row))
            foreach ($row as $val) {
                if (!empty($this->sort_array[$val[0]])) {
                    return $this->sort_array[$val[0]];
                }
            }
    }

}

?>