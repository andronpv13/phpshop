<?php

if (!defined("OBJENABLED")) {
    require_once(dirname(__FILE__) . "/product.class.php");
    require_once(dirname(__FILE__) . "/security.class.php");
}

/**
 * Корзина товаров
 * @author PHPShop Software
 * @version 2.2
 * @package PHPShopClass
 */
class PHPShopCart {

    var $_CART;
    var $message;

    /**
     * Режим проверки остатков на складе
     */
    var $store_check = true;
    var $PHPShopModules;

    /**
     * Конструктор
     */
    function __construct($import_cart = false) {
        global $PHPShopSystem, $PHPShopValutaArray, $PHPShopModules;

        if (!is_array($_SESSION['cart']))
            unset($_SESSION['cart']);

        // Режим контроля склада
        if ($PHPShopSystem->getSerilizeParam('admoption.sklad_status') == 1 or $_SESSION['sklad_status'] == 1)
            $this->store_check = false;


        if (!class_exists('PHPShopProduct')) {
            PHPShopObj::loadClass('array');
            PHPShopObj::loadClass('product');
            PHPShopObj::loadClass('promotions');
        }

        if (!class_exists('PHPShopValutaArray') || !is_object($PHPShopValutaArray)) {
            PHPShopObj::loadClass('array');
            PHPShopObj::loadClass('valuta');
            $PHPShopValutaArray = new PHPShopValutaArray();
        }


        $this->Valuta = $PHPShopValutaArray->getArray();
        $this->format = $PHPShopSystem->getSerilizeParam("admoption.price_znak");

        if ($import_cart)
            $this->_CART = $import_cart;
        else {
            if (empty($_SESSION['cart']) or ! is_array($_SESSION['cart'])) {
                $_SESSION['cart'] = array();
            }
            $this->_CART = &$_SESSION['cart'];
        }

        $this->PHPShopModules = &$PHPShopModules;
    }

    /**
     * Добавление в корзину товара
     * @param int $objID ИД товара
     */
    function add($objID, $num, $parentID = false, $var = false) {

        // Данные по товару
        $objProduct = new PHPShopProduct($objID, $var);

        // Учет свойств товара
        if (!empty($_REQUEST['addname'])) {
            $xid = $objID . '-' . $_REQUEST['addname'];
        } else
            $xid = $objID;

        if ($parentID == 'undefined')
            $parentID = false;

        // Имя товара
        $name = PHPShopSecurity::CleanStr($objProduct->getParam("name"));

        // Проверка на существование товара
        if (!empty($name)) {

            // Массив корзины
            $cart = array(
                "id" => $objProduct->getParam("id"),
                "name" => str_replace('&#43;', '+', $name),
                "price" => $this->getCartProductPrice($objProduct),
                "price_n" => $this->getCartProductPrice($objProduct, 'price_n'),
                "price_purch" => $this->applyCurrency($objProduct->getParam("price_purch"), true),
                "uid" => $objProduct->getParam("uid"),
                "num" => abs($this->_CART[$xid]['num'] + $num),
                "ed_izm" => $objProduct->getParam("ed_izm"),
                "pic_small" => $objProduct->getParam("pic_small"),
                "weight" => $objProduct->getParam("weight"),
                "length" => $objProduct->getParam("length"),
                "height" => $objProduct->getParam("height"),
                "width" => $objProduct->getParam("width"),
                "category" => $objProduct->getParam("category"),
                "type" => $objProduct->getParam("type")
            );

            // Объемный вес
            if (!empty($objProduct->getParam("length")) and ! empty($objProduct->getParam("width")) and ! empty($objProduct->getParam("height")))
                $cart['volume_weight'] = (int) ($objProduct->getParam("length") * $objProduct->getParam("width") * $objProduct->getParam("height")) / 5;

            // Сумма длин сторон
            if (!empty($objProduct->getParam("length")) and ! empty($objProduct->getParam("width")) and ! empty($objProduct->getParam("height")))
                $cart['sum_side'] = (int) ($objProduct->getParam("length") + $objProduct->getParam("width") + $objProduct->getParam("height"));

            if (!empty($parentID)) {
                $objID = $cart['parent'] = intval($parentID);
            }

            // Изображения главного товара
            if (empty($cart['pic_small'])) {
                $objProductParent = new PHPShopProduct($cart['parent']);
                $cart['pic_small'] = $objProductParent->getImage();
                $cart['parent_uid'] = $objProductParent->getParam('uid');
            }

            // Проверка кол-ва товара на складе
            if ($this->store_check) {
                if ($cart['num'] > PHPShopSecurity::TotalClean($objProduct->getParam("items"), 1))
                    $cart['num'] = PHPShopSecurity::TotalClean($objProduct->getParam("items"), 1);
            }

            // Учет свойств товара
            if (!empty($_REQUEST['addname']))
                $cart['name'] = $cart['name'] . '-' . $_REQUEST['addname'];

            // Журнал
            $this->log = $cart;

            // Успешное добавление
            if (!empty($cart['num'])) {
                $this->_CART[$xid] = $cart;

                // сообщение для вывода во всплывающее окно
                $this->message = __("Вы успешно добавили") . " <a href='" . $GLOBALS['SysValue']['dir']['dir'] . "/shop/UID_$objID.html' class='alert-link'>$name</a> 
            " . __("в вашу") . " <a href='" . $GLOBALS['SysValue']['dir']['dir'] . "/order/' class='alert-link'>" . __("корзину") . "</a>";

                $this->log['status'] = true;
            }
            // Товара нет в наличии
            else {

                // сообщение для вывода во всплывающее окно
                $this->message = __("Ошибка добавления") . " <a href='" . $GLOBALS['SysValue']['dir']['dir'] . "/shop/UID_$objID.html' class='alert-link'>$name</a> 
            " . __("в вашу") . " " . __("корзину") . ", " . __('товар отсутствует на складе');

                $this->log['status'] = false;
            }
        }

        return true;
    }

    /**
     * Получение сообщения для всплывающего окна
     */
    function getMessage() {
        return $this->message;
    }

    /**
     * Удаление из корзины товара
     * @param int $objID ИД товара
     */
    function del($objID) {

        if (!empty($this->_CART[$objID])) {
            unset($this->_CART[$objID]);
            return true;
        }
    }

    /**
     * Очистка всей корзины
     */
    function clean() {
        unset($this->_CART);
        $_SESSION['cart'] = array();
        unset($_SESSION['cart']);
    }

    /**
     * Вывод количества товаров
     * @return int
     */
    function getNum() {
        $num = 0;
        if (!empty($this->_CART) and is_array($this->_CART))
            foreach ($this->_CART as $val)
                $num += $val['num'];
        return $num;
    }

    /**
     * Вес корзины
     * @return int
     */
    function getWeight() {
        $weight = 0;
        foreach ($this->_CART as $val)
            $weight += (int) $val['num'] * (int) $val['weight'];
        return $weight;
    }

    /**
     * Максимальная высота
     * @return int
     */
    function getMaxWidth() {
        $width_max = 0;
        foreach ($this->_CART as $val) {
            if ($val['width'] > $width_max)
                $width_max = $val['width'];
        }
        return (int) $width_max;
    }

    /**
     * Максимальная высота
     * @return int
     */
    function getMaxHeight() {
        $height_max = 0;
        foreach ($this->_CART as $val) {
            if ($val['height'] > $height_max)
                $height_max = $val['height'];
        }
        return (int) $height_max;
    }

    /**
     * Максимальная длина
     * @return int
     */
    function getMaxLength() {
        $length_max = 0;
        foreach ($this->_CART as $val) {
            if ($val['length'] > $length_max)
                $length_max = $val['length'];
        }
        return (int) $length_max;
    }

    /**
     * Максимальная сумма сторон
     * @return int
     */
    function getMaxSumSide() {
        $sum_side = 0;
        foreach ($this->_CART as $val) {
            if ($val['sum_side'] > $sum_side)
                $sum_side = $val['sum_side'];
        }
        return (int) $sum_side;
    }

    /**
     * Объемный вес корзины
     * @return int
     */
    function getVolumeWeight() {
        $weight = 0;
        foreach ($this->_CART as $val)
            $weight += (int) $val['num'] * (int) $val['volume_weight'];
        return (int) $weight;
    }

    /**
     * Редактирование количества товара в корзине
     * @param int $objID ИД товара
     * @param int $num количество товара
     * @return int
     */
    function edit($objID, $num, $action = null) {

        // Данные по товару
        $objProduct = new PHPShopProduct(abs($objID));

        // Если корзина не пуста
        if (is_array($this->_CART)) {
            $num = abs($num);
            // если значения совпадают, увеличиваем|уменьшаем кол-во на единицу, иначе на указанное значение.
            if ($num == $this->_CART[$objID]['num'])
                if ($action == "minus")
                    $this->_CART[$objID]['num'] --;
                else
                    $this->_CART[$objID]['num'] ++;
            else
                $this->_CART[$objID]['num'] = $num;

            if (empty($this->_CART[$objID]['num']))
                unset($this->_CART[$objID]);

            // Проверка кол-ва товара на складе
            if ($this->store_check) {
                if ($this->_CART[$objID]['num'] > $objProduct->getParam("items"))
                    $this->_CART[$objID]['num'] = $objProduct->getParam("items");
            }

            return $num;
        }
    }

    /**
     * Сумма корзины
     * @param bool $order параметр заказа
     * @param string $format разделитель тысячных
     * @return float
     */
    function getSum($order = true, $format = '') {

        $sum = 0;
        if (is_array($this->_CART))
            foreach ($this->_CART as $val)
                $sum += $val['num'] * $val['price'];

        // Поправки по курсу
        return number_format($this->applyCurrency($sum, $order), $this->format, '.', $format);
    }

    /**
     * Сумма без скидки корзины
     * @param bool $order параметр заказа
     * @return float
     */
    function getSumNoDiscount($order = true) {

        $sum_n = 0;
        if (is_array($this->_CART))
            foreach ($this->_CART as $val) {
                if ((float) $val['price_n'] > 0)
                    $sum_n += $val['num'] * $val['price_n'];
                else
                    $sum_n += $val['num'] * $val['price'];
            }

        // Поправки по курсу
        return number_format($this->applyCurrency($sum_n, $order), $this->format, '.', '');
    }

    function getSumWithoutPromo($order = true) {

        $sum = 0;
        if (is_array($this->_CART))
            foreach ($this->_CART as $val) {
                if (empty($val['promo_price'])) {
                    $sum += $val['num'] * $val['price'];
                }
            }

        // Поправки по курсу
        return (float) $this->applyCurrency($sum, $order);
    }

    function getSumPromo($order = true) {
        $sum = 0;
        if (is_array($this->_CART))
            foreach ($this->_CART as $val) {
                if (!empty($val['promo_price'])) {
                    $sum += $val['num'] * $val['price'];
                }
            }

        // Поправки по курсу
        return (float) $this->applyCurrency($sum, $order);
    }

    /**
     * Шаблонизатор вывода списка товаров в корзине
     * @global obj $PHPShopOrder
     * @param string $function имя функции шаблона вывода
     * @param array $option массив дополнительных данных
     * @return string
     */
    function display($function, $option = false) {
        global $PHPShopOrder;
        $list = null;

        // Расчет данных с учетом скидки для заказа
        if (is_array($this->_CART)) {
            foreach ($this->_CART as $key => $val) {
                $product = new PHPShopProduct($val['id']);
                $name = PHPShopSecurity::CleanStr($product->getParam("name"));
                if (!empty($name)) {
                    $this->_CART[$key]['price'] = $this->getCartProductPrice($product);
                    $this->_CART[$key]['price_n'] = $this->getCartProductPrice($product, 'price_n');
                    $this->_CART[$key]['total'] = $PHPShopOrder->ReturnSumma($this->_CART[$key]['price'] * $val['num'], 0);

                    if ($this->store_check) {
                        if ($this->_CART[$key]['num'] > PHPShopSecurity::TotalClean($product->getParam("items"), 1)) {
                            if (PHPShopSecurity::TotalClean($product->getParam("items"), 1) > 0) {
                                $this->_CART[$key]['num'] = PHPShopSecurity::TotalClean($product->getParam("items"), 1);
                            } else {
                                unset($this->_CART[$key]);
                            }
                        }
                    }
                } else {
                    unset($this->_CART[$key]);
                }
            }
        }

        if (is_array($this->_CART))
            foreach ($this->_CART as $k => $v)
                if (function_exists($function)) {
                    $option['xid'] = $k;
                    $option['format'] = $this->format;
                    $list .= call_user_func_array($function, array($v, $option));
                }

        return $list;
    }

    /**
     * Итого сумма корзины с доставкой
     * @global obj $PHPShopOrder
     * @return float
     */
    function getTotal() {
        global $PHPShopOrder;
        return $PHPShopOrder->ReturnSumma($this->getSum(), $PHPShopOrder->ChekDiscount($this->getSum()));
    }

    public function isItemInCart($productId) {
        foreach ($this->_CART as $product) {
            // Товар в корзине
            if ((int) $product['id'] === (int) $productId) {
                return true;
            }
            // В корзине подтип товара
            if (isset($product['parent']) && (int) $product['parent'] === (int) $productId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Массив корзины
     * @return array
     */
    function getArray() {
        return $this->_CART;
    }

    /**
     * @param PHPShopProduct $product
     * @param bool $var
     * @param string $column
     * @return format
     */
    public function getCartProductPrice($product, $column = 'price') {
        global $PHPShopSystem;

        // Перехват модуля
        $hook = $this->setHook(__CLASS__, __FUNCTION__, array('product' => $product, 'column' => $column));
        if ($hook)
            return $hook['result'];

        $price_n = $product->getParam('price_n');

        // Промоакции
        $PHPShopPromotions = new PHPShopPromotions();
        $promotions = $PHPShopPromotions->getPrice($product->objRow);

        $prices = [
            'price' => $product->getParam('price'),
            'price2' => $product->getParam('price2'),
            'price3' => $product->getParam('price3'),
            'price4' => $product->getParam('price4'),
            'price5' => $product->getParam('price5')
        ];
        $priceColumn = $PHPShopSystem->getPriceColumn();

        if (is_array($promotions)) {
            $prices[$priceColumn] = $promotions[$column];
            if (!empty($promotions['price_n'])) {
                $price_n = $promotions['price_n'];
            }
        } else {
            if ($column !== 'price_n') {
                $prices[$priceColumn] = $product->getParam($priceColumn);
            }
        }

        return PHPShopProductFunction::GetPriceValuta($product->objID, $column === 'price_n' ? $price_n : array_values($prices), $product->getParam("baseinputvaluta"), true, $column !== 'price_n');
    }

    public function setHook($class_name, $function_name, $data = false, $rout = false) {
        if (!empty($this->PHPShopModules))
            return $this->PHPShopModules->setHookHandler($class_name, $function_name, array(&$this), $data, $rout);
    }

    /**
     * Применение курса к сумме.
     */
    private function applyCurrency($sum, $order) {
        global $PHPShopSystem;

        // Если выбрана другая валюта
        if ($order and isset($_SESSION['valuta'])) {
            $valuta = $_SESSION['valuta'];
            $kurs = $this->Valuta[$valuta]['kurs'];
        } else
            $kurs = $PHPShopSystem->getDefaultValutaKurs();

        return $sum * $kurs;
    }

}

?>