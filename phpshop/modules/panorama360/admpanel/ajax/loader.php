<?php
session_start();
$_classPath = "../../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass(array("base", "system", "admgui", "orm", "date", "xml", "security", "string", "parser", "mail", "lang"));
$subpath[0] = 'catalog';

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopBase->chekAdmin();

$categoryId = intval($_REQUEST['categoryId']);

$_FILES['fileUpload']['ext'] = PHPShopSecurity::getExt($_FILES['fileUpload']['name']);

if(!in_array($_FILES['fileUpload']['ext'],array('jpg','jpeg','gif','png')))
    exit;

$_FILES['fileUpload']['name'] = str_replace(' ', '', $_FILES['fileUpload']['name']);
$urls = array();

if ($categoryId > 0)
    $path = '../../../../../UserFiles/Image/panorama360/' . intval($_REQUEST['categoryId']) . '/';
else
    $path = '../../../../../UserFiles/Image/panorama360/';

if (file_exists($path))
    $req = 'ok';
else
    mkdir($path, 0700, true);

move_uploaded_file($_FILES['fileUpload']['tmp_name'], $path . '' . $_FILES['fileUpload']['name']);
$urls[] = str_replace('../../../../../', '/', $path) . '' . $_FILES['fileUpload']['name'];
$message = iconv('utf-8', 'windows-1251', 'Файлы успешно загружены');

echo json_encode(
        array(
            'message' => $message,
            'urls' => $urls,
        )
);
die;