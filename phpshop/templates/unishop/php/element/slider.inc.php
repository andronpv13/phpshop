<?php

class AddToTemplateBuy extends PHPShopProductIndexElements {

    var $debug = false;

    function __construct() {
        $this->objBase = $GLOBALS['SysValue']['base']['products'];
        parent::__construct();
    }

	/**
     * Элемент "сейчас покупают" для главной страницы
     * @return string
     */
    function nowBuy() {

       // Проверка запуска главной страницы
        if (!$this->PHPShopNav->index()) {
            $i = 1;
            $this->limitpos = 10; // Количество выводимых позиций
            $this->limitorders = 10; // Количество запрашиваемых заказов
            $disp = $li = null;
            $enabled = 2;
            $sort = null;

            // Перехват модуля
            $hook = $this->setHook(__CLASS__, __FUNCTION__);
            if ($hook)
                return $hook;

            // Количество ячеек
            $this->cell = 1;

			 // Шаблон ячейки
            $template = 'product_nowbuy';
            $this->SysValue['templates']['product_nowbuy'] = 'product/main_product_forma_nowbuy.tpl';

            if (!empty($enabled)) {

                // Последние заказы
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
                $PHPShopOrm->debug = $this->debug;
                $data = $PHPShopOrm->select(array('orders'), false, array('order' => 'id desc'), array('limit' => $this->limitorders));

                if (is_array($data)) {
                    foreach ($data as $row) {
                        $order = unserialize($row['orders']);
                        $cart = $order['Cart']['cart'];
                        if (is_array($cart))
                            foreach ($cart as $good) {
                                if ($i > $this->limitpos)
                                    break;
                                // Проверка подчиненного товара
                                if (!empty($good['parent']))
                                    $good['id'] = $good['parent'];

                                $sort.=' id=' . intval($good['id']) . ' OR';
                            }
                    }
                    $sort = substr($sort, 0, strlen($sort) - 2);

                    // Если есть товары
                    if (!empty($sort)) {
                        $PHPShopOrm = new PHPShopOrm();
                        $PHPShopOrm->debug = $this->debug;
                        $PHPShopOrm->sql = "select * from " . $this->objBase . " where (" . $sort . ") and enabled='1' LIMIT 0," . $this->limitpos;
                        $PHPShopOrm->comment = __CLASS__ . '.' . __FUNCTION__;
                        $dataArray = $PHPShopOrm->select();
                        if (is_array($dataArray)) {


                                // Количество ячеек для вывода товара
                                $this->set('productInfo', $this->lang('productInfo'));

                                // Добавляем в дизайн ячейки с товарами
                                $this->product_grid($dataArray, $this->cell, $template);

                                // Собираем и возвращаем таблицу с товарами
                                $disp = $this->compile();
                            

                            return $disp;
                        }
                    }
                }
			}   }
    }

}


/**
 * Элемент вывода случайного товарав перменную @showcase@
 */
class AddToTemplate extends PHPShopProductElements {

    var $debug = false;

    function __construct() {
        $this->objBase = $GLOBALS['SysValue']['base']['products'];
        parent::__construct();
    }

    function showcase() {



            // Шаблон ячейки
            $template = 'main_product_forma_5.tpl';
            $this->SysValue['templates']['product_showcase'] = 'product/' . $template . '.tpl';

            // Количество ячеек для вывода товара
            $cell = 2;

            // Кол-во товаров на странице
            $limit = 3;

            // Случаные товары
            //$where['id']=$this->setramdom($limit);
            $where['spec'] = "='1'";
            $where['enabled'] = "='1'";

            $this->dataArray[] = $this->select(array('*'), $where, array('order' => 'RAND()'), array('limit' => $limit));

            // Добавляем в дизайн ячейки с товарами
            $this->product_grid($this->dataArray, $cell, $template, $line = false);

            // Собираем и возвращаем таблицу с товарами
            $this->set('showcase', $this->compile());
        
    }
    /**
     * Изменение формата решетки между товарами c <td> на <li>
     * @return string
     */
    function setCell($d1,$d2=null,$d3=null,$d4=null,$d5=null) {
        $li=null;
        $panel=array('panel_l','panel_r','panel_l','panel_r');
        $arg=func_get_args();
 
        foreach($arg as $key=>$val) {
            if(!empty($val)) {
                $li.='<li class="'.$panel[$key].'">'.$val.'</li>';
            }
        }
 
        return $li;
    }
    /**
     * Изменение формата решетки между товарами c <td> на <li>, компиляция списка в <ul>
     * @return string
     */
    function compile() {
        $ul='<ul>'.$this->product_grid.'</ul>';
        $this->product_grid=null;
        return $ul;
    }

}





// Добавляем в шаблон элемент вывода случайного товара в индекс
$AddToTemplate = new AddToTemplate();
$AddToTemplate->showcase();


// Последние покупки
$AddToTemplateBuy = new AddToTemplateBuy();
$AddToTemplateBuy->init('nowBuy');
?>