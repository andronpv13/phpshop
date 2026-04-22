<?php

/**
 * 
 * @package PHPShopAjaxElements
 */
session_start();

$_classPath = "../../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
$PHPShopBase->chekAdmin();
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("product");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("string");
PHPShopObj::loadClass("cart");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("user");

$qty = $_REQUEST['qty'];
$promo_id = $_REQUEST['promo_id'];
$operation = $_REQUEST['operation'];

// 
$PHPShopModules = new PHPShopModules($_classPath . "modules/");

//  
$PHPShopSystem = new PHPShopSystem();
$currency = $PHPShopSystem->getDefaultValutaCode();


$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.promotions.promotions_codes"));
global $PHPShopOrm;
$PHPShopOrm->debug = false;

function rnd_generator($q) {

    $chars = '12345ABCDEFGHIJKLMNOPQRSTUVWXYZ_@abcdefghijkLmnopqrstuvwxyz67890';
    $hashpromo = '';
    for ($ichars = 0; $ichars < $q; ++$ichars) {
        $random = str_shuffle($chars);
        $hashpromo .= $random[0];
    }
    return $hashpromo;
}

function if_str($main_str, $my_str) {

    $pos = strpos($main_str, $my_str);
    if ($pos === false) {
        echo false;
    } else {
        return true;
    }
}

function Create($qty, $promo_id) {
    global $PHPShopOrm;

    $operation = "create";
    $PHPShopOrm->debug=false;
    for ($i = 0; $i < $qty; $i++) {

        $result = $PHPShopOrm->insert(array('promo_id_new' => $promo_id, 'code_new' => rnd_generator(10), 'enabled_new' => '1'));

        if (if_str($result, 'Duplicate entry'))
            $i--;
    }
    $PHPShopOrm->clean();
}

function Delete($promo_id) {
    global $PHPShopOrm;
    $operation = "delete";
    $PHPShopOrm->delete(array('promo_id' => '=' . $promo_id, 'enabled' => '="0"'));
}

//  switch-case
switch ($operation) {
    case 'delete':
        Delete($promo_id);
        break;
    case 'create':
        Create($qty, $promo_id);
        break;
    case 'download':
        Download($promo_id);
        break;
}

function Download($promo_id) {
    global $PHPShopOrm;
    $csv = $empty = null;
    $delim = '"';
    $_ = $delim . ';' . $delim;
    $next = '
';
    $data = $PHPShopOrm->select(array('code'), array('promo_id' => '=' . $promo_id, 'enabled' => '="1"'), false);

    if (is_array($data)) {
        foreach ($data as $row)
            $csv.= $delim . $row['code'] . $delim . $next;
    }


    $dir = "../../../../admpanel/csv/";
    $file = 'codes-' . PHPShopDate::dataV(false, false) . '-' . substr(md5(time()), 0, 5) . '.csv';
    PHPShopFile::write($dir . $file, $csv);
    return $file;
}

function CountPromocodes($promo_id) {
    global $PHPShopOrm;

    $PHPShopOrm->clean();
    $count_all = $PHPShopOrm->select(array('count("*") as count'), array('promo_id' => '=' . $promo_id), false);

    $PHPShopOrm->clean();
    $count_active = $PHPShopOrm->select(array('count("*") as count'), array('promo_id' => '=' . $promo_id, 'enabled' => '="1"'), false);

    return array('all' => $count_all['count'], 'active' => $count_active['count']);
}

$count = CountPromocodes($promo_id);

$_RESULT = array(
    'success' => true,
    'operation' => $operation,
    'count_all' => $count['all'],
    'count_active' => $count['active'],
    'file' => Download($promo_id)
);


echo json_encode($_RESULT);
?>