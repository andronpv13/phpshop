<?php

/**
 * SEO навигация для страницы, сео роутинг
 */
function index_seourl_hook($obj, $row, $rout) {
    global $seourl_option;

    if ($rout == 'START') {

        // Проверяем каталоги или страницы
        $seo_name = explode(".", str_replace("/page/", "", $obj->PHPShopNav->objNav['truepath']));


        // Блокировка ссылок .html.html
        if (count($seo_name) > 2) {
            $obj->setError404();
            return true;
        }

        if (!empty($seo_name[0])) {

            $PHPShopOrm = new PHPShopOrm();
            $PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['page_categories'];
            $PHPShopOrm->mysql_error = false;

            $result = $PHPShopOrm->select(array('id, name, page_cat_seo_name'), array('page_cat_seo_name' => "='" . PHPShopSecurity::TotalClean($seo_name[0]) . "'"));

            // Каталог
            if (!empty($result['id'])) {

                // Навигация SEO
                $obj->navigation_seourl_array[$result['id']] = $result['page_cat_seo_name'];
                $obj->category_name = $result['name'];
                $obj->category = $result['id'];
                $obj->PHPShopCategory = new PHPShopPageCategory($obj->category);
                $obj->PHPShopNav->objNav['id'] = $result['id'];

                $PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['page'];
                $dataArray = $obj->PHPShopOrm->select(array('*'), array('category' => '=' . $obj->category, 'enabled' => "='1'"), array('order' => 'num'), array('limit' => 100));
                if (count($dataArray) != 1 || empty($dataArray)) {
                    $PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['page_categories'];
                    $obj->CID();

                    return true;
                } else {
                    // Страница
                    displayPage($obj, $dataArray[0]['link']);

                    return true;
                }
            }

            // Страница
            else {

                // Редирект без .html
                if (strstr($_SERVER['REQUEST_URI'], ".html") and $GLOBALS['PHPShopSeoPro']->getSettings()['html_enabled'] == 2) {
                    header('Location: ' . $obj->getValue('dir.dir') . str_replace('.html', '', $_SERVER['REQUEST_URI']), true, 301);
                    return true;
                }
            }
        }
    }

    if ($rout == 'END' and $row['category'] != 1000 and ! empty($row['category']))
        navigation_seourl($obj, $row['name'], ['url' => '/page/', 'name' => __('Блог')]);
}

/**
 * SEO навигация для списка страниц 
 */
function ListPage_seourl_hook($obj, $row, $rout) {


    // Настройки модуля из кеша
    $seourl_option = $GLOBALS['PHPShopSeoPro']->getSettings();
    if ($seourl_option['seo_page_enabled'] != 2)
        return false;

    // Проверка уникальности SEO ссылки
    if ($rout == 'START') {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page_categories']);
        $seoURL = $PHPShopOrm->select(array("page_cat_seo_name"), array("id=" => $obj->PHPShopNav->getId()));

        $url = $obj->PHPShopNav->getName(true);
        if (!empty($seoURL['page_cat_seo_name']))
            $url_true = $seoURL['page_cat_seo_name'];
        else
            $url_true = $GLOBALS['PHPShopSeoPro']->setLatin($obj->category_name);

        $url_pack = 'CID_' . $obj->PHPShopNav->getId();

        // Если ссылка не сходится
        if ($url != $url_true and $url != $url_pack) {
            $obj->ListInfoItems = parseTemplateReturn($obj->getValue('templates.error_page_forma'));
            $obj->setError404();
            return true;
        }

        // Редирект со старых ссылок
        elseif ($url == $url_pack) {

            // Убираем окончание .html
            if ($GLOBALS['PHPShopSeoPro']->getSettings()['html_enabled'] == 1)
                $html = '.html';
            else
                $html = null;

            header('Location: ' . $url_true . $html, true, 301);
            return true;
        }
        // Редирект без .html
        elseif ($url == $url_true and strstr($_SERVER['REQUEST_URI'], ".html") and $GLOBALS['PHPShopSeoPro']->getSettings()['html_enabled'] == 2) {
            header('Location: ' . $obj->getValue('dir.dir') . $url_true, true, 301);
            return true;
        }
    }

    if ($rout == 'END') {
        navigation_seourl($obj, $obj->category_name, ['url' => '/page/', 'name' => __('Блог')]);
    }
}

/**
 * SEO Навигация списка каталогов
 */
function ListCategory_seourl_hook($obj, $dataArray, $rout) {

    // Проверка уникальности SEO ссылки
    if ($rout == 'END') {
        $dis = null;

        // Настройки модуля из кеша
        $seourl_option = $GLOBALS['PHPShopSeoPro']->getSettings();
        if ($seourl_option['seo_page_enabled'] != 2)
            return false;

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page_categories']);
        $seoURL = $PHPShopOrm->select(array("page_cat_seo_name"), array("id=" => $obj->PHPShopNav->getId()));

        $url = $obj->PHPShopNav->getName(true);
        if (!empty($seoURL['page_cat_seo_name']))
            $url_true = $seoURL['page_cat_seo_name'];
        else
            $url_true = $GLOBALS['PHPShopSeoPro']->setLatin($obj->category_name);

        $url_pack = 'CID_' . $obj->PHPShopNav->getId();

        // Если ссылка не сходится
        if ($url != $url_true and $url != $url_pack) {
            $obj->ListInfoItems = parseTemplateReturn($obj->getValue('templates.error_page_forma'));
            $obj->setError404();
            return true;
        } elseif ($url == $url_pack) {

            header('Location: ' . $url_true . '.html', true, 301);
            return true;
        }

        if (is_array($dataArray))
            foreach ($dataArray as $row) {
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page_categories']);
                $result = $PHPShopOrm->select(array("page_cat_seo_name"), array("id=" => $row['id']));
                if (!empty($result['page_cat_seo_name']))
                    $seoURL = $result['page_cat_seo_name'];
                else {
                    $seoURL = $GLOBALS['PHPShopSeoPro']->setLatin($row['name']);
                    $PHPShopOrm->update(array('page_cat_seo_name_new' => "$seoURL"), array('id=' => $row['id']));
                }
                $dis .= PHPShopText::li($row['name'], '/page/' . $seoURL . '.html');
            }

        $disp = PHPShopText::ul($dis);
        $obj->set('pageContent', $disp);

        // SEO хлебные крошки
        navigation_seourl($obj, $obj->category_name, ['url' => '/page/', 'name' => __('Блог')]);
        if (!empty($_GET['mobile']) and $_GET['mobile'] == 'true' and ! empty($GLOBALS['SysValue']['base']['mobile']['mobile_system'])) {
            header('Location: ' . $obj->getValue('dir.dir') . '/page/CID_' . $obj->PHPShopNav->getId() . '.html', true, 302);
            return true;
        }
    }
}

class PHPShopSeoPageCategoryArray extends PHPShopArray {

    function __construct($sql = false) {

        // Мультибаза
        if (defined("HostID"))
            $sql['servers'] = " REGEXP 'i" . HostID . "i'";

        $this->objSQL = $sql;

        $this->objBase = $GLOBALS['SysValue']['base']['page_categories'];
        $this->order = array('order' => 'num');
        parent::__construct("id", "name", "parent_to", "page_cat_seo_name");
    }

}

/**
 * SEO Навигация хлебных крошек
 */
function navigation_seourl($obj, $name, $title = false) {
    global $SysValue;

    $dis = null;

    $PHPShopSeoPageCategoryArray = new PHPShopSeoPageCategoryArray();

    $seourl_option = $GLOBALS['PHPShopSeoPro']->getSettings();

    // Настройки модуля из кеша
    if ($seourl_option['seo_page_enabled'] != 2)
        return false;

    // Шаблоны разделителя навигации
    $elementTemplate = $SysValue['dir']['templates'] . chr(47) . $_SESSION['skin'] . chr(47) . $obj->getValue('templates.breadcrumbs_splitter');
    $lastElemTemplate = $SysValue['dir']['templates'] . chr(47) . $_SESSION['skin'] . chr(47) . $obj->getValue('templates.breadcrumbs_splitter_last');

    if ((bool) PHPShopParser::check($obj->getValue('templates.breadcrumbs_splitter'), 'breadcrumbElemTitle') === false) {
        $elementTemplate = './phpshop/lib/templates/breadcrumbs/breadcrumbs_splitter.tpl';
    }
    $lastTemplatePath = $obj->getValue('templates.breadcrumbs_splitter_last');
    if (empty($lastTemplatePath) || PHPShopParser::checkFile($lastElemTemplate, true) === false) {
        $lastElemTemplate = './phpshop/lib/templates/breadcrumbs/breadcrumbs_last.tpl';
    }

    $currentIndex = 2;
    $home = ParseTemplateReturn($obj->getValue('templates.breadcrumbs_home'));
    if (empty($home))
        $home = PHPShopText::a('/', __('Главная'));

    if (is_array($obj->navigation_array))
        $arrayPath = array_reverse($obj->navigation_array);

    if (!empty($arrayPath) and is_array($arrayPath)) {
        foreach ($arrayPath as $v) {
            $seo = $PHPShopSeoPageCategoryArray->getParam($v['id'] . '.page_cat_seo_name');
            $obj->set('breadcrumbElemTitle', $v['name']);
            $obj->set('breadcrumbElemIndex', $currentIndex++);
            if (empty($seo)) {
                $obj->set('breadcrumbElemLink', '/page/' . $GLOBALS['PHPShopSeoPro']->setLatin($v['name']) . '.html');
            } else {
                $obj->set('breadcrumbElemLink', '/page/' . $seo . '.html');
            }

            $dis .= ParseTemplateReturn($elementTemplate, true, $obj->template_debug);
        }
    }

    // Указан массив родителя
    if (empty($dis) and is_array($title)) {
        $obj->set('breadcrumbElemLink', $title['url']);
        $obj->set('breadcrumbElemTitle', $title['name']);
        $obj->set('breadcrumbElemIndex', $currentIndex++);

        $home .= ParseTemplateReturn($elementTemplate, true, $obj->template_debug);
    }

    $obj->set('breadcrumbElemTitle', $name);

    $dis = $home . $dis . '' . ParseTemplateReturn($lastElemTemplate, true, $obj->template_debug);

    $obj->set('breadCrumbs', $dis);
}

function displayPage($obj, $link) {

    // Настройки модуля из кеша
    $seourl_option = $GLOBALS['PHPShopSeoPro']->getSettings();
    if ($seourl_option['seo_page_enabled'] != 2)
        return false;

    // Безопасность
    if (empty($link))
        $link = PHPShopSecurity::TotalClean($obj->PHPShopNav->getName(true), 2);

    // Страницы только для аторизованных
    if (isset($_SESSION['UsersId'])) {
        $sort = " and ((secure !='1') OR (secure ='1' AND secure_groups='') OR (secure ='1' AND secure_groups REGEXP 'i" . $_SESSION['UsersStatus'] . "-1i')) ";
    } else {
        $sort = " and (secure !='1') ";
    }


    // Мультибаза
    if (defined("HostID")) {
        $sort .= " and servers REGEXP 'i" . HostID . "i'";
    } elseif (defined("HostMain"))
        $sort .= " and (servers = '' or servers REGEXP 'i1000i')";

    $PHPShopOrm = new PHPShopOrm();
    $PHPShopOrm->debug = $obj->debug;
    $result = $PHPShopOrm->query("select * from " . $obj->objBase . " where link='$link' and enabled='1' $sort limit 1");
    $row = mysqli_fetch_array($result);

    // Прикрываем страницу от дубля
    if ($row['category'] == 2000)
        return $obj->setError404();
    elseif (empty($row['id']))
        return $obj->setError404();

    $obj->category = $row['category'];
    $obj->PHPShopCategory = new PHPShopPageCategory($obj->category);
    $obj->category_name = $obj->PHPShopCategory->getName();

    // Определяем переменные
    $obj->set('isPage', true);
    $obj->set('pageContent', Parser(stripslashes($row['content'])));
    $obj->set('pageTitle', $row['name']);
    $obj->set('catalogCategory', $obj->category_name);
    $obj->set('catalogId', $obj->category);
    $obj->PHPShopNav->objNav['id'] = $row['id'];

    // Выделяем меню раздела
    $obj->set('NavActive', $row['link']);

    // Однотипные товары
    $obj->odnotip($row);

    // Мета
    if (empty($row['title']))
        $title = $row['name'] . " - " . $obj->PHPShopSystem->getValue("name");
    else
        $title = $row['title'];

    $obj->title = $title;
    $obj->description = $row['description'];
    $obj->keywords = $row['keywords'];
    $obj->lastmodified = $row['datas'];

    // Навигация хлебные крошки
    navigation_seourl($obj, $obj->category_name, ['url' => '/page/', 'name' => __('Блог')]);

    // Подключаем шаблон
    $obj->parseTemplate($obj->getValue('templates.page_page_list'));
}

$addHandler = array(
    'ListCategory' => 'ListCategory_seourl_hook',
    'ListPage' => 'ListPage_seourl_hook',
    'index' => 'index_seourl_hook'
);
?>