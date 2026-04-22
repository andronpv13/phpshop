<?php

/**
 * Библиотека оптовых акций
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopClass
 */
class PHPShopWholesale{

    /**
     * Конструктор
     */
    function __construct() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['wholesale']['wholesale_forms']);
        $PHPShopOrm->debug = false;
        $where['enabled'] = '="1"';
        $this->giftlist = $PHPShopOrm->select(array('*'), $where, array('order' => 'id'),array('limit'=>1000),__CLASS__,__FUNCTION__);
    }

    /**
     * Возвращает дату числом вида ГГГГММДД или 0
     */
    function conv_date($date) {
        $arr = explode('-', $date);
        $date_new = $arr[2] . $arr[1] . $arr[0];
        if (is_numeric($date_new)) {
            return intval($date_new);
        } else {
            return 0;
        }
    }

    /**
     * Проверяет активность, возвращает 1 - Активна или 0 - Неактивна
     */
    function check_activity($active, $start, $end) {

        // По-умолчанию включена
        $result = 1;
        $now = intval(date('Ymd'));

        // Если стоит учитывать период и переданы даты
        if ($active == 1) {
            $date_start = $this->conv_date($start);
            $date_end = $this->conv_date($end);

            if (!empty($date_start) || !empty($date_end)) {
                // Если текущая дата больше даты окончания промоакции
                if ($now > $date_end && !empty($date_end)) {
                    $result = 0;
                }
                // Если текущая дата меньше даты начала промоакции
                if ($now < $date_start) {
                    $result = 0;
                }
            }
        }
        // Возвращаем значение
        return $result;
    }
    
    /**
     * Возвращает информацию по действующим акциям
     */
    function getOpt($row) {
 
        $data = $this->giftlist;
        $labels = $descriptions = $hidePrices = array();

        if (isset($data)) {
            foreach ($data as $pro) {
                
                // Проверим активность промоакции
                $date_act = $this->check_activity($pro['active_check'], $pro['active_date_ot'], $pro['active_date_do']);
                //$user_act = $this->promotion_check_userstatus($pro['status_check'], unserialize($pro['statuses']));
                $user_act = true;

                if ($date_act == 1 && $user_act) {
                    
                    if ($pro['categories_check'] == 1):
                        //категории массив
                        $category_ar = explode(',', $pro['categories']);
                    endif;

                    if ($pro['products_check'] == 1):
                        //категории массив
                        $products_ar = explode(',', $pro['products']);
                    endif;

                    $sumche = $sumchep = 0;
                    
                    // Не нулевая цена или выключен режим проверки нулевой цены
                    if (empty($row['price_n']) or empty($pro['block_old_price'])) {

                        //узнаем по каким категориям
                        if (isset($category_ar)) {
                            foreach ($category_ar as $val_c) {
                                if ($val_c == $row['category']) {
                                    $sumche = 1;
                                    break;
                                } else {
                                    $sumche = 0;
                                }
                            }
                        }

                        //узнаем по каким товарам
                        if (isset($products_ar)) {
                            foreach ($products_ar as $val_p) {
                                if ($val_p == $row['id']) {
                                    $sumchep = 1;
                                    break;
                                } else {
                                    $sumchep = 0;
                                }
                            }
                        }
                    }

                    //обнуляем категории и товары
                    unset($category_ar);
                    unset($products_ar);

                    // Товар попал в акцию
                    if ($sumche == 1 || $sumchep == 1) {
                      return array('id'=>$pro['id'],'tip' => $pro['discount_tip'], 'label' => $pro['label'], 'description' => $pro['description']);
                    }
                }
            }
        }
    }
}
?>