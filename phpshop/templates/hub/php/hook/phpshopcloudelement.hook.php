<?php


function template_index_cloud_hook($obj, $disp, $rout) {
    global $PHPShopShopCatalogElement;

    if ($rout == 'START') {
        
        // Меню каталога для мобильных устройств
        $menucatalog = null;
        
        if (is_array($PHPShopShopCatalogElement->tree_array[0]['sub']))
            foreach ($PHPShopShopCatalogElement->tree_array[0]['sub'] as $k => $v) {
                    $menucatalog.='<li><a href="/shop/CID_' . $k . '.html">' . $v . '</a></li>';
            }
        $GLOBALS['SysValue']['other']['menuCatal'] = $menucatalog;

        // Зеркальный каталог
        $GLOBALS['SysValue']['other']['catalogMenu'] = $PHPShopShopCatalogElement->leftCatal(array('mega-menu-column' => 'dropdown dropdown-right', 'mega-menu-block' => 'dropdown-menu dropdown-menu-right', 'hide' => 'show', 'mega-more-parent' => 'mega-more-parent hidden'));
    }

    if ($rout == 'END') {
        $disp = str_replace('a href', 'a class="btn btn-default" href ', $disp);
        $obj->set('leftMenuContent', '<div class="product-tags">' . $disp . '</div>');
    }
}

$addHandler = array
    (
    'index' => 'template_index_cloud_hook',
);
?>