<?php

/**
 * Генерация видео из изображений
 * Для включения поменяйте значение enabled на true
 */
// Включение
$enabled = false;

if (empty($_SERVER['DOCUMENT_ROOT'])){
    $_classPath = realpath(dirname(__FILE__)) . "/../../../";
    $enabled = true;
    $_SERVER['DOCUMENT_ROOT']=$_classPath."../";
}
else
    $_classPath = "../../../";

include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass(array("base", "system", "orm"));
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);

// Авторизация
if ($_GET['s'] == md5($PHPShopBase->SysValue['connect']['host'] . $PHPShopBase->SysValue['connect']['dbase'] . $PHPShopBase->SysValue['connect']['user_db'] . $PHPShopBase->SysValue['connect']['pass_db']))
    $enabled = true;

if (empty($enabled))
    exit("Ошибка авторизации!");

// Системные настройки
$PHPShopSystem = new PHPShopSystem();

function cleanup_import_directory($path) {
    $elements = scandir($path);
    $i = 0;
    foreach ($elements as $element) {
        if (in_array($element, array('.', '..')))
            continue;

        @unlink($path . '/' . $element);
        $i++;
    }

    return $i;
}

// Включаем таймер
$time = explode(' ', microtime());
$start_time = $time[1] + $time[0];

if (!empty($PHPShopSystem->ifSerilizeParam('admoption.ffmpeg_enabled')) and ! empty($PHPShopSystem->getSerilizeParam('admoption.ffmpeg_path'))) {

    $path = $PHPShopSystem->getSerilizeParam('admoption.ffmpeg_path');
    $limit = $PHPShopSystem->getSerilizeParam('admoption.ffmpeg_image');
    
    $tmp = $_SERVER['DOCUMENT_ROOT'] . "/phpshop/admpanel/editors/default/video/tmp/";

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);

    // Товары с видео
    $data_video = $PHPShopOrm->select(array('parent'), array('name' => ' LIKE "%.mp4"'), array('group' => 'parent'), array('limit' => 10000));

    if (is_array($data_video))
        foreach ($data_video as $row) {
            $data_video_array[] = $row['parent'];
        }

    // Товар без видео
    $productID = $PHPShopOrm->select(array('parent'), array('parent' => ' NOT IN (' . implode(",", $data_video_array) . ')'), array('order' => 'id desc'), array('limit' => 1))['parent'];

    if (!empty($productID)) {

        // Проверка существования товара
        $product = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['name'], ['id' => '=' . (int) $productID])['name'];
        if (empty($product)) {
            $PHPShopOrm->delete(['parent' => '=' . (int) $productID]);
            echo 'Delete ' . $productID;
            exit;
        }

        // Очистка временной папки
        cleanup_import_directory($tmp);

        $data = $PHPShopOrm->select(array('*'), array('parent' => '=' . intval($productID)), array('order' => 'num,id'), array('limit' => $limit));

        if (is_array($data)) {

            // Копирование изображений
            foreach ($data as $k => $row) {
                $ext = pathinfo($row['name'], PATHINFO_EXTENSION);
                copy($_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $row['name'], $tmp . "image" . $k . "." . $ext);
            }

            // Создание видео
            $cmd = $path . " -framerate 1 -i " . $tmp . "image%d." . $ext . " -vf \"scale=trunc(iw/2)*2:trunc(ih/2)*2\" -c:v libx264 -r 25 -pix_fmt yuv420p  " . $tmp . "output.mp4";
            exec($cmd);

            // Соль
            $RName = substr(abs(crc32(time())), 0, 5);

            // Имя файла
            $name = 'vid' . $productID . '_' . $RName . '.mp4';

            // Папка сохранения
            $path = $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $PHPShopSystem->getSerilizeParam('admoption.image_result_path');
            copy($tmp . "output.mp4", $_SERVER['DOCUMENT_ROOT'] . $path . $name);

            // Очистка временной папки
            cleanup_import_directory($tmp);

            // Добавление в таблицу фотогалереи
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path . $name)) {
                $PHPShopOrm->insert(['parent_new' => $productID, 'name_new' => $path . $name, 'num_new' => 10]);
            }

            // Расход памяти
            if (function_exists('memory_get_usage')) {
                $mem = memory_get_usage();
                $_MEM = round($mem / 1024000, 2) . " Mb";
            } else
                $_MEM = null;

            // Выключаем таймер
            $time = explode(' ', microtime());
            $seconds = ($time[1] + $time[0] - $start_time);
            $seconds = substr($seconds, 0, 6);

            echo "Done ~ " . round($seconds) . " sec, " . $_MEM . ", file: " . $path . $name;
        }
    }
}
