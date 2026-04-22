<?php
/**
 * Библиотека форматирования цифр
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopClass
 * @subpackage Helper
 */
class PHPShopMath {

    /**
     * Форматирование пустой цены
     * @param float $price цена
     * @return mixed 
     */
    static function DoZero($price) {
        if(empty($price)) return 0;
        else return $price;
    }

    /**
     * Форматирование пустой цены
     * @param float $price цена
     * @return mixed 
     */
    static function Zero($price) {
        return PHPShopMath::DoZero($price);
    }

    /**
     * Подсчет суммы от скидки и курса
     * @param float $sum сумма
     * @param float $disc скидка
     * @return float
     */
    static function ReturnSumma($sum,$disc) {
        global $PHPShopSystem;

        if(!$PHPShopSystem) {
            $PHPShopSystem = new PHPShopSystem();
        }
        
        $kurs=$PHPShopSystem->getDefaultValutaKurs();
        $sum*=$kurs;
        $sum=$sum-($sum*$disc/100);
        return number_format($sum,"2",".","");
    }
}
?>