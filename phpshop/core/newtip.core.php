<?php

/**
 * Обработчик товаров новинок
 * @author PHPShop Software
 * @version 1.5
 * @package PHPShopShopCore
 */
class PHPShopNewtip extends PHPShopShopCore {

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

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
            return true;

        // Путь для навигации
        $this->objPath = './newtip_';

        // Валюта
        $this->set('productValutaName', $this->currency());

        $this->set('catalogCategory', $this->lang('newprod'));

        // Количество ячеек
        if (empty($this->cell))
            $this->cell = $this->calculateCell("newtip", $this->PHPShopSystem->getValue('num_row_adm'));

        // Кол-во товаров на странице
        // если 0 делаем по формуле кол-во колонок * 2 строки.
        if (!$this->num_row)
            $this->num_row = (6 - $this->cell) * $this->cell;

        $where['enabled'] = "='1'";
        $where['parent_enabled'] = "='0'";

        // Мультибаза
        $queryMultibase = $this->queryMultibase();
        if (!empty($queryMultibase))
            $where['enabled'] .= ' ' . $queryMultibase;

        $order = $this->query_filter("newtip='1'");
        $where['newtip'] = "='1'";

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

        // Формула вывода
        $this->new_enabled = (int) $this->PHPShopSystem->getSerilizeParam("admoption.new_enabled");

        if (!is_array($this->dataArray) && $this->new_enabled > 0) {
            unset($where['newtip']);
            switch ($this->new_enabled) {
                case 1:
                    $order = $this->query_filter("spec='1'");
                    $where['spec'] = "='1'";
                    break;

                case 2:
                    $this->query_filter("enabled='1'");
                    $order = array('order' => 'id DESC');
                    break;
            }

            if (is_array($order)) {
                $this->dataArray = parent::getListInfoItem(array('*'), $where, $order, __CLASS__, __FUNCTION__);
            } else {
                // Сложный запрос
                $this->PHPShopOrm->sql = $order;
                $this->PHPShopOrm->comment = __CLASS__ . '.' . __FUNCTION__;
                $this->dataArray = $this->PHPShopOrm->select();
                $this->PHPShopOrm->clean();
            }
        }

        // Пагинатор
        if (is_array($this->dataArray))
            $this->setPaginator(count($this->dataArray));

        // Добавляем в дизайн ячейки с товарами
        $grid = $this->product_grid($this->dataArray, $this->cell);
        if (empty($grid))
            $grid = PHPShopText::h2($this->lang('empty_product_list'));
        $this->add($grid, true);

        // Заголовок
        $this->title = $this->lang('newprod') . " - " . $this->PHPShopSystem->getParam('title');

        // Навигация хлебные крошки
        $this->navigation(null, __('Новинки'));

        // Перехват модуля
        $this->setHook(__CLASS__, __FUNCTION__, $this->dataArray, 'END');

        // Подключаем шаблон
        $this->parseTemplate($this->getValue('templates.product_page_spec_list'));
    }

    /**
     * Генерация SQL запроса со сложными фильтрами и условиями
     * @return mixed
     */
    function query_filter($where = false, $v = false) {

        // Перехват модуля
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $where);
        if (!empty($hook))
            return $hook;

        return parent::query_filter($where);
    }

}

?>