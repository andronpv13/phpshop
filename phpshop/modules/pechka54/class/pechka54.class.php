<?php

/**
 * Библиотека онлайнкассы Pechka54
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopClass
 */
class Pechka54Rest {

    var $option = array();
    var $taxes = array();

    /**
     * Конструктор
     */
    public function __construct() {
        $this->option();
        $this->nds();
    }

    /**
     * Настройки модуля
     */
    public function option() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['pechka54']['pechka54_system']);
        $this->option = $PHPShopOrm->select();
    }

    /**
     * НДС
     */
    public function nds() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['pechka54']['pechka54_taxes']);
        $data = $PHPShopOrm->select(array('*'), false, false, array('limit' => 10));
        if (is_array($data))
            foreach ($data as $val)
                $this->taxes[$val['tax_id']] = $val;
    }

    /**
     * Отправка чека в Печка54
     * @param array $data данные заказа
     */
    function OFDStart($data) {

        $OrderId = $data['uid'];
        $sum = 0;

        // Данные заказа
        $order = @unserialize($data['orders']);

        // НДС
        $tax = $this->option['tax_product'];
        $tax_delivery = $this->option['tax_delivery'];

        if (class_exists('Pechka54Rest')) {


            $check['response']['kkm'] = $data['kkm'];
            $check['response']['OrderId'] = $OrderId;

            // Продажа
            if (empty($data['ofd_type']) or $data['ofd_type'] == 'registration') {
                $typeCheck = 'OpenCheckSell';
                $typeTable = 'registration';
            }
            // Возврат
            elseif ($data['ofd_type'] == 'return') {
                $typeCheck = 'OpenCheckReturn';
                $typeTable = 'return';
            }

            // Открытие чека
            $check['response']['taskTable'][] = array(
                "data" => "",
                "type" => $typeCheck
            );

            // Данные клиента
            $check['response']['taskTable'][] = array(
                "data" => $order['Person']['mail'],
                "type" => "ClientContact"
            );

            // Корзина
            if (is_array($order['Cart']['cart'])) {
                foreach ($order['Cart']['cart'] as $product) {
                    // Скидка
                    if ($order['Person']['discount'] > 0)
                        $price = $product['price'] - ($product['price'] * $order['Person']['discount'] / 100);
                    else
                        $price = $product['price'];

                    $check['response']['taskTable'][] = array(
                        'data' => $product['name'],
                        'type' => $typeTable,
                        'param' => array(
                            'price' => floatval(number_format($price, 2, '.', '')),
                            'quantity' => floatval(number_format($product['num'], 2, '.', '')),
                            'tax' => $tax
                        )
                    );
                    
                    // Сумма по товарам
                    $sum+=floatval(number_format($price*$product['num'], 2, '.', ''));
                    
                }
            }

            // Доставка
            if (!empty($order['Cart']['dostavka'])) {

                $check['response']['taskTable'][] = array(
                    'data' => 'Доставка',
                    'type' => 'registration',
                    'param' => array(
                        'price' => floatval(number_format($order['Cart']['dostavka'], 2, '.', '')),
                        'quantity' => 1,
                        'tax' => $tax_delivery
                    )
                );

                $sum+=floatval(number_format($order['Cart']['dostavka'], 2, '.', ''));
            }

            // Итого
            $check['response']['taskTable'][] = array(
                'data' => '',
                'type' => 'payment',
                'param' => array(
                    'summ' => floatval(number_format($sum, 2, '.', '')),
                    'typeclose' => 1
                )
            );

            // Закрытие чека
            $check['response']['taskTable'][] = array(
                "data" => "",
                "type" => "CloseCheck"
            );

            $check["resultCode"] = 0;
            
            if($typeTable == 'registration')
            $check["resultInfo"] = "Печать чека покупки заказа №" . $OrderId;
            else $check["resultInfo"] = "Печать чека возврата заказа №" . $OrderId;
            $check["operation"] = $typeTable;
            

            // Запрос на печать
            $ofd_status = 1;

            // Статус заказа
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
            $PHPShopOrm->update(array('ofd_status_new' => $ofd_status, 'ofd_type_new' => $typeTable), array('id' => '="' . $data['id'] . '"'));

            return $check;
        }
    }

    /**
     * Вывод JSON
     * @param array $result 
     */
    function compile($result) {
        header("HTTP/1.1 200");
        header("Content-Type: application/json; charset=utf8");

        if (is_array($result))
            echo json_encode(json_fix_cyr($result));
    }

}

?>