<?php

function button_footer_hook() {

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['button']['button_system']);
    $option = $PHPShopOrm->select();
    
    $dis=null;

    if($option['enabled'] == 1) {
        
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['button']['button_forms']);
        $data = $PHPShopOrm->select(array('content'),array('enabled'=>"='1'"),array('order'=>'num'),array('limit'=>100));

        if(is_array($data))
            foreach($data as $row) {
                $dis.=' '.str_replace('&#43;', '+',$row['content']);
            }

        $GLOBALS['SysValue']['other']['button_forms']=$dis;
        echo ParseTemplateReturn($GLOBALS['SysValue']['templates']['button']['button'],true);
    }
}


$addHandler=array
        (
        'footer'=>'button_footer_hook'
);
?>