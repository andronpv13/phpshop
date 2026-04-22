<?php

/**
 * Обработчик жалобы на цену
 * @author PHPShop Software
 * @version 1.6
 * @package PHPShopCore
 */
class PHPShopPricemail extends PHPShopShopCore {

    private $productId;

    /**
     * Конструктор
     */
    function __construct($productId = null) {

        // Отладка
        $this->debug = false;

        // список экшенов
        $this->action = array("nav" => "index", "post" => "send_price_link");
        parent::__construct();

        if (is_null($productId)) {
            $productId = $this->PHPShopNav->getId();
        }
        $this->productId = $productId;
    }

    /**
     * Экшен по умолчанию 
     */
    function index() {

        if (PHPShopSecurity::true_num($this->PHPShopNav->getId()))
            $this->forma();
        else
            $this->setError404();
    }

    /**
     * Проверка ботов
     * @param array $option параметры проверки [url|captcha|referer]
     * @return boolean
     */
    function security($option = array('url' => false, 'captcha' => true, 'referer' => true)) {
        global $PHPShopRecaptchaElement;

        return $PHPShopRecaptchaElement->security($option);
    }

    /**
     * Экшен отправка формы при получении $_POST[send_price_link]
     */
    function send_price_link() {

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__, $_POST))
            return true;

        if ($this->security()) {
            $this->send();
            $this->lead();
            $this->set('Error', PHPShopText::alert(__("Сообщение успешно отправлено"), 'success'));
        } else
            $this->set('Error', PHPShopText::alert(__("Ошибка ключа, повторите попытку ввода ключа")));
        $this->index();
    }

    /**
     * Добавление лида
     */
    function lead() {

        if (!empty($this->PHPShopNav->getId())) {
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['notes']);
            $content = "Для товара http://" . $_SERVER['SERVER_NAME'] . "/shop/UID_" . $this->PHPShopNav->getId() . ".html" . " указана ссылка с меньшей ценой " . $_POST['link_to_page'] . ' ' . $_POST['adr_name'];
            $insert = array('date_new' => time(), 'message_new' => __('Нашли дешевле'), 'name_new' => $_POST['name_person'], 'mail_new' => $_POST['mail'], 'tel_new' => $_POST['tel_name'], 'content_new' => PHPShopSecurity::TotalClean($content));
            $PHPShopOrm->insert($insert);
        }
    }

    /**
     * Отправление данных на почту и добавление в БД
     */
    function send() {

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__, $_POST, 'START'))
            return true;

        if (PHPShopSecurity::true_param($_POST['mail'], $_POST['name_person'], $_POST['link_to_page'])) {

            if (isset($_REQUEST['ajax'])) {
                $_POST['name_person'] = PHPShopString::utf8_win1251($_POST['name_person']);
                $_POST['link_to_page'] = PHPShopString::utf8_win1251($_POST['link_to_page']);
                $_POST['org_name'] = PHPShopString::utf8_win1251($_POST['org_name']);
                $_POST['adr_name'] = PHPShopString::utf8_win1251($_POST['adr_name']);
            }

            // Заголовок e-mail пользователю
            $this->title_mail = __('Сообщение о меньшей цене');

            $this->set('user_name', $_POST['name_person']);
            $this->set('user_org_name', $_POST['org_name']);
            $this->set('user_tel_code', $_POST['tel_code']);
            $this->set('user_tel', $_POST['tel_name']);
            $this->set('user_info', $_POST['adr_name']);
            $this->set('user_mail', $_POST['mail']);
            $this->set('link_to_page', $_POST['link_to_page']);

            // Данные по товару
            $PHPShopProduct = new PHPShopProduct($this->productId);

            $this->set('product_name', $PHPShopProduct->getName());
            $this->set('product_art', $PHPShopProduct->getParam('uid'));
            $this->set('product_id', $this->productId);
            $this->set('product_link', $_SERVER['SERVER_NAME'] . "/shop/UID_" . $this->productId . ".html");

            // Перехват модуля
            $this->setHook(__CLASS__, __FUNCTION__, $_POST, 'END');

            // Содержание e-mail пользователю
            $content = ParseTemplateReturn(dirname(__DIR__) . '/lib/templates/users/mail_pricemail.tpl', true);

            if (PHPShopSecurity::true_email($_POST['mail'])) {
                PHPShopObj::loadClass('mail');
                new PHPShopMail($this->PHPShopSystem->getEmail(), $this->PHPShopSystem->getEmail(), $this->title_mail, $content, true);
            }

            if (!isset($_REQUEST['ajax'])) {
                $this->redirect();
            }
        }
    }

    /**
     * Переход на описание товара
     */
    function redirect() {

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__))
            return true;

        header("Location: ../shop/UID_" . $this->PHPShopNav->getId() . ".html?pricemail=done");
    }

    /**
     * Валюта
     * @param string $name имя поля в таблице валют для выдачи
     * @return string
     */
    function currency($name = 'code') {

        if (isset($_SESSION['valuta']))
            $currency = $_SESSION['valuta'];
        else
            $currency = $this->PHPShopSystem->getParam('dengi');

        $PHPShopOrm = new PHPShopOrm($this->getValue('base.currency'));

        $row = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($currency)), false, array('limit' => 1), __FUNCTION__, array('base' => $this->getValue('base.currency'), 'cache' => 'true'));

        if ($name == 'code' and ( $row['iso'] == 'RUR' or $row['iso'] == "RUB"))
            return 'p';

        return $row[$name];
    }

    /**
     * Прорисовка формы
     */
    function forma() {

        // Перехват модуля
        if ($this->setHook(__CLASS__, __FUNCTION__, false, 'START'))
            return true;

        $PHPShopProduct = new PHPShopProduct($this->PHPShopNav->getId());

        // Промоакции
        $PHPShopPromotions = new PHPShopPromotions();
        $promotions = $PHPShopPromotions->getPrice($PHPShopProduct->objRow);
        if (is_array($promotions)) {
            $this->set('productPrice', $promotions['price']);
            $this->set(array('productPriceRub', 'productPriceOld'), number_format($promotions['price_n'], $this->format, '.', ' ') . ' ' . $this->PHPShopSystem->getValutaIcon());
        } else {
            $this->set('productPrice', number_format($PHPShopProduct->getPrice(), $this->format, '.', ' '));

            if ($PHPShopProduct->getPriceOld() > 0)
                $this->set(array('productPriceRub', 'productPriceOld'), number_format($PHPShopProduct->getPriceOld(), $this->format, '.', ' ') . ' ' . $this->PHPShopSystem->getValutaIcon());
            else
                $this->set(array('productPriceRub', 'productPriceOld'), '');
        }

        $this->set('productName', $PHPShopProduct->getName());
        $this->set('productDes', $PHPShopProduct->getParam('description'));
        $this->set('productImg', $PHPShopProduct->getImage());
        $this->set('productUid', $this->PHPShopNav->getId());
        $this->set('productValutaName', $this->currency());

        // Данные пользователя
        if (PHPShopSecurity::true_num($_SESSION['UsersId'])) {
            $PHPShopUsers = new PHPShopUser($_SESSION['UsersId']);
            $this->set('UserMail', $PHPShopUsers->getParam('mail'));
            $this->set('UserName', $PHPShopUsers->getParam('name'));
            $this->set('UserTel', $PHPShopUsers->getParam('tel'));
            $this->set('UserTelCode', $PHPShopUsers->getParam('tel_code'));
            $this->set('UserAdres', $PHPShopUsers->getParam('adres'));
            $this->set('UserComp', $PHPShopUsers->getParam('company'));
            $this->set('formaLock', 'readonly=1');
        }
        // Запоминаем данные при неудачной отправке формы
        elseif (!empty($_POST['send_price_link'])) {
            $this->set('UserMail', $_POST['mail']);
            $this->set('UserName', $_POST['name_person']);
            $this->set('UserTel', $_POST['tel_name']);
            $this->set('UserTelCode', $_POST['tel_code']);
            $this->set('UserAdres', $_POST['adr_name']);
            $this->set('UserComp', $_POST['org_name']);
            $this->set('UserLink', $_POST['link_to_page']);
        }

        // Заголовок
        $this->title = $PHPShopProduct->getName() . ' - ' . __('Пожаловаться на цену') . " - " . $this->PHPShopSystem->getName();
        $this->description = __('Пожаловаться на цену') . ': ' . $PHPShopProduct->getName();
        $this->keywords = $PHPShopProduct->getName();

        // Перехват модуля
        $this->setHook(__CLASS__, __FUNCTION__, $PHPShopProduct, 'END');

        // Вставка данных в шаблон
        $this->ParseTemplate($this->getValue('templates.pricemail_forma'));
    }

}

?>