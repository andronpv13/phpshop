<?php

require_once __DIR__ . '/intellectmoney.database.helper.php';
require_once __DIR__ . '/IntellectMoneyCommon/Order.php';
require_once __DIR__ . '/IntellectMoneyCommon/Payment.php';
require_once __DIR__ . '/IntellectMoneyCommon/Result.php';

/**
 * Класс для обработки уведомления от системы IntellectMoney.
 */
final class IntellectMoneyPhpShopSuccessHandler {

    /**
     * Вспомогательный класс, для взаимодействия с настройками модуля.
     * @var \DataBaseHelper
     */
    private $module_settings;

    /**
     * Вспомогательный класс, для взаимодействия с таблицой заказов.
     * @var \DataBaseHelper
     */
    private $orders;

    /**
     * Вспомогательный класс, для взаимодействия с таблицой доставок.
     * @var \DataBaseHelper
     */
    private $deliveries;

    public function __construct() {
        $this->module_settings = new DataBaseHelper('phpshop_modules_intellectmoney_settings');
        $this->orders = new DataBaseHelper('phpshop_orders');
        $this->deliveries = new DataBaseHelper('phpshop_delivery');
    }

    /**
     * Проверить данные уведомления от системы IntellectMoney 
     * и получить результат проверки.
     * 
     * @param array $params Параметры уведомления.
     * 
     * @return \PaySystem\Result
     * Возвращает экземпляр с данными результата проверки.
     */
    public function HandleNotification($params) {
        global $PHPShopOrder;

        $params_string = '';
        foreach($params as $key => $value) {
            $params_string .= $key . '=' . $value . '<br>';
        }
        IMLogger::Log('Notification recieved from IntellectMoney:<br>' . $params_string);

        $order = $this->orders->loadOrder($params['orderId']);
        $order_amount = $this->GetOrderAmount($order);
        IMLogger::Log('Order amount from PHPShop: ' . $order_amount);

        $im_settings = $this->module_settings->loadUserSettings();
        $im_order = \PaySystem\Order::getInstance($params['paymentId'], $order['uid'], $order_amount, $order_amount, null, null, $PHPShopOrder->default_valuta_iso, 0, 0);
        $im_result = \PaySystem\Result::getInstance($params, $im_settings, $im_order, 'en', false);
        return $im_result;
    }

    /**
     * Обновить статус заказа.
     * 
     * @param string $id Идентификатор заказа.
     * @param string $cmsState Новый статус заказа.
     * 
     * @return bool
     * Возвращает `true`, если обновление прошло успешно; 
     * в противном случае возвращает `false`.
     */
    public function UpdateOrderState($id, $cmsState) {
        IMLogger::Log('Updating order '. $id . ' to status:' . $cmsState);
        $update_result = $this->orders->updateOrder($id, $cmsState);
        
        if ($update_result) {
            IMLogger::Good('Order ' .$id . ' updated successfully to status ' . $cmsState);
        } else {
            IMLogger::Error('Order ' .$id . ' update failed');
        }
        return $update_result;
    }

    /**
     * Получение полной суммы заказа.
     * 
     * @param array $phpShopOrder Данные заказа.
     * 
     * @return float
     * Сумма заказа, с учетом доставки.
     */
    private function GetOrderAmount($phpShopOrder)
    {
        $order_data = unserialize($phpShopOrder['orders']);
        $items = $order_data['Cart']['cart'];
        
        $total_amount = 0;
        foreach($items as $item) {
            $total_amount += $this->ConvertToFloat($item['total']);
        }

        $delivery_id = $order_data['Person']['dostavka_metod'];
        if ($delivery_id != null && $delivery_id != 0) {
            $delivery_data = $this->deliveries->loadDelivery($delivery_id);
            $total_amount += $this->ConvertToFloat($delivery_data['price']);
        }

        return $total_amount;
    }

    /**
     * Преобразовать строку в `float` с двумя знаками после запятой.
     * 
     * @param string $inputString Входящая строка
     * 
     * @return float
     * Возвращает число с плавающей точкой.
     */
    function ConvertToFloat($inputString) {
        $cleanedString = preg_replace("/[^0-9.,]/", "", $inputString);
        $cleanedString = str_replace(',', '.', $cleanedString);
        $floatValue = (float) number_format((float) $cleanedString, 2, '.', '');
    
        return $floatValue;
    }
}