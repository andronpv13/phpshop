<?php

/**
 * SEO ссылки для элемента навигации подкаталога страниц
 */
function subcatalog_page_seourl_hook($obj, $data, $rout) {

    if ($rout == 'START') {
        $dis = null;
        $i = 0;
        if (is_array($data))
            foreach ($data as $row) {

                // Определяем переменные
                $obj->set('catalogId', $row['parent_to']);
                $obj->set('catalogUid', $row['id']);
                $obj->set('catalogI', $i);
                $obj->set('catalogLink', 'CID_' . $row['id']);
                $obj->set('catalogTemplates', $obj->getValue('dir.templates') . chr(47) . $obj->PHPShopSystem->getValue('skin') . chr(47));
                $obj->set('catalogName', $row['name']);
                $i++;

                // Подключаем шаблон
                $dis.=$obj->parseTemplate($obj->getValue('templates.podcatalog_page_forma'));

                if (!empty($row["page_cat_seo_name"]))
                    $dis = str_replace("page/CID_" . $row['id'] . ".html", "page/" . $row['page_cat_seo_name'] . ".html", $dis);
                else {
                    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page_categories']);
                    $seoURL = $GLOBALS['PHPShopSeoPro']->setLatin($row['name']);
                    $PHPShopOrm->update(array('page_cat_seo_name_new' => "$seoURL"), array('id=' => $row['id']));
                    $dis = str_replace("page/CID_" . $row['id'] . ".html", "page/" . $GLOBALS['PHPShopSeoPro']->setLatin($row['name']) . ".html", $dis);
                }
            }

        return $dis;
    }
}

/**
 * SEO ссылки для элемента навигации каталога страниц
 */
function pageCatal_seourl_hook($obj, $data, $rout) {
    
    if ($rout == "START") {

        // Настройки модуля
        $seourl_option = $GLOBALS['PHPShopSeoPro']->getSettings();

        if ($seourl_option["seo_page_enabled"] != 2)
            return false;

            $dis = null;
            $i = 0;

            if (is_array($data))
                foreach ($data as $row) {

                    // Определяем переменные
                    $obj->set('catalogId', $row['id']);
                    $obj->set('catalogI', $i);
                    $obj->set('catalogTemplates', $obj->getValue('dir.templates') . chr(47) . $obj->PHPShopSystem->getValue('skin') . chr(47));

                    // Если есть страницы
                    if ($obj->check($row['id'])) {

                        $obj->set('catalogName', $row['name']);
                        $obj->set('catalogId', $row['id']);
                        $obj->set('catalogPodcatalog', null);

                        $dis.=$obj->parseTemplate($obj->getValue('templates.catalog_page_forma_2'));
                    } else {
                        $obj->set('catalogPodcatalog', $obj->subcatalog($row['id']));
                        $obj->set('catalogName', $row['name']);

                        $dis.=$obj->parseTemplate($obj->getValue('templates.catalog_page_forma'));
                    }

                    if (!empty($row["page_cat_seo_name"]))
                        $dis = str_replace("page/CID_" . $row['id'] . ".html", "page/" . $row['page_cat_seo_name'] . ".html", $dis);
                    else {
                        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page_categories']);
                        $seoURL = $GLOBALS['PHPShopSeoPro']->setLatin($row['name']);
                        $PHPShopOrm->update(array('page_cat_seo_name_new' => "$seoURL"), array('id=' => $row['id']));
                        $dis = str_replace("page/CID_" . $row['id'] . ".html", "page/" . $GLOBALS['PHPShopSeoPro']->setLatin($row['name']) . ".html", $dis);
                    }
                    $i++;
                }
            return $dis;
        }
}

function topMenu_seourl_hook($obj, $data, $route)
{
    if($route === 'END') {
        $where['menu'] = "='1'";

        // Мультибаза
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['menu'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $PHPShopOrm = new PHPShopOrm($obj->objBase);
        $PHPShopOrm->debug = false;
        $pages = $PHPShopOrm->select(array('id', 'name', 'page_cat_seo_name'), $where, array('order' => 'num,name'), array("limit" => 20));

        if(is_array($pages)) {
            foreach ($pages as $page) {
                if(!empty($page['page_cat_seo_name'])) {
                    $seoUrl = $page['page_cat_seo_name'];
                } else {
                    $seoUrl = $GLOBALS['PHPShopSeoPro']->setLatin($page['name']);
                }

                $data = str_replace("page/CID_" . $page['id'] . ".html", "page/" . $seoUrl . ".html", $data);
            }
        }

        return $data;
    }
}

$addHandler = array(
    'subcatalog' => 'subcatalog_page_seourl_hook',
    'pageCatal' => 'pageCatal_seourl_hook',
    'topMenu' => 'topMenu_seourl_hook'
);
?>