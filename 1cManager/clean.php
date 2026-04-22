<?php

/**
 * Очистка временных файлов csv/log
 * Для включения поменяйте значение enabled на true
 */
// Включение
$enabled = false;

if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_classPath = realpath(dirname(__FILE__)) . "/../phpshop/";
    $enabled = true;
} else
    $_classPath = "../phpshop/";

$SysValue = parse_ini_file($_classPath . "inc/config.ini", 1);
$host = $SysValue['connect']['host'];
$dbname = $SysValue['connect']['dbase'];
$uname = $SysValue['connect']['user_db'];
$upass = $SysValue['connect']['pass_db'];

// Авторизация
if (!empty($_GET['s']) and $_GET['s'] == md5($host . $dbname . $uname . $upass))
    $enabled = true;

if (empty($enabled))
    exit("Ошибка авторизации!");

function cleanup_import_directory($path) {
    $elements = scandir($path);
    $i = 0;
    foreach ($elements as $element) {
        if (in_array($element, array('.', '..')))
            continue;

        if (is_dir($path . '/' . $element)){
            cleanup_import_directory($path . '/' . $element);
            rmdir($path . '/' . $element);

        }
        else
            unlink($path . '/' . $element);

        $i++;
    }

    return $i;
}

$clean = 0;
$clean += cleanup_import_directory('sklad');
$clean += cleanup_import_directory('log');

echo 'Успешно удалено файлов ' . (int) $clean;
