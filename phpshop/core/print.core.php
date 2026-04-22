<?php
/**
 * Обработчик печатной формы товара
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopShopCore
 */
class PHPShopPrint extends PHPShopShopCore {
    /**
     * Отладка
     * @var bool 
     */
    var $debug=false;
    /**
     * Кэширование
     * @var bool
     */
    var $cache=false;

    function __construct() {

        // Список экшенов
        $this->action=array("nav"=>"UID");
        parent::__construct();
    }


    /**
     * Экшен ошибки
     */
    function index() {

        // Перехват модуля
        if($this->setHook(__CLASS__,__FUNCTION__))
            return true;

        $this->setError404();
    }

    /**
     * Вывод таблицы характеристик товара
     * Функция вынесена в отдельный файл sort_table.php
     * @return mixed
     */
    function sort_table($row) {

        // Перехват модуля
        if($this->setHook(__CLASS__,__FUNCTION__))
            return true;

        $this->doLoadFunction(__CLASS__,__FUNCTION__,$row,'shop');
    }

    /**
     * Экшен выборки подробной информации при наличии переменной навигации UID
     */
    function UID() {

        // Перехват модуля
        if($this->setHook(__CLASS__,__FUNCTION__,false,'START'))
            return true;

        // Безопасность
        if(!PHPShopSecurity::true_num($this->PHPShopNav->getId())) return $this->setError404();

        // Выборка данных
        $row=parent::getFullInfoItem(array('*'),array('id'=>"=".$this->PHPShopNav->getId(),'enabled'=>"='1'",'parent_enabled'=>"='0'"),
                __CLASS__,__FUNCTION__);

        if(empty($row['id'])) return $this->setError404();

        // Категория
        $this->category=$row['category'];
        $this->PHPShopCategory = new PHPShopCategory($this->category);
        $this->category_name=$this->PHPShopCategory->getName();

        // Единица измерения
        if(empty($row['ed_izm'])) $ed_izm = $this->ed_izm;
        else $ed_izm = $row['ed_izm'];

        // Таблица характеристик
        $this->sort_table($row);

        
        $this->set('productName',$row['name']);
        $this->set('productArt',$row['uid']);
        $this->set('productDes',$row['content']);
        $this->set('productContent',$row['description']);
        $this->set('productImg',$row['pic_big']);
        $this->set('productPriceMoney',$this->dengi);
        $this->set('productBack',$this->lang('product_back'));
        $this->set('productSale',$this->lang('product_sale'));
        $this->set('productValutaName',$this->currency());
        $this->set('productUid',$row['id']);
        $this->set('productId',$row['id']);
        $this->set('logoShop',$this->PHPShopSystem->getValue('logo'));
        $this->set('descripShop',$this->PHPShopSystem->getValue('descrip'));
        $this->set('nameShop',$this->PHPShopSystem->getValue('name'));
        $this->set('serverShop',$_SERVER['SERVER_NAME']);

        // Опции склада
        $this->checkStore($row);

        // Заголовок
        $this->title=$row['name'];

        $this->template='templates.print_page_forma';

        // Перехват модуля
        $this->setHook(__CLASS__,__FUNCTION__,$row, 'END');

        // Подключаем шаблон
        $this->parseTemplate($this->getValue('templates.main_product_forma_full'));
    }
}
?>