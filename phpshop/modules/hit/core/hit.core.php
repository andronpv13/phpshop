<?php

class PHPShopHit extends PHPShopShopCore {

    var $debug = false;
    var $cache = true;
    var $cache_format = array('content', 'yml_bid_array');
    var $cell;

    /**
     * Конструктор
     */
    function __construct() {

        parent::__construct();
        $this->PHPShopOrm->cache_format = $this->cache_format;
    }

    /**
     * Вывод списка товаров
     */
    function index() {

        // Путь для навигации
        $this->objPath = './hit_';

        // Валюта
        $this->set('productValutaName', $this->currency());

        $this->set('catalogCategory', 'Хиты');

        $PHPShopOrm = new PHPShopOrm('phpshop_modules_hit_system');
        $options = $PHPShopOrm->select(array('*'));

        (int) $options['hit_page'] > 0 ? $this->cell = (int) $options['hit_page'] : $this->cell = 4;

        // Фильтр сортировки
        $order = $this->query_filter("hit='1'");

        // Кол-во товаров на странице
        // если 0 делаем по формуле кол-во колонок * 2 строки.
        if (!$this->num_row)
            $this->num_row = (6 - $this->cell) * $this->cell;

        $where['hit'] = "='1'";
        $where['enabled'] = "='1'";
        $where['parent_enabled'] = "='0'";

        // Мультибаза
        $queryMultibase = $this->queryMultibase();
        if (!empty($queryMultibase))
            $where['enabled'].= ' ' . $queryMultibase;

        // Простой запрос
        if (is_array($order)) {
            $this->dataArray = parent::getListInfoItem(array('*'), $where, $order, __CLASS__, __FUNCTION__);
        } else {
            // Сложный запрос
            $this->PHPShopOrm->sql = $order;
            $this->PHPShopOrm->comment = __CLASS__ . '.' . __FUNCTION__;
            $this->dataArray = $this->PHPShopOrm->select();
            $this->PHPShopOrm->clean();
        }


        // Пагинатор
        if (is_array($order))
            $this->setPaginator(count($this->dataArray));

        // Добавляем в дизайн ячейки с товарами
        $grid = $this->product_grid($this->dataArray, $this->cell);
        if (empty($grid))
            $grid = PHPShopText::h2($this->lang('empty_product_list'));
        $this->add($grid, true);

        // Заголовок
        $this->title = 'Хиты' . " - " . $this->PHPShopSystem->getParam('title');

        // Навигация хлебные крошки
        $this->navigation(null, __('Хиты'));

        // Подключаем шаблон
        $this->parseTemplate($this->getValue('templates.product_page_spec_list'));
    }
    
    function query_filter($where = false) {

        // Перехват модуля
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $where);
        if (!empty($hook))
            return $hook;

        return parent::query_filter($where);
    }

}

?>