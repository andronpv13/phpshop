<?php

/**
 * Сжатие и кэширование JS/CSS файлов
 * @author PHPShop Software
 * @version 1.1
 */
if (!empty($_GET['f'])) {

    session_start();

    // Парсируем установочный файл
    include("../phpshop/class/base.class.php");
    include("../phpshop/class/obj.class.php");
    $PHPShopBase = new PHPShopBase("../phpshop/inc/config.ini", true, true);

    // Системные настройки
    include(".".$SysValue['class']['array']);
    include(".".$SysValue['class']['system']);
    $PHPShopSystem = new PHPShopSystem();
    
    if(empty($_SESSION['skin']))
        $_SESSION['skin']= $PHPShopSystem->getParam('skin');

    // Сжатие данных GZIP
    include(".".$SysValue['class']['cache']);
    include(".".$SysValue['file']['gzip']);
    include(".".$SysValue['class']['security']);

    $ext = PHPShopSecurity::getExt($_GET['f']);
    
    if (in_array($ext, ['js', 'css']) and ! stristr($_GET['f'], 'http') and ! stristr($_GET['f'], 'config')) {
        $file = $_SERVER['DOCUMENT_ROOT'] . $_GET['f'];

        if (file_exists($file)) {

            $cache_key = md5(str_replace("www.", "", getenv('SERVER_NAME')) . $file).'.'.$ext;
            $PHPShopCache = new PHPShopCache($cache_key);

            $PHPShopFileCache = new PHPShopFileCache($PHPShopCache->time);
            $PHPShopFileCache->dir = "/UserFiles/Cache/static/";
            $PHPShopFileCache->check_time = false;

            $content = $PHPShopFileCache->get($cache_key);
            if (empty($content)) {

                $content = file_get_contents($file);

                // Шрифты и иконки
                $content = str_replace(['fonts/', 'images/', 'css/'], ['/phpshop/templates/' . $_SESSION['skin'] . '/fonts/', '/phpshop/templates/' . $_SESSION['skin'] . '/images/', '/phpshop/templates/' . $_SESSION['skin'] . '/css/'], $content);

                // Комментарии
                $content = preg_replace('#// .*#', '', $content);
                $content = preg_replace('#/\*(?:[^*]*(?:\*(?!/))*)*\*/#', '', $content);
                
                // Переводы строк
                $content = preg_replace('([\r\n\t])', '', $content);

                // 2 и более пробелов
                $content = preg_replace('/ {2,}/', '', $content);

                $PHPShopFileCache->set($cache_key, $content);
            }
            
            $PHPShopCache->init();

            // Кеширование браузером 30 дней
            header("Cache-Control: max-age=2592000");

            if ($ext == 'css')
                header("Content-Type: text/css");
            elseif ($ext == 'js')
                header("Content-Type: application/javascript");
            else
                $error = true;

            echo $content;
            
            $PHPShopCache->gzip(false);
        } else
            $error = true;
    } else
        $error = true;
} else
    $error = true;

if (!empty($error)) {
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
}