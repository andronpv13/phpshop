<?php

// Включение
$enabled = false;

if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_classPath = realpath(dirname(__FILE__)) . "/../../../";
    $enabled = true;
    $ssl = true;
} else
    $_classPath = "../../../";


include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("date");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);

// Авторизация
if ($_GET['s'] == md5($PHPShopBase->SysValue['connect']['host'] . $PHPShopBase->SysValue['connect']['dbase'] . $PHPShopBase->SysValue['connect']['user_db'] . $PHPShopBase->SysValue['connect']['pass_db']))
    $enabled = true;

if (empty($enabled))
    exit("Ошибка авторизации!");

// Настройки модуля
PHPShopObj::loadClass("modules");
$PHPShopModules = new PHPShopModules($_classPath . "modules/");

$brands = null;
$seourl_enabled = false;
$seourlpro_enabled = false;
$seo_news_enabled = false;
$seo_page_enabled = false;
$seo_brands_enabled = false;


if (!empty($_GET['hostID']))
    define("HostID", $_GET['hostID']);
else
    define("HostMain", true);

// SSL
if (isset($_GET['ssl']))
    $ssl = true;

if (!empty($ssl))
    $loc = 'https://';
else
    $loc = 'http://';

/**
 * Проверка прав каталога режима Multibase
 * @return string 
 */
function queryMultibase() {

    // Мультибаза
    if (defined("HostID") or defined("HostMain")) {


        $multi_cat = array();

        // Не выводить скрытые каталоги
        $where['skin_enabled '] = "!='1'";

        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['skin_enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
        $data = $PHPShopOrm->select(array('id'), $where, false, array('limit' => 1000), __CLASS__, __FUNCTION__);
        if (is_array($data)) {
            foreach ($data as $row) {
                $multi_cat[] = $row['id'];
            }
        }

        $multi_select = ' category IN (' . @implode(',', $multi_cat) . ') and ';

        return $multi_select;
    }
}

// Учет модуля SEOURL
if (!empty($GLOBALS['SysValue']['base']['seourl']['seourl_system'])) {
    $seourl_enabled = true;
}

// Учет модуля SEOURLPRO
if (!empty($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'])) {
    $seourlpro_enabled = true;

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system']);
    $settings = $PHPShopOrm->select(array('seo_news_enabled, seo_page_enabled', 'seo_brands_enabled', 'html_enabled'), array('id' => "='1'"));
    if ($settings['seo_news_enabled'] == 2)
        $seo_news_enabled = true;
    if ($settings['seo_page_enabled'] == 2)
        $seo_page_enabled = true;
    if ($settings['seo_brands_enabled'] == 2)
        $seo_brands_enabled = true;

    if ($settings['html_enabled'] == 2)
        $html = null;
    else
        $html = '.html';


    include_once dirname(dirname(__DIR__)) . '/seourlpro/inc/option.inc.php';
}

function sitemaptime($nowtime) {
    return PHPShopDate::dataV($nowtime, false, true);
}

// Библиотека
$title = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$title .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
$title .= '<url>' . "\n";
$title .= '<loc>' . $loc . $_SERVER['SERVER_NAME'] . '</loc>' . "\n";
$title .= '<changefreq>weekly</changefreq>' . "\n";
$title .= '<priority>1.0</priority>' . "\n";
$title .= '</url>' . "\n";


// Страницы
$where['enabled'] = "!='0'";
$where['category'] = "!=2000";

// Мультибаза
if (defined("HostID"))
    $where['servers'] = " REGEXP 'i" . HostID . "i'";
elseif (defined("HostMain"))
    $where['enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page']);
$data = $PHPShopOrm->select(array('id,datas,link'), $where, array('order' => 'datas DESC'), array('limit' => 10000));

if (is_array($data))
    foreach ($data as $row) {
        $stat_pages .= '<url>' . "\n";
        $stat_pages .= '<loc>' . $loc . $_SERVER['SERVER_NAME'] . '/page/' . $row['link'] . $html.'</loc>' . "\n";
        $stat_pages .= '<lastmod>' . sitemaptime($row['datas'], false) . '</lastmod>' . "\n";
        $stat_pages .= '<changefreq>weekly</changefreq>' . "\n";
        $stat_pages .= '<priority>1.0</priority>' . "\n";
        $stat_pages .= '</url>' . "\n";
    }

// Страницы каталоги  
unset($where);
$where = array('parent_to' => '=0');

// Мультибаза
if (defined("HostID"))
    $where['servers'] = " REGEXP 'i" . HostID . "i'";
elseif (defined("HostMain"))
    $where['parent_to'] .= ' and (servers ="" or servers REGEXP "i1000i")';
else
    $where = null;

$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page_categories']);
$data = $PHPShopOrm->select(array('*'), $where, false, array('limit' => 10000));

if (is_array($data))
    foreach ($data as $row) {

        // Стандартный url
        $url = '/page/CID_' . $row['id'];

        if ($seourl_enabled)
            $url = '/page/CID_' . $row['id'] . '_' . PHPShopString::toLatin($row['name']);

        //  SEOURLPRO
        if (!empty($seourlpro_enabled) && !empty($seo_page_enabled)) {
            if (empty($row['page_cat_seo_name']))
                $url = '/page/' . PHPShopString::toLatin($row['name']);
            else
                $url = '/page/' . $row['page_cat_seo_name'];
        }

        $stat_pages .= '<url>' . "\n";
        $stat_pages .= '<loc>' . $loc . $_SERVER['SERVER_NAME'] . $url .$html. '</loc>' . "\n";
        $stat_pages .= '<changefreq>weekly</changefreq>' . "\n";
        $stat_pages .= '<priority>0.5</priority>' . "\n";
        $stat_pages .= '</url>' . "\n";
    }

// Новости
unset($where);
$where['datau'] = '<' . time();

// Мультибаза
if (defined("HostID"))
    $where['servers'] = " REGEXP 'i" . HostID . "i'";
elseif (defined("HostMain"))
    $where['datau'] .= ' and (servers ="" or servers REGEXP "i1000i")';

$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['table_name8']);
$data = $PHPShopOrm->select(array('*'), $where, array('order' => 'datas DESC'), array('limit' => 10000));

if (is_array($data))
    foreach ($data as $row) {

        // Стандартный url
        $url = '/news/ID_' . $row['id'];

        if ($seourl_enabled)
            $url = '/news/ID_' . $row['id'] . '_' . PHPShopString::toLatin($row['zag']);

        //  SEOURLPRO
        if (!empty($seourlpro_enabled) && !empty($seo_news_enabled)) {
            if (empty($row['news_seo_name']))
                $url = '/news/' . PHPShopString::toLatin($row['zag']);
            else
                $url = '/news/' . $row['news_seo_name'];
        }

        $stat_news .= '<url>' . "\n";
        $stat_news .= '<loc>' . $loc . $_SERVER['SERVER_NAME'] . $url . $html.'</loc>' . "\n";
        $stat_news .= '<lastmod>' . sitemaptime(PHPShopDate::GetUnixTime($row['datas'])) . '</lastmod>' . "\n";
        $stat_news .= '<changefreq>daily</changefreq>' . "\n";
        $stat_news .= '<priority>0.5</priority>' . "\n";
        $stat_news .= '</url>' . "\n";
    }

// Товары
unset($where);

// Мультибаза
$queryMultibase = queryMultibase();
if (!empty($queryMultibase))
    $where .= ' ' . $queryMultibase;

$result = $PHPShopOrm->query("select * from " . $GLOBALS['SysValue']['base']['products'] . " where $where enabled='1' and parent_enabled='0' and price>0");
while ($row = mysqli_fetch_array($result)) {

    $stat_products .= '<url>' . "\n";


    // Стандартный урл
    $url = '/shop/UID_' . $row['id'];

    // SEOURL
    if (!empty($seourl_enabled))
        $url .= '_' . PHPShopString::toLatin($row['name']);

    //  SEOURLPRO
    if (!empty($seourlpro_enabled)) {
        if (empty($row['prod_seo_name']))
            $url = '/id/' . $GLOBALS['PHPShopSeoPro']->setLatin($row['name']) . '-' . $row['id'];
        else
            $url = '/id/' . $row['prod_seo_name'] . '-' . $row['id'];
    }

    $stat_products .= '<loc>' . $loc . $_SERVER['SERVER_NAME'] . $url .$html. '</loc>' . "\n";
    $stat_products .= '<lastmod>' . sitemaptime($row['datas']) . '</lastmod>' . "\n";
    $stat_products .= '<changefreq>daily</changefreq>' . "\n";
    $stat_products .= '<priority>1.0</priority>' . "\n";
    $stat_products .= '</url>' . "\n";
}

// Каталоги
unset($where);
$where['skin_enabled'] = "!='1'";

// Мультибаза
if (defined("HostID"))
    $where['servers'] = " REGEXP 'i" . HostID . "i'";
elseif (defined("HostMain"))
    $where['skin_enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
$data = $PHPShopOrm->select(array('*'), $where, false, array('limit' => 10000));

if (is_array($data))
    foreach ($data as $row) {

        // Стандартный урл
        $url = '/shop/CID_' . $row['id'];

        // SEOURL
        if ($seourl_enabled)
            $url .= '_' . PHPShopString::toLatin($row['name']);

        //  SEOURLPRO
        if (!empty($seourlpro_enabled)) {
            if (empty($row['cat_seo_name']))
                $url = '/' . str_replace("_", "-", PHPShopString::toLatin($row['name']));
            else
                $url = '/' . $row['cat_seo_name'];
        }

        $stat_products .= '<url>' . "\n";
        $stat_products .= '<loc>' . $loc . $_SERVER['SERVER_NAME'] . $url . $html.'</loc>' . "\n";
        $stat_products .= '<changefreq>weekly</changefreq>' . "\n";
        $stat_products .= '<priority>0.5</priority>' . "\n";
        $stat_products .= '</url>' . "\n";
    }

if ($seourlpro_enabled && $seo_brands_enabled) {
    $brandsOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
    $brandsIds = array();
    $result = $brandsOrm->getList(array('id'), array('brand' => '="1"'));
    foreach ($result as $value) {
        $brandsIds[] = $value['id'];
    }

    if (count($brandsIds) > 0) {
        $brandValuesOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);

        $brandValues = $brandValuesOrm->getList(array('sort_seo_name'), array(
            'category' => sprintf(' IN(%s)', implode(',', $brandsIds)),
            'sort_seo_name' => '<> ""'
        ));

        foreach ($brandValues as $brandValue) {
            $brands .= '<url>' . "\n";
            $brands .= '<loc>' . $loc . $_SERVER['SERVER_NAME'] . '/brand/' . $brandValue['sort_seo_name'] .$html. '</loc>' . "\n";
            $brands .= '<changefreq>weekly</changefreq>' . "\n";
            $brands .= '<priority>0.5</priority>' . "\n";
            $brands .= '</url>' . "\n";
        }
    }
}

$sitemap = $title . $stat_pages . $stat_news . $stat_products . $brands . '</urlset>';

if (defined("HostID"))
    $file = 'sitemap_' . HostID . '.xml';
else
    $file = 'sitemap.xml';

// Запись в файл
fwrite(@fopen('../../../../UserFiles/Files/' . $file, "w+"), $sitemap);
echo "Sitemap.xml done!";
