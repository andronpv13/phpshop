<?php

/**
 * Обработчик новостей
 * @author PHPShop Software
 * @version 1.7
 * @package PHPShopCore
 */
class PHPShopNews extends PHPShopCore {

    /**
     * Режим отладки
     * @var bool
     */
    var $debug = false;
    var $empty_index_action = true;
    var $odnootip_cell_center = 2;
    var $odnootip_cell_block = 1;

    /**
     * Конструктор
     */
    function __construct() {

        // Имя Бд
        $this->objBase = $GLOBALS['SysValue']['base']['news'];

        // Путь для навигации
        $this->objPath = "/news/news_";

        // Список экшенов
        $this->action = array("nav" => array("index", "ID"));
        parent::__construct();

        // Имя Бд
        $this->objBase = $GLOBALS['SysValue']['base']['news'];
    }

    /**
     * Однотипные товары
     * @param array $row массив данных
     */
    function odnotip($row) {
        global $PHPShopProductIconElements;

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
        if (is_array($odnotip))
            foreach ($odnotip as $value) {
                if (!empty($value))
                    $odnotipList.=' id=' . trim($value) . ' OR';
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
     * Экшен по умолчанию
     */
    function index() {

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
            return true;

        $where['datau'] = '<'.time();

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['datau'].= ' and (servers ="" or servers REGEXP "i1000i")';

        // Выборка данных
        $this->dataArray = parent::getListInfoItem(array('*'), $where, array('order' => 'datau DESC'));

        // 404
        if (!isset($this->dataArray))
            return $this->setError404();

        if (is_array($this->dataArray))
            foreach ($this->dataArray as $row) {

                // Определяем переменные
                $this->set('newsId', $row['id']);
                $this->set('newsData', $row['datas']);
                $this->set('newsZag', $row['zag']);
                $this->set('newsKratko', $row['kratko']);
                $this->set('newsIcon', $row['icon']);

                // Перехват модуля
                $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');

                // Подключаем шаблон
                $this->addToTemplate($this->getValue('templates.main_news_forma'));
            }

        // Пагинатор
        $this->setPaginator();

        // Мета
        $this->title = __("Новости") . " - " . $this->PHPShopSystem->getValue("name");
        $this->description = __('Новости') . '  ' . $this->PHPShopSystem->getValue("name");
        $this->keywords = __('Новости') . ', ' . $this->PHPShopSystem->getValue("name");

        $page = $this->PHPShopNav->getId();
        if ($page > 1) {
            $this->description.= ' Часть ' . $page;
            $this->title.=' - Страница ' . $page;
        }

        // Навигация хлебные крошки
        $this->navigation(false, __("Новости"));

        // Перехват модуля
        $this->setHook(__CLASS__, __FUNCTION__, $this->dataArray, 'END');

        // Подключаем шаблон
        return $this->parseTemplate($this->getValue('templates.news_page_list'));
    }

    /**
     * Экшен сортировки новостей по дате из календаря
     */
    function timestamp() {

        if (PHPShopSecurity::true_num($_GET['timestamp'])) {
            $year = date("Y", $_GET['timestamp']);
            $month = date("m", $_GET['timestamp']);
            $day = date("d", $_GET['timestamp']);
            $timestampstart = intval($_GET['timestamp']);
            $timestampend = mktime(23, 59, 59, $month, $day, $year);

            // Выборка данных
            $this->PHPShopOrm->sql = 'select * from ' . $this->objBase . ' where datau>=' . $timestampstart . ' AND datau<=' . $timestampend . ' order by datau desc';
            $this->dataArray = $this->PHPShopOrm->select();

            // 404
            if (!isset($this->dataArray))
                return $this->setError404();

            if (is_array($this->dataArray))
                foreach ($this->dataArray as $row) {

                    // Определяем переменные
                    $this->set('newsId', $row['id']);
                    $this->set('newsData', $row['datas']);
                    $this->set('newsZag', $row['zag']);
                    $this->set('newsKratko', $row['kratko']);

                    // Перехват модуля
                    $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');


                    // Подключаем шаблон
                    $this->addToTemplate($this->getValue('templates.main_news_forma'));
                }

            // Мета
            $this->title = "Новости - " . $this->PHPShopSystem->getValue("name");

            // Перехват модуля
            $this->setHook(__CLASS__, __FUNCTION__, $this->dataArray, 'END');

            // Подключаем шаблон
            $this->parseTemplate($this->getValue('templates.news_page_list'));
        } else {
            $this->setError404();
        }
    }

    /**
     * Экшен выборки подробной информации при наличии переменной навигации ID
     * @return string
     */
    function ID() {

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
            return true;

        // Безопасность
        if (!PHPShopSecurity::true_num($this->PHPShopNav->getId()))
            return $this->setError404();

        $where['id'] = '='.$this->PHPShopNav->getId();
        $where['datau'] = '<'.time();

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['datau'].= ' and (servers ="" or servers REGEXP "i1000i")';

        // Выборка данных
        $row = parent::getFullInfoItem(array('*'), $where);

        // 404
        if (!isset($row))
            return $this->setError404();

        // Однотипные товары
        $this->odnotip($row);

        // Определяем переменые
        $this->set('newsData', $row['datas']);
        $this->set('newsZag', $row['zag']);

        if(empty($row['podrob'])){
            $row['podrob'] = $row['kratko'];
            $row['kratko']=null;
        }
        
        $this->set('newsKratko', $row['kratko']);
        $this->set('newsPodrob', Parser($row['podrob']));
        $this->set('newsIcon', $row['icon']);

        // Перехват модуля
        $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');

        // Подключаем шаблон
        $this->addToTemplate($this->getValue('templates.main_news_forma_full'));

        // Мета
        $this->title = strip_tags($row['zag']) . " - " . $this->PHPShopSystem->getValue("name");
        $this->description = strip_tags($row['kratko']);
        $this->lastmodified = PHPShopDate::GetUnixTime($row['datas']);

        // Генератор keywords
        include('./phpshop/lib/autokeyword/class.autokeyword.php');
        $this->keywords = callAutokeyword($row['kratko']);

        // Навигация хлебные крошки
        $this->navigation(false, null, array('name' => __('Новости'), 'url' => '/news/'));

        // Перехват модуля
        $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');

        // Подключаем шаблон
        $this->parseTemplate($this->getValue('templates.news_page_full'));
    }
}

?>