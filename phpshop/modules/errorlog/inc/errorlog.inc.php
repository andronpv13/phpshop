<?php

if (!defined("OBJENABLED"))
    exit(header('Location: /?error=OBJENABLED'));

class PHPShopErrorlog {

    function PHPShopErrorlog() {
        $this->enabled=$this->option();
    }

    function option() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['errorlog']['errorlog_system']);
        $data=$PHPShopOrm->select(array('enabled'),false,array('order'=>'id DESC'),array('limit'=>1));
        return $data['enabled'];
    }

    function write($str) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['errorlog']['errorlog_log']);
        $PHPShopOrm->insert(array('date_new'=>date('U'),'ip_new'=>$_SERVER['REMOTE_ADDR'],'error_new'=>$str));
    }
}


$GLOBALS['PHPShopErrorlog'] = new PHPShopErrorlog();


function myErrorHandler($errno, $errstr, $errfile, $errline) {
    global $PHPShopErrorlog;
    $enabled = $PHPShopErrorlog->enabled;
    $errfile=str_replace('\\','/',$errfile);
    $url=$_SERVER['REQUEST_URI'];
    switch ($errno) {
        case E_USER_ERROR:
            echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
            echo "  Fatal error on line $errline in file $errfile";
            echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
            echo "Aborting...<br />\n";
            exit(1);
            break;

        case 8:
            if($enabled==2) $PHPShopErrorlog->write(__("Замечение:")." [$errno] $errstr $errfile $errline $url");
            break;

       case 2:
            if($enabled==1 or $enabled==2) $PHPShopErrorlog->write(__("Функция:")." [$errno] $errstr $errfile $errline $url");
            break;

        case E_USER_NOTICE:
            if($enabled==1) $PHPShopErrorlog->write(__("Сообщение:")." [$errno] $errstr $errfile $errline $url");
            break;

        default:
            if($enabled==1 or $enabled==2) $PHPShopErrorlog->write(__("Совет:")." [$errno] $errstr $errfile $errline $url");
            break;

    }

    return true;
}

set_error_handler("myErrorHandler");
?>