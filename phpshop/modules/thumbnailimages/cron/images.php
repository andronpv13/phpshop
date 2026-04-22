<?php

session_start();

// Включение
$enabled = false;

if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_classPath = realpath(dirname(__FILE__)) . "/../../../";
    $enabled = true;
    $mod = $argv[1];
} else {
    $_classPath = "../../../";

    if (!empty($_GET['mod']))
        $mod = $_GET['mod'];
}

include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("category");
PHPShopObj::loadClass("text");
PHPShopObj::loadClass("lang");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopSystem = new PHPShopSystem();
$_SESSION['lang'] = $PHPShopSystem->getSerilizeParam("admoption.lang_adm");
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'admin'));

// Авторизация
if ($_GET['s'] == md5($PHPShopBase->SysValue['connect']['host'] . $PHPShopBase->SysValue['connect']['dbase'] . $PHPShopBase->SysValue['connect']['user_db'] . $PHPShopBase->SysValue['connect']['pass_db']))
    $enabled = true;

if (empty($enabled))
    exit("Ошибка авторизации!");

// Настройки модуля
PHPShopObj::loadClass("modules");
$PHPShopModules = new PHPShopModules($_classPath . "modules/");
$data = (new PHPShopOrm($PHPShopModules->getParam("base.thumbnailimages.thumbnailimages_system")))->getOne(['*']);

include_once dirname(__DIR__) . '/class/ThumbnailImages.php';
$message = null;

$thumbnailImages = new ThumbnailImages();
if($thumbnailImages->options['stop'] == 1)
        exit('Задача уже выполнена, снимите блокировку запуска в настройках модуля.');
else if($thumbnailImages->options['run'] == 1)
        exit('Задача еще выполняется...');

if(empty($mod)){
   $message = __('Не указан аргумент mod [thumb / orig]');
}
// Маленькая картинка
elseif ($mod == 'thumb') {

    $result = $thumbnailImages->generateThumbnail();

    if ((int) $result['count'] < (int) $data['limit']) {
        $message = __(sprintf('Обработано изображений: с %s до %s. Все доступные изображения обработаны. Следующий вызов запустит операцию с 0.', (int) $data['processed'], (int) $data['processed'] + (int) $result['count']));
    }

    if ('thumb' !== $data['last_operation']) {
        $data['processed'] = 0;
    }

    if (!isset($message)) {
        $message = __(sprintf('Выполнено. Обработано изображений: с %s до %s', (int) $data['processed'], (int) $data['processed'] + (int) $result['count']));
    }
} else if ($mod == 'orig') {

    $result = $thumbnailImages->generateOriginal();

    if ((int) $result['count'] < (int) $data['limit']) {
        $message = __(sprintf('Обработано изображений: с %s до %s. Все доступные изображения обработаны. Следующий вызов запустит операцию с 0.', (int) $data['processed'], (int) $data['processed'] + (int) $result['count']));
    }

    if ('thumb' !== $data['last_operation']) {
        $data['processed'] = 0;
    }

    if (!isset($message)) {
        $message = __(sprintf('Выполнено. Обработано изображений: с %s до %s', (int) $data['processed'], (int) $data['processed'] + (int) $result['count']));
    }
}


echo $message;
