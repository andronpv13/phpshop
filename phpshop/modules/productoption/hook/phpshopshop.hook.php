<?php
function prodoption_CID_Product_hook($obj, $data, $rout){
    if($rout == "END"){
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
        $category = $PHPShopOrm->select(array('*'), array('id=' => $obj->category));

        $obj->set('catalogOption1', $category['option6']);
        $obj->set('catalogOption2', $category['option7']);
        $obj->set('catalogOption3', $category['option8']);
        $obj->set('catalogOption4', $category['option9']);
        $obj->set('catalogOption5', $category['option10']);
    }
}

function prodoption_CID_Category_hook($obj, $data, $rout){
    if($rout == "END"){
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
        $category = $PHPShopOrm->select(array('*'), array('id=' => $obj->category));

        $obj->set('catalogOption1', $category['option6']);
        $obj->set('catalogOption2', $category['option7']);
        $obj->set('catalogOption3', $category['option8']);
        $obj->set('catalogOption4', $category['option9']);
        $obj->set('catalogOption5', $category['option10']);
    }
}
$addHandler=array('CID_Product'=>'prodoption_CID_Product_hook', 'CID_Category' => 'prodoption_CID_Category_hook');