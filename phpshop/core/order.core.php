<?php

PHPShopObj::loadClass(['order', 'bonus']);
$PHPShopOrder = new PHPShopOrderFunction();

/**
 * Обработчик оформления заказа
 * @author PHPShop Software
 * @version 1.8
 * @package PHPShopCore
 */
class PHPShopOrder extends PHPShopCore {

    /**
     * Конструктор
     */
    function __construct() {

        // Отладка
        $this->debug = false;

        // Имя Бд
        $this->objBase = $GLOBALS['SysValue']['base']['orders'];

        // Список экшенов
        $this->action = array("post" => array('id_edit', 'id_delete'), "get" => "cart", 'nav' => 'index');
        parent::__construct();

        // Кол-во знаков в постфиксе заказа №_XX, по умолчанию 2
        $format = $this->getValue('my.order_prefix_format');
        if (!empty($format))
            $this->format = $format;
        else
            $this->format = 2;

        PHPShopObj::loadClass('cart');
        $this->PHPShopCart = new PHPShopCart();
    }

    /**
     * Экшен по умолчанию
     */
    function index() {
        global $PHPShopOrder;

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
            return true;

        // Тип работы
        if ($this->PHPShopSystem->getParam("shop_type") > 0)
            return $this->setError404();

        // Импорт данных
        $this->import();

        // Отключение lastmodified
        $this->SysValue['cache']['last_modified'] = false;

        // Title
        $this->title = $this->lang('order_title') . ' - ' . $this->PHPShopSystem->getValue("title");

        // Валюта
        if ($PHPShopOrder->default_valuta_iso == 'RUR' or $PHPShopOrder->default_valuta_iso == "RUB")
            $this->set('currency', 'p');
        else
            $this->set('currency', $PHPShopOrder->default_valuta_code);

        // Если есть корзина товаров
        if ($this->PHPShopCart->getNum() > 0)
            $this->order();
        else
            $this->error();

        $PHPShopCartElement = new PHPShopCartElement(true);
        $PHPShopCartElement->init('miniCart');
        $this->set('productValutaName', $this->get('currency'));
    }

    /**
     * Экшен очистки корзины
     */
    function cart() {

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__, $_POST))
            return true;

        $this->PHPShopCart->clean();
        $this->index();
    }

    /**
     * Экшен удаление товара в заказе
     */
    function id_delete() {
        global $PHPShopAnalitica;

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__, $_POST))
            return true;

        // Аналитика
        $PHPShopAnalitica->init(__FUNCTION__, $_POST);

        $this->PHPShopCart->del($_POST['id_delete']);

        $this->index();
    }

    /**
     * Экшен редактирования товара в заказе
     */
    function id_edit() {

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__, $_POST))
            return true;

        if (PHPShopSecurity::true_num($_POST['num_new']))
            $this->PHPShopCart->edit($_POST['id_edit'], $_POST['num_new'], $_POST['edit_num']);
        $this->index();
    }

    /**
     * Список товаров в заказе
     * @return string
     */
    function product() {
        global $PHPShopOrder;

        // Перехват модуля
        $hook = $this->setHook(__CLASS__, __FUNCTION__, false, 'START');
        if ($hook)
            return $hook;

        // Валюта
        $this->set('currency', $this->PHPShopSystem->getValutaIcon(true));

        $cart = $this->PHPShopCart->display('ordercartforma');
        $this->set('display_cart', $cart);
        $this->set('cart_num', $this->PHPShopCart->getNum());
        $this->set('discount', $PHPShopOrder->ChekDiscount($this->PHPShopCart->getSum(true), $this->PHPShopCart->getArray()));

        $sum_cart = $this->PHPShopCart->getSum(true);
        $sum_discount_off = $this->PHPShopCart->getSumNoDiscount(true);

        // Итого товары по акции
        $sum_discount_on = (float) $PHPShopOrder->returnSumma($this->PHPShopCart->getSumPromo(true));

        // Итого товары без акции
        $sum_discount_on += (float) $PHPShopOrder->returnSumma($this->PHPShopCart->getSumWithoutPromo(true), $this->get('discount'));

        // Бонусы
        $PHPShopBonus = new PHPShopBonus((int) $_SESSION['UsersId']);
        $bonus = (float) $PHPShopBonus->getUserBonus($sum_discount_on);
        $this->set('bonus', $bonus);
        $this->set('max_order_bonus', $PHPShopBonus->max_order_bonus);

        // Итого с учетом бонусов
        $sum_discount_on -= $bonus;

        // Сумма скидки
        if ($sum_cart > $sum_discount_on)
            $discount_sum = $sum_discount_off - $sum_discount_on;
        elseif ($sum_discount_off > $sum_cart)
            $discount_sum = $sum_discount_off - $sum_cart;
        else
            $discount_sum = 0;

        $this->set('discount_sum', number_format($discount_sum * $this->PHPShopSystem->getDefaultValutaKurs(false), $PHPShopOrder->format, '.', ''));
        $this->set('cart_sum', $sum_cart);
        $this->set('cart_sum_discount_off', number_format($sum_discount_off, $PHPShopOrder->format, '.', ''));
        $this->set('cart_weight', $this->PHPShopCart->getWeight());
        $this->set('cart_volume_weight', $this->PHPShopCart->getVolumeWeight());

        // Стоимость доставки
        PHPShopObj::loadClass('delivery');
        $this->set('delivery_price', PHPShopDelivery::getPriceDefault());

        // Итоговая стоимость
        $this->set('total', number_format($sum_discount_off + $this->get('delivery_price') - $discount_sum, $PHPShopOrder->format, '.', ' '));

        // Перехват модуля
        $this->setHook(__CLASS__, __FUNCTION__, false, 'END');

        if (PHPShopParser::checkFile('order/cart.tpl'))
            return ParseTemplateReturn('order/cart.tpl');
        else
            return ParseTemplateReturn('phpshop/lib/templates/order/cart.tpl', true);
    }

    /**
     * Вывод списка доставки
     * Функция вынесена в отдельный файл /order.core/delivery.php
     * @return mixed
     */
    function delivery() {

        // Перехват модуля
        $hook = $this->setHook(__CLASS__, __FUNCTION__);
        if ($hook)
            return $hook;

        return $this->doLoadFunction(__CLASS__, __FUNCTION__, @$_GET['d']);
    }

    /**
     * Сообщение об ошибке, пустая корзина
     */
    function error() {
        $message = '<div class="phpshop-empty-cart">' . $this->message($this->lang('bad_cart_1'), $this->lang('bad_order_mesage_2')) . '</div>';
        $message .= "<script language='JavaScript'>
document.getElementById('num').innerHTML = '0';
document.getElementById('sum').innerHTML = '0';
document.getElementById('order').style.display = 'none';
</script>";
        $this->set('mesageText', $message);
        $this->set('orderMesage', ParseTemplateReturn($this->getValue('templates.order_forma_mesage')));

        // Перехват модуля
        $this->setHook(__CLASS__, __FUNCTION__);

        // Подключаем шаблон
        $this->parseTemplate($this->getValue('templates.order_forma_mesage_main'));
    }

    /**
     * Сообщение
     * @param string $title заголовок
     * @param string $content содержание
     * @return string
     */
    function message($title, $content) {
        $message = PHPShopText::h4($title, 'text-danger');
        $message .= PHPShopText::message($content, false, false, false, 'text-muted');
        return $message;
    }

    /**
     * Выбор способа оплаты
     */
    function payment() {
        PHPShopObj::loadClass('payment');

        $where['name'] = '!=""';
        $disp = $showYurDataForPaymentClass = null;

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['name'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $PHPShopPayment = new PHPShopPaymentArray($where);
        $Payment = $PHPShopPayment->getArray();

        if (is_array($Payment))
            foreach ($Payment as $val) {

                // Блокировка по сумме
                if ((!empty($val['sum_max']) and $this->PHPShopCart->getSum(false) > $val['sum_max']) or ( !empty($val['sum_min']) and $this->PHPShopCart->getSum(false) < $val['sum_min'])) {
                    continue;
                }

                // Блокировка по скидке
                if ((!empty($val['discount_max']) and $this->get('discount') > $val['discount_max']) or ( !empty($val['discount_min']) and $this->get('discount') < $val['discount_min'])) {
                    continue;
                }

                if (!empty($val['enabled']) OR $val['path'] == 'modules') {
                    $this->value[$val['id']] = array($val['name'], $val['id'], false);
                    $this->set('paymentIcon', '');
                    if (!empty($val['icon'])) {
                        $this->set('paymentIcon', $val['icon']);
                    }
                    $this->set('paymentId', $val['id']);
                    $this->set('paymentId', $val['id']);
                    $this->set('paymentTitle', $val['name']);

                    if (PHPShopParser::checkFile('order/payment.tpl'))
                        $disp .= ParseTemplateReturn('order/payment.tpl');
                    else
                        $disp .= ParseTemplateReturn('phpshop/lib/templates/order/payment.tpl', true);
                }
                // формируем набор классов для яваскрипт функции для вывода доп. полей юр. данный в оформление
                // если для данного типа оплаты они требуются 
                if (!empty($val['yur_data_flag'])) {
                    $showYurDataForPaymentClass .= " showYurDataForPaymentClass" . $val['id'];
                }
            }

        // Перехват модуля
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $this->value);
        if ($hook)
            return $hook;

        if (!empty($showYurDataForPaymentClass)) {
            $this->set('showYurDataForPaymentClass', $showYurDataForPaymentClass);
            if (PHPShopParser::checkFile('payment/showYurDataForPayment.tpl')) {
                $this->set('showYurDataForPayment', ParseTemplateReturn('payment/showYurDataForPayment.tpl'));
            } else {
                $this->set('showYurDataForPayment', ParseTemplateReturn('phpshop/lib/templates/order/nt/showYurDataForPayment.tpl', true));
            }
        }
        $this->set('orderOplata', PHPShopText::select('order_metod', $this->value, 250, "", false, ""));
        $this->set('orderOplata', $disp);
    }

    /**
     * Форма заказа
     */
    function order() {

        // Шаблон формы заказа личных данных
        $this->template_order_forma = $this->getValue('templates.main_order_forma');

        // Шаблон страницы оформления заказа
        $this->template_order_list = $this->getValue('templates.main_order_list');

        // Перехват модуля в начале функции
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
            return true;

        // Номер заказа
        $this->setNum();

        // Корзина
        $this->set('orderContentCart', $this->product());
        $this->set('orderNum', $this->order_num);
        $this->set('orderDate', date("d-m-y"));

        // Минимальный заказ статуса
        if (!empty($_SESSION['UsersStatus']))
            $cart_min = (new PHPShopUserStatus($_SESSION['UsersStatus']))->getParam('cart_min');

        // Минимальный заказ без статуса
        if (empty($cart_min))
            $cart_min = $this->PHPShopSystem->getSerilizeParam('admoption.cart_minimum');

        // Форма личной информации по заказу
        if ($cart_min <= $this->PHPShopCart->getSum(false)) {

            // Доставка
            $this->delivery();
            // Поддержка импортируемых данных
            $this->payment();

            // Данные пользователя из личного кабинета
            if (!empty($_SESSION['UsersId']) and PHPShopSecurity::true_num($_SESSION['UsersId'])) {
                $PHPShopUser = new PHPShopUser($_SESSION['UsersId']);
                $this->set('UserMail', $PHPShopUser->getValue('mail'));

                $this->set('UserAdresList', $PHPShopUser->getAdresList());

                $this->set('UserName', $PHPShopUser->getValue('name'));
                $this->set('UserTel', $PHPShopUser->getValue('tel'));
                $this->set('UserTelCode', $PHPShopUser->getValue('tel_code'));
                $this->set('UserAdres', $PHPShopUser->getValue('adres'));
                $this->set('UserComp', $PHPShopUser->getValue('company'));
                $this->set('UserInn', $PHPShopUser->getValue('inn'));
                $this->set('UserKpp', $PHPShopUser->getValue('kpp'));
                $this->set('formaLock', 'readonly=1');
                $this->set('ComStartReg', PHPShopText::comment());
                $this->set('ComEndReg', PHPShopText::comment('>'));

                $this->set('authData', parseTemplateReturn($this->getValue('templates.main_order_forma_auth_data')));
            } else {
                if (PHPShopParser:: checkFile($this->getValue('templates.main_order_forma_no_auth')))
                    $this->set('noAuth', parseTemplateReturn($this->getValue('templates.main_order_forma_no_auth')));
                else
                    $this->set('noAuth', parseTemplateReturn($this->getValue('templates.main_order_forma_no_auth_nt'), true));
                if (PHPShopParser::checkFile($this->getValue('templates.main_order_forma_no_auth_adr')))
                    $this->set('noAuthAdr', parseTemplateReturn($this->getValue('templates.main_order_forma_no_auth_adr')));
                else
                    $this->set('noAuthAdr', parseTemplateReturn($this->getValue('templates.main_order_forma_no_auth_adr_nt'), true));
            }

            // Перехват модуля в конце функции
            $this->setHook(__CLASS__, __FUNCTION__, false, 'MIDDLE');

            // Форма данных, доставка, адрес, оплата
            // Проверяем есть ли метка нового шаблона, если нет, берём шаблон из phpshop/lib/templates/order/
            if (PHPShopParser::check($this->template_order_forma, 'checkLabelForOldTemplatesNoDelete')) {
                $this->set('orderContent', parseTemplateReturn($this->template_order_forma));
            } else {
                $this->temp = true;
                $this->set('orderContent', parseTemplateReturn($this->getValue('templates.main_order_forma_nt'), true));
            }
            // Перехват модуля в конце функции
            $this->setHook(__CLASS__, __FUNCTION__, false, 'MIDDLE-END');
        } else {
            // форма сообщения, что сумма заказа меньше минимальной.
            $this->set('orderContent', $this->message($this->lang('cart_minimum') . ' ' . $cart_min . ' ' . $this->get('currency'), $this->lang('bad_order_mesage_2')));
        }

        // Перехват модуля в конце функции
        $this->setHook(__CLASS__, __FUNCTION__, false, 'END');

        // Подключаем шаблон страницы заказа
        if (empty($this->temp))
            $this->parseTemplate($this->template_order_list);
        else
            $this->parseTemplate($this->getValue('templates.main_order_list_nt'), true);
    }

    /**
     * Генерация номера заказа
     */
    function setNum() {
        $row = $this->PHPShopOrm->select(array('uid'), false, array('order' => 'id desc'), array('limit' => 1));
        $last = $row['uid'];
        $all_num = explode("-", $last);
        $ferst_num = $all_num[0];
        $order_num = (int) $ferst_num + 1;

        if (empty($_SESSION['order_prefix']))
            $_SESSION['order_prefix'] = substr(rand(1000, 99999), 0, $this->format);

        $this->order_num = $order_num . "-" . $_SESSION['order_prefix'];

        // Перехват модуля
        $this->setHook(__CLASS__, __FUNCTION__, $row);
    }

    /**
     * Импорт данных
     * Функция вынесена в отдельный файл /order.core/import.php
     */
    function import() {

        if (!empty($_GET['from']))
            $from = $_GET['from'];
        else
            $from = null;

        $this->doLoadFunction(__CLASS__, __FUNCTION__, $from);

        // Перехват модуля
        $this->setHook(__CLASS__, __FUNCTION__, $_GET);
    }

}

/**
 * Шаблон вывода таблицы корзины
 * Основной шаблон печатной формы расположен в phpshop/lib/templates/cart/product.tpl
 */
PHPShopObj::loadClass('parser');

function ordercartforma($val, $option) {
    global $PHPShopModules, $PHPShopSystem;

    // Перехват модуля в начале функции
    $hook = $PHPShopModules->setHookHandler(__FUNCTION__, __FUNCTION__, array(&$val), $option, 'START');
    if ($hook)
        return $hook;

    // Проверка подтипа товара, выдача ссылки и изображения главного товара
    if (empty($val['parent'])) {
        PHPShopParser::set('cart_id', $val['id']);

        // Артикул
        if (!empty($val['parent_uid']))
            $val['uid'] = $val['parent_uid'];
    } else {
        PHPShopParser::set('cart_id', $val['parent']);
    }

    PHPShopParser::set('cart_pic_small', $val['pic_small']);
    PHPShopParser::set('cart_xid', $option['xid']);
    PHPShopParser::set('cart_name', $val['name']);
    PHPShopParser::set('cart_art', $val['uid']);
    PHPShopParser::set('cart_num', $val['num']);
    PHPShopParser::set('cart_price', number_format($val['price'] * $PHPShopSystem->getDefaultValutaKurs(true), $option['format'], '.', ' '));
    PHPShopParser::set('cart_price_all', number_format($val['price'] * $val['num'] * $PHPShopSystem->getDefaultValutaKurs(true), $option['format'], '.', ' '));

    if ((float) $val['price_n'] > 0)
        PHPShopParser::set('cart_price_all_old', number_format($val['price_n'] * $val['num'] * $PHPShopSystem->getDefaultValutaKurs(true), $option['format'], '.', ' ') . '<span class="rubznak">' . PHPShopParser::get('currency') . '</span>');
    else
        PHPShopParser::set('cart_price_all_old', null);


    PHPShopParser::set('cart_izm', $val['ed_izm']);
    PHPShopParser::set('cart_weight', $val['weight']);

    // Перехват модуля в конце функции
    $PHPShopModules->setHookHandler(__FUNCTION__, __FUNCTION__, array(&$val), $option, 'END');

    if (PHPShopParser::checkFile('order/product.tpl'))
        return ParseTemplateReturn('order/product.tpl');
    else
        return ParseTemplateReturn('./phpshop/lib/templates/order/product.tpl', true);
}

?>