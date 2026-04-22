<?php

class PHPShopPartner extends PHPShopCore {

    /**
     * Конструктор
     */
    function __construct() {

        // Имя Бд
        $this->objBase = $GLOBALS['SysValue']['base']['partner']['partner_users'];
        $this->debug = false;

        // Список экшенов
        $this->action = array("nav" => array("register", "sendpassword"),
            'post' => array("add_user", "update_user", "exit_user", "enter_user", "send_user", "addmoney_user", "key_id", "key_add"));

        $this->system();
        parent::__construct();

        // Навигация хлебные крошки
        $this->navigation(null, 'Партнерская программа');
    }

    /**
     * Настройка
     */
    function system() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['partner']['partner_system']);
        $this->data = $PHPShopOrm->select();
    }

    function addmoney_user() {

        if (PHPShopSecurity::true_num($_SESSION['partnerId']) and PHPShopSecurity::true_num($_POST['get_money_new'])) {

            // Проверка валидности суммы на счете партнера
            if ($_SESSION['partnerTotal'] < $_POST['get_money_new']) {
                $notice = PHPShopText::notice($GLOBALS['SysValue']['lang']['partner_notice'], $this->icon);
            } else {
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['partner']['partner_payment']);
                $PHPShopOrm->debug = false;
                $PHPShopOrm->insert(array('date_new' => time(), 'sum_new' => $_POST['get_money_new'], 'partner_id_new' => $_SESSION['partnerId']));
                $notice = PHPShopText::message($GLOBALS['SysValue']['lang']['partner_money_done'], $this->icon);
                PHPShopObj::loadClass("mail");

                // Сообщение администратору о заявке на вывод
                new PHPShopMail($this->PHPShopSystem->getValue('adminmail2'), $this->PHPShopSystem->getValue('adminmail2'), $this->PHPShopSystem->getValue('name') . ' - заявка на вывод средств от ' . $_SESSION['partnerName'], $GLOBALS['SysValue']['lang']['partner_money_mail'] . ' ' . $_POST['get_money_new'] . ' ' . $this->PHPShopSystem->getDefaultValutaCode(), false, false, array('replyto' => $_SESSION['partnerMail']));
            }
            unset($_POST['get_money_new']);
            $this->index($notice);
        } else
            header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    /**
     * Расчет бонуса от суммы заказа
     * @param array $order
     * @return float
     */
    function getSum($order, $percent, $enabled) {
        $sum = 0;
        $order = unserialize($order);
        if (is_array($order['Cart']['cart']))
            foreach ($order['Cart']['cart'] as $val)
                $sum += $val['num'] * $val['price'];
        $format = $this->PHPShopSystem->getSerilizeParam("admoption.price_znak");

        if (empty($enabled))
            $sum = 0;
        else
            $sum = $sum * $percent / 100;

        return number_format($sum, $format, '.', '');
    }

    /**
     * Экшен по умолчанию, личный кабинет
     */
    function index() {

        if (PHPShopSecurity::true_num($_SESSION['partnerId'])) {

            // Библиотека графики
            PHPShopObj::loadClass("admgui");
            $PHPShopInterface = new PHPShopFrontInterface();
            $PHPShopInterface->checkbox_action = false;

            /**
             * Заявки на вывод
             */
            $PHPShopInterface->setCaption(array("Дата", "20%"), array("Сумма (" . $this->PHPShopSystem->getDefaultValutaCode() . ')', "30%"), array('Статус', '20%'));
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['partner']['partner_payment']);
            $PHPShopOrm->debug = false;
            $data = $PHPShopOrm->select(array('*'), array('partner_id' => "='" . $_SESSION['partnerId'] . "'"), array('order' => 'id desc'), array('limit' => 300));
            $stop_money = false;
            if (is_array($data))
                foreach ($data as $row) {
                    $sum = number_format($row['sum'], "2", ".", "");

                    // Дата выполнения
                    if (!empty($row['enabled']))
                        $row['date'] = $row['date_done'];

                    if (!empty($row['enabled']))
                        $row['enabled'] = __('Выполнен');
                    else {
                        $row['enabled'] = __('В обработке');
                        $stop_money = "Заявка на выплату ".$sum." ".$this->PHPShopSystem->getDefaultValutaCode()." успешно заказана ".PHPShopDate::dataV($row['date']);
                    }

                    $PHPShopInterface->setRow(PHPShopDate::dataV($row['date']), $sum, $row['enabled']);
                }
            $Tab1 = $PHPShopInterface->frontCompile();

            // Форма заявки
            if (!$stop_money) {
                $Tab2 = $PHPShopInterface->setInputText('Сумма', 'get_money_new', $_SESSION['partnerTotal'], 200, $this->PHPShopSystem->getDefaultValutaCode());
                $Tab2 .= $PHPShopInterface->setLine('<br>');
                $Tab2 .= $PHPShopInterface->setInput('submit', 'addmoney_user', 'Подать заявку');
                $Tab2 = $PHPShopInterface->frontSetForm($Tab2);
            }
            else {
                $Tab2 = $PHPShopInterface->setInfo($stop_money);
            }


            /**
             * Заказы рефералов
             */
            $PHPShopTableOrders = new PHPShopFrontInterface();
            $PHPShopTableOrders->checkbox_action = false;
            $PHPShopTableOrders->setCaption(array("№ Заказа", '20%'), array("Дата", "20%"), array("Сумма (" . $this->PHPShopSystem->getDefaultValutaCode() . ')', "20%"), array("Бонус (" . $this->PHPShopSystem->getDefaultValutaCode() . ')', "20%"), array("Статус", "20%"));

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['partner']['partner_log']);
            $PHPShopOrm->debug = false;
            $result = $PHPShopOrm->query('SELECT a.*, b.orders FROM ' . $GLOBALS['SysValue']['base']['partner']['partner_log'] . ' AS a
            JOIN ' . $GLOBALS['SysValue']['base']['orders'] . ' AS b ON a.order_id = b.id where a.partner_id=' . $_SESSION['partnerId'] . ' order by a.id desc limit 0,100');
            while ($row = mysqli_fetch_array($result)) {

                if (!empty($row['enabled']))
                    $row['enabled'] = __('Выполнен');
                else
                    $row['enabled'] = __('В обработке');

                $PHPShopTableOrders->setRow($row['order_uid'], PHPShopDate::dataV($row['date']), $row['sum'], $this->getSum($row['orders'], $row['percent'], $row['enabled']), array('name' => $row['enabled']));
            }
            $Tab3 = $PHPShopTableOrders->frontCompile('table table-striped');


            /**
             * Настройки пользователя
             */
            $PHPShopOrm = new PHPShopOrm($this->objBase);
            $row = $PHPShopOrm->select(array('*'), array('id' => "='" . $_SESSION['partnerId'] . "'", 'enabled' => "='1'"));
            if (is_array($row)) {

                // Данные пользователя
                $this->set('userName', $row['name']);
                $this->set('userMail', $row['login']);
                $this->set('userPassword', base64_decode($row['password']));

                if ($row['money'] > 0)
                    $this->set('userMoney', PHPShopText::a('javascript:tabPane.setSelectedIndex(3);', $row['money'] . ' ' .
                                    $this->PHPShopSystem->getDefaultValutaCode(), 'Вывести средства', 'green', $size = 17));
                else
                    $this->set('userMoney', PHPShopText::b('0 ') . $this->PHPShopSystem->getDefaultValutaCode());

                $_SESSION['partnerTotal'] = $row['money'];
                $_SESSION['partnerMail'] = $row['mail'];
                $this->set('partnerId', $_SESSION['partnerId']);
                $this->set('userContent', $row['content']);
                $Tab4 = ParseTemplateReturn($GLOBALS['SysValue']['templates']['partner']['partner_forma_enter'], true);
            }

            /*
             * Рейтинг
             */
            $PHPShopOrm = new PHPShopOrm();

            // Интрвал
            $stat_start = time() - 60 * 60 * 24 * $this->data['stat_day'];

            $PHPShopOrm->sql = "SELECT a.partner_id, sum(a.sum) as total, count(a.id) as num, a.date, b.login, b.name FROM " . $GLOBALS['SysValue']['base']['partner']['partner_log'] . " AS a JOIN " . $GLOBALS['SysValue']['base']['partner']['partner_users'] . " AS b ON a.partner_id = b.id where a.date > " . $stat_start . " and a.enabled = '1' group by a.partner_id order by total desc limit 10";
            $data = $PHPShopOrm->select();

            $PHPShopTableRating = new PHPShopFrontInterface();
            $PHPShopTableRating->checkbox_action = false;
            $PHPShopTableRating->setCaption(array("Партнер", '20%'), array("Продаж", "20%"), array("Сумма (" . $this->PHPShopSystem->getDefaultValutaCode() . ')', "20%"));

            $stat = 0;
            if (is_array($data))
                foreach ($data as $row) {

                    if (empty($row['num']))
                        continue;

                    if (!empty($row['name']))
                        $name = $row['name'];
                    else {
                        $name = '*****' . substr($row['login'], 5, strlen($row['login']));
                    }

                    $PHPShopTableRating->setRow($name, $row['num'], $row['total']);
                    $stat++;
                }

            // Форма закладок навигации
            $PHPShopFrontGUI = new PHPShopFrontInterface();
            $TabName = explode("|", $GLOBALS['SysValue']['lang']['partner_menu']);

            if (!empty($stat)) {
                $Tab5 = $PHPShopTableRating->frontCompile('table table-striped');
            } else
                $TabName[4] = null;

            $Forma = $PHPShopFrontGUI->getContent($PHPShopFrontGUI->setTab(array($TabName[0], $Tab4, true), array($TabName[1], $Tab3, true), array($TabName[2], $Tab1, true), array($TabName[3], $Tab2, true), array($TabName[4], $Tab5, true)));


            // Подключаем шаблон
            $this->set('pageContent', $notice . $Forma);
            $this->set('pageTitle', $GLOBALS['SysValue']['lang']['partner_path_name']);

            // Мета
            $this->title = $GLOBALS['SysValue']['lang']['partner_path_name'] . " - " . $this->PHPShopSystem->getValue("name");

            // Подключаем шаблон
            $this->parseTemplate($this->getValue('templates.page_page_list'));
        } else
            $this->enter();
    }

    /**
     * Экшен форма авторизации
     */
    function enter() {

        // Определяем переменные
        $this->set('pageContent', ParseTemplateReturn($GLOBALS['SysValue']['templates']['partner']['partner_forma'], true));
        $this->set('pageTitle', __('Авторизация партнера'));

        // Мета
        $this->title = __("Кабинет партнера - Авторизация - ") . $this->PHPShopSystem->getValue("name");

        // Подключаем шаблон
        $this->parseTemplate($this->getValue('templates.page_page_list'));
    }

    /**
     * Экшен входа
     */
    function enter_user() {

        if (PHPShopSecurity::true_email($_POST['login']) and PHPShopSecurity::true_passw($_POST['password'])) {

            $PHPShopOrm = new PHPShopOrm($this->objBase);
            $PHPShopOrm->debug = $this->debug;
            $row = $PHPShopOrm->select(array('id,login'), array('enabled' => "='1'", 'login' => "='" . $_POST['login'] . "'",
                'password' => "='" . base64_encode($_POST['password']) . "'"), false, array('limit' => 1));

            if (!empty($row['id'])) {
                $_SESSION['partnerName'] = $row['login'];
                $_SESSION['partnerId'] = $row['id'];
            } else {
                $this->set('Error', PHPShopText::alert(__("Ошибка авторизации, повторите попытку ввода E-mail и Пароля")));
            }
        } else
            $this->set('Error', PHPShopText::alert(__("Ошибка авторизации, повторите попытку ввода E-mail и Пароля")));

        $this->index();
    }

    /**
     * Экшен выхода
     */
    function exit_user() {
        $_SESSION['partnerName'] = null;
        $_SESSION['partnerId'] = null;
        $this->index();
    }

    /**
     *  Экшен потеря пароля
     */
    function sendpassword() {

        // Определяем переменные
        $this->set('pageContent', ParseTemplateReturn($GLOBALS['SysValue']['templates']['partner']['partner_forma_lost'], true));
        $this->set('pageTitle', 'Напоминание пароля');

        // Мета
        $this->title = __("Кабинет партнера - Напоминание пароля - ") . $this->PHPShopSystem->getValue("name");

        // Подключаем шаблон
        $this->parseTemplate($this->getValue('templates.page_page_list'));
    }

    /**
     * Экшен отправка пароля
     */
    function send_user() {

        $login = PHPShopSecurity::TotalClean($_POST['login'], 2);

        if (PHPShopSecurity::true_email($login)) {

            // Выборка данных
            $PHPShopOrm = new PHPShopOrm($this->objBase);
            $PHPShopOrm->Option['where'] = ' OR ';
            $row = $PHPShopOrm->select(array('*'), array('login' => "='" . $login . "'"), false, array('limit' => 1));

            if (!empty($row['login'])) {

                PHPShopObj::loadClass("mail");
                $zag = __("Напоминание пароля в ") . $this->PHPShopSystem->getValue("name");
                $content = __('Доброго времени, ') . $row['login'] . '
Для доступа к сайту http://' . $_SERVER['SERVER_NAME'] . $this->getValue('dir.dir') . '/partner/ используйте данные:
Логин: ' . $row['login'] . '
Пароль: ' . base64_decode($row['password']) . '
';
                $this->set('message', nl2br($content));

                // Отправка e-mail пользователю
                $PHPShopMail = new PHPShopMail($row['login'], $this->PHPShopSystem->getEmail(), $zag, '', true, true);

                $content = ParseTemplateReturn('./phpshop/lib/templates/order/blank.tpl', true);
                $PHPShopMail->sendMailNow($content);

                $this->set('Error', PHPShopText::alert(__("Сообщение с паролем отправлено")));
            }
        }

        $this->index();
    }

    /**
     *  Экшен форма регистрации
     */
    function register() {

        // Определяем переменные
        $this->set('pageContent', ParseTemplateReturn($GLOBALS['SysValue']['templates']['partner']['partner_forma_register'], true));
        $this->set('pageTitle', __('Регистрация партнера'));

        // Мета
        $this->title = __("Кабинет партнера - Регистрация - ") . $this->PHPShopSystem->getValue("name");

        // Подключаем шаблон
        $this->parseTemplate($this->getValue('templates.page_page_list'));
    }

    /**
     * Экшен смены данных пользователя
     */
    function update_user() {

        $login = PHPShopSecurity::TotalClean($_POST['login'], 3);
        $password = PHPShopSecurity::TotalClean($_POST['password'], 2);
        $content = PHPShopSecurity::TotalClean($_POST['content'], 2);
        $name = PHPShopSecurity::TotalClean($_POST['name']);

        if (PHPShopSecurity::true_email($login)) {

            $PHPShopOrm = new PHPShopOrm($this->objBase);
            $PHPShopOrm->debug = $this->debug;

            $update_var = array(
                'mail_new' => $login,
                'content_new' => $content,
                'name_new' => $name
            );

            if (!empty($password))
                $update_var['password_new'] = base64_encode($password);

            $PHPShopOrm->debug = false;
            $PHPShopOrm->update($update_var, array('id' => '=' . intval($_SESSION['partnerId'])));

            $notice = PHPShopText::message($GLOBALS['SysValue']['lang']['partner_update'], $this->icon);
        } else
            $notice = PHPShopText::message($GLOBALS['SysValue']['lang']['partner_error'], $this->icon);

        $this->index($notice);
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
     * Есть ли пользователь в базе
     * @param string $login имя пользователя
     * @return bool
     */
    function chek($login) {
        $PHPShopOrm = new PHPShopOrm($this->objBase);
        $PHPShopOrm->debug = $this->debug;
        $num = $PHPShopOrm->select(array('id'), array('login' => "='$login'"), false, array('limit' => 1));
        if (empty($num['id']))
            return true;
    }

    /**
     * Экшен записи пользователя
     */
    function add_user() {
        $mes = null;

        $login = $_POST['login'];
        $password = $_POST['password'];
        $name = PHPShopSecurity::TotalClean($_POST['name']);

        if ($this->security()) {
            if (PHPShopSecurity::true_email($login) and PHPShopSecurity::true_passw($password)) {

                // проверка на уникальность имени
                if ($this->chek($login)) {

                    $PHPShopOrm = new PHPShopOrm($this->objBase);
                    $PHPShopOrm->debug = $this->debug;
                    $_SESSION['partnerId'] = $PHPShopOrm->insert(array('date' => date("d-m-y"), 'login' => $login, 'password' => base64_encode($password), 'enabled' => '1', 'name' => $name), '');

                    $_SESSION['partnerName'] = $login;

                    $this->index();
                } else
                    $mes = __('Партнер с таким логином уже зарегистрирован');
            } else
                $mes = __('Ошибка заполнения формы регистрации');
        } else
            $mes = __("Ошибка ключа, повторите попытку ввода ключа");

        // Еще попытка
        if (!empty($mes)) {

            $this->set('mesageText', PHPShopText::alert($mes));

            // Мета
            $this->title = "Кабинет партнера - Регистрация - " . $this->PHPShopSystem->getValue("name");

            // Определяем переменные
            $this->set('pageContent', ParseTemplateReturn($GLOBALS['SysValue']['templates']['partner']['partner_forma_register'], true));
            $this->set('pageTitle', 'Регистрация партнера');

            // Подключаем шаблон
            $this->parseTemplate($this->getValue('templates.page_page_list'));
        }
    }

}

?>