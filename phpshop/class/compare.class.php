<?php

if (!defined("OBJENABLED")) {
    require_once(dirname(__FILE__) . "/product.class.php");
    require_once(dirname(__FILE__) . "/security.class.php");
}

/**
 * Добавление товаров в сравнение
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopClass
 */
class PHPShopCompare {

    var $_COMPARE;
    var $message;

    /**
     * Конструктор
     */
    function __construct() {
        $this->_COMPARE = &$_SESSION['compare'];
    }
    
    /**
     * Удаление из сравнения товара
     * @param int $objID ИД товара
     */
    function del($objID) {

        if (!empty($this->_COMPARE[$objID])) {
            unset($this->_COMPARE[$objID]);
            return true;
        }
    }

    /**
     * Добавление в сравнение товара
     * @param int $objID ИД товара
     */
    function add($objID) {
        $objID = PHPShopSecurity::TotalClean($objID, 1);
        $objProduct = new PHPShopProduct($objID);
        $name = PHPShopSecurity::CleanStr($objProduct->getParam("name"));
        $seo_name = $objProduct->getParam("prod_seo_name");

        // готовим метки для шаблонов сообщений.
        PHPShopParser::set('prodId', $objID);
        PHPShopParser::set('prodName', $name);
        PHPShopParser::set('ShopDir', $GLOBALS['SysValue']['dir']['dir']);

        if (!is_array($this->_COMPARE[$objID])) {
            $new = array(
                "id" => $objID,
                "name" => $name,
                "category" => $objProduct->getParam("category"));

            // Учет модуля SEOURLPRO
            if (!empty($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'])) {
                if (!empty($seo_name))
                    $new['url'] = '/id/' . $seo_name . '-' . $objID;
                else
                    $new['url'] = '/id/' . str_replace("_", "-", PHPShopString::toLatin($name)) . '-' . $objID;
            }
            else
                $new['url'] = '/shop/UID_' . $objID;

            $this->_COMPARE[$objID] = $new;

            if (PHPShopParser::checkFile('../../' . $GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . "/users/compare/compare_add_alert_done.tpl",true)) {
                // сообщение для вывода во всплывающее окно
                $this->message = PHPShopParser::file('../../' . $GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . "/users/compare/compare_add_alert_done.tpl", true);
            }
            else
                $this->message = PHPShopParser::file('../lib/templates/compare/compare_add_alert_done.tpl', true);
        } else {
            // сообщение для вывода во всплывающее окно
            if (PHPShopParser::checkFile('../../' . $GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . "/users/compare/compare_add_alert_ready.tpl",true)) {

                $this->message = PHPShopParser::file('../../' . $GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . "/users/compare/compare_add_alert_ready.tpl", true);
            }
            else
                $this->message = PHPShopParser::file('../lib/templates/compare/compare_add_alert_ready.tpl', true);
        }
    }

    /**
     * Получение сообщения для всплывающего окна
     */
    function getMessage() {
        return $this->message;
    }

    /**
     * Подсчет количества товаров в сравнении
     * @return int
     */
    function getNum() {
        return count($this->_COMPARE);
    }

}

?>