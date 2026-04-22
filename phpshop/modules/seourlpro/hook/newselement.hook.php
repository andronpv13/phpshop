<?php

/**
 * Добавление SEO ссылки к новостям на главной
 */
function index_newselement_seourl_hook($obj, $row, $rout) {

    if ($rout == 'START') {

        // Настройки модуля
        $obj->seourl_option = $GLOBALS['PHPShopSeoPro']->getSettings();
    }

    if ($rout == 'MIDDLE') {
        $dis = null;
        
        if ($obj->seourl_option["seo_news_enabled"] != 2)
            return false;

        if (is_array($row)) {
            
            // Определяем переменные
            $obj->set('newsId', $row['id']);
            $obj->set('newsZag', $row['zag']);
            $obj->set('newsData', $row['datas']);
            $obj->set('newsKratko', $row['kratko']);
            $obj->set('newsIcon', $row['icon']);

            // Подключаем шаблон
            $dis .= $obj->parseTemplate($obj->getValue('templates.news_main_mini'));
            if (!empty($row["news_seo_name"]))
                $dis = str_replace("news/ID_" . $row['id'] . ".html", "news/" . $row['news_seo_name'] . ".html", $dis);
            else {
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['news']);
                $seoURL = $GLOBALS['PHPShopSeoPro']->setLatin($row['zag']);
                $PHPShopOrm->update(array("news_seo_name_new" => "$seoURL"), array("id=" => $row["id"]));

                $dis = str_replace("news/ID_" . $row['id'] . ".html", "news/" . PHPShopString::toLatin($row['zag']) . ".html", $dis);
            }

            return $dis;
        }
    }
}

$addHandler = array
    (
    'index' => 'index_newselement_seourl_hook'
);
?>