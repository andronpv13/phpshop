<?php

include_once dirname(__DIR__) . '/class/include.php';

class Pochta {

    /** @var PochtaRequest */
    private $request;

    /** @var Settings */
    public $settings;

    public function __construct() {
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_pochta_system');

        $options = $PHPShopOrm->select();

        $this->settings = new Settings($options);
        $this->request = new PochtaRequest($this->settings);
    }

    /**
     * @param array $order
     */
    public function send($order) {
        $cart = unserialize($order['orders']);
        $pochta = unserialize($order['pochta_settings']);

        if (!empty($pochta['address'])) {
            $normalized = $this->request->normalizeAddress(sprintf('%s %s %s %s', $order['index'], $order['city'], $order['state'], $pochta['address']));

            if (empty($order['street']) && isset($order['street'])) {
                $order['street'] = $normalized['street'];
            }
            if (empty($order['house']) && isset($order['house'])) {
                $order['house'] = $normalized['house'];
            }
            if (empty($order['flat']) && isset($order['flat'])) {
                $order['flat'] = $normalized['room'];
            }
        }

        if (empty($order['fio']))
            $name = $cart['Person']['name_person'];
        else
            $name = $order['fio'];
        $nameArr = explode(' ', $name);
        
        if((bool) $this->settings->getFromOrderOrSettings('paid', $pochta, false) == 1)
           $payment = 0;
        elseif((int) $order['paid'] === 1)
           $payment = 0;
        else $payment =  (int) ((float) $order['sum'] - (float) $cart['Cart']['dostavka']) * 100;
        
        $parameters = array(
            'address-type-to' => 'DEFAULT',
            'completeness-checking' => (bool) $this->settings->getFromOrderOrSettings('completeness_checking', $pochta, false),
            'compulsory-payment' => (int) $order['paid'] === 1 ? 0 : (int) $order['sum'] * 100,
            'courier' => $this->isCourier((int) $cart['Person']['dostavka_metod']),
            'easy-return' => (bool) $this->settings->getFromOrderOrSettings('easy_return', $pochta, false),
            'fragile' => (bool) $this->settings->getFromOrderOrSettings('fragile', $pochta, false),
            'given-name' => PHPShopString::win_utf8($name),
            'house-to' => PHPShopString::win_utf8($order['house']),
            'index-to' => (int) $order['index'],
            'insr-value' => (int) $cart['Cart']['sum'] * $this->settings->get('declared_percent'),
            'mail-category' => $this->settings->getFromOrderOrSettings('mail_category', $pochta, 'ORDINARY'),
            'mail-direct' => 643,
            'mail-type' => $this->settings->getFromOrderOrSettings('mail_type', $pochta, 'PARCEL_CLASS_1'),
            'mass' => $this->getWeight($cart['Cart']['cart']),
            'no-return' => (bool) $this->settings->getFromOrderOrSettings('no_return', $pochta, false),
            'order-num' => $order['uid'],
            'payment' => $payment,
            'place-to' => PHPShopString::win_utf8($order['city']),
            'postoffice-code' => $this->settings->get('index_from'),
            'recipient-name' => PHPShopString::win_utf8($name),
            'region-to' => PHPShopString::win_utf8($order['state']),
            'sms-notice-recipient' => (int) $this->settings->getFromOrderOrSettings('sms_notice', $pochta, false),
            'street-to' => PHPShopString::win_utf8($order['street']),
            'room-to' => PHPShopString::win_utf8($order['flat']),
            'surname' => PHPShopString::win_utf8($nameArr[0]),
            'tel-address' => str_replace(array('(', ')', ' ', '+', '-', '&#43;'), '', $order['tel']),
            'vsd' => (bool) $this->settings->getFromOrderOrSettings('electronic_notice', $pochta, false),
            'with-electronic-notice' => (bool) $this->settings->getFromOrderOrSettings('electronic_notice', $pochta, false),
            'with-order-of-notice' => (bool) $this->settings->getFromOrderOrSettings('order_of_notice', $pochta, false),
            'with-simple-notice' => (bool) $this->settings->getFromOrderOrSettings('simple_notice', $pochta, false),
            'wo-mail-rank' => (bool) $this->settings->getFromOrderOrSettings('wo_mail_rank', $pochta, false)
        );

        if ($parameters['mail-type'] === 'ECOM') {
            $parameters['dimension-type'] = $this->settings->getFromOrderOrSettings('dimension_type', $pochta, 'S');
        }

        // Наложенный платеж
        if ($parameters['mail-category'] === 'WITH_DECLARED_VALUE_AND_CASH_ON_DELIVERY') {
            unset($parameters['compulsory-payment']);
            $parameters['insr-value'] = $parameters['payment'];
        }

        if (isset($pochta['pvz_type']) && $pochta['pvz_type'] === 'postamat') {

            $parameters['dimension-type'] = $this->settings->getFromOrderOrSettings('dimension_type', $pochta, 'S');
            if ($this->settings->get('declared_percent') > 0) {
                $parameters['mail-category'] = 'COMBINED_WITH_DECLARED_VALUE';
            } else {
                $parameters['mail-category'] = 'COMBINED_ORDINARY';
            }
            $parameters['ecom-data']['identity-methods'] = ['PIN'];
            $parameters['ecom-data']['delivery-point-index'] = $pochta['pvz_index'];
            $parameters['sms-notice-recipient'] = 1;
        }

        $result = $this->request->createOrder($parameters);

        if ($result['success']) {
            $orm = new PHPShopOrm('phpshop_orders');
            $orm->update(array('pochta_order_status_new' => 'SEND'), array('id' => "='" . $order['id'] . "'"));
        }

        return $result;
    }

    public function isPostOffice($deliveryId) {
        if ((int) $deliveryId === 0) {
            return false;
        }

        return (int) $this->settings->get('delivery_id') === $deliveryId;
    }

    public function isCourier($deliveryId) {
        if ((int) $deliveryId === 0) {
            return false;
        }

        return (int) $this->settings->get('delivery_courier_id') === $deliveryId;
    }

    public function buildOrderTab($order) {
        global $PHPShopGUI;

        $pochta = unserialize($order['pochta_settings']);
        $disabledSettings = '';
        if (!empty($order['pochta_order_status'])) {
            PHPShopParser::set('pochta_hide_actions', 'display: none;');
            $disabledSettings = 'disabled="disabled"';
        }
        
        if((bool) $this->settings->getFromOrderOrSettings('paid', $pochta, false) == 1)
           $order['paid'] = 1;

        $orderInfo = PHPShopText::tr(
                        __('Статус заказа'), '<span class="pochta-status">' . __($this->getOrderStatusText($order['pochta_order_status'])) . '</span>'
                ) .
                PHPShopText::tr(
                        __('Адрес доставки с виджета'), '<strong>' . $pochta['delivery_info'] . '<strong>'
                ) .
                PHPShopText::tr(
                        __('Статус оплаты'), $PHPShopGUI->setCheckbox("pochta_payment_status", 1, 'Заказ оплачен', (int) $order['paid'], $disabledSettings)
                ) .
                PHPShopText::tr(
                        __('Комплектность'), $PHPShopGUI->setCheckbox("pochta_completeness-checking", 1, 'Услуга проверки комплектности', $this->settings->getFromOrderOrSettings('completeness_checking', $pochta, false), $disabledSettings)
                ) .
                PHPShopText::tr(
                        __('Лёгкий возврат'), $PHPShopGUI->setCheckbox("pochta_easy_return", 1, 'Отметка "Лёгкий возврат"', $this->settings->getFromOrderOrSettings('easy_return', $pochta, false), $disabledSettings)
                ) .
                PHPShopText::tr(
                        __('Возврату не подлежит'), $PHPShopGUI->setCheckbox("pochta_no_return", 1, 'Отметка "Возврату не подлежит"', $this->settings->getFromOrderOrSettings('no_return', $pochta, false), $disabledSettings)
                ) .
                PHPShopText::tr(
                        __('Осторожно/Хрупкое'), $PHPShopGUI->setCheckbox("pochta_fragile", 1, 'Отметка "Осторожно/Хрупкое"', $this->settings->getFromOrderOrSettings('fragile', $pochta, false), $disabledSettings)
                ) .
                PHPShopText::tr(
                        __('SMS уведомление'), $PHPShopGUI->setCheckbox("pochta_sms_notice", 1, 'Услуга SMS уведомление', $this->settings->getFromOrderOrSettings('sms_notice', $pochta, false), $disabledSettings)
                ) .
                PHPShopText::tr(
                        __('Электронное уведомление'), $PHPShopGUI->setCheckbox("pochta_electronic_notice", 1, 'Услуга электронное уведомление', $this->settings->getFromOrderOrSettings('electronic_notice', $pochta, false), $disabledSettings)
                ) .
                PHPShopText::tr(
                        __('Заказное уведомление'), $PHPShopGUI->setCheckbox("pochta_order_of_notice", 1, 'Услуга заказное уведомление', $this->settings->getFromOrderOrSettings('order_of_notice', $pochta, false), $disabledSettings)
                ) .
                PHPShopText::tr(
                        __('Простое уведомление'), $PHPShopGUI->setCheckbox("pochta_simple_notice", 1, 'Услуга простое уведомление', $this->settings->getFromOrderOrSettings('simple_notice', $pochta, false), $disabledSettings)
                ) .
                PHPShopText::tr(
                        __('Без разряда'), $PHPShopGUI->setCheckbox("pochta_wo_mail_rank", 1, 'Отметка "Без разряда"', $this->settings->getFromOrderOrSettings('wo_mail_rank', $pochta, false), $disabledSettings)
                ) .
                PHPShopText::tr(
                        __('Сопроводительные документы'), $PHPShopGUI->setCheckbox("pochta_vsd", 1, 'Возврат сопроводительных документов', $this->settings->getFromOrderOrSettings('vsd', $pochta, false), $disabledSettings)
                ) .
                PHPShopText::tr(
                        __('Категория РПО'), $PHPShopGUI->setSelect('pochta_mail_category', Settings::getMailCategoryVariants($this->settings->getFromOrderOrSettings('mail_category', $pochta, 'ORDINARY')))
                ) .
                PHPShopText::tr(
                        __('Вид РПО'), $PHPShopGUI->setSelect('pochta_mail_type', Settings::getMailTypeVariants($this->settings->getFromOrderOrSettings('mail_type', $pochta, 'PARCEL_CLASS_1')))
                ) .
                PHPShopText::tr(
                        __('Типоразмер'), $PHPShopGUI->setSelect('pochta_dimension_type', Settings::getDimensionVariants($this->settings->getFromOrderOrSettings('dimension_type', $pochta, 'S')))
        );

        PHPShopParser::set('pochta_order_info', PHPShopText::table($orderInfo, 3, 1, 'left', '100%', false, 0, 'pochta-table', 'list table table-striped table-bordered'));
        PHPShopParser::set('pochta_order_id', $order['id']);


        // Widget
        PHPShopParser::set('cdek_order_id', $order['id']);

        $cart = unserialize($order['orders']);
        $weight=10;


        PHPShopParser::set('pochta_widget_id', $this->settings->get('widget_id'));
        PHPShopParser::set('pochta_courier_widget_id', $this->settings->get('courier_widget_id'));
        PHPShopParser::set('pochta_weight', $weight);
        PHPShopParser::set('pochta_ins_value', (int) $order['sum'] * $this->settings->get('declared_percent'));

        return ParseTemplateReturn(dirname(__DIR__) . '/templates/order.tpl', true);
    }
    
      /**
     * @param $orderId
     * @throws Exception
     */
    public function getOrderById($orderId) {
        $orm = new PHPShopOrm('phpshop_orders');

        $order = $orm->getOne(array('*'), array('id' => "='" . (int) $orderId . "'"));
        if (!$order) {
            throw new \Exception('Заказ не найден');
        }

        return $order;
    }
    
     /**
     * @param array $request
     * @throws Exception
     */
    public function changeAddress($request) {
        $orm = new PHPShopOrm('phpshop_orders');
        $order = $this->getOrderById($request['orderId']);

        $cart = unserialize($order['orders']);
        $cart['Cart']['dostavka'] = (float) $request['cost'];
        $sum = $cart['Cart']['sum'] + $cart['Cart']['dostavka'];

        $pochta_settings = unserialize($order['pochta_settings']);
        $pochta_settings['delivery_info'] = PHPShopString::utf8_win1251($request['info']);

        $orm->update(['orders_new' => serialize($cart), 'pochta_settings_new'=>serialize($pochta_settings), 'sum_new' => $sum], ['id' => "='" . $order['id'] . "'"]);
    }

    public function getOrderStatusText($status) {
        if ($status === 'SEND') {
            return __('Отправлен');
        }

        return __('Не отправлен');
    }

    /**
     * Вес, с учетом веса по умолчанию в модуле, если не задан в товаре.
     * @return int
     */
    private function getWeight($cart) {
        $weight = 0;
        foreach ($cart as $cartProduct) {
            if ((int) $cartProduct['weight'] > 0) {
                $weight += (int) $cartProduct['weight'] * (float) $cartProduct['num'];
            } else {
                $weight += (int) $this->settings->get('weight') * (float) $cartProduct['num'];
            }
        }

        return $weight;
    }

}
