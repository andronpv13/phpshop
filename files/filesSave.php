<?php

session_start();

$_classPath = "../phpshop/";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
$PHPShopSystem = new PHPShopSystem();

// Цифровой контент
if ($PHPShopSystem->getSerilizeParam('admoption.digital_product_enabled') == 1) {
    
    if (isset($_GET['F'])) {
        $code = str_replace("$", $SysValue['my']['digital_pass2'], $_GET['F']);
        $code = str_replace("!", $SysValue['my']['digital_pass1'], $code);
        $code = base64_decode($code);
        $code = unserialize($code);

        $files = $code['files'];
        $time = $code['time'];

        $file_1 = "../" . $files;
        $_Name = pathinfo($file_1);

        if (in_array($_Name['extension'], array('php', 'ini', 'html', 'tpl')) or empty($_Name['extension']))
            exit('<h3>Запрещенный формат...</h3>');

        if ($time > date("U")) {
            if (file_exists($file_1)) {
                header("Content-Description: File Transfer");
                header('Content-Type: application/force-download');
                header('Content-Disposition: attachment; filename=' . $_Name['basename']);
                header("Content-Transfer-Encoding: binary");
                header('Content-Length: ' . filesize($file_1));
                readfile($file_1);
            } else {
                header("Location: /error/");
                exit;
            }
        } else {
            header("Location: /error/");
            exit;
        }
    } else
        exit("<h3>Ссылка просрочена...</h3>");
}else {
    header("Location: /error/");
    exit;
}
?>