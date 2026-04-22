<?php

function query_multibase($obj) {

    $multi_cat = null;

    // Мультибаза
    if (defined("HostID") or defined("HostMain")) {

        // Основные каталоги
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['servers'] = ' ="" or servers REGEXP "i1000i"';

        $multi_cat = array();
        $multi_dop_cat = null;
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
        $PHPShopOrm->debug = $obj->debug;
        $data = $PHPShopOrm->select(array('id'), $where, false, array('limit' => 10000));

        if (is_array($data)) {
            foreach ($data as $row) {
                $multi_cat[] = $row['id'];
                $multi_dop_cat .= " or dop_cat REGEXP '#" . $row['id'] . "#'";
            }
        }

        $multi_select = ' and ( category IN (' . @implode(',', $multi_cat) . ')' . $multi_dop_cat . ')';

        return $multi_select;
    }
}

/**
 * Составление SQL запроса для поиска товара
 * @author PHPShop Software
 * @version 1.4
 * @package PHPShopCoreFunction
 * @param obj $obj объект класса
 * @return mixed
 */
function query_filter($obj) {
    global $SysValue;

    if (!empty($_REQUEST['v']))
        $v = $_REQUEST['v'];
    else
        $v = null;

    if (!empty($_REQUEST['pole']))
        $pole = intval($_REQUEST['pole']);
    else
        $pole = $obj->PHPShopSystem->getSerilizeParam('admoption.search_pole');

    if (empty($pole))
        $pole = 1;

    if (!empty($_REQUEST['p']))
        $p = intval($_REQUEST['p']);
    else
        $p = 1;

    if (!empty($_REQUEST['cat']))
        $cat = intval($_REQUEST['cat']);
    else
        $cat = null;

    $num_row = $obj->num_row;
    $num_ot = $q = 0;

    $sortV = $sort = null;

    // Сортировка по характеристикам
    if (empty($_POST['v']))
        @$v = $SysValue['nav']['query']['v'];
    if (is_array($v))
        foreach ($v as $key => $value) {
            if (!empty($value)) {
                $hash = $key . "-" . $value;
                $sortV .= " and vendor REGEXP 'i" . $hash . "i' ";
            }
        }

    // Чистка запроса Secure Fix
    $words = PHPShopSecurity::true_search(PHPShopSecurity::TotalClean($_REQUEST['words'], 2), true);

    // Разделяем слова
    $_WORDS = explode(" ", $words);

    $wrd = null;
    foreach ($_WORDS as $w)
        $wrd .= '%' . $w;

    $wrd .= '%';

    switch ($pole) {
        case(1):
            $sort .= "(name LIKE '$wrd' or keywords LIKE '$wrd' or id LIKE '$wrd') and ";
            break;

        case(2):

            // Учет модуля ProductOption
            if (!empty($GLOBALS['SysValue']['base']['productoption']['productoption_system']))
                $sort .= "(name LIKE '$wrd' or content LIKE '$wrd' or description LIKE '$wrd' or keywords LIKE '$wrd' or uid LIKE '$wrd' or option1 LIKE '$wrd' or option2 LIKE '$wrd' or option3 LIKE '$wrd' or option4 LIKE '$wrd' or option5 LIKE '$wrd') and ";
            else
                $sort .= "(name LIKE '$wrd' or content LIKE '$wrd' or description LIKE '$wrd' or keywords LIKE '$wrd' or uid LIKE '$wrd') and ";

            break;
    }


    $sort = substr($sort, 0, strlen($sort) - 4);

    // По категориям
    if (!empty($cat) and ! defined("HostID"))
        $string = " category=$cat and";
    else
        $string = null;

    // Перенаправление поиска
    $prewords = search_base($obj, $words);

    // Мультибаза
    $multibase = query_multibase($obj);

    // Все страницы
    if ($p == "all") {
        $sql = "select * from " . $SysValue['base']['products'] . " where $sort $prewords $multibase and enabled='1' and parent_enabled='0' order by num desc, items desc";
    } else
        while ($q < $p) {

            $sql = "select * from " . $SysValue['base']['products'] . " where  $string ($sort) $prewords $sortV $multibase and enabled='1' and parent_enabled='0' order by name LIKE '$wrd' desc, content LIKE '$wrd' desc, description LIKE '$wrd' desc, num desc, items desc LIMIT $num_ot, $num_row";
            $q++;
            $num_ot = $num_ot + $num_row;
        }

    // SQL для выборки по id товаров, найденных Яндекс.Поиском. Если нет переадресации поиска.
    if ($obj->isYandexSearch and empty($prewords)) {
        $sql = getYandexSearchSql($obj, $words, $p, $multibase, $cat);
    }

    if ($obj->isYandexSearchCloud and empty($prewords) and empty($_REQUEST['ajax'])) {
        $sql = getYandexSearchCloudSql($words, $multibase);
    }

    $obj->search_order = array(
        'words' => $words,
        'pole' => $pole,
        'cat' => $cat,
        'string' => $string,
        'sort' => $sort,
        'prewords' => $prewords,
        'sortV' => $sortV
    );

    $obj->set('searchString', $words);

    if ($pole == 1)
        $obj->set('searchSetC', 'checked');
    else
        $obj->set('searchSetD', 'checked');

    // Возвращаем SQL запрос
    return $sql;
}

/**
 * Yandex Search Cloud
 * @param string $search
 * @param string $multibase
 * @return string
 */
function getYandexSearchCloudSql($search, $multibase = null) {

    $YandexSearch = new YandexSearch();
    $site=$_SERVER['SERVER_NAME'];
    //$site='myphpshop.ru';

    // Учет модуля SEOURLPRO
    if (!empty($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'])) {
        $site.='/id/';
    }
    else {
        $site.='/shop/';
    }

    $result = $YandexSearch->search(PHPShopString::win_utf8($search) . ' site:'.$site);

    if (is_array($result)) {
        $ids = array();
        foreach ($result as $document) {

            $id_seo = preg_replace('#^.*/id/.*-(.*)\.html$#', '$1', $document['url']);
            $id = preg_replace('#^.*/shop/UID_(.*)\.html$#', '$1', $document['url']);

            if (!empty($id_seo))
                $ids[] = $id_seo;
            elseif (!empty($id))
                $ids[] = $id;
        }

        if (is_array($ids))
            return "select * from " . $GLOBALS['SysValue']['base']['products'] . " where id IN (" . implode(',', $ids) . ") $multibase  and enabled='1' and parent_enabled='0' order by num desc, items desc";
    }
}

/**
 * Yandex Search
 * @param PHPShopSearch $obj
 * @param string $search
 * @param int $p
 * @param string $multibase
 * @param int $cat
 */
function getYandexSearchSql($obj, $search, $p, $multibase = null, $cat = 0) {
    global $SysValue;

    if (isset($_REQUEST['ajax'])) {
        $_WORDS = explode(" ", $search);

        // Убираем дублирование фразы на другой раскладке.
        $wordsCount = count($_WORDS);
        if ($wordsCount > 1) {
            $search = '';
            for ($i = 0; $i < $wordsCount / 2; $i++) {
                $search .= ' ' . $_WORDS[$i];
            }
        }
    }

    $params = array(
        'apikey' => $obj->yandexSearchAPI,
        'searchid' => $obj->yandexSearchId,
        'text' => PHPShopString::win_utf8($search),
        'page' => $p - 1,
        'per_page' => $obj->num_row
    );

    if ((int) $cat > 0) {
        $params['category_id'] = $cat;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, PHPShopSearch::YANDEX_SEARCH_API_URL . '?' . http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = json_decode(curl_exec($ch), 1);

    $obj->set('hideSearchType', 'hidden');
    if (is_array($data['misspell']['misspell'])) {
        $obj->set('searchMisspell', __('Может быть, Вы искали: ') . '«<a href="/search?words=' . PHPShopString::utf8_win1251($data['misspell']['misspell']['text']) . '">' . PHPShopString::utf8_win1251($data['misspell']['misspell']['text']) . '</a>».');
    } else {
        $obj->set('searchMisspell', '');
    }

    if (is_array($data['documents'])) {
        $ids = array();
        foreach ($data['documents'] as $document) {
            $ids[] = $document['id'];
        }

        return "select * from " . $SysValue['base']['products'] . " where id IN (" . implode(',', $ids) . ") $multibase and enabled='1' and parent_enabled='0' order by num desc, items desc";
    }

    return null;
}

/**
 * Выдача переадресации поиска из БД
 * @package PHPShopCoreFunction
 * @param obj $obj объект класса
 * @param string $words строка поиска
 * @return string 
 */
function search_base($obj, $words) {
    $string = null;

    $PHPShopOrm = new PHPShopOrm();
    $PHPShopOrm->mysql_error = false;
    $PHPShopOrm->debug = $obj->debug;
    $result = $PHPShopOrm->query("select * from " . $GLOBALS['SysValue']['base']['search_base'] . " where name REGEXP 'i" . PHPShopSecurity::true_search($words) . "i'  and enabled='1' limit 1");
    $row = mysqli_fetch_array($result);

    // Переадресация на товары
    if (!empty($row['uid'])) {

        $uid = $row['uid'];
        if (strstr($row['uid'], ','))
            $uids = explode(",", $uid);
        else
            $uids[] = $uid;

        if (is_array($uids))
            $string = ' or id IN (' . @implode(",", $uids) . ') ';

        return $string;
    }
    // Переадресация на категорию
    else if (!empty($row['category'])) {
        header('Location: /' . $GLOBALS['dir']['dir'] . 'shop/CID_' . $row['category'] . '.html');
    } else if (!empty($row['url']) and empty($_REQUEST['ajax'])) {
        header('Location: http://' . $GLOBALS['dir']['dir'] . $row['url']);
    }
}

?>