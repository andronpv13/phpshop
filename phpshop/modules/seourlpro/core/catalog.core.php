<?php

class PHPShopCatalog extends PHPShopCore {

    function __construct() {
        global $PHPShopNav;
        $url=$PHPShopNav->getName(true);
        
        $true_url = str_replace('/catalog/', '', $url);
        if(stristr($true_url, '/'))
        $true_url = str_replace('/catalog/', '/cat/', $url);
        else $true_url = str_replace('/catalog/', '/', $url);
        
        header('Location: '.$true_url. '.html',true,301);
    }
}

?>