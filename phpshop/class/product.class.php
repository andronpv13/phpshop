<?php

if (!defined("OBJENABLED")) {
    require_once(dirname(__FILE__) . "/obj.class.php");
    require_once(dirname(__FILE__) . "/array.class.php");
}

/**
 * Библиотека данных по товарам
 * @author PHPShop Software
 * @version 1.6
 * @package PHPShopObj
 */
class PHPShopProduct extends PHPShopObj {

    /**
     * Конструктор
     * @param Int $objID ИД товара
     * @param string $var параметр поиска товара [id|uid]
     */
    function __construct($objID, $var = 'id') {
        $this->objID = $objID;
        $this->objBase = $GLOBALS['SysValue']['base']['products'];
        $this->cache = true;
        $this->debug = false;
        $this->cache_format = [];
        $this->var=$var;

        // Учет подтипов для выборки по артикулу
        if (empty($var)) {
            if (PHPShopProductFunction::true_parent($objID))
                $var = 'uid';
            else {
                $this->objID = PHPShopSecurity::TotalClean($objID, 1);
                $var = 'id';
            }
        }
        // Настраиваемая опция поиска товара
        else
            $this->objID = PHPShopSecurity::true_search($objID);

        parent::__construct($var);
    }

    /**
     * Имя товара
     * @return string
     */
    function getName() {
        return parent::getParam("name");
    }

    /**
     * ИД валюты товара
     * @return int
     */
    function getValutaID() {
        return parent::getParam("baseinputvaluta");
    }

    /**
     * Цена товара
     * @return float 
     */
    function getPrice() {
        $price_array = array($this->objRow['price'], $this->objRow['price2'], $this->objRow['price3'], $this->objRow['price4'], $this->objRow['price5']);
        return PHPShopProductFunction::GetPriceValuta($this->objID, $price_array, $this->objRow['baseinputvaluta']);
    }
    
     /**
     * Старая цена товара
     * @return float 
     */
    function getPriceOld() {
        $price_array = array($this->objRow['price_n'], $this->objRow['price2'], $this->objRow['price3'], $this->objRow['price4'], $this->objRow['price5']);
        return PHPShopProductFunction::GetPriceValuta($this->objID, $price_array, $this->objRow['baseinputvaluta']);
    }

    /**
     * Изображение товара
     * @return string 
     */
    function getImage() {
        return parent::getParam("pic_small");
    }

    /**
     * Удалить товар со склада
     * @param int $count кол-во
     * @param int $parent ИД родителя
     * @param int $warehouseId ИД склада
     */
    public function removeFromWarehouse($count, $parent = 0, $warehouseId = null)
    {
        // Склад
        if (!empty($warehouseId)) {
            @$this->objRow['items' . $warehouseId] -= $count;
        }
        $this->objRow['items'] -= $count;

        $this->applyWarehouseControl($parent, $warehouseId,$count);
    }


    /**
     * Добавить товар на склад
     * @param int $count кол-во
     * @param int $parent ИД родителя
     * @param int $warehouseId ИД склада
     */
    public function addToWarehouse($count, $parent = 0, $warehouseId = null)
    {
        // Склад
        if (!empty($warehouseId)) {
            $this->objRow['items' . $warehouseId] += $count;
        }
        $this->objRow['items'] += $count;

        $this->applyWarehouseControl($parent, $warehouseId,$count);
    }

   
    /**
     * Контроль склада
     * @param int $parent ИД родителя
     * @param int $warehouseId ИД склад
     * @param int $count кол-во
     */
    public function applyWarehouseControl($parent = 0, $warehouseId = null,$count = 0)
    {
        $PHPShopSystem = new PHPShopSystem();
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
        $PHPShopOrm->debug = false;

        $product_update = [
            'items_new' => $this->objRow['items'],
            'datas_new' => time()
        ];
        if(!empty($warehouseId)) {
            $product_update['items' . $warehouseId . '_new'] = $this->objRow['items' . $warehouseId];
        }

        $disabled = true;
        
        // Если товар НЕ подтип - проверяем наличие подтипов и их наличие на складе
        if((int) $this->getParam('parent_enabled') === 0) {
            $parentsIds = explode(",", $this->getParam('parent'));

            if(is_array($parentsIds)) {
                $parentsIds = array_diff($parentsIds, ['']);
                if(count($parentsIds) > 0) {
                    // Выбираем включенные и НЕ под заказ подтипы
                    if ($PHPShopSystem->ifSerilizeParam('1c_option.update_option'))
                        $parents = $PHPShopOrm->getList(['*'], ['uid' => ' IN ("' . @implode('","', $parentsIds) . '")', 'enabled' => "='1'", 'sklad' => "!='1'"]);
                    else
                        $parents = $PHPShopOrm->getList(['*'], ['id' => ' IN ("' . @implode('","', $parentsIds) . '")', 'enabled' => "='1'", 'sklad' => "!='1'"]);

                    foreach ($parents as $parentItem) {
                        if((int) $parentItem['items'] > 0) {
                            $disabled = false;
                            break;
                        }
                    }
                } else {
                    $disabled = $this->objRow['items'] < 1;
                }
            }
        } else {
            $disabled = $this->objRow['items'] < 1;
        }

        switch ($PHPShopSystem->getSerilizeParam('admoption.sklad_status')) {
            
            // Товара нет в наличии
            case(3):
                if ($disabled) {
                    $product_update['sklad_new'] = 1;
                    $this->objRow['sklad'] = 1;
                    //$product_update['enabled_new'] = 1;
                    //$this->objRow['enabled'] = 1;
                } else {
                    $product_update['sklad_new'] = 0;
                    $this->objRow['sklad'] = 0;
                    //$product_update['enabled_new'] = 1;
                    //$this->objRow['enabled'] = 1;
                }
                break;

            // Товар убирается с продаж
            case(2):
                if ($disabled) {
                    $product_update['enabled_new'] = 0;
                    $this->objRow['enabled'] = 0;
                    //$product_update['sklad_new'] = 0;
                    //$this->objRow['sklad'] = 0;
                } else {
                    $product_update['enabled_new'] = 1;
                    $this->objRow['enabled'] = 1;
                    //$product_update['sklad_new'] = 0;
                    //$this->objRow['sklad'] = 0;

                }
                break;
            default:
                break;
        }

        // Обновляем данные
        $PHPShopOrm->update($product_update, [$this->var => '=' . (int) $this->objID]);

        // Если товар подтип
        if($parent > 0) {
            $mainProduct = new PHPShopProduct($parent);
            $parentsIds = explode(",", $mainProduct->getParam('parent'));

            if(is_array($parentsIds)) {
                $mainProductDisabled = true; // по умолчанию выключен, если есть в наличии какой-то подтип - включим
                // Выбираем включенные и НЕ под заказ подтипы
                if ($PHPShopSystem->ifSerilizeParam('1c_option.update_option'))
                    $parents = $PHPShopOrm->getList(['*'], ['uid' => ' IN ("' . @implode('","', $parentsIds) . '")', 'enabled' => "='1'", 'sklad' => "!='1'"]);
                else
                    $parents = $PHPShopOrm->getList(['*'], ['id' => ' IN ("' . @implode('","', $parentsIds) . '")', 'enabled' => "='1'", 'sklad' => "!='1'"]);

                foreach ($parents as $parentItem) {
                    if((int) $parentItem['items'] > 0) {
                        $mainProductDisabled = false;
                        break;
                    }
                }

                $mainProductUpdate = [];
                switch ($PHPShopSystem->getSerilizeParam('admoption.sklad_status')) {
                    case(3):
                        if ($mainProductDisabled) {
                            $mainProductUpdate['sklad_new'] = 1;
                            $mainProductUpdate['enabled_new'] = 1;
                            $mainProductUpdate['p_enabled_new'] = 0;
                        } else {
                            $mainProductUpdate['sklad_new'] = 0;
                            $mainProductUpdate['enabled_new'] = 1;
                            $mainProductUpdate['p_enabled_new'] = 1;
                        }
                        break;

                    case(2):
                        if ($mainProductDisabled) {
                            $mainProductUpdate['enabled_new'] = 0;
                            $mainProductUpdate['sklad_new'] = 0;
                            $mainProductUpdate['p_enabled_new'] = 0;
                        } else {
                            $mainProductUpdate['enabled_new'] = 1;
                            $mainProductUpdate['sklad_new'] = 0;
                            $mainProductUpdate['p_enabled_new'] = 1;
                        }
                        break;
                    default:
                        break;
                }
                
                // Склад главного товара
                $mainProductUpdate['items_new'] = $mainProduct->getParam('items') - $count;

                // Обновляем данные
                $PHPShopOrm->update($mainProductUpdate, [$this->var => '=' . (int) $parent]);
            }
        }
    }
}

/**
 * Массив данных по товарам
 * @author PHPShop Software
 * @version 1.2
 * @package PHPShopArray
 */
class PHPShopProductArray extends PHPShopArray {

    /**
     * Конструктор
     * @param array $sql SQL условие выборки
     */
    function __construct($sql = false) {
        $this->objSQL = $sql;
        $this->objBase = $GLOBALS['SysValue']['base']['products'];
        parent::__construct('id', 'uid', 'name', 'category', 'price', 'price_n', 'sklad', 'odnotip', 'vendor', 'title_enabled', 'datas', 'page', 'user', 'descrip_enabled', 'keywords_enabled', 'pic_small', 'pic_big', 'parent', 'baseinputvaluta','items','ed_izm');
    }

}

/**
 * Библиотека функций по товарам
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopClass
 */
class PHPShopProductFunction {

    static function getLink() {
        $Arg = func_get_args();
        $link = '/shop/UID_' . $Arg[0] . '.html';
        return $link;
    }

    /**
     * Проверка на подтип товара из 1С или CML
     * @param string $str артикул товара или список подтипов
     * @return bool
     */
    static function true_parent($str) {
        if (strstr($str, '-')){
            if (strstr($str, ',')){
                $str_array = explode(",",$str);
                if(is_array($str_array))
                    return preg_match("/^[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}$/", $str_array[0]);
                
            }
            else return preg_match("/^[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}$/", $str);
        }
        else
            return preg_match("/^[a-zA-Z0-9]{36}$/", $str);

    }

    /**
     * Цена с учетом валюты
     * @param int $id ИД товара
     * @param mixed $price_array стоимость товара
     * @param int $baseinputvaluta ИД валюты товара
     * @param bool $order параметр расчета заказе [true/false]
     * @param bool $check_user_price учитывать персональную колонку цен пользователя [true/false]
     * @return format
     */
    static function GetPriceValuta($id, $price_array, $baseinputvaluta = false, $order = false, $check_user_price = true) {
        global $PHPShopValutaArray, $PHPShopSystem;

        if (!is_array($price_array))
            $price = $price_array;
        else
            $price = $price_array[0];

        if (!$PHPShopValutaArray)
            $PHPShopValutaArray = new PHPShopValutaArray();

        $LoadItems['Valuta'] = $PHPShopValutaArray->getArray();
        $LoadItems['System'] = $PHPShopSystem->getArray();
        $LoadItems['System']['dengi'] = $PHPShopSystem->getParam("dengi");

        // Форматирование цены
        $format = intval($PHPShopSystem->getSerilizeParam("admoption.price_znak"));

        if (!empty($_SESSION['UsersStatus']) and !empty($check_user_price)) {

            if (empty($_SESSION['UsersStatusPice'])) {

                // Выборка из базы нужной колонки цены для автор. пользователя
                $PHPShopUser = new PHPShopUserStatus($_SESSION['UsersStatus']);
                $GetUsersStatusPrice = $PHPShopUser->getPrice();
                $_SESSION['UsersStatusPice'] = $GetUsersStatusPrice;
            }
            else
                $GetUsersStatusPrice = $_SESSION['UsersStatusPice'];

            if ($GetUsersStatusPrice > 1) {
                $pole = "price" . $GetUsersStatusPrice;

                // Если не известны другие колонки цен
                if (!is_array($price_array)) {
                    $PHPShopProduct = new PHPShopProduct($id);
                    $user_price = $PHPShopProduct->getParam($pole);
                } else {
                    // Берем цену из массива
                    $user_price = $price_array[$GetUsersStatusPrice - 1];
                }
                if (!empty($user_price))
                    $price = $user_price;
            }
        }

        // Витрины с колонками цен
        if (defined("HostPrice") and HostPrice > 1) {
            $pole = "price" . HostPrice;
            $PHPShopProduct = new PHPShopProduct($id);
            $server_price = $PHPShopProduct->getParam($pole);

            if (!empty($server_price))
                $price = $server_price;
        }

        // Учет валюты товара
        if ($baseinputvaluta) { //Если прислали баз. валюту
            if ($baseinputvaluta !== $LoadItems['System']['dengi']) {//Если присланная валюта отличается от базовой
                if ($LoadItems['Valuta'][$baseinputvaluta]['kurs'] > 0)
                    $price = $price / $LoadItems['Valuta'][$baseinputvaluta]['kurs']; //Приводим цену в базовую валюту
            }
        }

        // Если выбрана другая валюта, order - флаг для расчета корзины толкьо в деф. валюте
        if ($order)
            $valuta = $LoadItems['System']['dengi'];
        elseif (isset($_SESSION['valuta']))
            $valuta = $_SESSION['valuta'];
        else
            $valuta = $LoadItems['System']['dengi'];

        // Правка на курс
        if (!empty($valuta))
            $price = $price * $LoadItems['Valuta'][$valuta]['kurs'];

        // Наценка
        $price = ($price + (($price * intval($LoadItems['System']['percent'])) / 100));

        return number_format($price, $format, '.', '');
    }
}

?>