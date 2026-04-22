<?php
/**
 * Добавление SEO ссылки к товарам
 */
function price_seo_hook($obj,$row){
    $GLOBALS['PHPShopSeoPro']->setMemory($row['id'],$row['name'],2);
    if (!empty($row['prod_seo_name']))
        $link='/id/'.$row['prod_seo_name'].'-'.$row['id'].'.html';
    else  $link='/id/'.$GLOBALS['PHPShopSeoPro']->setLatin($row['name']).'-'.$row['id'].'.html';
    return  $link;
}

$addHandler=array
        (
        'seourl'=>'price_seo_hook'
);
?>