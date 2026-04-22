<?php
/**
 * Обработчик перехода с интерактивного прайса
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopCore
 */
class PHPShopExcel extends PHPShopCore {

    /**
     * Конструктор
     */
    function __construct() {

        // Имя Бд
        $this->objBase=$GLOBALS['SysValue']['base']['products'];

        // Список экшенов
        $this->action=array("get"=>"UID",'nav'=>'index');

        parent::__construct();
    }
    
    function decode($str){
        return base64_decode($str);
    }

     /**
     * Экшен перехода к товару
     */
    function UID() {
        
        $product_uid=PHPShopSecurity::TotalClean($this->decode($_GET['UID']),2);
        $row=$this->PHPShopOrm->select(array('id'),array('uid'=>'="'.PHPShopString::utf8_win1251($product_uid).'"'),false,array('limit'=>1));
        if(is_array($row))
            if(!empty($row['id'])){
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: ".$this->getValue('dir.dir')."/shop/UID_".$row['id'].".html");
            exit();
        }
    }


    /**
     * Экшен
     */
    function index() {
        $this->setError404();

    }
}

?>