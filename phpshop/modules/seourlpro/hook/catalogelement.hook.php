<?php

/**
 * SEO ссылки для элемента навигации каталога
 */
function leftCatal_seourl_hook($obj, $row, $rout) {
    if ($rout == 'END') {

        if (!empty($row['cat_seo_name']))
            $GLOBALS['PHPShopSeoPro']->setMemory($row['id'], $row['cat_seo_name'], 1, false);
        else
            $GLOBALS['PHPShopSeoPro']->setMemory($row['id'], $row['name']);
    }
}

$addHandler = array
    (
    'leftCatal' => 'leftCatal_seourl_hook',
);
?>