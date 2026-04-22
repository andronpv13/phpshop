<?php

// PHP Version Warning
if (floatval(phpversion()) < 5.6) {
    exit("PHP " . phpversion() . " не поддерживается. Требуется PHP 5.6 или выше.");
}

session_start();
$_classPath = "../";
include($_classPath . "class/obj.class.php");
require_once $_classPath . '/lib/phpass/passwordhash.php';
PHPShopObj::loadClass(array("base", "system", "admgui", "orm", "security", "modules", "mail", "lang"));

if (isset($_POST['base']))
    $_GET['base'] = $_POST['base'];

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopSystem = new PHPShopSystem();

// Locale
$_SESSION['lang'] = $PHPShopSystem->getSerilizeParam("admoption.lang_adm");

$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'admin'));

$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['users']);
$PHPShopOrm->debug = false;
PHPShopParser::set('logo', $PHPShopSystem->getLogo());
PHPShopParser::set('serverPath', $_SERVER['SERVER_NAME']);

// Модули
$PHPShopModules = new PHPShopModules($_classPath . "modules/");

// Перехват модуля
$PHPShopModules->setHookHandler('signin', 'signin');

// Редактор GUI
$PHPShopGUI = new PHPShopGUI();

// Проверка черного списка
if ($PHPShopBase->getNumRows('black_list', 'where ip="' . PHPShopSecurity::TotalClean($_SERVER['REMOTE_ADDR']) . '"')) {
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
    if (file_exists('../../404.html'))
        include_once('../../404.html');
    exit();
}

// Проверка 10 неправильных попыток авторизаций и блокировка IP на 1 час
if ($PHPShopBase->getNumRows('jurnal', 'where ip="' . PHPShopSecurity::TotalClean($_SERVER['REMOTE_ADDR']) . '" and flag=\'1\' and datas > ' . (time() - 3600)) > 10) {
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
    if (file_exists('../../404.html'))
        include_once('../../404.html');
    exit();
}

function generatePassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $numChars = strlen($chars);
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= substr($chars, rand(1, $numChars) - 1, 1);
    }
    return $string;
}

// Выбор шаблона панели управления
function GetAdminSkinList($skin) {
    global $PHPShopGUI;
    $dir = "./css/";
    $id = 0;

    $color = array(
        'default' => '#178ACC',
        'cyborg' => '#000',
        'flatly' => '#D9230F',
        'spacelab' => '#46709D',
        'slate' => '#4E5D6C',
        'yeti' => '#008CBA',
        'simplex' => '#DF691A',
        'sardbirds' => '#45B3AF',
        'wordless' => '#468966',
        'wildspot' => '#564267',
        'loving' => '#FFCAEA',
        'retro' => '#BBBBBB',
        'cake' => '#E3D2BA',
        'dark' => '#3E444C'
    );

    if (is_dir($dir)) {
        if (@$dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {

                if (preg_match("/^bootstrap-theme-([a-zA-Z0-9_]{1,30}).css$/", $file, $match)) {
                    $icon = $color[$match[1]];

                    $file = str_replace(array('.css', 'bootstrap-theme-'), '', $file);

                    if ($skin == $file)
                        $sel = "selected";
                    else
                        $sel = "";

                    if ($file != "." and $file != ".." and ! strpos($file, '.')) {

                        if ($file == 'default')
                            $name = 'тема';
                        else
                            $name = $file;

                        $value[] = array($file, $file, $sel, 'data-content="<span class=\'glyphicon glyphicon-picture\' style=\'color:' . $icon . '\'></span> ' . $name . '"');
                        $id++;
                    }
                }
            }
            closedir($dh);
        }
    }

    return $PHPShopGUI->setSelect('theme', $value, 100, null, false, false, false, 1, false, 'theme');
}

// Экшен выхода
function actionLogout() {
    global $notification;
    $notification = __('Пользователь') . ' ' . $_SESSION['logPHPSHOP'] . ' ' . __('выполнил выход');
    session_destroy();
}

// Экшен генерация хеша на смену пароля
function actionHash() {
    global $PHPShopOrm, $notification, $PHPShopSystem;

    if (PHPShopSecurity::true_param($_POST['actionHash']) and PHPShopSecurity::true_login($_POST['log']) and strpos($_SERVER["HTTP_REFERER"], $_SERVER['SERVER_NAME'])) {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['users']);
        $data = $PHPShopOrm->select(array('password', 'mail', 'id', 'login'), array('login' => '="' . $_POST['log'] . '"'), false, array('limit' => 1));

        if (is_array($data)) {

            $hash = md5($data['id'] . $_POST['log'] . $data['mail'] . $data['password'] . time());
            $PHPShopOrm->clean();
            $PHPShopOrm->update(array('hash_new' => $hash), array('id' => '=' . $data['id']));

            PHPShopParser::set('hash', $hash);
            PHPShopParser::set('login', $data['login']);
            new PHPShopMail($data['mail'], $PHPShopSystem->getEmail(), __('Доступ к PHPShop'), PHPShopParser::file('tpl/hash.mail.tpl', true), true);

            $notification = __('Письмо с инструкциями выслано на') . ' ' . $data['mail'];
        }
    }
}

// Экшен геренация пароля
function actionUpdate() {
    global $PHPShopOrm, $notification, $PHPShopSystem;


    $hash = mysqli_real_escape_string($PHPShopOrm->link_db, stripslashes($_GET['newPassGen']));

    if (PHPShopSecurity::true_param($_GET['newPassGen'])) {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['users']);
        $data = $PHPShopOrm->select(array('password', 'mail', 'id', 'login'), array('hash' => '="' . $hash . '"'), false, array('limit' => 1));

        if (is_array($data)) {

            // генерируем новый пароль для администратора
            $newPass = generatePassword();

            // кодируем новый пароль 
            $hasher = new PasswordHash(8, false);
            $password = $hasher->HashPassword($newPass);

            $PHPShopOrm->update(array('password_new' => $password, 'hash_new' => ''), array('id' => '=' . $data['id']));

            PHPShopParser::set('login', $data['login']);
            PHPShopParser::set('password', $newPass);
            new PHPShopMail($data['mail'], $PHPShopSystem->getEmail(), __('Доступ к PHPShop'), PHPShopParser::file('tpl/pass.mail.tpl', true), true);

            $notification = __('Письмо с новым паролем выслано на') . ' ' . $data['mail'];
        }
    }
}

// Экшен входа
function actionEnter() {
    global $PHPShopOrm, $PHPShopModules;

    $_POST['log'] = trim($_POST['log']);
    $_POST['pas'] = trim($_POST['pas']);

    $hasher = new PasswordHash(8, false);
    $data = $PHPShopOrm->select(array('*'), array('enabled' => "='1'"), false, array('limit' => 30));
    if (is_array($data)) {
        foreach ($data as $row) {

            if ($row['login'] == $_POST['log'] and $hasher->CheckPassword($_POST['pas'], $row['password'])) {

                $_SESSION['logPHPSHOP'] = $_POST['log'];
                $_SESSION['pasPHPSHOP'] = $_POST['pas'];
                $_SESSION['idPHPSHOP'] = $row['id'];
                $_SESSION['namePHPSHOP'] = $row['name'];

                // Запрос модуля на закладку
                $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

                if (isset($_SESSION['return']))
                    $return = '?' . $_SESSION['return'];
                else
                    $return = null;

                // Запись в журнал авторизации
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['jurnal']);
                $PHPShopOrm->insert(array('user' => $_POST['log'], 'datas' => time(), 'flag' => 0, 'ip' => PHPShopSecurity::TotalClean($_SERVER['REMOTE_ADDR'])), '');

                // Смена цветовой темы
                if (!empty($_POST['theme'])) {
                    $theme = PHPShopSecurity::TotalClean($_POST['theme']);
                    if (!file_exists('./css/bootstrap-theme-' . $theme . '.css'))
                        $theme = 'default';
                    else
                        $_SESSION['admin_theme'] = $theme;
                }

                header('Location: ./admin.php' . $return);
                return true;
            }
        }
    }
    // Запись в журнал авторизации
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['jurnal']);
    $PHPShopOrm->insert(array('user' => PHPShopSecurity::TotalClean($_POST['log']), 'datas' => time(), 'flag' => 1, 'ip' => PHPShopSecurity::TotalClean($_SERVER['REMOTE_ADDR'])), '');

    PHPShopParser::set('error', 'has-error');
}

function actionStart() {
    global $PHPShopSystem, $PHPShopBase, $PHPShopGUI, $notification;

    $License = parse_ini_file_true("../../license/" . PHPShopFile::searchFile('../../license/', 'getLicense', true), 1);

    if (is_array($License)) {
        $_SESSION['support'] = $License['License']['SupportExpires'];
        if ($License['License']['SupportExpires'] > time() and $License['License']['RegisteredTo'] != 'Trial NoName')
            $_SESSION['update'] = 1;
        elseif ($License['License']['SupportExpires'] > time() and $License['License']['RegisteredTo'] == 'Trial NoName')
            $_SESSION['update'] = 2;
        else
            $_SESSION['update'] = 0;
    }

    if ($License['License']['Pro'] == 'Start')
        $_SESSION['mod_limit'] = 5;
    elseif ($License['License']['Pro'] == 'Enabled') {
        $_SESSION['mod_pro'] = true;
        $_SESSION['mod_limit'] = 100;
    } else
        $_SESSION['mod_limit'] = 50;

    if (getenv("COMSPEC"))
        $_SESSION['mod_pro'] = true;

    if (!empty($License['License']['YandexCloud']) and ($License['License']['YandexCloud'] !='No' and $License['License']['YandexCloud'] > time())) {
        $_SESSION['yandexcloud'] = $License['License']['YandexCloud'];
    } else {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['system']);
        $data = $PHPShopOrm->select();
        $option = unserialize($data['ai']);
        $option['yandexgpt_seo'] = 0;
        $option['yandexgpt_seo_import'] = 0;
        $option['yandexgpt_chat_enabled'] = 0;
        $option['yandexsearch_site_enabled'] = 0;
        $PHPShopOrm->update(['ai_new' => serialize($option)]);
    }

    // Ознакомительный режим
    if (is_array($License)) {
        if ($License['License']['Expires'] != 'Never' and $License['License']['Expires'] < time()) {
            PHPShopParser::set('title', __('Окончание работы PHPShop'));
            PHPShopParser::set('server', getenv('SERVER_NAME'));
            PHPShopParser::set('serverLocked', getenv('SERVER_NAME'));
            exit(PHPShopParser::file($_SERVER['DOCUMENT_ROOT'] . '/phpshop/lib/templates/error/license.tpl'));
            exit("Ошибка проверки лицензии для SERVER_NAME=" . $_SERVER["SERVER_NAME"] . ", HardwareLocked=" . getenv('SERVER_NAME'));
        } elseif (strstr($License['License']['HardwareLocked'], '-') and getenv('SERVER_NAME') != $License['License']['DomenLocked']) {
            //header('Location: //' . $License['License']['DomenLocked'] . '/phpshop/admpanel/admin.php');
        }
    }

    // Trial
    if ($License['License']['Expires'] != 'Never' or getenv("COMSPEC")) {
        $_SESSION['is_trial'] = true;
    }

    if (!empty($_SESSION['logPHPSHOP']) and empty($_SESSION['return'])) {
        header('Location: ./admin.php');
    }

    // Тема оформления
    if (empty($_SESSION['admin_theme']))
        $theme = PHPShopSecurity::TotalClean($PHPShopSystem->getSerilizeParam('admoption.theme'));
    else
        $theme = $_SESSION['admin_theme'];
    if (!file_exists('./css/bootstrap-theme-' . $theme . '.css'))
        $theme = 'default';

    // Демо-режим
    if ($PHPShopBase->getParam('template_theme.demo') == 'true' and ! isset($_GET['login'])) {
        PHPShopParser::set('user', 'demo');
        PHPShopParser::set('password', 'demouser');
        PHPShopParser::set('readonly', 'readonly');
        PHPShopParser::set('disabled', 'disabled');
        PHPShopParser::set('hide', 'hide');
        PHPShopParser::set('hide_home', 'hide');
        PHPShopParser::set('themeSelect', GetAdminSkinList($theme));
    } else {
        PHPShopParser::set('autofocus', 'autofocus');
    }

    // Выбор БД
    if (is_array($GLOBALS['SysValue']['connect_select'])) {
        foreach ($GLOBALS['SysValue']['connect_select'] as $k => $v)
            $connect_select[] = array($v, $k, $_SESSION['base']);

        PHPShopParser::set('hide_home', 'hide');
        PHPShopParser::set('themeSelect', $PHPShopGUI->setSelect('base', $connect_select, 120));
    }


    PHPShopParser::set('title', 'PHPShop - ' . __('Авторизация'));
    PHPShopParser::set('version', $PHPShopBase->getParam('upload.version'));
    PHPShopParser::set('theme', $theme);
    PHPShopParser::set('notification', $notification);
    PHPShopParser::set('code', $GLOBALS['PHPShopLang']->code);
    PHPShopParser::set('charset', $GLOBALS['PHPShopLang']->charset);
    PHPShopParser::set('lang', $_SESSION['lang']);
    PHPShopParser::file('tpl/signin.tpl');
}

// Смена пароля
$_REQUEST['actionList']['newPassGen'] = 'actionUpdate';
$_REQUEST['actionList']['logout'] = 'actionLogout';


// Обработка событий
$PHPShopGUI->getAction();

if (!empty($_GET['logout']))
    $logout = $_GET['logout'];
else
    $logout = null;

// Вывод формы при старте
$PHPShopGUI->setLoader($logout, 'actionStart');
?>