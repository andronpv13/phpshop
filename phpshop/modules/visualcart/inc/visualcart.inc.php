<?php

if (!defined("OBJENABLED"))
    exit(header('Location: /?error=OBJENABLED'));

/**
 * Элемент корзины
 */
PHPShopObj::loadClass('order');
PHPShopObj::loadClass('cart');

class AddToTemplateVisualCart extends PHPShopElements {

    var $debug = false;
    var $store_check = true;

    /**
     * Конструктор
     */
    function __construct() {
        global $PHPShopSystem;

        $this->option();

        // Режим проверки остатков на складе
        if ($PHPShopSystem->getSerilizeParam('admoption.sklad_status') == 1)
            $this->store_check = false;

        if ($this->option['memory'] == 1) {
            $this->check_user_memory();
            $this->get_memory();
        }

        $this->PHPShopCart = new PHPShopCart();

        parent::__construct();
    }

    /**
     * Настройки
     */
    function option() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['visualcart']['visualcart_system']);
        $PHPShopOrm->debug = $this->debug;
        $this->option = $PHPShopOrm->select();
    }

    /**
     * Учет реферала
     */
    function referal() {

        if (!empty($_SERVER['HTTP_REFERER'])) {

            $url = parse_url($_SERVER['HTTP_REFERER']);
            $referal = $url["host"];
            $query = null;

            // Поиск UTM меток
            if (!empty($url['query']))
                parse_str($url['query'], $query);
            $q = null;

            if (is_array($query))
                foreach ($query as $k => $v)
                    if (in_array($k, array('utm_source', 'utm_medium', 'utm_campaign')))
                        $q .= $k . '=' . PHPShopSecurity::TotalClean($v) . '&';

            if (!empty($q))
                $referal .= '?' . substr($q, 0, strlen($q) - 1);

            if (isset($_COOKIE['ps_referal']))
                $partner = base64_encode(base64_decode($_COOKIE['ps_referal']) . "," . $referal);
            else
                $partner = base64_encode($referal);

            if (strlen($_SERVER['HTTP_REFERER']) > 5 and ! strpos($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME']))
                setcookie("ps_referal", $partner, time() + 60 * 60 * 24 * 90, "/", $_SERVER['SERVER_NAME'], 0);
        }
    }

    /**
     *  JS библиотека
     */
    function addJS() {
        $this->set('visualcart_lib', '<script src="' . $this->get('shopDir') . 'phpshop/modules/visualcart/js/visualcart.js"></script>');
    }

    /**
     * Проверка ключа активации
     */
    function true_key($str) {
        return preg_match("/^[a-zA-Z0-9_]{4,35}$/", $str);
    }

    /**
     * Номер записи памяти в кукус
     */
    function add_cookie() {
        setcookie("visualcart_memory", $this->memory, time() + 60 * 60 * 24 * 90, "/", $_SERVER['SERVER_NAME'], 0);
    }

    /**
     * Проверка на авторизацию пользователя
     */
    function check_user_memory() {

        if (!empty($_SESSION['UsersId']))
            $UsersId = $_SESSION['UsersId'];
        else
            $UsersId = null;

        if (empty($_SESSION['cart']) and PHPShopSecurity::true_num($UsersId)) {
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['visualcart']['visualcart_memory']);
            $PHPShopOrm->debug = $this->debug;
            $data = $PHPShopOrm->select(array('memory'), array('user' => "=" . $UsersId), array('order' => 'date'), array('limit' => 1));
            if (is_array($data)) {
                $this->memory = $data['memory'];
                $this->add_cookie();
            }
        }
    }

    /**
     * Данные из БД по ключу памяти
     */
    function get_memory() {
        if (empty($_SESSION['cart_sig'])) {

            // Реферал
            $this->referal();

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['visualcart']['visualcart_memory']);
            $PHPShopOrm->debug = $this->debug;
            if (!empty($_COOKIE['visualcart_memory']) and $this->true_key($_COOKIE['visualcart_memory'])) {
                $data = $PHPShopOrm->select(array('*'), array('memory' => "='" . $_COOKIE['visualcart_memory'] . "'"), false, array('limit' => 1));

                if (is_array($data)) {
                    $this->memory = $data['memory'];
                    $_SESSION['cart'] = $this->update_price(unserialize($data['cart']));
                    $this->data = $data;

                    // Чистка
                    $this->clean_memory();

                    // Запись в БД
                    $this->add_memory();
                }
            }

            // Дата обновления корзины для первого вызова
            @setcookie("cart_update_time", time(), 0, "/", $_SERVER['SERVER_NAME'], 0);
        }
    }

    /**
     * Проверка цен на изменение
     * @param array $cart
     * @return array
     */
    function update_price($cart) {
        if (is_array($cart)) {
            foreach ($cart as $k => $v) {

                // Данные по товару
                $objProduct = new PHPShopProduct($k);

                // Проверка кол-ва товара на складе
                if ($this->store_check) {
                    if ($cart[$k]['num'] > PHPShopSecurity::TotalClean($objProduct->getParam("items"), 1))
                        $cart[$k]['num'] = PHPShopSecurity::TotalClean($objProduct->getParam("items"), 1);
                }

                $cart[$k]['price'] = PHPShopProductFunction::GetPriceValuta($k, $objProduct->getParam("price"), $objProduct->getParam("baseinputvaluta"), true);
            }
        }
        return $cart;
    }

    /**
     * Затирание старой памяти
     */
    function clean_memory() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['visualcart']['visualcart_memory']);
        $PHPShopOrm->delete(array('memory' => "='" . $this->memory . "'"));
    }

    /**
     * Запись корзины в БД
     */
    function add_memory() {
        $insert = array();
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['visualcart']['visualcart_memory']);
        $insert['memory_new'] = $this->memory;
        $insert['date_new'] = time();

        if (!empty($_SESSION['UsersId']))
            $insert['user_new'] = $_SESSION['UsersId'];
        else
            $insert['user_new'] = $this->data['user'];

        $insert['cart_new'] = serialize($_SESSION['cart']);
        $insert['ip_new'] = $_SERVER["REMOTE_ADDR"];

        if (!empty($_SESSION['UsersTel']))
            $insert['tel_new'] = $_SESSION['UsersTel'];
        elseif(!empty($this->data['tel']))
            $insert['tel_new'] = $this->data['tel'];

        if (!empty($_SESSION['UsersLogin']))
            $insert['mail_new'] = $_SESSION['UsersLogin'];
        elseif(!empty($this->data['mail']))
            $insert['mail_new'] = $this->data['mail'];

        if (!empty($_SESSION['UsersName']))
            $insert['name_new'] = $_SESSION['UsersName'];
        elseif(!empty($this->data['name']))
            $insert['name_new'] = $this->data['name'];

        if (isset($_COOKIE['ps_referal']))
            $insert['referal_new'] = base64_decode($_COOKIE['ps_referal']);
        else
            $insert['referal_new'] = $this->data['referal'];

        if (defined("HostID"))
            $insert['server_new'] = HostID;

        $insert['sendmail_new'] = 0;

        $PHPShopOrm->insert($insert);
    }

    /**
     * Прорисовка элемента визуальной корзины
     */
    function visualcart() {

        // Учет модуля SEOURL
        if (!empty($GLOBALS['SysValue']['base']['seourl']['seourl_system']))
            PHPShopObj::loadClass('string');

        $GLOBALS['PHPShopOrder'] = new PHPShopOrderFunction();

        // Валюта
        $this->currency = $GLOBALS['PHPShopSystem']->getValutaIcon();

        $this->addJS();

        $this->set('visualcart_pic_width', $this->option['pic_width']);

        // Если есть товары в корзине
        if ($this->PHPShopCart->getNum() > 0) {
            $list = $this->PHPShopCart->display('visualcartform', array('currency' => $this->currency));
            $this->set('visualcart_list', $list, true);
            $this->set('visualcart_order', '');
        } else {
            $this->set('visualcart_list', $this->lang('visualcart_empty'), true);
            $this->set('visualcart_order', 'display:none');
        }

        //$this->cart = parseTemplateReturn($GLOBALS['SysValue']['templates']['visualcart']['visualcart_cart'], true);
        $this->cart = PHPShopParser::file($GLOBALS['SysValue']['templates']['visualcart']['visualcart_cart'], true, false, true);

        $this->set('leftMenuContent', $this->cart);
        $this->set('leftMenuName', $this->option['title']);

        // Подключаем шаблон
        $dis = $this->parseTemplate($this->getValue('templates.left_menu'));

        // Назначаем переменную шаблона
        switch ($this->option['enabled']) {

            case 1:
                $this->set('leftMenu', $dis, true);
                break;

            case 2:
                $this->set('rightMenu', $dis, true);
                break;

            default: $this->set('visualcart', $this->cart);
        }
    }

}

/**
 * Шаблон вывода таблицы корзины
 */
PHPShopObj::loadClass('parser');

function visualcartform($val, $option) {

    // Проверка подтипа товара, выдача ссылки главного товара
    if (empty($val['parent'])) {
        PHPShopParser::set('visualcart_product_id', $val['id']);

        // Учет модуля SEOURL
        if (!empty($GLOBALS['SysValue']['base']['seourl']['seourl_system'])) {
            PHPShopParser::set('visualcart_product_seo', '_' . PHPShopString::toLatin($val['name']));
        }
    } else {
        PHPShopParser::set('visualcart_product_id', $val['parent']);
        PHPShopParser::set('visualcart_product_seo', null);
    }

    PHPShopParser::set('visualcart_product_xid', $val['id']);
    PHPShopParser::set('visualcart_product_name', $val['name']);
    PHPShopParser::set('visualcart_product_pic_small', $val['pic_small']);
    PHPShopParser::set('visualcart_product_price', $val['price'] * $val['num']);
    PHPShopParser::set('visualcart_product_currency', $option['currency']);
    PHPShopParser::set('visualcart_product_num', $val['num']);

    $dis = PHPShopParser::file($GLOBALS['SysValue']['templates']['visualcart']['visualcart_product'], true, false, true);
    return $dis;
}

// Добавляем в шаблон элемент
if ($PHPShopNav->notPath(array('done'))) {
    $GLOBALS['AddToTemplateVisualCart'] = new AddToTemplateVisualCart();
    $GLOBALS['AddToTemplateVisualCart']->visualcart();
}
?>