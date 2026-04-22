<?php

if (!defined("OBJENABLED")) {
    require_once(dirname(__FILE__) . "/obj.class.php");
}

/**
 * Библиотека данных администраторов
 * @author PHPShop Software
 * @version 1.3
 * @package PHPShopObj
 */
class PHPShopUser extends PHPShopObj {

    /**
     * Конструктор
     * @param Int $objID ИД пользователя
     */
    function __construct($objID) {
        $this->objID = $objID;
        $this->cache = false;
        $this->objBase = $GLOBALS['SysValue']['base']['shopusers'];
        parent::__construct();
    }

    /**
     * Вывод данных по ключу
     * @param string $str ключ
     * @return string 
     */
    function getParam($str) {
        return str_replace("\"", "&quot;", parent::getParam($str));
    }

    /**
     * Вывод списка адресов пользователя для выбора
     * @return string 
     */
    function getAdresList() {
        $data_adres = unserialize(parent::getParam('data_adres'));
        if (!is_array($data_adres) OR !count($data_adres['list']))
            return "";
        
        $disp = null;

        foreach ($data_adres['list'] as $index => $data_adres_one) {
            
            
            
            $dis = "";
            foreach ($data_adres_one as $key => $value) {
                if ($value)
                    $dis .= ", " . $value;
                
                $data_adres['list'][$index][$key]=htmlspecialchars_decode($value,ENT_QUOTES);
                
            }
            if ($dis) {
                if ($index == $data_adres['main'])
                    $sel = 'selected="selected"';
                else
                    $sel = "";
                $disp .= '<option value="' . $index . '" ' . $sel . '>' . substr($dis, 2) . '</option>';
            }
        }
        if ($disp)
            $disp = '
                <b>'.__('Выбрать адрес доставки').'</b>    
                <select name="adres_id" id="adres_id" class="form-control selectpicker show-menu-arrow">
                <option value="none">'.__('Создать новый адрес').'</option>
                ' . $disp . '
                </select><br>
                <input type="checkbox" name="adres_this_default" value="1">  '.__('сделать выбранный вариант адресом по умолчанию').'
                <input type="hidden" class="adresListJson" value=\'' . PHPShopString::json_safe_encode($data_adres['list']) . '\'>
                ';
        return $disp;
    }

    /**
     * Вывод данных по ключу
     * @param string $str ключ
     * @return string 
     */
    function getValue($str) {
        return $this->getParam($str);
    }

    /**
     * Вывод логина пользователя
     * @return string
     */
    function getLogin() {
        return $this->getParam("login");
    }
    
    /**
     * Вывод имени пользователя
     * @return string
     */
    function getName() {
        return $this->getParam("name");
    }

    /**
     * Вывод персональной скидки
     * @return string
     */
    function getPersonalDiscount() {
        return $this->getParam("cumulative_discount");
    }


    /**
     * Вывод ID статуса
     * @return int 
     */
    function getStatus() {
        return $this->getParam("status");
    }

    /**
     * Вывод названия статуса
     * @return string 
     */
    function getStatusName() {
        $PHPShopUserStatus = new PHPShopUserStatus($this->getStatus());
        return $PHPShopUserStatus->getParam("name");
    }

    /**
     * Вывод размера скидки
     * @return float 
     */
    function getDiscount() {
        $PHPShopUserStatus = new PHPShopUserStatus($this->getStatus());
        return $PHPShopUserStatus->getDiscount();
    }
    
    /**
     * Вывод бонусов
     * @return int
     */
    function getBonus(){
        return intval($this->getParam("bonus"));
    }
    
}

/**
 * Библиотека данных пользователей
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopObj
 */
class PHPShopUserStatus extends PHPShopObj {

    /**
     * Конструктор
     * @param Int $objID ИД статуса пользователя
     * @param array $import_data массив импорта данных
     */
    function __construct($objID, $import_data = null) {
        $this->objID = $objID;
        $this->cache = true;
        $this->objBase = $GLOBALS['SysValue']['base']['table_name28'];
        parent::__construct('id', $import_data);
    }

    /**
     * Вывод колонки прайса у пользователя
     * @return int
     */
    function getPrice() {
        return parent::getParam("price");
    }

    /**
     * Вывод скидки у пользователя
     * @return float
     */
    function getDiscount() {
        //Скидка по статусу
        $discount_status = parent::getParam("discount");
        //Персональная скидка
        $PHPShopUser = new PHPShopUser($_SESSION['UsersId']);
        $discount_user = $PHPShopUser->getPersonalDiscount();
        //Максимальная
        $discount = max($discount_status, $discount_user);

        return $discount;
    }

    public function isDisplayWarehouse()
    {
        return (int) $this->getParam('warehouse') === 1;
    }
}

/**
 * Библиотека функций для пользователей
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopClass
 */
class PHPShopUserFunction {

    /**
     * Проверка наивысшей скидки у пользователя
     * @param float $mysum стоимость заказа
     * @return array
     */
    static function ChekDiscount($mysum) {
        global $PHPShopSystem,$link_db;

        $maxsum = 0;
        $userdiscount = 0;
        $maxdiscount = 0;

        $sql = "select * from " . $GLOBALS['SysValue']['base']['table_name23'] . " where sum < '$mysum' and enabled='1'";
        $result = mysqli_query($link_db,$sql);
        while ($row = mysqli_fetch_array($result)) {
            $sum = $row['sum'];
            if ($sum > $maxsum) {
                $maxsum = $sum;
                $maxdiscount = $row['discount'];
            }
        }

        if (!empty($_SESSION['UsersStatus'])) {
            $PHPShopUserStatus = new PHPShopUserStatus($_SESSION['UsersStatus']);
            $userdiscount = $PHPShopUserStatus->getDiscount();
        }
        else
            $userdiscoun = 0;

        if ($userdiscount > $maxdiscount)
            $maxdiscount = $userdiscount;

        $sum = $mysum - ($mysum * @$maxdiscount / 100);
        $format = $PHPShopSystem->getSerilizeParam("admoption.price_znak");
        $array = array(0 + @$maxdiscount, number_format($sum, $format, ".", ""));

        return $array;
    }

}


/**
 * Массив данных по статусам покупателей
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopArray
 */
class PHPShopUserStatusArray extends PHPShopArray {

    function __construct() {
        $this->objBase=$GLOBALS['SysValue']['base']['shopusers_status'];
        $this->objSQL=array('enabled'=>"='1'");
        parent::__construct('id',"name",'discount','price');
    }
    
    
    
    
}
?>