<?php

$postdata = file_get_contents("php://input");

if (!empty($postdata)) {
    $data = json_decode($postdata, true);
    
    if (is_array($data) && isset($data['reference']) && isset($data['appId'])) {
        $_classPath = $_SERVER['DOCUMENT_ROOT']."/phpshop/";
        include($_classPath . "class/obj.class.php");

        PHPShopObj::loadClass("base");
        $PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
    
        PHPShopObj::loadClass("orm");
        PHPShopObj::loadClass("system");
        PHPShopObj::loadClass("text");
        PHPShopObj::loadClass("string");
        
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['alfacredit']['alfacredit_log']);
        $PHPShopOrm->debug = false;
        $row = $PHPShopOrm->select(array('*'), array('reference' => "='".$data['reference']."'"), false, array('limit' => 1));
        if (isset($row['id'])) {
            $_update = array();
            $_update['status_new'] = serialize(PHPShopString::json_fix_utf($data));    
            $PHPShopOrm->update($_update, array('id' => '='.$row['id']));
            
            echo json_encode(array('appId' => $data['appId']));
            exit();            
        }
    }
}

throw new Exception('Error');

?>