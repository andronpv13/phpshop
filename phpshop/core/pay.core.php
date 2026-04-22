<?php

/**
 * Генерация ссылки на оплату
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopCore
 */
class PHPShopPay extends PHPShopCore {

    /**
     * Конструктор
     */
    function __construct() {
        $this->empty_index_action = true;
        $GLOBALS['SysValue']['other']['isPage'] = true;
        parent::__construct();
    }

    /**
     * Авторизация
     */
    private function check($s, $id) {

        $host = $GLOBALS['SysValue']['connect']['host'];
        $dbname = $GLOBALS['SysValue']['connect']['dbase'];
        $uname = $GLOBALS['SysValue']['connect']['user_db'];
        $upass = $GLOBALS['SysValue']['connect']['pass_db'];

        if ($s == md5($host . $dbname . $uname . $upass . $id))
            return true;
    }

    /**
     * Экшен по умолчанию
     */
    function index() {

        $orderID = (int) explode("-", $_GET['orderID'])[0];
        $security = (string) explode("-", $_GET['orderID'])[1];

        if ($this->check($security, $orderID) and ! empty($_GET['orderID'])) {

            // Библиотека работы с заказом
            $PHPShopOrderFunction = new PHPShopOrderFunction($orderID);

            if (!empty($PHPShopOrderFunction->objRow)) {

                $link = $this->setHook('userorderpaymentlink', 'userorderpaymentlink', $PHPShopOrderFunction);
                if (empty($link))
                    return $this->setError404();

                $uid = $PHPShopOrderFunction->getParam('uid');

                $this->set('pay_link_uid', '&#8470;' . $uid);
                $this->set('pay_link_name', $PHPShopOrderFunction->getParam('fio'));
                $this->set('pay_link_sum', $PHPShopOrderFunction->getParam('sum'));
                $this->set('pay_link', $link);
                $dis = PHPShopParser::file('./phpshop/lib/templates/order/pay_link.tpl', true, true, true);

                $this->title = __('Оплата заказа') . ' &#8470;' . $uid . ' - ' . $this->PHPShopSystem->getValue("name");
                $this->description = __('Оплата заказа');
                $this->navigation(0, __('Оплата заказа'));
                $this->set('pageContent', $dis);
                $this->set('pageTitle', __('Оплата заказа') . ' &#8470;' . $uid);

                // Перехват модуля
                $this->setHook(__CLASS__, __FUNCTION__,$PHPShopOrderFunction);

                $this->parseTemplate($this->getValue('templates.page_page_list'));
            } else
                return $this->setError404();
        } else
            return $this->setError404();
    }

}
