<?php

function lock_hook($obj, $row, $rout) {


    if ($rout == 'START') {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['lock']['lock_system']);
        $option = $PHPShopOrm->select();

        if ($option['flag'] == 2) {

            if ((!isset($_SERVER['PHP_AUTH_USER'])) || !(($_SERVER['PHP_AUTH_USER'] == $option['login']) && ( $_SERVER['PHP_AUTH_PW'] == $option['password'] ))) {
                header("WWW-Authenticate: Basic entrer=\"Admin Login\"");
                header("HTTP/1.0 401 Unauthorized");
                return die("Not authorized");
            }
        }
    }
}

function lockadm_hook($obj, $row, $rout) {



    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['lock']['lock_system']);
    $option = $PHPShopOrm->select();

    if ($option['flag_admin'] == 2 and !is_file('../../unlock.txt')) {

        if ((!isset($_SERVER['PHP_AUTH_USER'])) || !(($_SERVER['PHP_AUTH_USER'] == $option['login']) && ( $_SERVER['PHP_AUTH_PW'] == $option['password'] ))) {
            header("WWW-Authenticate: Basic entrer=\"Admin Login\"");
            header("HTTP/1.0 401 Unauthorized");
            return die("Not authorized");
        } else
            return true;
    } else
        return true;
}

$addHandler = array
    (
    'topMenu' => 'lock_hook',
    'signin' => 'lockadm_hook',
);
?>