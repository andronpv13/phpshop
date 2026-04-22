<?php

session_start();

$_classPath = "../../../";
include_once($_classPath . "class/obj.class.php");
include_once($_classPath . "modules/novaposhta/class/NovaPoshta.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
PHPShopObj::loadClass('modules');
PHPShopObj::loadClass('orm');
PHPShopObj::loadClass('system');
PHPShopObj::loadClass('security');
PHPShopObj::loadClass('order');

$PHPShopBase->chekAdmin();

$NovaPoshta = new NovaPoshta();

if(isset($_REQUEST['operation']) && strlen($_REQUEST['operation']) > 2) {
    $result = [];
    try {
        switch ($_REQUEST['operation']) {
            case 'getPvz':
                $result['pvz'] = $NovaPoshta->getPvz($_REQUEST['city']);
                break;
        }

        $result['success'] = true;
    } catch (\Exception $exception) {
        $result = ['success' => false, 'error' => PHPShopString::win_utf8($exception->getMessage())];
    }
} else {
    $result = ['success' => false, 'error' => PHPShopString::win_utf8('Не найден параметр operation')];
}

echo (json_encode($result)); exit;