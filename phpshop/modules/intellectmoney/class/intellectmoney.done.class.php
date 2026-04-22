<?php

require_once __DIR__ . '/intellectmoney.database.helper.php';
require_once __DIR__ . '/IntellectMoneyCommon/Order.php';
require_once __DIR__ . '/IntellectMoneyCommon/Currency.php';
require_once __DIR__ . '/IntellectMoneyCommon/Payment.php';

/**
 * Класс, занимающийся обработкой хука done.
 */
final class IntellectMoneyPhpShopDoneHandler
{
    /**
     * Вспомогательный класс, для взаимодействия с базой данных PHPShop.
     * @var \DataBaseHelper
     */
    private $databaseHelper;

    /**
     * Настройки модуля.
     * @var \PaySystem\UserSettings
     */
    private $im_usersettings;

    /**
     * Класс, занимающийся обработкой хука done.
     */
    public function __construct()
    {
        $this->databaseHelper = new DataBaseHelper('phpshop_modules_intellectmoney_settings');
        $this->im_usersettings = $this->databaseHelper->loadUserSettings();
    }

    /**
     * Проверить, может ли модуль обработать хук создания класса.
     * 
     * @param string $order_method
     * Метод оплаты заказа, обычно представляет строковое имя системы или ее идентификатор.
     * 
     * @return bool
     * Возвращает `true`, если модуль может обработать хук заказа; 
     * в противном случае возвращает `false`.
     */
    public function CanHandleOrder($order_metod)
    {
        $order_method = mb_strtolower($order_metod);
        $module_id = $this->databaseHelper->getSetting('module_id');
        
        $is_intellectmoney = $order_method == 'intellectmoney' || $order_method == $module_id;
        return $is_intellectmoney;
    }

    /**
     * Преобразовать данные заказа PHPShop в заказ IntellectMoney.
     *
     * @param mixed $phpShopData Объект с данными PHPShop.
     * @return \PaySystem\Payment 
     * Экземляр заказа, который понимает модуль IntellectMoney.
     */
    public function ReadOrderData($phpShopData, $phpShopCustomer)
    {
        try
        {
            $cart_amount = $this->ConvertToFloat($phpShopData->PHPShopCart->getSum());
            $dilivery_amount = $this->ConvertToFloat($phpShopData->PHPShopDelivery->objRow['price']);
            $total_amount = $cart_amount + $dilivery_amount;
            
            $im_customer = \PaySystem\Customer::getInstance($phpShopCustomer['mail'], $phpShopCustomer['name_new'], $phpShopCustomer['tel_new']);
            $im_order = \PaySystem\Order::getInstance(null, $phpShopData->ouid, $total_amount, $total_amount, null, $dilivery_amount, $phpShopData->PHPShopOrder->default_valuta_iso, 0, null);
            
            $shipped_items = $phpShopData->PHPShopCart->getArray();
            foreach($shipped_items as $item) {
                $im_order->addItem(floatval($item['price']), intval($item['num']), $this->ToUTF8($item['name']), $this->im_usersettings->getTax(), 1);
            }
            
            if ($dilivery_amount != 0) {
                $im_order->addItem($dilivery_amount, 1, $this->ToUTF8($phpShopData->PHPShopDelivery->objRow['city']), $this->im_usersettings->getTax(), 4);
            }

            $im_payment = \PaySystem\Payment::getInstance($this->im_usersettings, $im_order, $im_customer, 'ru');
            IMLogger::Log('Has read data successfully: ' . $im_order->getOrderID());
            return $im_payment;
        }
        catch(\Exception $e)
        {
            IMLogger::Error($e->getMessage());
            return null;
        }
    }

    /**
     * Отобразить форму оплаты в PHPShop.
     * 
     * @param \PaySystem\Payment $payment 
     * Экземпляр с данными платежа.
     * 
     * @param mixed $phpShopObj
     * Объект PHPShop использующий для отображения формы.
     */
    public function RenderForm($payment, $phpShopObj)
    {
        $form = $payment->generateForm($this->im_usersettings->getIntegrationMethod() === 'Default', true);
        $phpShopObj->set('orderMesage', $form);
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
    
    /**
     * Преобразовать строку из кодировки 1251 в UTF-8.
     * 
     * @param string $inputString Входящая строка в кодировке 1251.
     * 
     * @return string
     * Строка в кодеровке UTF-8.
     */
    function ToUTF8($inputString) {
        return mb_convert_encoding($inputString, "utf-8", "windows-1251");
    }
}