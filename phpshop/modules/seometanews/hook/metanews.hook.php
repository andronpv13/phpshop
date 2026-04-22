<?php

/**
 * Изменение meta title description keywords новостей на странице
 */
function meta_news_mod_ID_hook($obj, $data, $rout) {

    if ($rout == 'END') {
        $title = trim($data['meta_title']);
        $keywords = trim($data['meta_keywords']);
        $description = trim($data['meta_description']);

        if ($title != '')
            $obj->title = $title;
        if ($keywords != '')
            $obj->keywords = $keywords;
        if ($description != '')
            $obj->description = $description;
    }
}

function meta_news_mod_index_hook($obj, $data, $rout) {
    global $PHPShopModules;
    if ($rout == 'END') {

        $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.seometanews.seometanews_system"));
        $data = $PHPShopOrm->select();

        $title = trim($data['title']);
        $keywords = trim($data['keywords']);
        $description = trim($data['description']);

        if ($title != '')
            $obj->title = $title;
        if ($keywords != '')
            $obj->keywords = $keywords;
        if ($description != '')
            $obj->description = $description;
        
        $page = $obj->PHPShopNav->getId();
        if ($page > 1) {
            $obj->description.= ' Часть ' . $page;
            $obj->title.=' - Страница ' . $page;
        }
    }
}

$addHandler = array
    (
    'index' => 'meta_news_mod_index_hook',
    'ID' => 'meta_news_mod_ID_hook'
);
?>