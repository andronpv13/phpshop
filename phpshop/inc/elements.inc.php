<?php

/**
 * Элемент стандартных системных переменных
 * @author PHPShop Software
 * @version 2.3
 * @package PHPShopElements
 */
class PHPShopCoreElement extends PHPShopElements {

    /**
     * Конструктор
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * Режим обслуживания
     */
    function service() {
        if ($this->PHPShopSystem->ifSerilizeParam('admoption.service_enabled', 1)) {

            $ip = explode(",", $this->PHPShopSystem->getSerilizeParam('admoption.service_ip'));
            if (is_array($ip) and in_array(trim($_SERVER['REMOTE_ADDR']), $ip))
                return;
            else {

                $title = $this->PHPShopSystem->getSerilizeParam('admoption.service_title');
                $message = $this->PHPShopSystem->getSerilizeParam('admoption.service_content');

                if (empty($title))
                    $title = '503 Service Temporarily Unavailable';

                if (empty($message))
                    $message = 'Website is under construction';


                PHPShopParser::set('message', $message);
                PHPShopParser::set('title', $title);
                header('HTTP/1.1 503 Service Temporarily Unavailable');
                header('Status: 503 Service Temporarily Unavailable');
                exit(PHPShopParser::file($_SERVER['DOCUMENT_ROOT'] . '/phpshop/lib/templates/error/service.tpl', false, true, true));
            }
        }
    }

    /**
     * Назначение текущего шаблона
     * @return string
     */
    function skin() {

        if (empty($_SESSION['skin'])) {

            // Мультибаза
            if (defined("HostSkin"))
                $_SESSION['skin'] = HostSkin;
            else
                $_SESSION['skin'] = $this->PHPShopSystem->getValue('skin');
        }

        return $_SESSION['skin'];
    }

    /**
     * Проверка существования шаблона,
     * смена на другой найденный шаблон при переименовании папки с шаблоном
     * @return string
     */
    function checkskin() {
        if (!@file_exists("phpshop/templates/" . $_SESSION['skin'] . "/main/index.tpl")) {
            $dir = $this->getValue('dir.templates') . chr(47);
            if (is_dir($dir)) {
                if (@$dh = opendir($dir)) {
                    while (($file = readdir($dh)) !== false) {
                        if (@is_file($dir . $file . chr(47) . 'main/index.tpl')) {
                            $_SESSION['skin'] = $file;
                            header('Location: /?status=template_error');
                        }
                    }
                    closedir($dh);
                }
            }
            exit('Template error!');
        }
    }

    /**
     * Определение системных переменных для шаблонов
     * (имя, телефон, почта администратора, дата, логотип)
     */
    function setdefault() {
        global $PHPShopBase;

        // Мультибаза
        if (defined("HostID")) {
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['servers']);
            $PHPShopOrm->debug = false;
            $showcaseData = $PHPShopOrm->select(array('*'), array('enabled' => "='1'", 'host' => "='" . str_replace('www.', '', $_SERVER['HTTP_HOST']) . "'"), array('order' => 'id'), array('limit' => 1));
            if (is_array($showcaseData)) {

                if (!empty($showcaseData['currency'])) {
                    $_SESSION['valuta'] = $showcaseData['currency'];
                    $lang = $showcaseData['lang'];
                }

                if (!empty($showcaseData['tel']))
                    $this->PHPShopSystem->setParam("tel", $showcaseData['tel']);

                if (!empty($showcaseData['adminmail']))
                    $this->PHPShopSystem->setParam("adminmail2", $showcaseData['adminmail']);

                if (!empty($showcaseData['shop_type']))
                    $this->PHPShopSystem->setParam("shop_type", $showcaseData['shop_type']);

                if (!empty($showcaseData['company_id']))
                    $this->PHPShopSystem->setCompany($showcaseData['company_id']);

                if (!empty($showcaseData['name']))
                    $this->PHPShopSystem->setParam('name', $showcaseData['name']);

                if (!empty($showcaseData['title']))
                    $this->PHPShopSystem->setParam('title', $showcaseData['title']);

                if (!empty($showcaseData['descrip']))
                    $this->PHPShopSystem->setParam('descrip', $showcaseData['descrip']);

                if (!empty($showcaseData['logo']))
                    $this->PHPShopSystem->setParam('logo', $showcaseData['logo']);

                if (!empty($showcaseData['icon']))
                    $this->PHPShopSystem->setParam('icon', $showcaseData['icon']);

                if (!empty($showcaseData['skin']))
                    define("HostSkin", $showcaseData['skin']);

                if (!empty($showcaseData['price']))
                    define("HostPrice", $showcaseData['price']);

                define("HostAdmin", $showcaseData['admin']);

                $admoption = unserialize($showcaseData['admoption']);
                if (is_array($admoption)) {

                    if (!empty($admoption['org_tel']))
                        $this->PHPShopSystem->setSerilizeParam('bank.org_tel', $admoption['org_tel']);

                    if (isset($admoption['user_price_activate']))
                        $this->PHPShopSystem->setSerilizeParam('admoption.user_price_activate', $admoption['user_price_activate']);

                    if (isset($admoption['user_mail_activate']))
                        $this->PHPShopSystem->setSerilizeParam('admoption.user_mail_activate', $admoption['user_mail_activate']);

                    if (isset($admoption['user_mail_activate_pre']))
                        $this->PHPShopSystem->setSerilizeParam('admoption.user_mail_activate_pre', $admoption['user_mail_activate_pre']);

                    if (isset($admoption['smtp_user']))
                        $this->PHPShopSystem->setSerilizeParam('admoption.mail_smtp_user', $admoption['smtp_user']);

                    if (isset($admoption['smtp_password']))
                        $this->PHPShopSystem->setSerilizeParam('admoption.mail_smtp_pass', $admoption['smtp_password']);

                    if (isset($admoption['user_status']))
                        $this->PHPShopSystem->setSerilizeParam('admoption.user_status', $admoption['user_status']);

                    if (isset($admoption['metrica_id']))
                        $this->PHPShopSystem->setSerilizeParam('admoption.metrica_id', $admoption['metrica_id']);

                    if (isset($admoption['google_id']))
                        $this->PHPShopSystem->setSerilizeParam('admoption.google_id', $admoption['google_id']);

                    if (isset($admoption['fee']))
                        $this->PHPShopSystem->setParam('percent', (int) $admoption['fee']);

                    if (!empty($admoption['org_adres']))
                        $this->PHPShopSystem->setSerilizeParam('bank.org_adres', $admoption['org_adres']);

                    if (!empty($admoption['org_time']))
                        $this->PHPShopSystem->setSerilizeParam('bank.org_time', $admoption['org_time']);

                    // Контроль склада
                    if (!empty($admoption['sklad_status'])){
                        $this->PHPShopSystem->setSerilizeParam('admoption.sklad_status', $admoption['sklad_status']);
                        $_SESSION['sklad_status']=$admoption['sklad_status'];
                    }
                }
            }
        } else {
            $lang = $this->PHPShopSystem->getSerilizeParam("admoption.lang");
        }

        // Контроль склада
        $_SESSION['sklad_status'] = (int) $this->PHPShopSystem->getSerilizeParam('admoption.sklad_status');

        $this->set('streetAddress', $this->PHPShopSystem->getSerilizeParam('bank.org_adres'));

        $_SESSION['lang'] = $lang;

        // Язык
        $GLOBALS['PHPShopLang'] = new PHPShopLang(array('locale' => $lang, 'path' => 'shop'));
        $this->set('charset', $GLOBALS['PHPShopLang']->charset);
        $this->set('lang', $GLOBALS['PHPShopLang']->code);

        // Телефон
        $tel = $this->PHPShopSystem->getValue('tel');
        $this->set('telNum', $tel);
        $this->set('telNum2', $this->PHPShopSystem->getSerilizeParam("bank.org_tel"));
        $this->set('workingTime', $this->PHPShopSystem->getSerilizeParam("bank.org_time"));

        // SMS
        if ($this->PHPShopSystem->getSerilizeParam("admoption.sms_login") != 1 and $this->PHPShopSystem->getSerilizeParam("admoption.wappi_enabled") != 1)
            $this->set('sms_login_enabled', 'hidden d-none');
        else {
            $this->set('sms_login_enabled', 'req');
            $this->set('sms_login_control', 'required=""');
        }

        // Телефон для звонков
        if (strstr($tel, ","))
            $tel_xs = explode(",", $tel);
        else
            $tel_xs[] = $tel;

        $this->set('telNumMobile', $tel_xs[0]);
        $this->set('rule', $this->lang('rule'));
        $this->set('name', $this->PHPShopSystem->getValue('name'));

        // Favicon
        $icon = $this->PHPShopSystem->getValue('icon');
        if (empty($icon))
            $icon = '/apple-touch-icon.png';
        $this->set('icon', $icon);

        // Социальные сети
        $this->set('vk', $this->PHPShopSystem->getSerilizeParam('bank.vk'));
        $this->set('telegram', $this->PHPShopSystem->getSerilizeParam('bank.telegram'));
        $this->set('odnoklassniki', $this->PHPShopSystem->getSerilizeParam('bank.odnoklassniki'));
        $this->set('whatsapp', $this->PHPShopSystem->getSerilizeParam('bank.whatsapp'));
        $this->set('youtube', $this->PHPShopSystem->getSerilizeParam('bank.youtube'));

        $this->set('company', $this->PHPShopSystem->getValue('company'));
        $this->set('descrip', $this->PHPShopSystem->getValue('descrip'));
        $this->set('adminMail', $this->PHPShopSystem->getValue('adminmail2'));
        $this->set('pathTemplate', $this->getValue('dir.templates') . chr(47) . $_SESSION['skin']);

        $this->set('serverName', PHPShopString::check_idna($_SERVER['SERVER_NAME']));
        $this->set('serverShop', PHPShopString::check_idna($_SERVER['SERVER_NAME']));
        if (!empty($_SESSION['UserLogin']))
            $this->set('UserLogin', $_SESSION['UserLogin']);
        $this->set('ShopDir', $this->getValue('dir.dir'));
        $this->set('date', date("d-m-y H:i a"));
        $this->set('year', date("Y"));
        $this->set('user_ip', $_SERVER['REMOTE_ADDR']);
        $this->set('NavActive', $this->PHPShopNav->getPath());
        $v = $this->getValue('upload.version');
        $this->set('version', substr($v, 0, 1) . '.' . substr($v, 1, 1));
        $this->set('hcs', '<!--');
        $this->set('hce', '-->');

        // Цветовая тема шаблона
        $theme = $this->PHPShopSystem->getSerilizeParam('admoption.' . $_SESSION['skin'] . '_theme');
        if (!empty($theme))
            $this->set($_SESSION['skin'] . '_theme', $theme);

        $theme2 = $this->PHPShopSystem->getSerilizeParam('admoption.' . $_SESSION['skin'] . '_theme2');
        if (!empty($theme2))
            $this->set($_SESSION['skin'] . '_theme2', $theme2);

        $theme3 = $this->PHPShopSystem->getSerilizeParam('admoption.' . $_SESSION['skin'] . '_theme3');
        if (!empty($theme3))
            $this->set($_SESSION['skin'] . '_theme3', $theme3);

        // Настройка шаблона
        if (empty($_SESSION['editor'][$_SESSION['skin']])) {
            $editor = $this->PHPShopSystem->getSerilizeParam('admoption.' . $_SESSION['skin'] . '_editor');
            if (is_array($editor))
                $_SESSION['editor'][$_SESSION['skin']] = $editor;
        }

        // Логотип
        $this->set('logo', $this->PHPShopSystem->getLogo());

        // DaData.ru
        if ($this->PHPShopSystem->getSerilizeParam('admoption.dadata_enabled')) {
            $dadataToken = $this->PHPShopSystem->getSerilizeParam('admoption.dadata_token');
            if (empty($dadataToken))
                $dadataToken = 'b13e0b4fd092a269e229887e265c62aba36a92e5';
            $this->set('dadataToken', $dadataToken);
        } else
            $this->set('dadataToken', null);

        // Demo режим
        if (isset($_GET['demo']))
            $PHPShopBase->setParam('template_theme.demo', 'false');

        // Сжатие статики
        if ($this->PHPShopSystem->ifSerilizeParam('admoption.min')) {
            $this->set('pathTemplateMin', '/files/min.php?f=' . $this->getValue('dir.templates') . chr(47) . $_SESSION['skin']);
            $this->set('pathMin', '/files/min.php?f=');
        } else {
            $this->set('pathTemplateMin', $this->getValue('dir.templates') . chr(47) . $_SESSION['skin']);
            $this->set('pathMin', null);
        }

        // Тип работы
        $shop_type = $this->PHPShopSystem->getParam("shop_type");

        // Магазин
        if (empty($shop_type)) {
            $this->set('hideShop', 'hide d-none');
            $this->set('showSite', 'hide d-none');
        }
        // Каталог
        else if ($shop_type == 1) {
            $this->set('hideCatalog', 'hide d-none');
            $this->set('showSite', 'hide d-none');
            $this->PHPShopSystem->setSerilizeParam('admoption.nowbuy_enabled', 0);
        }
        // Сайт
        else if ($shop_type == 2) {
            $this->set('hideSite', 'hide d-none');
            $this->set('showSite', 'hide d-none');
            $this->set('hideCatalog', 'hide d-none');
            $this->PHPShopSystem->setSerilizeParam('admoption.nowbuy_enabled', 0);
        }

        // Яндекс ID
        if ($this->PHPShopSystem->ifSerilizeParam('admoption.yandex_id_enabled') and $this->PHPShopSystem->getSerilizeParam('admoption.yandex_id_apikey') != "") {
            $this->set('yandex_id_apikey', $this->PHPShopSystem->getSerilizeParam('admoption.yandex_id_apikey'));
            $this->set('yandex_redirect_uri', $_SERVER['SERVER_NAME'] . 'phpshop/ajax/yandexid.php');

            if (PHPShopParser::checkFile("users/yandexid.tpl"))
                $this->set('yandexid', ParseTemplateReturn('users/yandexid.tpl'));
            else
                $this->set('yandexid', ParseTemplateReturn('phpshop/lib/templates/users/yandexid.tpl', true));
        }

        // VK ID
        if ($this->PHPShopSystem->ifSerilizeParam('admoption.vk_id_enabled') and $this->PHPShopSystem->getSerilizeParam('admoption.vk_id_apikey') != "") {
            $this->set('vk_app', $this->PHPShopSystem->getSerilizeParam('admoption.vk_id'));
            $this->set('vk_redirect_uri', $_SERVER['SERVER_NAME'] . 'phpshop/ajax/vkid.php');

            if (PHPShopParser::checkFile("users/vkid.tpl"))
                $this->set('vkid', ParseTemplateReturn('users/vkid.tpl'));
            else
                $this->set('vkid', ParseTemplateReturn('phpshop/lib/templates/users/vkid.tpl', true));
        }

        // Verification
        $this->set('verification', $this->PHPShopSystem->getSerilizeParam('admoption.service_yandex_metatag'), true);
        $this->set('verification', $this->PHPShopSystem->getSerilizeParam('admoption.service_google_metatag'), true);
    }

    /**
     * JS настройки
     */
    function setjs() {
        $js = null;

        // Маска телефона
        $phone_mask = $this->PHPShopSystem->getSerilizeParam("admoption.user_phone_mask");
        $phone_mask_enabled = $this->PHPShopSystem->getSerilizeParam("admoption.user_phone_mask_enabled");

        if (!empty($phone_mask))
            $js .= 'var PHONE_MASK = "' . str_replace('&#43;', '+', $phone_mask) . '";';
        else
            $js .= 'var PHONE_MASK = "";';
        if (!empty($phone_mask_enabled))
            $js .= 'var PHONE_FORMAT = false;';
        else
            $js .= 'var PHONE_FORMAT = true;';

        // Динамическая прокрутка товаров
        $ajax_scroll = $this->PHPShopSystem->getSerilizeParam("admoption.ajax_scroll");

        if (empty($ajax_scroll))
            $js .= 'var AJAX_SCROLL = true;';
        else
            $js .= 'var AJAX_SCROLL = false;';

        // Кеширование фильтра
        if ($this->PHPShopSystem->ifSerilizeParam("admoption.filter_cache_enabled"))
            $js .= 'var FILTER_CACHE = true;';
        else
            $js .= 'var FILTER_CACHE = false;';

        if ($this->PHPShopSystem->ifSerilizeParam("admoption.filter_products_count"))
            $js .= 'var FILTER_COUNT = true;';
        else
            $js .= 'var FILTER_COUNT = false;';

        // Согласие на COOKIE
        if (defined('isMobil')) {
            if ($this->PHPShopSystem->ifSerilizeParam("admoption.user_cookie_mobile_enabled"))
                $js .= 'var COOKIE_AGREEMENT = false;';
            else
                $js .= 'var COOKIE_AGREEMENT = true;';
        }
        else {
            if ($this->PHPShopSystem->ifSerilizeParam("admoption.user_cookie_enabled"))
                $js .= 'var COOKIE_AGREEMENT = false;';
            else
                $js .= 'var COOKIE_AGREEMENT = true;';
        }

        if (!empty($js)) {
            $this->set('editor', '
        <script>' . $js . '</script>', true);
        }
    }

    /**
     * Стили шаблона дизайна
     * @return string
     */
    function pageCss() {
        $this->set('pathTemplate', $this->getValue('dir.templates') . chr(47) . $_SESSION['skin']);
        return $this->getValue('dir.templates') . chr(47) . $_SESSION['skin'] . chr(47) . $this->getValue('css.default');
    }

}

/**
 * Элемент формы авторизации пользователя
 * @author PHPShop Software
 * @version 1.3
 * @package PHPShopElements
 */
class PHPShopUserElement extends PHPShopElements {

    /**
     * Конструктор
     */
    function __construct() {
        $this->debug = false;
        $this->template_debug = true;
        $this->objBase = $GLOBALS['SysValue']['base']['shopusers'];
        parent::__construct();

        // Если есть параметр from, нужно сохранить реферальную страницу и вернуть на нее пользователя после авторизации, регистрации.
        if (!empty($_REQUEST['from']) and $_REQUEST['from'] AND empty($_REQUEST['fromSave']))
            $this->set('fromSave', $_SERVER['HTTP_REFERER']);
        elseif (!empty($_REQUEST['fromSave']))
            $this->set('fromSave', $_REQUEST['fromSave']);

        // Экшены
        $this->setAction(array('post' => array('user_enter', 'user_register'), 'get' => 'logout'));
    }

    /**
     * Кодирование пароля
     * @param string $str строка
     * @return string кодированная строка
     */
    function encode($str) {
        return base64_encode(trim($str));
    }

    /**
     * Экшен выхода пользователя
     */
    function logout() {
        unset($_SESSION['UsersId']);
        unset($_SESSION['UsersStatus']);
        unset($_SESSION['UsersLogin']);
        unset($_SESSION['UsersName']);
        unset($_SESSION['UsersMail']);
        unset($_SESSION['UsersStatus']);
        unset($_SESSION['UsersStatusPice']);
        unset($_SESSION['UsersBan']);
        unset($_COOKIE['UserLogin']);
        unset($_COOKIE['UserPassword']);
        unset($_COOKIE['UserChecked']);

        setcookie("UserLogin", '', time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);
        setcookie("UserPassword", '', time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);
        setcookie("UserChecked", '', time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);

        $url_user = str_replace("?logout=true", "", $_SERVER['REQUEST_URI']);
        header("Location: " . $url_user);
    }

    /**
     * ссылка на вишлист с кол-вом в нём товара
     */
    function wishlist() {
        if (!empty($_SESSION['UsersId']) and PHPShopSecurity::true_num($_SESSION['UsersId'])) {
            $this->set('wishlistCount', $_SESSION['wishlistCount']);
            $dis = $this->parseTemplate('users/wishlist/wishlist_top_enter.tpl');
        } else {

            if (!empty($_SESSION['wishlist']) and is_array($_SESSION['wishlist']))
                $wishlistCount = count($_SESSION['wishlist']);
            else
                $wishlistCount = 0;

            $this->set('wishlistCount', $wishlistCount);

            $dis = $this->parseTemplate('users/wishlist/wishlist_top_enter.tpl');
        }
        return $dis;
    }

    public function authorize($user) {
        $PHPShopOrm = new PHPShopOrm($this->objBase);

        // сохраняем вишлист который был в сессии до авторизаци.
        $wishlist = unserialize($user['wishlist']);
        if (!is_array($wishlist))
            $wishlist = array();
        if (!empty($_SESSION['wishlist']) and is_array($_SESSION['wishlist']))
            foreach ($_SESSION['wishlist'] as $key => $value) {
                $wishlist[$key] = 1;
            }
        $_SESSION['wishlistCount'] = count($wishlist);

        // Очищаем вишлист из сессии, он сохранён в БД
        unset($_SESSION['wishlist']);

        $wishlist = serialize($wishlist);
        $PHPShopOrm->update(array('wishlist' => $wishlist), array('id' => '=' . $user['id']), false);

        // ID пользователя
        $_SESSION['UsersId'] = $user['id'];

        // Логин пользователя
        $_SESSION['UsersLogin'] = $user['login'];

        // Имя пользователя
        $_SESSION['UsersName'] = $user['name'];

        // Телефон пользователя
        $_SESSION['UsersTel'] = $user['tel'];

        // Статус пользователя
        $_SESSION['UsersStatus'] = $user['status'];

        // Bot
        $_SESSION['UsersBot'] = $user['bot'];

        // Блокировка диалогов
        $_SESSION['UsersBan'] = $user['dialog_ban'];

        // E-mail пользователя для заказа
        if (PHPShopSecurity::true_email($user['login']))
            $_SESSION['UsersMail'] = $user['login'];
        else
            $_SESSION['UsersMail'] = $user['mail'];

        // Дата входа
        $this->log();
    }

    /**
     * Проверка авторизации
     * @return bool
     */
    function autorization() {
        if (PHPShopSecurity::true_login($_POST['login']) and PHPShopSecurity::true_passw($_POST['password'])) {
            $PHPShopOrm = new PHPShopOrm($this->objBase);
            $PHPShopOrm->debug = $this->debug;

            $where = array('login' => '="' . trim($_POST['login']) . '"', 'password' => '="' . $this->encode($_POST['password']) . '"', 'enabled' => "='1'");

            // Мультибаза
            if ($this->PHPShopSystem->ifSerilizeParam("admoption.user_servers_control"))
                $where['servers'] = '=' . intval(HostID);

            $data = $PHPShopOrm->select(array('*'), $where, false, array('limit' => 1));
            if (is_array($data) AND PHPShopSecurity::true_num($data['id'])) {

                $GLOBALS['SysValue']['other']['user_link'] = 'href="/users/"';
                $GLOBALS['SysValue']['other']['user_active'] = 'active';

                $this->authorize($data);

                // Перехват модуля
                $this->setHook(__CLASS__, __FUNCTION__, $data);

                // Редирект если активация
                if (!empty($_GET['key']))
                    header('Location: /users/');

                return true;
            } else
                $this->set("shortAuthError", __("Неверный логин или пароль"));
        } else
            $this->set("shortAuthError", __("Неверный логин или пароль"));
    }

    /**
     * Запись даты авторизации пользователя
     */
    function log() {
        $PHPShopOrm = new PHPShopOrm($this->objBase);
        $PHPShopOrm->debug = $this->debug;
        $PHPShopOrm->update(array('datas_new' => time()), array('id' => '=' . $_SESSION['UsersId']));
    }

    /**
     * Экшен входа регистрации пользователя по страницы оформления заказа
     */
    function user_register() {
        // Импортируем роутер личного кабинета для возможности регистрации со страницы оформления заказа
        if (!class_exists('PHPShopUsers'))
            PHPShopObj::importCore('users');
        if (class_exists('PHPShopUsers')) {
            $PHPShopUsers = new PHPShopUsers();
            $PHPShopUsers->action_add_user();
        }
    }

    /**
     * Экшен входа пользователя
     */
    function user_enter() {
        if ($this->autorization()) {

            // Запоминаем пользователя в cookie
            if (!empty($_POST['safe_users'])) {
                setcookie("UserLogin", trim($_POST['login']), time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);
                setcookie("UserPassword", trim($_POST['password']), time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);
                setcookie("UserChecked", 1, time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);
            } else {
                setcookie("UserLogin", "", time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);
                setcookie("UserPassword", "", time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);
                setcookie("UserChecked", "", time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);
            }

            // Редирект
            if (preg_match("/LogOut/", $_SERVER['REQUEST_URI']))
                $url_user = str_replace("?LogOut", "#userPage", $_SERVER['REQUEST_URI']);
            elseif (!empty($_GET['key']))
                $url_user = $this->getValue('dir.dir') . '/users/';
            else
                $url_user = $_SERVER['REQUEST_URI'];

            header("Location: " . $url_user);
            //$this->checkRedirect();
        } else
            $this->set('usersError', $this->lang('error_login'));
    }

    /**
     * если после авторизации, регистрации необходимо направить на страницу с которой пришли, перенаправляем
     */
    function checkRedirect() {
        // если после авторизации, регистрации необходимо направить на страницу с которой пришли, перенаправляем
        if ($_REQUEST['from'] AND $_REQUEST['fromSave'])
            header("Location: " . $_REQUEST['fromSave']);
    }

    /**
     * Форма авторизации пользователя
     */
    function usersDisp() {

        if (empty($_SESSION['UsersId']) && (!empty($_COOKIE['UserLogin']) && !empty($_COOKIE['UserPassword']))) {
            $PHPShopOrm = new PHPShopOrm($this->objBase);
            $PHPShopOrm->debug = $this->debug;

            $where = array('login' => '="' . trim($_COOKIE['UserLogin']) . '"', 'password' => '="' . $this->encode($_COOKIE['UserPassword']) . '"', 'enabled' => "='1'");

            // Мультибаза
            if ($this->PHPShopSystem->ifSerilizeParam("admoption.user_servers_control"))
                $where['servers'] = '=' . intval(HostID);

            $user = $PHPShopOrm->select(array('*'), $where, false, array('limit' => 1));
            if (is_array($user) AND PHPShopSecurity::true_num($user['id'])) {
                $this->authorize($user);
            }
        }

        if (!empty($_SESSION['UsersId']) and PHPShopSecurity::true_num($_SESSION['UsersId'])) {
            $this->set('UsersLogin', $_SESSION['UsersLogin']);
            $this->set('UsersName', $_SESSION['UsersName']);
            $dis = $this->parseTemplate($this->getValue('templates.users_forma_enter'));
        } else {

            // Блок авторизации, данные из cookie
            if (!empty($_COOKIE['UserChecked']) and PHPShopSecurity::true_num($_COOKIE['UserChecked']))
                $this->set('UserChecked', 'checked');

            if (!empty($_COOKIE['UserLogin']) and PHPShopSecurity::true_email($_COOKIE['UserLogin']))
                $this->set('UserLogin', $_COOKIE['UserLogin']);

            if (!empty($_COOKIE['UserPassword']) and PHPShopSecurity::true_passw($_COOKIE['UserPassword']))
                $this->set('UserPassword', $_COOKIE['UserPassword']);

            // Перехват модуля
            $this->setHook(__CLASS__, __FUNCTION__);

            $dis = $this->parseTemplate($this->getValue('templates.users_forma'));
        }
        return $dis;
    }

}

/**
 * Элемент каталоги страниц
 * @author PHPShop Software
 * @version 1.3
 * @package PHPShopElements
 */
class PHPShopPageCatalogElement extends PHPShopElements {

    /**
     * @var bool проверять на единичные каталоги
     */
    var $check_page = true;
    var $debug = false;

    /**
     * @var int количество для вывода последних записей 
     */
    var $limit_last = 2;

    /**
     * Конструктор
     */
    function __construct() {
        $this->template_debug = true;
        $this->objBase = $GLOBALS['SysValue']['base']['page_categories'];
        parent::__construct();
    }

    /**
     * Вывод навигации каталогов
     * @return string
     */
    function pageCatal() {
        $dis = null;
        $i = 0;

        $where = array('parent_to' => '=0');

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['parent_to'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $this->PHPShopOrm->cache = true;
        $data = $this->PHPShopOrm->select(array('*'), $where, array('order' => 'num,id desc'), array("limit" => 100));

        // Перехват модуля в начале
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $data, 'START');
        if ($hook)
            return $hook;

        if (is_array($data))
            foreach ($data as $row) {

                // Определяем переменные
                $this->set('catalogId', $row['id']);
                $this->set('catalogI', $i);
                $this->set('catalogTemplates', $this->getValue('dir.templates') . chr(47) . $this->PHPShopSystem->getValue('skin') . chr(47));

                // Если есть страницы
                if ($this->check($row['id'])) {

                    $this->set('catalogName', $row['name']);
                    $this->set('catalogId', $row['id']);
                    $this->set('catalogPodcatalog', null);

                    // Перехват модуля
                    $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');

                    $dis .= $this->parseTemplate($this->getValue('templates.catalog_page_forma_2'));
                } else {
                    $this->set('catalogPodcatalog', $this->subcatalog($row['id']));
                    $this->set('catalogName', $row['name']);

                    // Перехват модуля
                    $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');

                    $dis .= $this->parseTemplate($this->getValue('templates.catalog_page_forma'));
                }

                $i++;
            }
        return $dis;
    }

    /**
     * Проверка подкаталогов
     * @param Int $id ИД каталога
     * @return bool
     */
    function check($id) {
        $PHPShopOrm = new PHPShopOrm($this->getValue('base.page_categories'));
        $PHPShopOrm->debug = $this->debug;

        $where = array('parent_to' => "=$id");

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['parent_to'] .= ' and (servers ="" or servers REGEXP "i1000i")';


        $num = $PHPShopOrm->select(array('id'), $where, false, array('limit' => 1));
        if (empty($num['id']))
            return true;
    }

    /**
     * Проверка подкаталогов
     * @param Int $id ИД каталога
     * @return bool
     */
    function checkPages($id) {
        $PHPShopOrm = new PHPShopOrm($this->getValue('base.page'));
        $PHPShopOrm->debug = $this->debug;

        $where = array('category' => "=$id");

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['category'] .= ' and (servers ="" or servers REGEXP "i1000i")';


        $num = $PHPShopOrm->select(array('id'), $where, false, array('limit' => 1));
        if (empty($num['id']))
            return true;
    }

    /**
     * Вывод подкаталогов
     * @param Int $n ИД каталога
     * @return string
     */
    function subcatalog($n) {
        $dis = null;
        $i = 0;
        $n = PHPShopSecurity::TotalClean($n, 1);

        $where = array('parent_to' => '=' . $n);

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['parent_to'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $PHPShopOrm = new PHPShopOrm($this->getValue('base.page_categories'));
        $data = $PHPShopOrm->select(array('*'), $where, array('order' => 'num,id desc'), array("limit" => 100));

        // Перехват модуля в начале
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $data, 'START');
        if ($hook)
            return $hook;

        if (is_array($data))
            foreach ($data as $row) {

                // Определяем переменные
                $this->set('catalogId', $n);
                $this->set('catalogUid', $row['id']);
                $this->set('catalogI', $i);
                $this->set('catalogLink', 'CID_' . $row['id']);
                $this->set('catalogTemplates', $this->getValue('dir.templates') . chr(47) . $this->PHPShopSystem->getValue('skin') . chr(47));
                $this->set('catalogName', $row['name']);
                $i++;

                // Перехват модуля
                $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');

                // Подключаем шаблон
                $dis .= $this->parseTemplate($this->getValue('templates.podcatalog_page_forma'));
            }
        return $dis;
    }

    /**
     * Вывод меню каталогов страниц в главного навигационного меню
     * @return string
     */
    function topMenu() {
        $dis = $dis_page = null;

        // Перехват модуля
        $hook = $this->setHook(__CLASS__, __FUNCTION__, null, 'START');
        if ($hook)
            return $hook;

        $where['menu'] = "='1'";

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['menu'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $PHPShopOrm = new PHPShopOrm($this->objBase);
        $PHPShopOrm->debug = false;
        $data = $PHPShopOrm->select(array('id', 'name'), $where, array('order' => 'num,name'), array("limit" => 20));
        if (is_array($data))
            foreach ($data as $row) {

                $dis_page = null;

                // Определяем переменные
                $this->set('topMenuName', $row['name']);
                $this->set('topMenuLink', $row['id']);

                // Если есть страницы
                if (!$this->checkPages($row['id'])) {
                    $PHPShopOrm = new PHPShopOrm($this->getValue('base.page'));
                    $PHPShopOrm->debug = $this->debug;
                    $dataPage = $PHPShopOrm->select(array('link', 'name'), array('category' => '=' . $row['id'], 'enabled' => '="1"'), array('order' => 'num,name'), array("limit" => 100));
                    if (is_array($dataPage)) {
                        foreach ($dataPage as $rowPage) {
                            $dis_page .= PHPShopText::li($rowPage['name'], '/page/' . $rowPage['link'] . '.html', null);
                        }
                        $this->set('topMenuList', $dis_page);

                        // Перехват модуля
                        $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');

                        // Подключаем шаблон
                        $dis .= $this->parseTemplate($this->getValue('templates.page_top_menu'));
                    }
                } else
                    $dis .= str_replace('page/', 'page/CID_', $this->parseTemplate($this->getValue('templates.top_menu')));
            }

        // Перехват модуля
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $dis, 'END');
        if ($hook)
            return $hook;

        return $dis;
    }

    /**
     * Вывод последних записей из страниц
     * @return string
     */
    function getLastPages() {
        $dis = null;

        // Перехват модуля
        $hook = $this->setHook(__CLASS__, __FUNCTION__, false, 'START');
        if ($hook)
            return $hook;

        $where = array('enabled' => "='1'", 'preview' => '!=""');

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['preview'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page']);
        $PHPShopOrm->debug = $this->debug;
        $result = $PHPShopOrm->select(array('link', 'name', 'icon', 'datas', 'preview'), $where, array('order' => 'datas DESC'), array("limit" => $this->limit_last));

        // Проверка на еденичную запись
        if ($this->limit_last > 1)
            $data = $result;
        else
            $data[] = $result;

        if (is_array($data))
            foreach ($data as $row) {

                // Определяем переменные
                $this->set('pageLink', $row['link']);
                $this->set('pageName', $row['name']);
                $this->set('pageIcon', $row['icon']);
                $this->set('pageData', PHPShopDate::get($row['datas']));
                $this->set('pagePreview', Parser(stripslashes($row['preview'])));

                // Перехват модуля
                $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');

                // Подключаем шаблон
                $dis .= parseTemplateReturn($this->getValue('templates.page_mini'));
            }

        return $dis;
    }

}

/**
 * Элемент текстовые блоки
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopElements
 */
class PHPShopTextElement extends PHPShopElements {

    var $debug = false;

    /**
     * Конструктор
     */
    function __construct() {
        $this->objBase = $GLOBALS['SysValue']['base']['menu'];
        $this->template_debug = true;
        parent::__construct();
    }

    /**
     * Вывод текстового блока в левую часть
     * @return string
     */
    function leftMenu() {
        $dis = null;

        $where['flag'] = "='1'";
        $where['element'] = "='0'";

        // Мобильный
        $isMobile = PHPShopString::is_mobile();

        if ($isMobile) {
            $where['mobile'] = '="1"';
        } else
            $where['mobile'] = '="0"';

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['element'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        // Каталоги
        if (!empty($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'])) {

            // Корневой каталог
            $true_cid = $GLOBALS['PHPShopSeoPro']->getCID();

            // Товар
            if (empty($true_cid) and $this->PHPShopNav->getPath() == "id") {
                $product_id = $GLOBALS['PHPShopSeoPro']->getID();
                $PHPShopProduct = new PHPShopProduct((int) $product_id);
                $true_cid = $PHPShopProduct->getParam('category');
                PHPShopParser::set('productName', $PHPShopProduct->getParam('name'));
            }
            // Вложенный подкаталог
            else if (empty($true_cid) and $this->PHPShopNav->objNav['truepath'] != '/' and $this->PHPShopNav->notPath(array('page', 'news', 'gbook'))) {
                $GLOBALS['PHPShopSeoPro']->catArrayToMemory();
                $true_cid = $GLOBALS['PHPShopSeoPro']->getCID();
            }
        } else
            $true_cid = $this->PHPShopNav->getId();

        $data = $this->PHPShopOrm->select(array('*'), $where, array('order' => 'num'), array("limit" => 100));
        if (is_array($data))
            foreach ($data as $row) {
                if (empty($row['dir'])) {

                    // Привязка к каталогам
                    if (!empty($row['dop_cat'])) {

                        if (empty($true_cid)) {
                            continue;
                        } elseif (!empty($true_cid) and ! strstr($row['dop_cat'], "#" . $true_cid . "#")) {
                            continue;
                        }
                    }

                    // Определяем переменные
                    $this->set('leftMenuName', $row['name']);
                    $this->set('leftMenuContent', Parser($row['content']));

                    // Перехват модуля
                    $this->setHook(__CLASS__, __FUNCTION__, $row);

                    // Подключаем шаблон
                    $dis .= $this->parseTemplate($this->getValue('templates.left_menu'));
                } else {
                    $dirs = explode(",", $row['dir']);
                    foreach ($dirs as $dir)
                        if (@strpos($_SERVER['REQUEST_URI'], $dir) or $_SERVER['REQUEST_URI'] == $dir) {
                            $this->set('leftMenuName', $row['name']);
                            $this->set('leftMenuContent', Parser($row['content']));

                            // Перехват модуля
                            $this->setHook(__CLASS__, __FUNCTION__, $row);

                            // Подключаем шаблон
                            $dis .= $this->parseTemplate($this->getValue('templates.left_menu'));
                        }
                }
            }
        return $dis;
    }

    /**
     * Вывод текстового блока в правую часть
     * @return string
     */
    function rightMenu() {
        $dis = null;

        $where['flag'] = "='1'";
        $where['element'] = "='1'";

        // Мобильный
        $isMobile = PHPShopString::is_mobile();

        if ($isMobile) {
            $where['mobile'] = '="1"';
        } else
            $where['mobile'] = '="0"';

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['element'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        // Каталоги
        if (!empty($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'])) {

            // Корневой каталог
            $true_cid = $GLOBALS['PHPShopSeoPro']->getCID();

            // Товар
            if (empty($true_cid) and $this->PHPShopNav->getPath() == "id") {

                $product_id = $GLOBALS['PHPShopSeoPro']->getID();
                $PHPShopProduct = new PHPShopProduct((int) $product_id);
                $true_cid = $PHPShopProduct->getParam('category');
                PHPShopParser::set('productName', $PHPShopProduct->getParam('name'));
            }
            // Вложенный подкаталог
            else if (empty($true_cid) and $this->PHPShopNav->objNav['truepath'] != '/' and $this->PHPShopNav->notPath(array('page', 'news', 'gbook'))) {
                $GLOBALS['PHPShopSeoPro']->catArrayToMemory();
                $true_cid = $GLOBALS['PHPShopSeoPro']->getCID();
            }
        } else
            $true_cid = $this->PHPShopNav->getId();

        $PHPShopOrm = new PHPShopOrm($this->objBase);
        $data = $PHPShopOrm->select(array('*'), $where, array('order' => 'num'), array("limit" => 100));
        if (is_array($data))
            foreach ($data as $row) {
                if (empty($row['dir'])) {

                    // Привязка к каталогам
                    if (!empty($row['dop_cat'])) {

                        if (empty($true_cid)) {
                            continue;
                        } elseif (!empty($true_cid) and ! strstr($row['dop_cat'], "#" . $true_cid . "#")) {
                            continue;
                        }
                    }

                    // Определяем переменные
                    $this->set('leftMenuName', $row['name']);
                    $this->set('leftMenuContent', Parser($row['content']));

                    // Перехват модуля
                    $this->setHook(__CLASS__, __FUNCTION__, $row);

                    $dis .= $this->parseTemplate($this->getValue('templates.right_menu'));
                } else {
                    $dirs = explode(",", $row['dir']);
                    foreach ($dirs as $dir)
                        if (@strpos($_SERVER['REQUEST_URI'], $dir) or $_SERVER['REQUEST_URI'] == $dir) {
                            $this->set('leftMenuName', $row['name']);
                            $this->set('leftMenuContent', Parser($row['content']));

                            // Перехват модуля
                            $this->setHook(__CLASS__, __FUNCTION__, $row);

                            // Подключаем шаблон
                            $dis .= $this->parseTemplate($this->getValue('templates.right_menu'));
                        }
                }
            }
        return $dis;
    }

    /**
     * Вывод главного навигационного меню
     * @return string
     */
    function topMenu() {
        $dis = null;

        // Перехват модуля
        $hook = $this->setHook(__CLASS__, __FUNCTION__, null, 'START');
        if ($hook)
            return $hook;

        $where['category'] = "=1000";
        $where['enabled'] = "='1'";

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $objBase = $GLOBALS['SysValue']['base']['page'];
        $PHPShopOrm = new PHPShopOrm($objBase);
        $PHPShopOrm->debug = $this->debug;
        $data = $PHPShopOrm->select(array('*'), $where, array('order' => 'num'), array("limit" => 20));
        if (is_array($data))
            foreach ($data as $row) {

                // Определяем переменные
                $this->set('topMenuName', $row['name']);
                $this->set('topMenuLink', $row['link']);

                // Активная страница
                if ($row['link'] == $this->PHPShopNav->getName(true))
                    $this->set('topMenuActive', 'active');
                else
                    $this->set('topMenuActive', '');

                // Перехват модуля
                $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');

                // Подключаем шаблон
                $dis .= $this->parseTemplate($this->getValue('templates.top_menu'));
            }

        return $dis;
    }

    /**
     * Вывод нижнего навигационного меню
     * @return string
     */
    function bottomMenu() {
        $dis = null;

        // Перехват модуля
        $hook = $this->setHook(__CLASS__, __FUNCTION__, null, 'START');
        if ($hook)
            return $hook;

        $where['enabled'] = "='1'";
        $where['footer'] = "='1'";

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $objBase = $GLOBALS['SysValue']['base']['page'];
        $PHPShopOrm = new PHPShopOrm($objBase);
        $PHPShopOrm->debug = $this->debug;
        $data = $PHPShopOrm->select(array('name', 'link'), $where, array('order' => 'num'), array("limit" => 20));
        if (is_array($data))
            foreach ($data as $row) {

                // Определяем переменные
                $this->set('topMenuName', $row['name']);
                $this->set('topMenuLink', $row['link']);

                // Активная страница
                if ($row['link'] == $this->PHPShopNav->getName(true))
                    $this->set('topMenuActive', 'active');
                else
                    $this->set('topMenuActive', '');

                // Перехват модуля
                $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');

                // Подключаем шаблон
                if (PHPShopParser::checkFile($this->getValue('templates.bottom_menu')))
                    $dis .= $this->parseTemplate($this->getValue('templates.bottom_menu'));
                else
                    $dis .= $this->parseTemplate($this->getValue('templates.top_menu'));
            }

        return $dis;
    }

}

/**
 * Элемент смена шаблонов
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopElements
 */
class PHPShopSkinElement extends PHPShopElements {

    function __construct() {
        parent::__construct();

        // Экшены
        $this->setAction(array('post' => 'skin', 'get' => 'skin'));
    }

    /**
     * Экшен по умолчанию, вывод формы выбора шаблона
     * @return string
     */
    function index() {
        if ($this->PHPShopSystem->getSerilizeParam("admoption.user_skin") == 1) {
            $dir = $this->getValue('dir.templates') . chr(47);
            if (is_dir($dir)) {
                if (@$dh = opendir($dir)) {
                    while (($file = readdir($dh)) !== false) {
                        if (@file_exists($dir . '/' . $file . "/main/index.tpl")) {
                            if ($_SESSION['skin'] == $file)
                                $sel = "selected";
                            else
                                $sel = "";

                            if ($file != "." and $file != ".." and $file != "index.html") {


                                $value[] = array($file, $file, $sel);
                            }
                        }
                    }
                    closedir($dh);
                }
            }

            if (is_array($value))
                sort($value);

            // Определяем переменные
            $forma = PHPShopText::div(PHPShopText::form(PHPShopText::select('skin', $value, '100%', $float = "none", $caption = false, $onchange = "ChangeSkin()"), 'SkinForm', 'get'), 'left', 'padding:10px');
            $this->set('leftMenuContent', $forma);
            $this->set('leftMenuName', __("Сменить дизайн"));

            // Подключаем шаблон
            $dis = $this->parseTemplate($this->getValue('templates.right_menu'));
        }
        return $dis;
    }

    /**
     * Экшен смены шаблона
     */
    function skin() {
        if ($this->PHPShopSystem->getValue('num_vitrina')) {
            if (@file_exists("phpshop/templates/" . $_REQUEST['skin'] . "/main/index.tpl")) {
                $skin = $_REQUEST['skin'];
                if (PHPShopSecurity::true_skin($skin)) {
                    unset($_SESSION['Memory']);
                    unset($_SESSION['gridChange']);
                    $_SESSION['skin'] = $skin;

                    // Сжатие статики
                    if ($this->PHPShopSystem->ifSerilizeParam('admoption.min')) {
                        $this->set('pathTemplateMin', '/files/min.php?f=' . $this->getValue('dir.templates') . chr(47) . $_SESSION['skin']);
                        $this->set('pathMin', '/files/min.php?f=');
                    } else {
                        $this->set('pathTemplateMin', $this->getValue('dir.templates') . chr(47) . $_SESSION['skin']);
                        $this->set('pathMin', null);
                    }
                }
            }
        }
    }

}

/**
 * Элемент последние отзывы
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopElements
 */
class PHPShopGbookElement extends PHPShopElements {

    /**
     * @var bool  показывать только на главной
     */
    var $disp_only_index = true;

    /**
     * @var Int Кол-во отзывов
     */
    var $limit = 10;

    /**
     * Конструктор
     */
    function __construct() {

        // Отладка
        $this->debug = false;

        // Имя Бд
        $this->objBase = $GLOBALS['SysValue']['base']['gbook'];
        parent::__construct();
    }

    /**
     * Вывод последних отзывов
     * @return string
     */
    function index() {
        global $PHPShopModules;
        $dis = null;

        // Выполнение только на главной странице
        if ($this->disp_only_index) {
            if ($this->PHPShopNav->index())
                $view = true;
            else
                $view = false;
        } else
            $view = true;

        if ($view) {

            $where['flag'] = "='1'";

            // Мультибаза
            if (defined("HostID"))
                $where['servers'] = " REGEXP 'i" . HostID . "i'";
            elseif (defined("HostMain"))
                $where['flag'] .= ' and (servers ="" or servers REGEXP "i1000i")';

            $data = $this->PHPShopOrm->select(array('*'), $where, array('order' => 'id DESC'), array("limit" => $this->limit));
            if (is_array($data))
                foreach ($data as $row) {

                    // Ссылка на автора
                    if (!empty($row['mail']))
                        $d_mail = PHPShopText::a('mailto:' . $row['mail'], PHPShopText::b($row['name']), $row['name']);
                    else
                        $d_mail = PHPShopText::b($row['name']);

                    // Определяем переменые
                    $this->set('gbookData', PHPShopDate::dataV($row['datas'], false, true));
                    $this->set('gbookName', $row['name']);
                    $this->set('gbookTema', $row['tema']);
                    $this->set('gbookMail', $d_mail);
                    $this->set('gbookOtsiv', $row['otsiv']);
                    $this->set('gbookOtvet', $row['otvet']);
                    $this->set('gbookId', $row['id']);

                    // Перехват модуля
                    $PHPShopModules->setHookHandler(__CLASS__, __FUNCTION__, $this, $row);

                    // Подключаем шаблон
                    $dis .= $this->parseTemplate($this->getValue('templates.gbook_main_mini'));
                }

            return $dis;
        }
    }

}

/**
 * Элемент последние новости
 * @author PHPShop Software
 * @version 1.2
 * @package PHPShopElements
 */
class PHPShopNewsElement extends PHPShopElements {

    /**
     * @var bool Показывать новости только на главной
     */
    var $disp_only_index = true;

    /**
     * @var int  Кол-во новостей
     */
    var $limit = 3;

    /**
     * Конструктор
     */
    function __construct() {
        $this->debug = false;
        $this->template_debug = true;
        $this->objBase = $GLOBALS['SysValue']['base']['news'];
        parent::__construct();
    }

    /**
     * Вывод последних новостей
     * @return string
     */
    function index() {
        $dis = null;

        // Перехват модуля
        $hook = $this->setHook(__CLASS__, __FUNCTION__, false, 'START');
        if ($hook)
            return $hook;

        // Выполнение только на главной странице
        if ($this->disp_only_index) {
            if ($this->PHPShopNav->index())
                $view = true;
            else
                $view = false;
        } else
            $view = true;

        $where['datau'] = '<' . time();

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['datau'] .= ' and (servers ="" or servers REGEXP "i1000i")';


        if (!empty($view)) {

            $result = $this->PHPShopOrm->select(array('*'), $where, array('order' => 'datau DESC'), array("limit" => $this->limit));

            // Проверка на еденичную запись
            if ($this->limit > 1)
                $data = $result;
            else
                $data[] = $result;

            if (is_array($data))
                foreach ($data as $row) {

                    // Определяем переменные
                    $this->set('newsId', $row['id']);
                    $this->set('newsZag', $row['zag']);
                    $this->set('newsData', $row['datas']);
                    $this->set('newsKratko', $row['kratko']);
                    $this->set('newsIcon', $this->setImage($row['icon']));

                    // Перехват модуля
                    $hook = $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');
                    if ($hook)
                        $dis .= $hook;
                    else
                        $dis .= $this->parseTemplate($this->getValue('templates.news_main_mini'));
                }
            return $dis;
        }
    }

}

/**
 * Элемент вывода изображений в слайдер
 * @author PHPShop Software
 * @version 1.2
 * @package PHPShopElements
 */
class PHPShopSliderElement extends PHPShopElements {

    /**
     * @var bool Показывать слайдер только на главной
     */
    var $disp_only_index = true;
    var $template_debug = false;
    var $debug = false;

    /**
     * @var int  Кол-во изображений
     */
    var $limit = 25;

    /**
     * Конструктор
     */
    function __construct() {
        $this->objBase = $GLOBALS['SysValue']['base']['slider'];
        parent::__construct();
    }

    /**
     * Вывод изображений в слайдер
     * @return string
     */
    function index() {
        $dis = null;

        // Перехват модуля
        $this->setHook(__CLASS__, __FUNCTION__, false, 'START');

        // Выполнение только на главной странице
        $view = true;
        if ($this->disp_only_index && $this->PHPShopNav->index() === false) {
            $view = false;
        }

        // Мобильный
        $isMobile = PHPShopString::is_mobile();

        $where = [
            'enabled' => '="1"',
        ];

        if ($isMobile) {
            $where['mobile'] = '="1"';
        } else
            $where['mobile'] = '="0"';

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        if (!empty($view)) {
            $result = $this->PHPShopOrm->select(array('*'), $where, array('order' => 'num, id DESC'), array("limit" => $this->limit));

            // Проверка на еденичную запись
            if ($this->limit > 1)
                $data = $result;
            else
                $data[] = $result;

            if (is_array($data))
                foreach ($data as $row) {

                    // Определяем переменные
                    $this->set('image', $this->setImage($row['image']));
                    $this->set('alt', $row['alt']);
                    $this->set('link', $row['link']);
                    $this->set('sliderID', $row['id']);
                    $this->set('sliderName', $row['name']);
                    $this->set('sliderLinkName', $row['link_text']);
                    $this->set('sliderColor', (int) $row['color']);

                    // Перехват модуля
                    $this->setHook(__CLASS__, __FUNCTION__, $row, 'END');

                    // Подключаем шаблон
                    $dis .= $this->parseTemplate("/slider/slider_oneImg.tpl");
                }
            if ($dis) {
                $this->set('imageSliderContent', $dis);
                return $this->parseTemplate("/slider/slider_main.tpl");
            }
            return false;
        }
    }

}

/**
 * Элемент баннер
 * @author PHPShop Software
 * @version 2.4
 * @package PHPShopElements
 */
class PHPShopBannerElement extends PHPShopElements {

    var $popup;
    var $horizontal;
    var $vertical;
    var $menu;

    function __construct() {
        $this->debug = false;
        $this->template_debug = true;
        $this->objBase = $GLOBALS['SysValue']['base']['banner'];
        parent::__construct();
        $this->index();
    }

    function template($row) {

        // Размер
        $size_value = array('modal-sm', '', 'modal-lg');

        // Определяем переменные
        $this->set('banerTitle', $row['name']);
        $this->set('banerContent', $row['content']);
        $this->set('banerDescription', $row['description']);
        $this->set('banerImage', $this->setImage($row['image']));
        $this->set('banerLink', $row['link']);
        $this->set('banerColor', (int) $row['color']);
        $this->set('popupSize', $size_value[$row['size']]);
        $this->set('popupId', $row['id']);

        switch ($row['type']) {

            // Баннер в колонке
            case 0:

                $this->vertical = $this->parseTemplate($this->getValue('templates.baner_list_forma'));
                break;

            // PopUp
            case 1:

                // Первый заход на сайт
                if ($row['display'] == 1) {

                    if (empty($_COOKIE['popup' . $row['id'] . '_close'])) {
                        if (PHPShopParser::checkFile($this->getValue('templates.banner_window_forma'))) {
                            $this->popup = $this->parseTemplate($this->getValue('templates.banner_window_forma'));
                        } else {
                            $this->popup = $this->parseTemplate('phpshop/lib/templates/banner/banner_window_forma.tpl', true);
                        }
                    }
                } else {

                    if (PHPShopParser::checkFile($this->getValue('templates.banner_window_forma'))) {
                        $this->popup = $this->parseTemplate($this->getValue('templates.banner_window_forma'));
                    } else {
                        $this->popup = $this->parseTemplate('phpshop/lib/templates/banner/banner_window_forma.tpl', true);
                    }
                }
                break;

            // Баннер под шапкой
            case 2:

                $this->horizontal = $this->parseTemplate($this->getValue('templates.banner_horizontal_forma'));
                break;

            // Баннер в меню
            case 3:

                $this->menu = $this->parseTemplate($this->getValue('templates.banner_menu_forma'));
                break;
        }
    }

    /**
     * Выборка данных
     * @return string
     */
    function index() {

        $where['flag'] = "='1'";

        // Мобильный
        $isMobile = PHPShopString::is_mobile();

        if ($isMobile) {
            $where['mobile'] = '="1"';
        } else
            $where['mobile'] = '="0"';

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['flag'] .= ' and (servers ="" or servers REGEXP "i1000i")';

// Каталоги
        if (!empty($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'])) {

            // Корневой каталог
            $true_cid = $GLOBALS['PHPShopSeoPro']->getCID();

            // Товар
            if (empty($true_cid) and $this->PHPShopNav->getPath() == "id") {

                $product_id = $GLOBALS['PHPShopSeoPro']->getID();
                $PHPShopProduct = new PHPShopProduct((int) $product_id);
                $true_cid = $PHPShopProduct->getParam('category');
            }
            // Вложенный подкаталог
            else if (empty($true_cid) and $this->PHPShopNav->objNav['truepath'] != '/' and $this->PHPShopNav->notPath(array('page', 'news', 'gbook'))) {
                $GLOBALS['PHPShopSeoPro']->catArrayToMemory();
                $true_cid = $GLOBALS['PHPShopSeoPro']->getCID();
            }
        } else
            $true_cid = $this->PHPShopNav->getId();

        $this->PHPShopOrm->debug = false;
        $data = $this->PHPShopOrm->select(array('*'), $where, array('order' => 'RAND()'), array("limit" => 100));

        if (is_array($data))
            foreach ($data as $row) {
                if (empty($row['dir'])) {

                    // Привязка к каталогам
                    if (!empty($true_cid) and ! empty($row['dop_cat']) and ! strstr($row['dop_cat'], "#" . $true_cid . "#")) {
                        continue;
                    } elseif (!empty($row['dop_cat']) and empty($true_cid)) {
                        continue;
                    }

                    // Шаблон
                    $this->template($row);
                } else {

                    $dirs = explode(",", $row['dir']);
                    if (!is_array($dirs))
                        $dirs[] = $row['dir'];

                    // Таргетинг
                    foreach ($dirs as $dir) {
                        if (!empty($dir))
                            if ($this->PHPShopNav->objNav['truepath'] == trim($dir) or ! empty($true_cid)) {

                                if (!empty($true_cid) and empty($row['dop_cat']))
                                    continue;

                                // Шаблон
                                $this->template($row);
                            }
                    }


                    if (!empty($row['dop_cat']) and empty($true_cid))
                        continue;
                }
            }
    }

    /**
     * Вывод popup
     * @return string
     */
    function getPopup() {
        echo $this->popup;
    }

    /**
     * Вывод горизонтального баннера
     * @return string
     */
    function banersDispHorizontal() {
        return $this->horizontal;
    }

    /**
     * Вывод баннера в меню
     * @return string
     */
    function banersDispMenu() {
        return $this->menu;
    }

    /**
     * Вывод баннера в колонке
     * @return string
     */
    function banersDisp() {
        return $this->vertical;
    }

}

/**
 * Элемент фото галерея
 * @author PHPShop Software
 * @version 1.4
 * @package PHPShopElements
 */
class PHPShopPhotoElement extends PHPShopElements {

    /**
     * Конструктор
     */
    function __construct() {

        // Отладка
        $this->debug = false;

        // Имя Бд
        $this->objBase = $GLOBALS['SysValue']['base']['photo_categories'];
        parent::__construct();
    }

    /**
     * Вывод фото по таргетингу
     * @return string
     */
    function getPhotos() {
        $dis = null;
        $url = addslashes(substr($this->SysValue['nav']['url'], 1));
        if (empty($url))
            $url = '/';

        $PHPShopOrm = new PHPShopOrm($this->getValue('base.photo_categories'));
        $PHPShopOrm->debug = $this->debug;
        $data = $PHPShopOrm->select(array('*'), array('enabled' => "='1'", "page" => " LIKE '%$url'"), array('order' => 'num'), array("limit" => 1000));

        if (is_array($data))
            foreach ($data as $row) {
                $this->set('photoTitle', $row['name']);
                $this->set('photoLink', $row['id']);
                $this->set('photoContent', $this->ListPhoto($row['id'], $row['count']));


                if (PHPShopParser::checkFile('photo/photo_list_forma.tpl'))
                    $dis .= ParseTemplateReturn('photo/photo_list_forma.tpl');
                else
                    $dis .= $this->parseTemplate('./phpshop/lib/templates/photo/photo_list_forma.tpl', true);
            }
        return $dis;
    }

    /**
     * Вывод фото
     * @param int $cat ИД категории фото
     * @param int $num кол-во фото для вывода
     * @return string
     */
    function ListPhoto($cat, $num) {
        $dis = null;

        // Выборка данных
        $PHPShopOrm = new PHPShopOrm($this->getValue('base.photo'));
        $PHPShopOrm->debug = $this->debug;
        $data = $PHPShopOrm->select(array('*'), array('category' => '=' . intval($cat), 'enabled' => "='1'"), array('order' => 'num'), array('limit' => $num));
        if ($num == 1)
            $dataArray[] = $data;
        else
            $dataArray = $data;

        if (is_array($dataArray))
            foreach ($dataArray as $row) {

                // Видео
                if (in_array(pathinfo($row['name'], PATHINFO_EXTENSION), array('mp4', 'mov'))) {
                    $this->set('photoVideo', $row['name']);
                    $this->set('photoInfo', $row['info']);

                    if (PHPShopParser::checkFile('photo/photo_element_preview_video.tpl'))
                        $dis .= ParseTemplateReturn('photo/photo_element_preview_video.tpl');
                    else
                        $dis .= $this->parseTemplate('./phpshop/lib/templates/photo/photo_element_preview_video.tpl', true);
                }
                // Изображение
                else {
                    $name_s = str_replace(".", "s.", $row['name']);
                    $this->set('photoIcon', $name_s);
                    $this->set('photoInfo', $row['info']);
                    $this->set('photoImg', $row['name']);

                    if (PHPShopParser::checkFile('photo/photo_element_preview.tpl'))
                        $dis .= ParseTemplateReturn('photo/photo_element_preview.tpl');
                    else
                        $dis .= $this->parseTemplate('./phpshop/lib/templates/photo/photo_element_preview.tpl', true);
                }
            }
        return $dis;
    }

}

/**
 * Элемент защитной картинки Captcha
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopElements
 */
class PHPShopRecaptchaElement extends PHPShopElements {

    // Общие ключи Recaptcha 
    public $secret = 'empty';
    public $public = 'empty';
    protected $api = 'https://www.google.com/recaptcha/api/siteverify';
    // Общие ключи Hcaptcha         
    public $hsecret = 'empty';
    public $hpublic = 'empty';
    protected $hapi = 'https://hcaptcha.com/siteverify';
    // Общие ключи SmartCaptcha         
    public $smartsecret = 'empty';
    public $smartpublic = 'empty';
    protected $smartapi = 'https://smartcaptcha.cloud.yandex.ru/validate';

    public function __construct() {

        parent::__construct();

        // Smartcaptcha
        if ($this->PHPShopSystem->ifSerilizeParam('admoption.smartcaptcha_enabled')) {

            $public = $this->PHPShopSystem->getSerilizeParam('admoption.smartcaptcha_pkey');
            if (!empty($public))
                $this->public = $public;
            else
                $this->public = $this->smartpublic;

            $secret = $this->PHPShopSystem->getSerilizeParam('admoption.smartcaptcha_skey');
            if (!empty($secret))
                $this->secret = $secret;
            else
                $this->secret = $this->smartsecret;

            if (isset($_POST['smart-token']))
                $this->check = $_POST['smart-token'];

            $this->api = $this->smartapi;
        }
        // Recaptcha
        elseif ($this->PHPShopSystem->ifSerilizeParam('admoption.recaptcha_enabled')) {

            $public = $this->PHPShopSystem->getSerilizeParam('admoption.recaptcha_pkey');
            if (!empty($public))
                $this->public = $public;

            $secret = $this->PHPShopSystem->getSerilizeParam('admoption.recaptcha_skey');

            if (!empty($secret))
                $this->secret = $secret;

            if (isset($_POST['g-recaptcha-response']))
                $this->check = $_POST['g-recaptcha-response'];
        }
        // Hcaptcha
        elseif ($this->PHPShopSystem->ifSerilizeParam('admoption.hcaptcha_enabled')) {

            $public = $this->PHPShopSystem->getSerilizeParam('admoption.hcaptcha_pkey');
            if (!empty($public))
                $this->public = $public;
            else
                $this->public = $this->hpublic;

            $secret = $this->PHPShopSystem->getSerilizeParam('admoption.hcaptcha_skey');

            if (!empty($secret))
                $this->secret = $secret;
            else
                $this->secret = $this->hsecret;

            if (isset($_POST['h-captcha-response']))
                $this->check = $_POST['h-captcha-response'];
            $this->api = $this->hapi;
        }
    }

    /**
     * Проверка правильности заполнения каптчи Recaptcha, Hcaptcha
     * @return boolean
     */
    public function check() {
        if (!empty($this->check)) {

            // Smartcaptcha
            if ($this->PHPShopSystem->ifSerilizeParam('admoption.smartcaptcha_enabled')) {
                $res = $this->request_smart();

                if ($res->status == 'ok' and $res->host == $_SERVER['SERVER_NAME'])
                    return true;
            }
            // Recaptcha, Hcaptcha
            else {
                $res = $this->request();

                if (!empty($res['success']))
                    return true;
            }
        }
    }

    /**
     * Проверка правильности заполнения каптчи Smart Captcha
     * @return boolean
     */
    protected function request_smart() {
        $ch = curl_init($this->api);
        $args = [
            "secret" => $this->secret,
            "token" => $this->check,
            "ip" => $_SERVER['REMOTE_ADDR']
        ];
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
    }

    /**
     * Проверка ботов
     * @param array $option параметры проверки [url|captcha|referer]
     * @return boolean
     */
    function security($option = array('url' => false, 'captcha' => true, 'referer' => true)) {

        // Проверка вхождения ссылок
        if (!empty($option['url'])) {
            preg_match_all('/http:?/', $_POST[$option['url']], $url, PREG_SET_ORDER);
            if (count($url) > 0)
                return false;
        }

        // Проверка Referer
        if (!empty($option['referer'])) {
            if (!strpos($_SERVER["HTTP_REFERER"], $_SERVER['SERVER_NAME']))
                return false;
        }

        // Проверка каптчи
        if ($option['captcha'] === true) {

            // Каптча включена
            if (!$this->PHPShopSystem->ifSerilizeParam('admoption.user_captcha_enabled')) {

                // Recaptcha
                if ($this->true()) {
                    $result = $this->check();
                    return $result;
                }

                // Обычная каптча
                elseif (!empty($_SESSION['text']) and strtoupper($_POST['key']) == strtoupper($_SESSION['text'])) {
                    return true;
                } else
                    return false;
            } else
                return true;
        }

        return true;
    }

    /**
     * Проверка каптчи на сервере
     * @return array
     */
    protected function request() {


        $rout = "?secret=" . $this->secret . "&response=" . $this->check;

        $data_string = $rout;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api . $rout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string)
        ));

        $output = curl_exec($ch);
        curl_close($ch);

        $response = explode("\r\n\r\n", $output);
        $responsecontent = $response[1];


        return json_decode($responsecontent, true);
    }

    /**
     * Вывод защитной картинки captcha
     * @param string $name ИД каптчи
     * @param string $size размер каптчи [normal|compact]   
     * @return string
     */
    public function captcha($name = 'default', $size = 'normal') {

        // Каптча включена
        if (!$this->PHPShopSystem->ifSerilizeParam('admoption.user_captcha_enabled')) {

            if ($this->PHPShopSystem->ifSerilizeParam('admoption.smartcaptcha_enabled')) {
                $dis = '<div id="smartcaptcha_' . $name . '" class="smart-captcha" data-sitekey="' . $this->public . '"></div>';
                $this->recaptcha = true;
            } elseif ($this->PHPShopSystem->ifSerilizeParam('admoption.recaptcha_enabled')) {
                $dis = '<div id="recaptcha_' . $name . '" data-size="' . $size . '" data-key="' . $this->public . '"></div>';
                $this->recaptcha = true;
            } else if ($this->PHPShopSystem->ifSerilizeParam('admoption.hcaptcha_enabled')) {
                $dis = '<div id="hcaptcha_' . $name . '" data-size="' . $size . '" data-key="' . $this->public . '"></div>';
                $this->recaptcha = true;
            } else {
                $dis = '<div><img src="phpshop/lib/captcha/captcha.php" align="left" style="margin-right:10px" alt=""> <input type="text" name="key" class="form-control" placeholder="' . __('Код с картинки') . '" style="width:150px" required=""></div>';
                $this->recaptcha = false;
            }
        } else
            $dis = null;

        return $dis;
    }

    /**
     * Используется рекаптча
     * @return boolen
     */
    public function true(){
    return $this->recaptcha;










    }

}
