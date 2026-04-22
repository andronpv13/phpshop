<?php

session_start();
$_classPath = "../../../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass(array("base", "system", "admgui", "orm", "security", "string", "lang"));

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopBase->chekAdmin();

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

if (!empty($PHPShopSystem->ifSerilizeParam('admoption.ffmpeg_enabled')) and ! empty($PHPShopSystem->getSerilizeParam('admoption.ffmpeg_path'))) {
    $path = $PHPShopSystem->getSerilizeParam('admoption.ffmpeg_path');
    $tmp = $_SERVER['DOCUMENT_ROOT'] . "/phpshop/admpanel/editors/default/video/tmp/";
    
    // Очистка временной папки
    cleanup_import_directory($tmp);

    $limit = $PHPShopSystem->getSerilizeParam('admoption.ffmpeg_image');
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
    $data = $PHPShopOrm->select(array('*'), array('parent' => '=' . intval($_POST['productID'])), array('order' => 'num,id'), array('limit' => $limit));

    if (is_array($data)) {

        // Копирование изображений
        foreach ($data as $k => $row) {
            $ext = pathinfo($row['name'],PATHINFO_EXTENSION);
            copy($_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $row['name'], $tmp . "image" . $k . ".".$ext);
        }
        
        // Создание видео
        $cmd = $path . " -framerate 1 -i " . $tmp . "image%d.".$ext." -vf \"scale=trunc(iw/2)*2:trunc(ih/2)*2\" -c:v libx264 -r 25 -pix_fmt yuv420p  " . $tmp . "output.mp4";
        exec($cmd);

        // Соль
        $RName = substr(abs(crc32(time())), 0, 5);

        // Имя файла
        $name = 'vid' . $_POST['productID'] . '_' . $RName . '.mp4';

        // Папка сохранения
        $path = $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $PHPShopSystem->getSerilizeParam('admoption.image_result_path');
        copy($tmp . "output.mp4", $_SERVER['DOCUMENT_ROOT'] . $path . $name);

        // Очистка временной папки
        cleanup_import_directory($tmp);

        // Добавление в таблицу фотогалереи
        if (file_exists( $_SERVER['DOCUMENT_ROOT'].$path . $name))
            $PHPShopOrm->insert(['parent_new' => $_POST['productID'], 'name_new' => $path . $name, 'num_new' => 10]);


        $result = true;
    } else
        $result = false;
} else
    $result = false;

header("Content-Type: application/json");
exit(json_encode(['success' => $result]));
