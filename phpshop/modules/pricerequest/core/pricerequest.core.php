<?php

class PHPShopPricerequest extends PHPShopCore {

    var $empty_index_action = false;
    var $system;

    /**
     * Конструктор
     */
    function __construct() {

        // Имя Бд
        $this->objBase = $GLOBALS['SysValue']['base']['pricerequest']['pricerequest_jurnal'];

        // Отладка
        $this->debug = false;

        // Настройка
        $this->system();

        // Список экшенов
        $this->action = array(
            'post' => 'pricerequest_mod_product_id',
            'name' => 'done',
            'nav' => 'index'
        );
        parent::__construct();
        
  
        // Хлебные крошки
        $this->navigation(null, __('Запрос цены'));
        $this->system['title']=__('Запрос цены');
        $this->system['captcha'] = 0;

        // Мета
        $this->title = $this->system['title'] . " - " . $this->PHPShopSystem->getValue("name");
        $this->description = $this->system['title'] . " " . $this->PHPShopSystem->getValue("name");
        $this->keywords = $this->system['title'] . ", " . $this->PHPShopSystem->getValue("name");
    }

    /**
     * Настройка
     */
    function system() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['pricerequest']['pricerequest_system']);
        $this->system = $PHPShopOrm->select();
    }

    /**
     * Сообщение об удачной заявке
     */
    function done() {
        $message = $this->system['message'];
        $this->set('pageTitle', $this->system['title']);
        $this->set('pageContent', $message);
        $this->parseTemplate($this->getValue('templates.page_page_list'));
    }

    /**
     * Сообщение о неудачной заявке
     */
    function error($message) {
        $this->set('pageTitle', __('Ошибка'));
        $this->set('pageContent', $message);
        $this->parseTemplate($this->getValue('templates.page_page_list'));
    }

    /**
     * Экшен по умолчанию, вывод формы звонка
     */
    function index($message = false) {
        if (!empty($message))
            $this->error($message);
        else
            return $this->setError404();
    }

    /**
     * Проверка ботов
     * @param array $option параметры проверки [url/captcha]
     * @return boolean
     */
    function security($option = array('url' => false, 'captcha' => true, 'referer' => true)) {
        global $PHPShopRecaptchaElement;
        return $PHPShopRecaptchaElement->security($option);
    }

    /**
     * Экшен записи при получении $_POST[pricerequest_mod_send]
     */
    function pricerequest_mod_product_id() {

        if ($this->security(array('url' => false, 'captcha' => (bool) $this->system['captcha'], 'referer' => true))) {
            $product = new PHPShopProduct((int) $_POST['pricerequest_mod_product_id']);

            $_POST['pricerequest_mod_product_option'] = PHPShopSecurity::TotalClean($_POST['pricerequest_mod_product_option']);
            $this->order_num = $this->write($product,(string) $_POST['pricerequest_mod_product_option']);

            $this->sendMail($product,(string) $_POST['pricerequest_mod_product_option']);

            // SMS администратору
            $this->sms($product, (string) $_POST['pricerequest_mod_product_option']);

            header('Location: ./done.html');
            exit();
        }

        $message = __($GLOBALS['SysValue']['lang']['pricerequest_error']);
        $this->index($message);
    }

    /**
     * SMS оповещение
     * @param PHPShopProduct $product
     */
    function sms($product, $option) {

        if ($this->PHPShopSystem->ifSerilizeParam('admoption.sms_enabled')) {

            $msg = substr($this->lang('mail_title_adm'), 0, strlen($this->lang('mail_title_adm')) - 1) . ' ' . $product->getName().$option;

            include_once($this->getValue('file.sms'));
            SendSMS($msg);
        }
    }

    /**
     * @param PHPShopProduct $product
     */
    function write($product,$option) {

        $insert = array();
        $insert['name_new'] = PHPShopSecurity::TotalClean($_POST['pricerequest_mod_name'], 2);
        $insert['tel_new'] = PHPShopSecurity::TotalClean($_POST['tel'], 2);
        $insert['datas_new'] = $insert['date_new'] = time();
        $insert['message_new'] = PHPShopSecurity::TotalClean($_POST['pricerequest_mod_message'], 2);
        $insert['ip_new'] = $_SERVER['REMOTE_ADDR'];
        $insert['product_name_new'] = $product->getName().$option;
        $insert['product_image_new'] = $product->getImage();
        $insert['product_id_new'] = $product->objID;
        $insert['product_price_new'] = $this->getPrice($product);

        if (PHPShopSecurity::true_email($_POST['pricerequest_mod_mail']))
            $insert['mail_new'] = $_POST['pricerequest_mod_mail'];

        // Запись в базу
        return $this->PHPShopOrm->insert($insert);
    }

    /**
     * @param PHPShopProduct $product
     */
    public function sendMail($product,$option) {
        PHPShopObj::loadClass("mail");

        $title = $this->PHPShopSystem->getValue('name') . " - " . __('Запрос цены') . " - " . PHPShopDate::dataV();

        $productId = " / ID " . $product->objID . " / ";
        if ($product->getParam('uid') != "") {
            $productId .= "{Артикул} " . $product->getParam('uid') . " / ";
        }

        PHPShopParser::set('tel', PHPShopSecurity::TotalClean($_POST['tel'], 2));
        PHPShopParser::set('content', PHPShopSecurity::TotalClean($_POST['pricerequest_mod_message'], 2));
        PHPShopParser::set('name', PHPShopSecurity::TotalClean($_POST['pricerequest_mod_name'], 2));

        if (PHPShopSecurity::true_email($_POST['pricerequest_mod_mail']))
            PHPShopParser::set('mail', $_POST['pricerequest_mod_mail']);

        PHPShopParser::set('product', $product->getName() . $option. $productId . $this->getPrice($product) . " " . $this->PHPShopSystem->getDefaultValutaCode());
        PHPShopParser::set('product_id', $product->objID);
        PHPShopParser::set('date', PHPShopDate::dataV(false, false));
        PHPShopParser::set('uid', $this->order_num);

        $template=PHPShopParser::file('./phpshop/lib/templates/users/mail_admin_one_click.tpl', true, false);
        $template = str_replace(__('быстрый заказ'),__('запрос цены'),$template);
        
        (new PHPShopMail($this->system['mail'], $this->system['mail'], $title, '', true, true))->sendMailNow($template);
    }

    /**
     * @param PHPShopProduct $product
     */
    private function getPrice($product) {
        global $PHPShopPromotions;

        $price = $product->getPrice();

        // Промоакции
        $promotions = $PHPShopPromotions->getPrice($product->objRow);
        if (is_array($promotions)) {
            $prices = [$promotions['price'], $product->objRow['price2'], $product->objRow['price3'], $product->objRow['price4'], $product->objRow['price5']];
            $price = PHPShopProductFunction::GetPriceValuta($product->objID, $prices, $product->objRow['baseinputvaluta']);
        }

        return $price;
    }

}
