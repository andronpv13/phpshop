<?php

/**
 * Обработчик распродаж с учетом промоакций
 * @author PHPShop Software
 * @version 1.4
 * @package PHPShopShopCore
 */
class PHPShopNewprice extends PHPShopShopCore {

    var $debug = false;
    var $cache = true;
    var $cache_format = array('content', 'yml_bid_array');

    /**
     * Сетка товаров
     * @var int 
     */
    var $cell;

    /**
     * Конструктор
     */
    function __construct() {

        parent::__construct();
        $this->PHPShopOrm->cache_format = $this->cache_format;

        // Навигация хлебные крошки
        $this->navigation(null, __('Распродажа'));
    }

    function promotions() {
        global $PHPShopPromotions;

        $categories = $products = $where = null;
        if (is_array($PHPShopPromotions->promotionslist)) {
            
            foreach ($PHPShopPromotions->promotionslist as $pro) {

                // Проверим активность промоакции
                $date_act = $PHPShopPromotions->promotion_check_activity($pro['active_check'], $pro['active_date_ot'], $pro['active_date_do']);

                // Проверяем статус пользователя
                $user_act = $PHPShopPromotions->promotion_check_userstatus(unserialize($pro['statuses']));

                if ($date_act == 1 && $user_act and $pro['sum_order_check'] == 0) {
                    $categories .= $pro['categories'];
                    $products .= $pro['products'];
                }
            }

            if (!empty($categories)) {
                $category = array_diff(explode(',', $categories), ['']);
                $where .= 'category IN (' . implode(",", $category) . ')';
            }

            if (!empty($products))
                $where .= ' or id IN (' . $products . ')';

            if (!empty($where))
                return ' and (' . $where . ' or price_n!="")';
        }
    }

    /**
     * Вывод списка товаров
     */
    function index() {

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
            return true;

        // Путь для навигации
        $this->objPath = './newprice_';

        // Валюта
        $this->set('productValutaName', $this->currency());

        $this->set('catalogCategory', $this->lang('newprice'));

        // Количество ячеек
        if (empty($this->cell))
            $this->cell = $this->calculateCell("newprice", $this->PHPShopSystem->getValue('num_vitrina'));

        $promotions = $this->promotions();
        if (empty($promotions))
            $where['price_n'] = "!=''";

        // Фильтр сортировки
        $order = $this->query_filter("price_n!=''" . $promotions);


        // Кол-во товаров на странице
        // если 0 делаем по формуле 6 минус кол-во колонок * кол-во колонок.
        if (!$this->num_row)
            $this->num_row = (6 - $this->cell) * $this->cell;


        $where['enabled'] = "='1'";
        $where['parent_enabled'] = "='0'" . $promotions;

        // Мультибаза
        $queryMultibase = $this->queryMultibase();
        if (!empty($queryMultibase))
            $where['enabled'] .= ' ' . $queryMultibase;

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
        if (is_array($this->dataArray))
            $this->setPaginator(count($this->dataArray));

        // Добавляем в дизайн ячейки с товарами
        $grid = $this->product_grid($this->dataArray, $this->cell);
        if (empty($grid))
            $grid = PHPShopText::h2($this->lang('empty_product_list'));
        $this->add($grid, true);

        // Заголовок
        $this->title = $this->lang('newprice') . " - " . $this->PHPShopSystem->getParam('title');

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