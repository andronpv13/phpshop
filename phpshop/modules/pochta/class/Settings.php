<?php

include_once dirname(__DIR__) . '/class/include.php';

class Settings
{
    /** @var array */
    private $options;

    /** @var int */
    public $format;

    /**
     * Settings constructor.
     * @param $options
     */
    public function __construct($options)
    {
        $this->options = $options;

        $system = new PHPShopSystem();
        $this->format = (int) $system->getSerilizeParam('admoption.price_znak');
    }

    public function get($key)
    {
        if(isset($this->options[$key])) {
            return $this->options[$key];
        }

        return null;
    }

    public function getFromOrderOrSettings($key, $orderSettings, $defaultValue)
    {
        if(isset($orderSettings[$key])) {
            return $orderSettings[$key];
        }

        if(isset($this->options[$key])) {
            return $this->options[$key];
        }

        return $defaultValue;
    }

    /**
     * @param string $field
     * @param string $value
     * @param int $orderId
     * @throws Exception
     */
    public function changeSettings($field, $value, $orderId)
    {
        $orm = new PHPShopOrm('phpshop_orders');
        $order = $this->getOrderById($orderId);

        $pochta = unserialize($order['pochta_settings']);
        $pochta[$field] = $value;

        $orm->update(array('pochta_settings_new' => serialize($pochta)), array('id' => "='" . $order['id'] . "'"));
    }

    /**
     * @param int $orderId
     * @return array
     * @throws Exception
     */
    public function getOrderById($orderId)
    {
        $orm = new PHPShopOrm('phpshop_orders');

        $order = $orm->getOne(array('*'), array('id' => "='" . (int) $orderId . "'"));
        if(!$order) {
            throw new \Exception('Заказ не найден');
        }

        return $order;
    }

    public static function getMailTypeVariants($current)
    {
        return array(
            array(__('Посылка "нестандартная"'), 'POSTAL_PARCEL', $current),
            array(__('Посылка "онлайн"'), 'ONLINE_PARCEL', $current),
            array(__('Курьер "онлайн"'), 'ONLINE_COURIER', $current),
            array(__('Отправление EMS'), 'EMS', $current),
            array(__('EMS оптимальное'), 'EMS_OPTIMAL', $current),
            array(__('EMS РТ'), 'EMS_RT', $current),
            array(__('EMS тендер'), 'EMS_TENDER', $current),
            array(__('Письмо'), 'LETTER', $current),
            array(__('Письмо 1-го класса'), 'LETTER_CLASS_1', $current),
            array(__('Бандероль'), 'BANDEROL', $current),
            array(__('Бизнес курьер'), 'BUSINESS_COURIER', $current),
            array(__('Бизнес курьер экпресс'), 'BUSINESS_COURIER_ES', $current),
            array(__('Посылка 1-го класса'), 'PARCEL_CLASS_1', $current),
            array(__('Бандероль 1-го класса'), 'BANDEROL_CLASS_1', $current),
            array(__('ВГПО 1-го класса'), 'VGPO_CLASS_1', $current),
            array(__('Мелкий пакет'), 'SMALL_PACKET', $current),
            array(__('Легкий возврат'), 'EASY_RETURN', $current),
            array(__('Отправление ВСД'), 'VSD', $current),
            array(__('ЕКОМ'), 'ECOM', $current),
        );
    }

    public static function getMailCategoryVariants($current)
    {
        return array(
            array(__('Простое'), 'SIMPLE', $current),
            array(__('Заказное'), 'ORDERED', $current),
            array(__('Обыкновенное'), 'ORDINARY', $current),
            array(__('С объявленной ценностью'), 'WITH_DECLARED_VALUE', $current),
            array(__('С объявленной ценностью и наложенным платежом'), 'WITH_DECLARED_VALUE_AND_CASH_ON_DELIVERY', $current),
            array(__('С объявленной ценностью и обязательным платежом'), 'WITH_DECLARED_VALUE_AND_COMPULSORY_PAYMENT', $current),
            array(__('С обязательным платежом'), 'WITH_COMPULSORY_PAYMENT', $current),
            array(__('Доставка в почтомат'), 'COMBINED_ORDINARY', $current),
            array(__('Доставка в почтомат с объявленной ценностью'), 'COMBINED_WITH_DECLARED_VALUE', $current)
        );
    }

    public static function getDimensionVariants($current)
    {
        return array(
            array(__('до 260х170х80 мм'), 'S', $current),
            array(__('до 300х200х150 мм'), 'M', $current),
            array(__('до 400х270х180 мм'), 'L', $current),
            array(__('до 530х260х220 мм'), 'XL', $current),
            array(__('Негабарит'), 'OVERSIZED', $current)
        );
    }

    public static function getStatusesVariants($current)
    {
        $statusesObj = new PHPShopOrderStatusArray();
        $statuses = $statusesObj->getArray();

        $result[] = array(__('Новый заказ'), 0, $current);
        if (is_array($statuses)) {
            foreach ($statuses as $status) {
                $result[] = array($status['name'], $status['id'], $current);
            }
        }

        return $result;
    }

    public static function getDeliveryVariants($currentDelivery)
    {
        $PHPShopDeliveryArray = new PHPShopDeliveryArray(array('is_folder' => "!='1'", 'enabled' => "='1'"));

        $DeliveryArray = $PHPShopDeliveryArray->getArray();
        $deliveries = array(
            array(__('Не выбрано'), 0, $currentDelivery)
        );
        if (is_array($DeliveryArray)) {
            foreach ($DeliveryArray as $delivery) {

                if (strpos($delivery['city'], '.')) {
                    $name = explode(".", $delivery['city']);
                    $delivery['city'] = $name[0];
                }

                $deliveries[] = array($delivery['city'], $delivery['id'], $currentDelivery);
            }
        }

        return $deliveries;
    }
}