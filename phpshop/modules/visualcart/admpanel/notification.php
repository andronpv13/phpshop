<?php

function notificationVisualcart() {
    global $PHPShopModules;
    
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.visualcart.visualcart_memory"));
    $data = $PHPShopOrm->select(array('COUNT(id) as count'),false,false,array('limit'=>'1'));
    
    if($data['count'] > 99)
        $data['count']=99;

    if(!empty($data['count']))
    echo '<a class="navbar-btn btn btn-sm btn-success navbar-right visible-lg" href="?path=modules.dir.visualcart" data-toggle="tooltip" data-placement="bottom" title="'.__('Корзины').'"> '.__('Корзины').' <span class="badge">'.$data['count'].'</span></a>';
}

?>