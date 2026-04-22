<?php

/**
 * Обработчик страниц
 * @author PHPShop Software
 * @version 2.0
 * @package PHPShopCore
 */
class PHPShopPage extends PHPShopCore {

    /**
     * Таблица для навигации хлебных крошек
     * @var string
     */
    var $navigationBase = 'base.page_categories';

    /**
     * Режим отладки
     * @var bool
     */
    var $debug = false;
    var $odnootip_cell_center = 2;
    var $odnootip_cell_block = 1;

    /**
     * Кол-во последних статей
     * @var int 
     */
    var $limit = 3;
    var $odnotip_setka_num;

    /**
     * Конструктор
     */
    function __construct() {

        // Имя Бд
        $this->objBase = $GLOBALS['SysValue']['base']['page'];

        // Список экшенов
        $this->action = array("nav" => "CID");
        $this->empty_index_action = true;
        $this->cid_cat_with_foto_template = 'catalog/cid_page_category.tpl';

        // Учет модуля SEOURLPRO
        if (!empty($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'])) {
            $this->seourlpro_enabled = true;
        }

        parent::__construct();
    }

    function getAll() {

        // Мета
        $title = __('Блог');
        $this->title = $title . " - " . $this->PHPShopSystem->getValue("name");
        $this->description = $title . ", " . $this->PHPShopSystem->getValue("name");

        $PHPShopOrm = new PHPShopOrm($this->getValue('base.page_categories'));
        $PHPShopOrm->debug = $this->debug;
        $data = $PHPShopOrm->select(array('*'), array('parent_to' => "=0"), array('order' => 'num,id desc'), array('limit' => 300));
        $dis = null;
        if (is_array($data)) {
            foreach ($data as $row) {

                if (empty($row['page_cat_seo_name']) or empty($this->seourlpro_enabled))
                    $url = 'CID_' . $row['id'];
                else
                    $url = $row['page_cat_seo_name'];

                // Определяем переменные
                $this->set('pageLink', $url);
                $this->set('pageName', $row['name']);
                $this->set('pageIcon', $row['icon']);
                $this->set('pageData', PHPShopDate::get($row['datas']));
                $this->set('pagePreview', Parser(stripslashes($row['preview'])));

                // Перехват модуля
                $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');

                // Подключаем шаблон
                $dis .= parseTemplateReturn($this->getValue('templates.page_mini'));
            }
        }
        else 
            return $this->setError404();

        // Навигация хлебные крошки
        $this->navigation(0, $title);

        $this->set('pageContent', $dis);
        $this->set('pageTitle', $title);
        $this->set('pageIcon', $data['icon']);

        // Подключаем шаблон
        $this->parseTemplate($this->getValue('templates.page_catalog_list'));
    }

    /**
     * Экшен по умолчанию, вывод данных по странице
     * @return string
     */
    function index($link = false) {

        $hook_start = $this->setHook(__CLASS__, __FUNCTION__, false, 'START');
        if ($hook_start)
            return true;

        // Безопасность
        if (empty($link))
            $link = PHPShopSecurity::TotalClean($this->PHPShopNav->getName(true), 2);

        if (empty($link) and $this->PHPShopNav->objNav['truepath'] == '/page/')
            return $this->getAll();

        // Страницы только для аторизованных
        if (isset($_SESSION['UsersId'])) {
            $sort = " and ((secure !='1') OR (secure ='1' AND secure_groups='') OR (secure ='1' AND secure_groups REGEXP 'i" . $_SESSION['UsersStatus'] . "-1i')) ";
        } else {
            $sort = " and (secure !='1') ";
        }

        // Мультибаза
        if (defined("HostID")) {
            $sort .= " and servers REGEXP 'i" . HostID . "i'";
        } elseif (defined("HostMain"))
            $sort .= " and (servers = '' or servers REGEXP 'i1000i')";

        $PHPShopOrm = new PHPShopOrm();
        $PHPShopOrm->debug = $this->debug;
        $result = $PHPShopOrm->query("select * from " . $this->objBase . " where link='$link' and enabled='1' $sort limit 1");
        $row = mysqli_fetch_array($result);

        // Прикрываем страницу от дубля
        if ($row['category'] == 2000)
            return $this->setError404();
        elseif (empty($row['id']) or $link != $row['link'])
            return $this->setError404();

        $this->category = $row['category'];
        $this->PHPShopCategory = new PHPShopPageCategory($this->category);
        $this->category_name = $this->PHPShopCategory->getName();

        // Однотипные товары
        $this->odnotip($row);

        // Определяем переменные
        $this->set('isPage', true);
        $this->set('pageContent', Parser(stripslashes($row['content'])));
        $this->set('pageTitle', $row['name']);
        $this->set('catalogCategory', $this->category_name);
        $this->set('catalogId', $this->category);
        $this->set('pageMainIcon', $row['icon']);
        $this->set('pageMainPreview', Parser(stripslashes($row['preview'])));
        $this->PHPShopNav->objNav['id'] = $row['id'];

        // Выделяем меню раздела
        $this->set('NavActive', $row['link']);

        // Мета
        if (empty($row['title']))
            $title = $row['name'] . " - " . $this->PHPShopSystem->getValue("name");
        else
            $title = $row['title'];

        // OpenGraph
        $this->set('ogTitle', $row['name']);
        if (!empty($row['icon']))
            $this->set('ogImage', $row['icon']);
        $this->set('ogDescription', $row['description']);

        $this->title = $title;
        
        if(empty($row['description']))
            $row['description']=$row['name'];
            
        $this->description = $row['description'];
        $this->keywords = $row['keywords'];
        $this->lastmodified = $row['datas'];

        // Навигация хлебные крошки
        if($row['category'] == 1000 or empty($row['category'])) 
            $this->navigation($row['category'], $row['name']);
        else $this->navigation($row['category'], $row['name'], ['url' => '/page/', 'name' => __('Блог')]);

        // Последние записи
        $this->set('pageLast', $this->getLast($link));

        // Перехват модуля
        $hook_end = $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');
        if ($hook_end)
            return true;

        // Подключаем шаблон
        $this->parseTemplate($this->getValue('templates.page_page_list'));
    }

    /**
     * Экшен выборки подробной информации при наличии переменной навигации CID
     */
    function CID() {

        if ($this->setHook(__CLASS__, __FUNCTION__))
            return true;

        // ID категории
        $this->category = PHPShopSecurity::TotalClean($this->PHPShopNav->getId(), 1);
        $this->PHPShopCategory = new PHPShopPageCategory($this->category);
        $this->category_name = $this->PHPShopCategory->getName();

        $PHPShopOrm = new PHPShopOrm($this->getValue('base.page_categories'));
        $PHPShopOrm->debug = $this->debug;
        $row = $PHPShopOrm->select(array('id,name'), array('parent_to' => "=" . $this->category), false, array('limit' => 1));

        // Если страницы
        if (empty($row['id'])) {

            $this->ListPage();
        }
        // Если каталоги
        else {

            $this->ListCategory();
        }
    }

    /**
     * Вывод списка страниц
     * @return string
     */
    function ListPage() {
        $dis = null;
        $lastmodified = 0;

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
            return true;

        // 404
        if (empty($this->category_name))
            return $this->setError404();

        $where = array('category' => '=' . $this->category, 'enabled' => "='1'");

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        // Выборка данных
        $dataArray = $this->PHPShopOrm->select(array('*'), $where, array('order' => 'num,id desc'), array('limit' => 100));
        if (is_array($dataArray)) {

            foreach ($dataArray as $row) {

                // Максимальная дата изменения
                if ($row['datas'] > $lastmodified)
                    $lastmodified = $row['datas'];

                // Определяем переменные
                $this->set('pageLink', $row['link']);
                $this->set('pageName', $row['name']);
                $this->set('pageIcon', $row['icon']);
                $this->set('pageData', PHPShopDate::get($row['datas']));
                $this->set('pagePreview', Parser(stripslashes($row['preview'])));

                // Перехват модуля
                $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');

                // Подключаем шаблон
                $dis .= parseTemplateReturn($this->getValue('templates.page_mini'));
            }
        }

        $this->set('catContent', $this->PHPShopCategory->getContent());
        $this->set('catIcon', $this->PHPShopCategory->getParam('icon'));

        $this->set('pageContent', $dis);
        $this->set('pageTitle', $this->category_name);
        $this->set('pageIcon', $row['icon']);
        $this->set('pagePreview', Parser(stripslashes($row['preview'])));

        // Данные родительской категории
        $cat = $this->PHPShopCategory->getValue('parent_to');
        if (!empty($cat)) {
            $PHPShopOrm = new PHPShopOrm($this->getValue('base.page_categories'));
            $PHPShopOrm->cache = true;
            $PHPShopOrm->debug = $this->debug;
            $parent_category_row = $PHPShopOrm->select(array('id,name'), array('id' => '=' . $cat), false, array('limit' => 1), __FUNCTION__);
        } else {
            $parent_category_row['name'] = $this->lang('catalog');
        }
        $this->set('catalogCategory', $parent_category_row['name']);
        $this->set('catalogId', $cat);

        // Мета
        if ($this->PHPShopCategory->getValue('title') != "")
            $this->title = $this->PHPShopCategory->getValue('title');
        else
            $this->title = $this->category_name . " - " . $this->PHPShopSystem->getValue("name");

        if ($this->PHPShopCategory->getValue('description') != "")
            $this->description = $this->PHPShopCategory->getValue('description');

        if ($this->PHPShopCategory->getValue('keywords') != "")
            $this->keywords = $this->PHPShopCategory->getValue('keywords');

        $this->lastmodified = $lastmodified;

        // Навигация хлебные крошки
        $this->navigation($cat, $this->category_name, ['url' => '/page/', 'name' => __('Блог')]);

        // Перехват модуля
        $this->setHook(__CLASS__, __FUNCTION__, $dataArray, 'END');

        // Подключаем шаблон
        $this->parseTemplate($this->getValue('templates.page_catalog_list'));
    }

    /**
     * Вывод списка категорий
     */
    function ListCategory() {
        $dis = null;

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
            return true;

        // 404
        if (empty($this->category_name))
            return $this->setError404();

        $where = array('parent_to' => '=' . $this->category);

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['parent_to'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        // Выборка данных
        $PHPShopOrm = new PHPShopOrm($this->getValue('base.page_categories'));
        $PHPShopOrm->debug = $this->debug;
        $dataArray = $PHPShopOrm->select(array('name', 'id'), $where, array('order' => 'num,id desc'), array('limit' => 100));
        if (is_array($dataArray))
            foreach ($dataArray as $row) {

                if (empty($row['page_cat_seo_name']) or empty($this->seourlpro_enabled))
                    $url = '/page/CID_' . $row['id'] . '.html';
                else
                    $url = '/page/' . $row['page_cat_seo_name'] . '.html';

                // Определяем переменные
                $this->set('pageLink', $row['link']);
                $this->set('pageName', $row['name']);
                $this->set('pageIcon', $row['icon']);
                $this->set('pageData', PHPShopDate::get($row['datas']));
                $this->set('pagePreview', Parser(stripslashes($row['preview'])));

                // Перехват модуля
                $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');

                // Подключаем шаблон
                $dis .= parseTemplateReturn($this->getValue('templates.page_mini'));
            }


        // Описание каталога
        $this->set('catContent', $this->PHPShopCategory->getContent());
        $this->set('catIcon', $this->PHPShopCategory->getParam('icon'));
        $this->set('catName', $this->category_name);
        $this->set('catName', $this->category_name);


        $this->set('pageContent', $dis);
        $this->set('pageTitle', $this->category_name);

        // Мета
        $this->title = $this->category_name . " - " . $this->PHPShopSystem->getValue("name");

        // Навигация хлебные крошки
        $this->navigation($this->PHPShopCategory->getParam('parent_to'), $this->category_name, ['url' => '/page/', 'name' => __('Блог')]);

        // Перехват модуля
        $this->setHook(__CLASS__, __FUNCTION__, $dataArray, 'END');

        // Подключаем шаблон
        $this->parseTemplate($this->getValue('templates.page_catalog_list'));
    }

    /**
     * Однотипные товары
     * @param array $row массив данных
     */
    function odnotip($row) {
        global $PHPShopProductIconElements;

        //$this->odnotip_setka_num = 2;
        $this->line = false;
        $this->template_odnotip = 'main_spec_forma_icon';

        // Перехват модуля в начале функции
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $row, 'START');
        if ($hook)
            return true;

        $disp = null;
        $odnotipList = null;
        if (!empty($row['odnotip'])) {
            if (strpos($row['odnotip'], ','))
                $odnotip = explode(",", $row['odnotip']);
            elseif (is_numeric(trim($row['odnotip'])))
                $odnotip[] = trim($row['odnotip']);
        }

        // Список для выборки
        if (!empty($odnotip) and is_array($odnotip))
            foreach ($odnotip as $value) {
                if (!empty($value))
                    $odnotipList .= ' id=' . trim($value) . ' OR';
            }

        $odnotipList = substr($odnotipList, 0, strlen($odnotipList) - 2);

        // Режим проверки остатков на складе
        if ($this->PHPShopSystem->getSerilizeParam('admoption.sklad_status') == 2)
            $chek_items = ' and items>0';
        else
            $chek_items = null;

        if (!empty($odnotipList)) {

            $PHPShopOrm = new PHPShopOrm();
            $PHPShopOrm->debug = $this->debug;
            $result = $PHPShopOrm->query("select * from " . $this->getValue('base.products') . " where (" . $odnotipList . ") " . $chek_items . " and  enabled='1' and parent_enabled='0' and sklad!='1' order by num");
            while ($product_row = mysqli_fetch_assoc($result))
                $data[] = $product_row;

            // Сетка товаров
            if (!empty($data) and is_array($data))
                $disp = $PHPShopProductIconElements->seamply_forma($data, $this->odnotip_setka_num, $this->template_odnotip, $this->line);
        }


        if (!empty($disp)) {
            // Вставка в центральную часть
            if (PHPShopParser::check($this->getValue('templates.main_product_odnotip_list'), 'productOdnotipList')) {
                $this->set('productOdnotipList', $disp);
                $this->set('productOdnotip', __('Рекомендуемые товары'));
            } else {
                // Вставка в правый столбец
                $this->set('specMainTitle', __('Рекомендуемые товары'));
                $this->set('specMainIcon', $disp);
            }

            // Перехват модуля в середине функции
            $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');

            $odnotipDisp = ParseTemplateReturn($this->getValue('templates.main_product_odnotip_list'));
            $this->set('odnotipDisp', $odnotipDisp);
        }
        // Выводим последние новинки
        else {
            $this->set('specMainIcon', $PHPShopProductIconElements->specMainIcon(true, $this->category));
        }

        // Перехват модуля в конце функции
        $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');
    }

    /**
     * Вывод последних записей
     * @return string
     */
    function getLast($link) {
        $dis = null;


        // Перехват модуля
        $hook = $this->setHook(__CLASS__, __FUNCTION__, false, 'START');
        if ($hook)
            return $hook;


        $where = array('enabled' => "='1'", 'preview' => '!=""', 'link' => '!="' . $link . '"');

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['preview'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $PHPShopOrm = new PHPShopOrm($this->objBase);
        $PHPShopOrm->debug = $this->debug;
        $result = $PHPShopOrm->select(array('link', 'name', 'icon', 'datas', 'preview'), $where, array('order' => 'datas DESC'), array("limit" => $this->limit));

        // Проверка на еденичную запись
        if ($this->limit > 1)
            $data = $result;
        else
            $data[] = $result;

        if (is_array($data))
            foreach ($data as $row) {

                // Определяем переменные
                $this->set('pageLink', $row['link']);
                $this->set('pageName', $row['name']);
                $this->set('pageIcon', $row['icon']);
                $this->set('pageData', PHPShopDate::get($row['datas']));
                $this->set('pagePreview', Parser(stripslashes($row['preview'])));

                // Перехват модуля
                $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');

                // Подключаем шаблон
                $dis .= parseTemplateReturn($this->getValue('templates.page_mini'));
            }

        return $dis;
    }

}

?>