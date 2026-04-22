<?php

function toLatin_hook($str) {
    $str = strtolower($str);
    $str = str_replace("&nbsp;", "", $str);
    $str = str_replace("/", "", $str);
    $str = str_replace("\\", "", $str);
    $str = str_replace("(", "", $str);
    $str = str_replace(")", "", $str);
    $str = str_replace(":", "", $str);
    $str = str_replace("-", "", $str);
    $str = str_replace(" ", "_", $str);
    $str = str_replace("!", "", $str);
    $str = str_replace("|", "_", $str);
    $str = str_replace(".", "_", $str);
    $str = str_replace("№", "N", $str);
    $str = str_replace("?", "", $str);
    $str = str_replace("&nbsp", "_", $str);
    $str = str_replace("&amp;", '_', $str);
    $str = str_replace("ь", "", $str);
    $str = str_replace("Ь", "", $str);
    $str = str_replace("ъ", "", $str);
    $str = str_replace("«", "", $str);
    $str = str_replace("»", "", $str);
    $str = str_replace("“", "", $str);
    $str = str_replace(",", "", $str);
    $str = str_replace("™", "", $str);
    $str = str_replace("’", "", $str);
    $str = str_replace("®", "", $str);

    $new_str = '';
    $_Array = array(" " => "_", "а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ё" => "e", "ж" => "zh", "з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l", "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r", "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h", "ц" => "c", "ч" => "ch", "ш" => "sh", "щ" => "sch", "ъ" => "i", "ы" => "y", "ь" => "i", "э" => "e", "ю" => "u", "я" => "ya", "А" => "a", "Б" => "b", "В" => "v", "Г" => "g", "Д" => "d", "Е" => "e", "Ё" => "e", "Ж" => "zh", "З" => "z", "И" => "i", "Й" => "y", "К" => "k", "Л" => "l", "М" => "m", "Н" => "n", "О" => "o", "П" => "p", "Р" => "r", "С" => "s", "Т" => "t", "Ы" => "Y", "У" => "u", "Ф" => "f", "Х" => "h", "Ц" => "c", "Ч" => "ch", "Ш" => "sh", "Щ" => "sch", "Э" => "e", "Ю" => "u", "Ы" => "y", "Я" => "ya", "." => "_", "$" => "i", "%" => "i", "&" => "_and_");

    $chars = preg_split('//', $str, -1, PREG_SPLIT_NO_EMPTY);

    foreach ($chars as $val)
        if (empty($_Array[$val]))
            $new_str .= $val;
        else
            $new_str .= $_Array[$val];

    return preg_replace('([^a-z0-9_\.-])', '', $new_str);
}

/*
 * SEO обработка ссылок в списке товаров /shop/
 */

function CID_Product_seourlpro_hook($obj, $row, $rout) {

    $catalog_name = $obj->PHPShopCategory->getName();
    $seo_name = $obj->PHPShopCategory->getParam('cat_seo_name');
    $obj->seo_name = $seo_name;

    // Проверка уникальности SEO ссылки
    if ($rout == 'START') {

        // Поддержка сложных ссылок /cat/*
        $obj->objPath = '/CID_' . $obj->category . '_';

        if (!empty($seo_name))
            $url_true = '/' . $seo_name;
        else
            $url_true = '/' . $GLOBALS['PHPShopSeoPro']->setLatin($catalog_name);

        $url = $obj->PHPShopNav->getName(true);

        // Учет первой страницы
        if ($obj->PHPShopNav->getPage() == 1)
            $url_true_nav = $url_true;
        else
            $url_true_nav = $url_true . '-' . $obj->PHPShopNav->getPage();


        $url_pack = '/shop/CID_' . $obj->PHPShopNav->getId();
        $url_nav = '/shop/CID_' . $obj->PHPShopNav->getId() . '_' . $obj->PHPShopNav->getPage();
        $url_old_seo = '/shop/CID_' . $obj->PHPShopNav->getId() . '_' . str_replace("-", "_", toLatin_hook($catalog_name));
        if ($obj->PHPShopNav->getId())
            $url_old_seo_nav = '/shop/CID_' . $obj->PHPShopNav->getId() . '_' . $obj->PHPShopNav->getPage() . '_' . str_replace("-", "_", toLatin_hook($catalog_name));

        // Query
        if (!empty($_SERVER["QUERY_STRING"]))
            $url_query = '?' . $_SERVER["QUERY_STRING"];
        else
            $url_query = null;


        // Если ссылка не сходится
        if ($url != $url_true and $url != $url_pack and $url != $url_true_nav and $url != $url_nav and $url != $url_old_seo and $url != $url_old_seo_nav) {
            $obj->ListInfoItems = parseTemplateReturn($obj->getValue('templates.error_page_forma'));
            $obj->set('breadCrumbs', null);
            $obj->set('odnotipDisp', null);
            $obj->setError404();
            return true;
        } 
        
        // Редирект со старых ссылок
        elseif ($url == $url_pack or $url == $url_nav or $url == $url_old_seo or $url == $url_old_seo_nav) {

            // Убираем окончание .html
            if ($GLOBALS['PHPShopSeoPro']->getSettings()['html_enabled'] == 1)
                $html = '.html';
            else
                $html = null;


            if ($url_true != '/')
                header('Location: ' . $obj->getValue('dir.dir') . $url_true_nav . $html . $url_query, true, 301);
            else
                $obj->setError404();
            return true;
        }
        
        // Редирект без .html
        elseif($url == $url_true and (strstr($_SERVER['REQUEST_URI'],".html") and !strstr($_SERVER['REQUEST_URI'],".html/filters/")) and $GLOBALS['PHPShopSeoPro']->getSettings()['html_enabled'] == 2){
            header('Location: ' . $obj->getValue('dir.dir') . $url_true, true, 301);
            return true;
        }
    }


    if ($rout == 'END') {
        // Учет модуля Mobile
        if (!empty($_GET['mobile']) and $_GET['mobile'] == 'true' and ! empty($GLOBALS['SysValue']['base']['mobile']['mobile_system'])) {
            header('Location: ' . $obj->getValue('dir.dir') . '/shop/CID_' . $obj->PHPShopNav->getId() . '.html', true, 302);
            return true;
        }
    }
}

/**
 * SEO Навигация списка каталогов
 */
function CID_Category_seourlpro_hook($obj, $dataArray, $rout) {

    $catalog_name = $obj->PHPShopCategory->getName();
    $seo_name = $obj->PHPShopCategory->getParam('cat_seo_name');

    if ($rout == 'START') {

        if (!empty($seo_name))
            $url_true = '/' . $seo_name;
        else
            $url_true = '/' . $GLOBALS['PHPShopSeoPro']->setLatin($catalog_name);

        // Учет первой страницы
        if ($obj->PHPShopNav->getPage() == 1)
            $url_true_nav = $url_true;
        else
            $url_true_nav = $url_true . '-' . $obj->PHPShopNav->getPage();

        $url = $obj->PHPShopNav->getName(true);
        $url_pack = '/shop/CID_' . $obj->PHPShopNav->getId();
        $url_old_seo = '/shop/CID_' . $obj->PHPShopNav->getId() . '_' . str_replace("-", "_", toLatin_hook($catalog_name));
        $url_nav = '/shop/CID_' . $obj->PHPShopNav->getId() . '_' . $obj->PHPShopNav->getPage();

        if ($obj->PHPShopNav->getId())
            $url_old_seo_nav = '/shop/CID_' . $obj->PHPShopNav->getId() . '_' . $obj->PHPShopNav->getPage() . '_' . str_replace("-", "_", toLatin_hook($catalog_name));

        // Query
        if (!empty($_SERVER["QUERY_STRING"]))
            $url_query = '?' . $_SERVER["QUERY_STRING"];
        else
            $url_query = null;

        // Если ссылка не сходится
        if ($url != $url_true and $url != $url_pack and $url != $url_true_nav and $url != $url_nav and $url != $url_old_seo and $url != $url_old_seo_nav) {
            $obj->ListInfoItems = parseTemplateReturn($obj->getValue('templates.error_page_forma'));
            $obj->set('breadCrumbs', null);
            $obj->set('odnotipDisp', null);
            $obj->setError404();
            return true;
        } 
        // Редирект со старых ссылок
        elseif ($url == $url_pack or $url == $url_old_seo) {

            // Убираем окончание .html
            if ($GLOBALS['PHPShopSeoPro']->getSettings()['html_enabled'] == 1)
                $html = '.html';
            else
                $html = null;

            header('Location: ' . $obj->getValue('dir.dir') . $url_true . $html . $url_query, true, 301);
            return true;
        }
        // Редирект без .html
        elseif($url == $url_true and (strstr($_SERVER['REQUEST_URI'],".html") and !strstr($_SERVER['REQUEST_URI'],".html/filters/")) and $GLOBALS['PHPShopSeoPro']->getSettings()['html_enabled'] == 2){
            header('Location: ' . $obj->getValue('dir.dir') . $url_true, true, 301);
            return true;
        }
        
    }

    if ($rout == 'END') {

        if (is_array($dataArray))
            foreach ($dataArray as $row) {

                if (!empty($row['cat_seo_name']))
                    $GLOBALS['PHPShopSeoPro']->setMemory($row['id'], $row['cat_seo_name'], 1, false);
                else
                    $GLOBALS['PHPShopSeoPro']->setMemory($row['id'], $row['name']);
            }

        seoPaginatorFeatures($obj);

        // Учет модуля Mobile
        if (!empty($_GET['mobile']) and $_GET['mobile'] == 'true' and ! empty($GLOBALS['SysValue']['base']['mobile']['mobile_system'])) {
            header('Location: ' . $obj->getValue('dir.dir') . '/shop/CID_' . $obj->PHPShopNav->getId() . '.html', true, 302);
            return true;
        }
    }
}

/**
 * Проверка уникальности SEO имени товара
 */
function UID_seourlpro_hook($obj, $row, $rout) {
    if ($rout == 'END') {
        if (!empty($row['prod_seo_name']))
            $url_true = '/id/' . $row['prod_seo_name'] . '-' . $row['id'];
        else
            $url_true = '/id/' . $GLOBALS['PHPShopSeoPro']->setLatin($row['name']) . '-' . $row['id'];


        $url = $obj->PHPShopNav->getName(true);
        $url_pack = '/shop/UID_' . $obj->PHPShopNav->getId();
        $url_old_seo = '/shop/UID_' . $obj->PHPShopNav->getId() . '_' . str_replace("-", "_", toLatin_hook($row['name']));

        // Если ссылка не сходится
        if ($url != $url_true and $url != $url_pack and $url != $url_old_seo) {
            $obj->ListInfoItems = parseTemplateReturn($obj->getValue('templates.error_page_forma'));
            $obj->set('breadCrumbs', null);
            $obj->set('odnotipDisp', null);
            $obj->setError404();
        } 
        // Редирект со старых ссылок
        elseif ($url == $url_pack or $url == $url_old_seo) {

            // Убираем окончание .html
            if ($GLOBALS['PHPShopSeoPro']->getSettings()['html_enabled'] == 1)
                $html = '.html';
            else
                $html = null;

            header('Location: ' . $obj->getValue('dir.dir') . $url_true . $html, true, 301);
            return true;
        }
        
        // Редирект без .html
        elseif($url == $url_true and strstr($_SERVER['REQUEST_URI'],".html") and $GLOBALS['PHPShopSeoPro']->getSettings()['html_enabled'] == 2){
            header('Location: ' . $obj->getValue('dir.dir') . $url_true, true, 301);
            return true;
        }

        // Учет модуля Mobile
        if (!empty($_GET['mobile']) and $_GET['mobile'] == 'true' and ! empty($GLOBALS['SysValue']['base']['mobile']['mobile_system'])) {
            header('Location: ' . $obj->getValue('dir.dir') . '/shop/UID_' . $obj->PHPShopNav->getId() . '.html', true, 302);
            return true;
        }
    }
}

function catalog_content_hook($obj, $data) {
    seoPaginatorFeatures($obj);
}

function seoPaginatorFeatures($obj) {
    // Настройки модуля
    $seourl_option = $GLOBALS['PHPShopSeoPro']->getSettings();

    $page = $obj->PHPShopNav->getPage();

    // Рекомендации BDBD
    if ($seourl_option['paginator'] == 2) {

        if ($page > 1) {
            // Отключение описания каталога в пагинаторе
            if ($seourl_option['cat_content_enabled'] == 2) {
                $obj->set('catalogContent', null);
            }

            // Добавление номера страниц в имя каталога
            $obj->set('catalogCategory', ' - ' . __('страница') . ' ' . $page, true);
        }

        // Создание переменной точного адреса canonical для отсеивания дублей
        if (!empty($_SERVER["QUERY_STRING"])) {

            // Учет первой страницы
            if ($page > 1)
                $obj->set('seourl_canonical', '<link rel="canonical" href="http://' . $_SERVER['SERVER_NAME'] . $obj->get('ShopDir') . '/shop/CID_' . $obj->PHPShopNav->getId() . '-' . $page . '.html">');
            else
                $obj->set('seourl_canonical', '<link rel="canonical" href="http://' . $_SERVER['SERVER_NAME'] . $obj->get('ShopDir') . '/shop/CID_' . $obj->PHPShopNav->getId() . '.html">');
        }
        /*
          if (empty($page))
          $obj->set('seourl_canonical', '<link rel="canonical" href="http://' . $_SERVER['SERVER_NAME'] . $obj->get('ShopDir') . '/shop/CID_' . $obj->PHPShopNav->getId() . '-1' . $seo_name . '.html">'); */
    }
}

$addHandler = array(
    'UID' => 'UID_seourlpro_hook',
    'CID_Category' => 'CID_Category_seourlpro_hook',
    'CID_Product' => 'CID_Product_seourlpro_hook',
    'catalog_content' => 'catalog_content_hook'
);
?>