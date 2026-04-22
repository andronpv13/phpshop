<?php

if (!defined("OBJENABLED"))
    require_once(dirname(__FILE__) . "/obj.class.php");

/**
 * Библиотека для работы с заказами
 * @author PHPShop Software
 * @version 1.4
 * @package PHPShopObj
 */
class PHPShopOrderFunction extends PHPShopObj {

    var $objID;
    var $productID;
    var $default_valuta_iso;
    var $default_valuta_name;
    var $default_valuta_code;
    var $PHPShopModules;

    /**
     * Конструктор
     * @param int $objID ИД заказа
     * @param array $import_data массив импорта данных заказа
     */
    function __construct($objID = false, $import_data = null) {
        global $PHPShopSystem, $PHPShopModules;

        if ($objID) {
            $this->objID = $objID;
            $this->objBase = $GLOBALS['SysValue']['base']['orders'];
            parent::__construct('id', $import_data);

            // Содержание корзины
            $paramOrder = parent::unserializeParam("orders");

            if (!empty($paramOrder['Person']['order_metod']))
                $this->order_metod_id = $paramOrder['Person']['order_metod'];
        }

        parent::loadClass("system");
        parent::loadClass("delivery");

        // Системные настройки
        if (!$PHPShopSystem)
            $this->PHPShopSystem = new PHPShopSystem();
        else
            $this->PHPShopSystem = &$PHPShopSystem;

        $this->format = intval($this->PHPShopSystem->getSerilizeParam("admoption.price_znak"));

        // Валюта
        $this->getDefaultValutaObj();

        $this->PHPShopModules = &$PHPShopModules;
    }

    /**
     * Импорт данных
     * @param array $data массив данных
     */
    function import($data) {
        $this->objRow = $data;

        // Содержание корзины
        $paramOrder = parent::unserializeParam("orders");
        $this->order_metod_id = $paramOrder['Person']['order_metod'];
    }

    /**
     * Вывод ID метода оплаты
     * @return int
     */
    function getOplataMetodId() {
        return $this->order_metod_id;
    }

    /**
     * Вывод название метода оплаты
     * @return string
     */
    function getOplataMetodName() {
        parent::loadClass("payment");

        // Метод оплаты
        $Payment = new PHPShopPayment($this->order_metod_id);
        $this->order_metod_name = $Payment->getName();
        return $this->order_metod_name;
    }

    /**
     * Статус заказа
     * @return string
     */
    function getStatus() {
        global $PHPShopOrderStatusArray;

        if (empty($PHPShopOrderStatusArray))
            $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();

        $status = $this->getParam('statusi');
        if (!empty($status))
            return $PHPShopOrderStatusArray->getParam($this->getParam('statusi') . '.name');
        else
            return __('Новый заказ');
    }

    /**
     * Цвет статуса заказа
     * @return string
     */
    function getStatusColor() {
        global $PHPShopOrderStatusArray;

        if (empty($PHPShopOrderStatusArray))
            $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();

        $status = $this->getParam('statusi');
        if (!empty($status))
            return $PHPShopOrderStatusArray->getParam($this->getParam('statusi') . '.color');
    }

    /**
     * ID валюты товара в заказе
     * @param int $productID ИД товара
     * @return int
     */
    function getValutaId($productID) {
        parent::loadClass("product");

        // Данные по товару
        $Product = new PHPShopProduct($productID);
        $this->valutaID = $Product->getValutaID();

        return $this->valutaID;
    }

    /**
     * ISO валюты товара в заказе
     * @param int $productID ИД товара
     * @return string
     */
    function getValutaIso($productID) {
        $this->getValutaId($productID);
        parent::loadClass("valuta");
        $valutaID = $this->valutaID;

        // Валюта
        $Valuta = new PHPShopValuta($valutaID);
        $this->ValutaIso = $Valuta->getIso();

        if (empty($this->ValutaIso)) {
            return $this->default_valuta_iso;
        }
        return $Valuta->getIso();
    }

    /**
     * Валюта по умолчанию в заказе
     * @param Obj $System системные настройки
     */
    function getDefaultValutaObj() {

        $this->default_valuta_id = $this->PHPShopSystem->getDefaultValutaId();
        parent::loadClass("valuta");

        $PHPShopValuta = new PHPShopValuta($this->default_valuta_id);
        $this->default_valuta_iso = $PHPShopValuta->getIso();
        $this->default_valuta_name = $PHPShopValuta->getName();
        $this->default_valuta_code = $PHPShopValuta->getCode();
        $this->default_valuta_kurs = $PHPShopValuta->getKurs();
        $this->default_valuta_kurs_beznal = $this->default_valuta_kurs;
    }

    /**
     * Сумма c учетом скидки
     * @param float $sum сумма
     * @param float $disc скидка
     * @param string $def разделитель
     * @param float $delivery доставка
     * @return float
     */
    function returnSumma($sum, $disc = 0, $def = '', $delivery = 0) {
        global $PHPShopSystem;

        if (!$PHPShopSystem) {
            $kurs = $this->default_valuta_kurs;
            $this->format = 0;
        } else {
            $kurs = $PHPShopSystem->getDefaultValutaKurs(false);
        }

        $sum *= $kurs;
        $sum = $sum - ($sum * $disc / 100);

        return number_format($sum + $delivery, $this->format, ".", $def);
    }

    /**
     * Поправки по курсу безнал
     * @param float $sum сумма
     * @param float $disc скидка
     * @return float
     */
    function returnSummaBeznal($sum, $disc) {
        $kurs = $this->default_valuta_kurs_beznal;
        $sum *= $kurs;
        $sum = $sum - ($sum * $disc / 100);
        return number_format($sum, $this->format, ".", "");
    }

    /**
     * Выдача максимальной скидки пользователя
     * @param float $mysum сумма заказа
     * @param array $cart корзина товаров
     * @param bool $admin проверка запуска из панели управления
     * @return float
     */
    function ChekDiscount($mysum, $cart = null, $admin = false) {

        if (!class_exists('PHPShopUserStatus'))
            PHPShopObj::loadClass("user");

        if (!class_exists('PHPShopOrm'))
            PHPShopObj::loadClass("orm");

        if (!class_exists('PHPShopSecurity'))
            PHPShopObj::loadClass("security");

        if (!class_exists('PHPShopCart'))
            PHPShopObj::loadClass("cart");

        $maxsum = 0;
        $maxdiscount = 0;
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['discount']);
        $row = $PHPShopOrm->select(array('*'), array('sum' => "<=" . intval($mysum), 'enabled' => "='1'"), array('order' => 'sum desc'), array('limit' => 1));
        if (is_array($row)) {
            $sum = $row['sum'];
            if ($sum > $maxsum) {
                $PHPShopCart = new PHPShopCart();
                // Отключаем определенные товары из акции
                if (!is_null($cart) && is_array($cart)) {
                    foreach ($cart as $key => $product) {
                        unset($PHPShopCart->_CART[$key]['promo_price']);
                        unset($PHPShopCart->_CART[$key]['order_discount_disabled']);

                        if (isset($row['block_old_price']) && (int) $row['block_old_price'] === 1 && (int) $product['price_n'] > 0) {
                            $PHPShopCart->_CART[$key]['promo_price'] = $product['price'];
                            $PHPShopCart->_CART[$key]['order_discount_disabled'] = true;
                        }

                        $category_ar = array_diff(explode(',', $row['block_categories']), ['']);
                        if (in_array($product['category'], $category_ar)) {
                            $PHPShopCart->_CART[$key]['promo_price'] = $product['price'];
                            $PHPShopCart->_CART[$key]['order_discount_disabled'] = true;
                        }
                    }
                }

                $maxsum = $sum;
                $action = $row['action'];
                $maxdiscount = $row['discount'];
            }
        }

        // Проверка статуса пользователя
        if (!empty($_SESSION['UsersStatus']) and PHPShopSecurity::true_num($_SESSION['UsersStatus']) and ! $admin) {
            $PHPShopUserStatus = new PHPShopUserStatus($_SESSION['UsersStatus']);
            $userdiscount = $PHPShopUserStatus->getDiscount();

            // Максимальная скидка
            if ($action == 1) {

                if ($userdiscount > $maxdiscount)
                    $maxdiscount = $userdiscount;
            }
            // Сумма скидой
            else {
                $maxdiscount = $maxdiscount + $userdiscount;
            }
        }

        // Перехват модуля
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $mysum);
        if ($hook)
            $maxdiscount = $hook;

        return $maxdiscount;
    }

    /**
     * Шаблонизатор вывода корзины в заказе
     * @param string $function имя функции шаблона вывода
     * @param atrray $option дополнительные опции, передающиеся в шаблон
     * @return string
     */
    function cart($function, $option = false) {
        $list = null;
        $order = $this->unserializeParam('orders');
        if (is_array($order['Cart']['cart'])) {
            $cart = $order['Cart']['cart'];
            foreach ($order['Cart']['cart'] as $key => $val) {
                $cart[$key]['price'] = $this->ReturnSummaBeznal($val['price'], 0);
                $cart[$key]['total'] = $this->ReturnSummaBeznal($val['price'] * $val['num'], 0);
            }
        }

        if (is_array($cart))
            foreach ($cart as $v)
                if (function_exists($function)) {
                    $list .= call_user_func_array($function, array($v, $option));
                }

        return $list;
    }

    /**
     * Вывод юр. данных по заказу.
     * @param atrray $row данные заказа
     * @return string
     */
    function yurData($row) {
        $fielsName = array(
            "org_name" => "Наименование организации ",
            "org_inn" => "ИНН",
            "org_kpp" => "КПП",
            "org_yur_adres" => "Юридический адрес",
            "org_fakt_adres" => "Фактический адрес",
            "org_ras" => "Расчётный счёт",
            "org_bank" => "Наименование банка",
            "org_kor" => "Корреспондентский счет",
            "org_bik" => "БИК",
            "org_city" => "Город",
        );

        $disp = "";
        foreach ($fielsName as $key => $value) {
            if (!empty($row[$key]))
                $disp .= PHPShopText::b($value . ": ") . $row[$key] . "<br>";
        }

        return $disp;
    }

    /**
     * Шаблонизатор вывода доставки в заказе
     * @param string $function имя функции шаблона вывода
     * @param atrray $option дополнительные опции, передающиеся в шаблон
     * @return string
     */
    function delivery($function, $option = false) {
        $list = null;
        $i = 0;
        $order = $this->unserializeParam('orders');

        $PHPShopDelivery = new PHPShopDelivery($order['Person']['dostavka_metod']);
        $delivery['id'] = $order['Person']['dostavka_metod'];
        $name = $PHPShopDelivery->getCity();

        if (!isset($order['Cart']['dostavka']))
            $delivery['price'] = number_format($PHPShopDelivery->getPrice($order['Cart']['sum'], $order['Cart']['weight']), $this->format, '.', '');
        else
            $delivery['price'] = number_format($order['Cart']['dostavka'], $this->format, '.', '');
        $delivery['data_fields'] = $PHPShopDelivery->getParam('data_fields');

        $PID = $PHPShopDelivery->getParam('PID');
        while ($PID AND $i < 5) {
            $PHPShopDeliveryTemp = new PHPShopDelivery($PID);
            $PID = $PHPShopDeliveryTemp->getParam('PID');
            $name = $PHPShopDeliveryTemp->getCity() . ", $name";
            $i++;
        }

        $delivery['name'] = $name;

        if (function_exists($function)) {
            $list = call_user_func_array($function, array($delivery, $option));
            return $list;
        } else
            return $delivery;
    }

    /**
     * Выдача суммы товаров в заказе
     * @return float
     */
    function getCartSumma() {
        $order = $this->unserializeParam('orders');
        if (!empty($order['Person']['discount']))
            $discount = $order['Person']['discount'];
        else
            $discount = 0;

        if (!empty($order['Cart']['sum'])) {
            $sum = $this->ReturnSumma($order['Cart']['sum'], $discount);
            return number_format($sum, $this->format, '.', '');
        }
    }

    /**
     * Выдача стоимости доставки в заказе
     * @return float
     */
    function getDeliverySumma() {
        $order = $this->unserializeParam('orders');

        if (isset($order['Cart']['dostavka']))
            return (float) $order['Cart']['dostavka'];

        if (!empty($order['Person']['discount']))
            $discount = $order['Person']['discount'];
        else
            $discount = 0;
        if (!empty($order['Cart']['sum']))
            $sum = $this->ReturnSumma($order['Cart']['sum'], $discount);
        else
            $sum = 0;
        if (!empty($order['Person']['dostavka_metod']) and ! empty($sum)) {
            $PHPShopDelivery = new PHPShopDelivery($order['Person']['dostavka_metod']);
            return (float) $PHPShopDelivery->getPrice($sum, $order['Cart']['weight']);
        }
    }

    /**
     * Выдача скидки в заказе
     * @return float
     */
    function getDiscount() {
        $order = $this->unserializeParam('orders');
        if (!empty($order['Person']['discount']))
            return $order['Person']['discount'];
        else
            return 0;
    }

    /**
     * Выдача количества товаров в заказе
     * @return int
     */
    function getNum() {
        $order = $this->unserializeParam('orders');
        if (!empty($order['Cart']['num']))
            return $order['Cart']['num'];
        else
            return 0;
    }

    /**
     * Выдача почты покупателя в заказе
     * @return string
     */
    function getMail() {
        $order = $this->unserializeParam('orders');
        return $order['Person']['mail'];
    }

    /**
     * Выдача итоговой сумма заказа
     * @param bool $nds учет НДС
     * @return float
     */
    function getTotal($nds = false, $def = '') {

        if ($this->getValue('sum') > 0)
            $total = $this->getValue('sum');
        else {
            $cart = $this->getCartSumma();
            $delivery = $this->getDeliverySumma();
            $total = $cart + $delivery;
        }
        if (!empty($nds))
            $total = $total * $this->PHPShopSystem->getParam('nds') / (100 + $this->PHPShopSystem->getParam('nds'));
        else
            $total = $total;

        $total = number_format($total, $this->format, '.', $def);
        return $total;
    }

    /**
     * Выдача времени изменения состояния заказа
     * @return string
     */
    function getStatusTime() {
        return $this->getSerilizeParam('status.time');
    }

    /**
     * Выдача сериализованного значения
     * @param string $param
     * @return string
     */
    function getSerilizeParam($param) {
        $param = explode(".", $param);
        $val = parent::unserializeParam($param[0]);
        if (!empty($param) and is_array($param) and count($param) > 2) {
            if (!empty($val[$param[1]][$param[2]]))
                return $val[$param[1]][$param[2]];
        } else
            return $val[$param[1]];
    }

    /**
     * Проверка статуса электронной оплаты
     * @return string дата оплаты
     */
    function checkPay() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['payment']);
        $data = $PHPShopOrm->select(array('*'), array('uid' => "=" . intval(str_replace('-', '', $this->getParam('uid'))), 'sum' => '=' . $this->getParam('sum')), array('order' => 'datas desc'), array('limit' => 1));
        if (is_array($data))
            return $data['datas'];
    }

    public function changePaymentStatus($paymentStatus) {
        $orm = new PHPShopOrm($this->objBase);

        $orm->update(array('paid_new' => (int) $paymentStatus), array('id' => "='" . $this->objID . "'"));
    }

    /**
     * Смена статуса заказа
     * @param Int $statusId ИД нового статуса
     * @param Int $oldStatus ИД старого статуса
     */
    public function changeStatus($statusId, $oldStatus) {
        global $PHPShopBase, $_classPath;

        $order = $this->unserializeParam('orders');
        $statusObj = new PHPShopOrderStatusArray();
        $statuses = $statusObj->getArray();
        $DeliveryArray = (new PHPShopDeliveryArray())->getArray();
        $warehouseID = $DeliveryArray[$order['Person']['dostavka_metod']]['warehouse'];

        // Нет данных или статус не изменился
        if (!is_array($order['Cart']['cart']) || $oldStatus === $statusId) {
            return;
        }

        $PHPShopSystem = new PHPShopSystem();

        // Если новый статус Аннулирован, а был статус не Новый заказ, то мы не списываем, а добавляем обратно
        if ((int) $oldStatus != 0 && $statusId == 1) {
            if ((int) $PHPShopSystem->getSerilizeParam('admoption.sklad_status') > 1) {
                foreach ($order['Cart']['cart'] as $val) {
                    $product = new PHPShopProduct((int) $val['id']);
                    if (is_array($product->objRow)) {
                        $product->addToWarehouse($val['num'], (int) $val['parent'], $warehouseID);
                    }
                }
            }
        } else if ($statuses[$statusId]['sklad_action'] == 1 and $statuses[$oldStatus]['sklad_action'] != 1) {
            foreach ($order['Cart']['cart'] as $val) {
                $product = new PHPShopProduct((int) $val['id']);
                if (is_array($product->objRow)) {
                    $product->removeFromWarehouse($val['num'], (int) @$val['parent'], $warehouseID);
                }
            }
        }

        // SMS оповещение пользователю о смене статуса заказа
        if (!empty($statuses[$statusId]['sms_action'])) {

            $this->objRow['tel'];
            
            $message = $statuses[$statusId]['bot_message'];
            if (!empty($message)) {
                $this->setParserVars();
                $message = preg_replace_callback("/@([a-zA-Z0-9_]+)@/", 'PHPShopParser::SysValueReturn', $message);
            } else
                $message = strtoupper(PHPShopString::check_idna($_SERVER['SERVER_NAME'], true)) . ': ' . $PHPShopBase->getParam('lang.sms_user') . $this->objRow['uid'] . " - " . $statuses[$statusId]['name'];

            $phone = trim(str_replace(array('(', ')', '-', '+', '&#43;'), '', $this->objRow['tel']));
            // Проверка на первую 7 или 8
            $first_d = substr($phone, 0, 1);
            if ($first_d != 8 and $first_d != 7)
                $phone = '7' . $phone;

            $lib = str_replace('./phpshop/', $_classPath, $PHPShopBase->getParam('file.sms'));
            include_once $lib;
            SendSMS($message, $phone);
        }

        // E-mail оповещение
        if ((int) $statusObj->getParam($statusId . '.mail_action') === 1) {
            $this->sendStatusChangedMail();
        }

        // Оповещение в мессенджеры
        if ((int) $statusObj->getParam($statusId . '.bot_action') === 1) {

            PHPShopObj::loadClass('bot');

            $message = $statuses[$statusId]['bot_message'];
            if (!empty($message)) {
                $this->setParserVars();
                $message = preg_replace_callback("/@([a-zA-Z0-9_]+)@/", 'PHPShopParser::SysValueReturn', $message);
            } else
                $message = $PHPShopBase->getParam('lang.sms_user') . $this->objRow['uid'] . " - " . $statuses[$statusId]['name'];

            $user_id = $this->getParam('user');

            // Wappi
            if ($this->PHPShopSystem->ifSerilizeParam('admoption.wappi_enabled', 1)) {
                $PHPShopWappi = new PHPShopWappi();
                $message = PHPShopString::win_utf8($message);
                $PHPShopWappi->cascade($message, $this->getParam('tel'));
            } else {

                // Telegram
                if ($this->PHPShopSystem->ifSerilizeParam('admoption.telegram_enabled', 1)) {
                    $bot = new PHPShopTelegramBot();
                    $chat_id = $bot->find($user_id);
                    if (!empty($chat_id))
                        $bot->send($chat_id, PHPShopString::win_utf8($message));
                }

                // Vk
                if ($this->PHPShopSystem->ifSerilizeParam('admoption.vk_enabled', 1)) {
                    $bot = new PHPShopVKBot();
                    $chat_id = $bot->find($user_id);
                    if (!empty($chat_id))
                        $bot->send($chat_id, PHPShopString::win_utf8($message));
                }
            }

            // Чат
            if (!$bot) {
                $bot = new PHPShopBot();
                $chat_id = $user_id;
            }

            // Диалоги
            $insert = array(
                'user_id' => $user_id,
                'chat' => array
                    (
                    'id' => $chat_id,
                    'first_name' => "Администрация",
                    'last_name' => "",
                ),
                'date' => time(),
                'text' => $message,
                'staffid' => 0,
                'attachments' => null,
                'isview' => 1,
                'order_id' => $this->getParam('id')
            );

            if ($bot)
                $bot->dialog($insert);
        }

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $PHPShopOrm->debug = false;

        $serializedStatus = $this->unserializeParam('status');
        $serializedStatus['time'] = PHPShopDate::dataV();
        $update = [
            'statusi_new' => $statusId,
            'status_new' => serialize($serializedStatus)
        ];
        // Если статус "Оплачено платежными системами" - отмечаем заказ оплаченным
        if ($statusId === 101) {
            $update['paid_new'] = 1;
        }

        $PHPShopOrm->update($update, ['id' => '=' . $this->objID]);
        $this->objRow['statusi'] = $statusId;
    }

    /**
     * Переменные для письма
     */
    private function setParserVars() {
        global $PHPShopSystem;

        PHPShopObj::loadClass("parser");
        PHPShopParser::set('ouid', $this->getParam('uid'));
        PHPShopParser::set('date', PHPShopDate::dataV($this->getParam('datas')));
        PHPShopParser::set('status', $this->getStatus());
        PHPShopParser::set('fio', $this->getParam('fio'));
        PHPShopParser::set('sum', $this->getParam('sum'));
        PHPShopParser::set('company', $PHPShopSystem->getParam('name'));
        PHPShopParser::set('manager', $this->getSerilizeParam('status.maneger'));
        PHPShopParser::set('tracking', $this->getParam('tracking'));

        $protocol = 'http://';
        if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])) {
            $protocol = 'https://';
        }
        PHPShopParser::set('account', $protocol . $_SERVER['SERVER_NAME'] . '/phpshop/forms/account/forma.html?orderId=' . $this->objID . '&tip=2&datas=' . $this->getParam('datas'));
        PHPShopParser::set('bonus', $this->getParam('bonus_plus'));
        
        $host = $GLOBALS['SysValue']['connect']['host'];
        $dbname = $GLOBALS['SysValue']['connect']['dbase'];
        $uname = $GLOBALS['SysValue']['connect']['user_db'];
        $upass = $GLOBALS['SysValue']['connect']['pass_db'];
        
        $security = md5($host . $dbname . $uname . $upass . $this->objID);

        // Ссылка на оплату
        PHPShopParser::set('pay', $protocol . $_SERVER['SERVER_NAME'] . '/pay/?orderID=' . $this->objID . '-' . $security);
        
        // Бланк заказа
        PHPShopParser::set('order', $protocol . $_SERVER['SERVER_NAME'] . '/phpshop/admpanel/order/forms/order.php?orderID=' . $this->objID . '-' . $security);
        
        // Товарный чек
        PHPShopParser::set('receipt', $protocol . $_SERVER['SERVER_NAME'] . '/phpshop/admpanel/order/forms/receipt.php?orderID=' . $this->objID . '-' . $security);
        
        // Счет-фактура
        PHPShopParser::set('receipt', $protocol . $_SERVER['SERVER_NAME'] . '/phpshop/admpanel/order/forms/invoice.php?orderID=' . $this->objID . '-' . $security);
        
        // Торг-12
        PHPShopParser::set('torg', $protocol . $_SERVER['SERVER_NAME'] . '/phpshop/admpanel/order/forms/torg-12.php?orderID=' . $this->objID . '-' . $security);
        
        // Гарантия
        PHPShopParser::set('warranty', $protocol . $_SERVER['SERVER_NAME'] . '/phpshop/admpanel/order/forms/warranty.php?orderID=' . $this->objID . '-' . $security);
        
        // Акт
        PHPShopParser::set('act', $protocol . $_SERVER['SERVER_NAME'] . '/phpshop/admpanel/order/forms/act.php?orderID=' . $this->objID . '-' . $security);
        
    }

    /**
     * Оповещение пользователя о новом статусе
     */
    private function sendStatusChangedMail() {
        global $PHPShopGUI;

        $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
        $PHPShopSystem = new PHPShopSystem();

        PHPShopObj::loadClass("parser");
        PHPShopObj::loadClass("mail");

        // Переменные для письма
        $this->setParserVars();

        $title = __('Cтатус заказа') . ' ' . $this->getParam('uid') . ' ' . __('поменялся на') . ' ' . $this->getStatus();

        $message = $PHPShopOrderStatusArray->getParam($this->getParam('statusi') . '.mail_message');

        if (strlen($message) < 7)
            $message = '<h3>' . __('Статус вашего заказа') . '  &#8470;' . $this->getParam('uid') . '  ' . __('поменялся на') . ' "' . $this->getStatus() . '"</h3>';

        PHPShopParser::set('message', preg_replace_callback("/@([a-zA-Z0-9_]+)@/", 'PHPShopParser::SysValueReturn', $message));
        $PHPShopMail = new PHPShopMail($this->getMail(), $PHPShopSystem->getValue('adminmail2'), $title, '', true, true);

        // Если заказ с витрины
        if ((int) $this->getParam('servers') > 0) {
            $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['servers']);
            $showcaseData = $orm->getOne(['*'], ['id' => sprintf("='%s'", (int) $this->getParam('servers'))]);

            if (is_array($showcaseData)) {
                PHPShopParser::set('serverPath', $showcaseData['host'] . "/" . $GLOBALS['SysValue']['dir']['dir']);

                if (!empty($showcaseData['name']))
                    PHPShopParser::set('shopName', $showcaseData['name']);
                if (!empty($showcaseData['logo']))
                    PHPShopParser::set('logo', $showcaseData['logo']);
                if (!empty($showcaseData['company']))
                    PHPShopParser::set('org_name', $showcaseData['company']);
                if (!empty($showcaseData['adres']))
                    PHPShopParser::set('org_adres', $showcaseData['adres']);
                if (!empty($showcaseData['tel']))
                    PHPShopParser::set('telNum', $showcaseData['tel']);
                if (!empty($showcaseData['adminmail']))
                    PHPShopParser::set('adminMail', $showcaseData['adminmail']);

                $PHPShopMail->mail->setFrom($PHPShopMail->from, $showcaseData['name']);
            }
        }
        $content = PHPShopParser::file(dirname(__DIR__) . '/lib/templates/order/status.tpl', true);
        if (!empty($content)) {
            $PHPShopMail->sendMailNow($content);
        }
    }

    public function setHook($class_name, $function_name, $data = false, $rout = false) {
        if (!empty($this->PHPShopModules)) {
            return $this->PHPShopModules->setHookHandler($class_name, $function_name, array(&$this), $data, $rout);
        }
    }

}

PHPShopObj::loadClass('array');

/**
 * Массив статусов заказов
 * Упрощенный доступ к статусам заказов
 * @author PHPShop Software
 * @version 1.2
 * @package PHPShopObj
 */
class PHPShopOrderStatusArray extends PHPShopArray {

    /**
     * Конструктор
     */
    function __construct() {
        $this->objBase = $GLOBALS['SysValue']['base']['order_status'];
        $this->order = array('order' => 'num');
        parent::__construct('id', 'name', 'color', 'sklad_action', 'cumulative_action', 'mail_action', 'mail_message', 'sms_action', 'num', 'bot_action', 'bot_message');
    }

}
