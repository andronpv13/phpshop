<?php

use Shuchkin\SimpleXLSXGen;

$TitlePage = __("Экспорт данных");
PHPShopObj::loadClass('sort');
PHPShopObj::loadClass('array');

// Описания полей
$key_name = array(
    'id' => 'Id',
    'name' => 'Наименование',
    'uid' => 'Артикул',
    'price' => 'Цена 1',
    'price2' => 'Цена 2',
    'price3' => 'Цена 3',
    'price4' => 'Цена 4',
    'price5' => 'Цена 5',
    'price_n' => 'Старая цена',
    'sklad' => 'Под заказ',
    'newtip' => 'Новинка',
    'spec' => 'Спецпредложение',
    'items' => 'Склад',
    'weight' => 'Вес',
    'num' => 'Приоритет',
    'enabled' => 'Вывод',
    'content' => 'Подробное описание',
    'description' => 'Краткое описание',
    'pic_small' => 'Маленькое изображение',
    'pic_big' => 'Большое изображение',
    'category' => 'Категория',
    'yml' => 'Яндекс.Маркет',
    'icon' => 'Иконка',
    'parent_to' => 'Родитель',
    'category' => 'Каталог',
    'title' => 'Заголовок',
    'login' => 'Логин',
    'tel' => 'Телефон',
    'datas' => 'Дата',
    'cumulative_discount' => 'Накопительная скидка',
    'seller' => 'Статус загрузки в 1С',
    'statusi' => 'Статус состояния заказа',
    'fio' => 'Ф.И.О',
    'city' => 'Город',
    'street' => 'Улица',
    'odnotip' => 'Сопутствующие товары',
    'page' => 'Страницы',
    'parent' => 'Подчиненные товары',
    'dop_cat' => 'Дополнительные каталоги',
    'ed_izm' => 'Единица измерения',
    'baseinputvaluta' => 'Валюта',
    'vendor_array' => 'Характеристики',
    'p_enabled' => 'Наличие в Яндекс.Маркет',
    'parent_enabled' => 'Подтип',
    'rate' => 'Рейтинг',
    'rate_count' => 'Голоса в рейтинге',
    'descrip' => 'Meta description',
    'keywords' => 'Meta keywords',
    'parent_enabled' => 'Подтип',
    'price_search' => 'Цена для поиска',
    'index' => 'Индекс',
    'fio' => 'ФИО',
    'tel' => 'Телефон',
    'street' => 'Улица',
    'house' => 'Дом',
    'porch' => 'Подъезд',
    'door_phone' => 'Домофон',
    'flat' => 'Квартира',
    'delivtime' => 'Время доставки',
    'door_phone' => 'Домофон',
    'tel' => 'Телефон',
    'house' => 'Дом',
    'porch' => 'Подъезд',
    'org_name' => 'Компания',
    'org_inn' => 'ИНН',
    'org_kpp' => 'КПП',
    'org_yur_adres' => 'Юр. адрес',
    'org_fakt_adres' => 'Факт. адрес',
    'org_ras' => 'Р/С',
    'org_bank' => 'Банк',
    'org_kor' => 'К/С',
    'org_bik' => 'БИК',
    'org_city' => 'Город',
    'dop_info' => 'Примечание покупателя',
    'status' => 'Примечание менеджера',
    'seller' => 'Загружено в CRM',
    'country' => 'Страна',
    'statusi' => 'Статус заказа',
    'status' => 'Статус пользователя',
    'state' => 'Регион/штат',
    'city' => 'Город',
    'sum' => 'Сумма',
    'user' => 'ID Пользователя',
    'orders_cart' => 'Корзина',
    'orders_email' => 'Email',
    "prod_seo_name" => 'SEO ссылка',
    'num_row' => 'Товаров в длину',
    'num_cow' => 'Товаров на странице',
    'count' => 'Содержит товаров',
    'cat_seo_name' => 'SEO ссылка каталога',
    'sum' => 'Сумма',
    'servers' => 'Витрины',
    'items1' => 'Склад 2',
    'items2' => 'Склад 3',
    'items3' => 'Склад 4',
    'items4' => 'Склад 5',
    'mail' => 'Почта',
    'data_adres' => 'Телефон',
    'color' => 'Код цвета',
    'parent2' => 'Цвет',
    'rate' => 'Рейтинг',
    'productday' => 'Товар дня',
    'hit' => 'Хит',
    'url' => 'URL',
    'path' => 'Путь каталога',
    'length' => 'Длина',
    'width' => 'Ширина',
    'height' => 'Высота',
    'moysklad_product_id' => 'МойСклад Id',
    'price_purch' => 'Закупочная цена',
    'files' => 'Файлы',
    'external_code' => 'Внешний код',
    'barcode' => 'Штрихкод',
    'productservices_products' => 'Услуги'
);

//if ($GLOBALS['PHPShopBase']->codBase == 'utf-8')
// unset($key_name);
// Стоп лист
$key_stop = array('password', 'wishlist', 'sort', 'yml_bid_array', 'vendor', 'vid', 'name_rambler', 'skin', 'skin_enabled', 'secure_groups', 'icon_description', 'title_enabled', 'title_shablon', 'descrip_shablon', 'descrip_enabled', 'productsgroup_check', 'productsgroup_product', 'keywords_enabled', 'keywords_shablon', 'rate_count');

if (empty($subpath[2]))
    $subpath[2] = null;

switch ($subpath[2]) {
    case 'catalog':
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
        $key_base = array('id', 'name', 'icon', 'parent_to');
        break;
    case 'user':
        PHPShopObj::loadClass('user');
        $PHPShopUserStatusArray = new PHPShopUserStatusArray();
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
        $key_base = array('id', 'login', 'name', 'data_adres');
        array_push($key_stop, 'tel_code', 'adres', 'inn', 'kpp', 'company', 'tel');
        break;
    case 'order':
        PHPShopObj::loadClass('order');
        $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $key_base = array('id', 'uid', 'fio', 'tel', 'datas');
        $key_name['uid'] = __('№ Заказа');
        $TitlePage .= ' ' . __('заказов');
        break;
    default: $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
        $key_base = array('id', 'name', 'uid', 'category', 'price', 'newtip', 'spec', 'items', 'enabled');
        array_push($key_stop, 'user', 'title_enabled', 'descrip_enabled', 'title_shablon', 'descrip_shablon', 'title_shablon', 'keywords_enabled', 'keywords_shablon');
        break;
}

// Память полей
if (!empty($_COOKIE['check_memory'])) {
    $memory = json_decode($_COOKIE['check_memory'], true);
    if (is_array($memory[$_GET['path']])) {
        $key_base = array_keys($memory[$_GET['path']]);
    }
}

// Добавление товар в выбор
function actionSelect() {
    global $subpath;
    unset($_SESSION['select']);

    // Выбранные товары
    if (!empty($_POST['select'])) {
        if (is_array($_POST['select'])) {
            foreach ($_POST['select'] as $k => $v)
                if (!empty($v))
                    $select[intval($k)] = intval($v);
            $_SESSION['select'][$subpath[2]] = $select;
        }
    }

    return array("success" => true);
}

// Проверка ключей для implode
function implodeCheck(&$value) {
    $value = intval($value);
}

// Проверка полей
function patternCheck(&$value) {
    $value = "`" . $value . "`";
}

// Разбор сериализованных полей
function serializeSelect($str, $cat) {
    $delim = $_POST['export_delim'];
    $sortdelim = $_POST['export_sortdelim'];
    $array_line = $csv_line = null;
    $cols_array = unserialize($str);

    if (is_array($cols_array)) {

        // Запомимаем каталог
        if (empty($GLOBALS['sort_cat']))
            $GLOBALS['sort_cat'] = $cat;
        elseif ($sortdelim == ';' and $GLOBALS['sort_cat'] != $cat)
            return true;

        // Заголовки
        $key = array_keys($cols_array);
        array_walk_recursive($key, 'implodeCheck');
        $idcat_list = implode(',', $key);


        if (!empty($idcat_list)) {
            $where = array('id' => ' IN (' . $idcat_list . ')');

            $PHPShopSortCategoryArray = new PHPShopSortCategoryArray($where);
            $data = $PHPShopSortCategoryArray->getArray();
        }

        if (is_array($data)) {

            foreach ($cols_array as $k => $v) {
                if (is_array($v)) {

                    // Значения
                    $val = array_values($v);
                    array_walk_recursive($val, 'implodeCheck');
                    $id_list = implode(',', $val);
                    if (!empty($id_list)) {
                        $where = array('id' => ' IN (' . $id_list . ')');
                        $PHPShopSortArray = new PHPShopSortArray($where);
                        $data_v = $PHPShopSortArray->getArray();
                    }

                    $array_line_value = null;
                    foreach ($v as $a_v) {
                        if ($sortdelim != ';') {
                            $array_line .= $data[$k]['name'] . '/' . $data_v[$a_v]['name'] . $sortdelim;
                        } else {
                            $array_line_value .= $data_v[$a_v]['name'] . ',';
                        }
                    }

                    if ($sortdelim == ';') {

                        // Создаем новую колонку
                        if (empty($GLOBALS['sort_col_name'][$k])) {
                            $GLOBALS['sort_col_name'][$data[$k]['name']] = $data_v[$a_v]['name'];
                        }

                        $array_line .= '"' . substr($array_line_value, 0, (strlen($array_line_value) - 1)) . '"' . $delim;
                    }
                }
            }

            if ($sortdelim != ';')
                $csv_line .= '"' . substr($array_line, 0, (strlen($array_line) - 1)) . '"' . $delim;
            else
                $csv_line .= $array_line;
        }
    }

    if (empty($csv_line))
        $csv_line = '""' . $delim;

    return $csv_line;
}

// Функция выгрузки
function actionSave() {
    global $key_name, $subpath, $PHPShopOrderStatusArray, $PHPShopUserStatusArray, $PHPShopGUI, $csv_title, $_classPath, $csv_export_count;

    // Экспорт только выбранных
    $select_action_path = $subpath[2];
    if (empty($select_action_path))
        $select_action_path = 'product';

    // Выбрать настройку
    if ($_POST['exchanges'] != 'new') {
        $PHPShopOrmExchanges = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges']);

        // Изменить имя настройки
        if (!empty($_POST['exchanges_new'])) {
            $PHPShopOrmExchanges->update(array('name_new' => $_POST['exchanges_new']), array('id' => '=' . intval($_POST['exchanges'])));
        }

        $data_exchanges = $PHPShopOrmExchanges->select(array('*'), array('id' => '=' . intval($_POST['exchanges'])), false, array("limit" => 1));
        if (is_array($data_exchanges)) {
            unset($_POST);
            $_POST = unserialize($data_exchanges['option']);
            unset($_POST['exchanges_new']);

            $_SESSION['select'][$select_action_path] = $_POST['export_select'][$select_action_path];
        }
    }

    // Удалить настройки
    if (!empty($_POST['exchanges_remove']) and is_array($_POST['exchanges_remove'])) {
        $PHPShopOrmExchanges = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges']);
        foreach ($_POST['exchanges_remove'] as $v)
            $data = $PHPShopOrmExchanges->delete(array('id' => '=' . intval($v)));
    }

    // Раздел из памяти настроек
    if (!empty($_POST['subpath']))
        $subpath[2] = $_POST['subpath'];

    switch ($subpath[2]) {
        case 'catalog':
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
            break;
        case 'user':
            PHPShopObj::loadClass('user');
            $PHPShopUserStatusArray = new PHPShopUserStatusArray();
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
            break;
        case 'order':
            PHPShopObj::loadClass(array('order', 'user'));
            $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
            $PHPShopUserStatusArray = new PHPShopUserStatusArray();
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
            break;
        default: $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
            break;
    }

    if (empty($_POST['export_gzip']))
        $_POST['export_gzip'] = null;

    $PHPShopOrm->debug = false;
    $PHPShopOrm->mysql_error = false;
    $delim = $_POST['export_delim'];
    $sortdelim = $_POST['export_sortdelim'];
    $delim_img = $_POST['export_imgdelim'];
    $csv = null;
    $csv_title = null;
    $gz = $_POST['export_gzip'];
    $pattern_cols = $_POST['pattern_cols'];
    if (!is_array($pattern_cols))
        $pattern_cols = array('id', 'name', 'price');
    else {
        $pattern_cols = prepareCols($pattern_cols);
    }

    if (!empty($_SESSION['select']) and is_array($_SESSION['select'][$select_action_path])) {
        $val = array_values($_SESSION['select'][$select_action_path]);
        $_POST['export_select'][$select_action_path] = $_SESSION['select'][$select_action_path];
        $where = array('id' => ' IN (' . implode(',', $val) . ')');
    } else
        $where = null;



    // Память выбранных полей
    if (is_array($_POST['pattern_cols'])) {

        if (!empty($_COOKIE['check_memory'])) {
            $memory = json_decode($_COOKIE['check_memory'], true);
            unset($memory[$_GET['path']]);
        }
        foreach ($_POST['pattern_cols'] as $k => $v) {
            $memory[$_GET['path']][$v] = 1;

            // URL
            if ($v == 'url') {
                $url = true;
                unset($pattern_cols[$k]);
            }
            // Путь каталога
            if ($v == 'path') {
                $path = true;
                unset($pattern_cols[$k]);

                if (empty($pattern_cols['category']))
                    $pattern_cols[] = 'category';
            }
        }
        if (is_array($memory))
            setcookie("check_memory", json_encode($memory), time() + 3600000, $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/admpanel/');
    }

    // SSL
    if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS']))
        $ssl = 'https://';
    else
        $ssl = 'http://';

    // XML выгрузка всех полей
    if ($_POST['export_format'] == 'cml')
        $pattern_cols = array('*');


    if ($_POST['export_format'] != 'yml')
        $data = $PHPShopOrm->select($pattern_cols, $where, array('order' => 'id desc'), array('limit' => $_POST['export_limit']));

    // CML
    if ($_POST['export_format'] == 'cml') {

        PHPShopObj::loadClass('cml');
        $PHPShopCommerceML = new PHPShopCommerceML();

        // Заказы
        if ($subpath[2] == 'order') {
            PHPShopObj::loadClass('order');
            $csv = $PHPShopCommerceML->getOrders($data);
        }
        // Товары
        elseif (empty($subpath[2])) {
            $csv = $PHPShopCommerceML->getProducts($data);
        }

        $ext_file = 'xml';
    }
    // YML
    else if ($_POST['export_format'] == 'yml') {
        PHPShopObj::loadClass("valuta");
        PHPShopObj::loadClass("promotions");
        PHPShopObj::loadClass('yml');

        $_GET['allimage'] = true;

        if ($_POST['export_code'] == 'utf')
            $_GET['utf'] = true;

        $PHPShopYml = new PHPShopYml();
        $PHPShopYml->where = $val;

        $csv = $PHPShopYml->compile();

        $ext_file = 'xml';
    }
    // Webmaster
    else if ($_POST['export_format'] == 'webmaster') {
        PHPShopObj::loadClass("valuta");
        PHPShopObj::loadClass("promotions");
        PHPShopObj::loadClass('yml');

        //$_GET['getall'] = true;
        $_GET['available'] = true;

        if ($_POST['export_code'] == 'utf')
            $_GET['utf'] = true;

        $PHPShopYml = new PHPShopYml();
        $PHPShopYml->where = $val;

        $csv = $PHPShopYml->compile();

        $ext_file = 'xml';
    }
    // RSS
    else if ($_POST['export_format'] == 'rss') {
        PHPShopObj::loadClass("valuta");
        PHPShopObj::loadClass("promotions");
        PHPShopObj::loadClass('rssgoogle');

        $PHPShopRssGoogle = new PHPShopRssGoogle();
        $PHPShopRssGoogle->where = $val;

        $csv = $PHPShopRssGoogle->compile();

        $ext_file = 'xml';
    }
    // CSV
    else {
        $ext_file = 'csv';
        foreach ($_POST['pattern_cols'] as $cols_name) {

            if (!empty($key_name[$cols_name]))
                $name = $key_name[$cols_name];
            else
                $name = $cols_name;

            if ($sortdelim == ';' and $cols_name == 'vendor_array')
                continue;
            else
                $csv_title .= '"' . $name . '"' . $delim;
        }

        if (is_array($data)) {
            foreach ($data as $row) {
                $csv_line = null;
                $csv_export_count++;

                foreach ($_POST['pattern_cols'] as $cols_name) {

                    if ($cols_name == 'datas')
                        $csv_line .= PHPShopDate::get($row[$cols_name]) . $delim;

                    // Полный путь к изображениям
                    elseif ($cols_name == 'pic_small' and isset($_POST['export_imgpath']) and ! empty($row['pic_small'])) {
                        $csv_line .= '"' . $ssl . $_SERVER['SERVER_NAME'] . $row['pic_small'] . '"' . $delim;
                    } elseif ($cols_name == 'pic_big') {

                        $img_line = '"';

                        if (!empty($delim_img) and ! empty($row['id'])) {

                            // Дополнительные изображения
                            $PHPShopOrmImg = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
                            $data_img = $PHPShopOrmImg->select(array('*'), array('parent' => '=' . intval($row['id'])), array('order' => 'id desc'), array('limit' => 100));
                        }

                        // Фотогалерея
                        if (is_array($data_img)) {
                            foreach ($data_img as $row_img) {

                                // Полный путь к изображениями
                                if (isset($_POST['export_imgpath']) and ! empty($row_img['name']))
                                    $img_line .= $ssl . $_SERVER['SERVER_NAME'] . $row_img['name'] . $delim_img;
                                else
                                    $img_line .= $row_img['name'] . $delim_img;
                            }

                            $img_line = substr($img_line, 0, strlen($img_line) - 1);
                        }
                        // Нет фотогалереи
                        else {
                            // Полный путь к изображениями
                            if (isset($_POST['export_imgpath']) and ! empty($row['pic_big']))
                                $img_line .= $ssl . $_SERVER['SERVER_NAME'] . $row['pic_big'];
                            else
                                $img_line .= $row['pic_big'];
                        }

                        $csv_line .= $img_line . '"' . $delim;
                    }

                    // Корзина
                    elseif ($cols_name == 'orders_cart') {
                        $order = unserialize($row['orders']);
                        $csv_line .= '"';
                        if (is_array($order['Cart']['cart']))
                            foreach ($order['Cart']['cart'] as $k => $v) {
                                $csv_line .= '[' . $v['uid'] . ' ' . $v['name'] . '(' . $v['num'] . '*' . $v['price'] . ')]';
                            }
                        $csv_line .= '[' . __('Доставка') . '(' . $order['Cart']['dostavka'] . ')]';
                        $csv_line .= '"' . $delim;
                    }

                    // Файлы
                    elseif ($cols_name == 'files') {
                        $files = unserialize($row['files']);
                        if (is_array($files)) {
                            $csv_line .= '"';
                            $file_line = null;
                            foreach ($files as $file) {
                                $file_line .= $file['path'] . ',';
                            }
                            $file_line = substr($file_line, 0, strlen($file_line) - 1);
                            $csv_line .= $file_line . '"' . $delim;
                        }
                    }

                    // Email в заказе
                    elseif ($cols_name == 'orders_email') {
                        $order = unserialize($row['orders']);
                        $csv_line .= '"' . $order['Person']['mail'] . '"' . $delim;
                    }
                    // Телефон пользователя
                    elseif ($cols_name == 'data_adres' and $subpath[2] == 'user') {
                        $data_adres = unserialize($row['data_adres']);
                        $csv_line .= '"' . $data_adres['list'][$data_adres['main']]['tel_new'] . '"' . $delim;
                    }
                    // URL
                    elseif (!empty($url) and $cols_name == 'url') {

                        if (isset($row['prod_seo_name'])) {

                            if (empty($row['prod_seo_name']))
                                $csv_line .= $ssl . $_SERVER['SERVER_NAME'] . '/id/' . str_replace("_", "-", PHPShopString::toLatin($row['name'])) . '-' . $row['id'] . '.html' . $delim;
                            else
                                $csv_line .= $ssl . $_SERVER['SERVER_NAME'] . '/id/' . $row['prod_seo_name'] . '-' . $row['id'] . '.html' . $delim;
                        } else
                            $csv_line .= $ssl . $_SERVER['SERVER_NAME'] . '/shop/UID_' . $row['id'] . '.html' . $delim;
                    }
                    // Путь каталога
                    elseif (!empty($path) and $cols_name == 'path') {

                        if (isset($row['category'])) {

                            $category = new PHPShopCategory($row['category']);
                            $result = $category->getChildrenCategories(100, ['id', 'parent_to', 'name'], false, false, true);
                            $search_str = implode("/", array_reverse($category->search_str));

                            $csv_line .= $search_str . $delim;
                        }
                    }

                    // Статус заказа
                    elseif ($cols_name == 'statusi') {
                        $csv_line .= '"' . $PHPShopOrderStatusArray->getParam($row['statusi'] . '.name') . '"' . $delim;
                    }

                    // Статус пользователя
                    elseif ($cols_name == 'status') {
                        $csv_line .= '"' . $PHPShopUserStatusArray->getParam($row['status'] . '.name') . '"' . $delim;
                    }
                    // Сериализованное значение
                    elseif (PHPShopString::is_serialized($row[$cols_name])) {
                        $csv_line .= serializeSelect($row[$cols_name], $row['category']);
                    } else {

                        // Проверка старых заказов < 4.0
                        if ($cols_name == 'fio' and ( empty($row['fio'])) and empty($row['tel'])) {
                            $orders = unserialize($row['orders']);
                            if (is_array($orders["Person"])) {
                                $row['fio'] = $orders["Person"]['name_person'] . ' ' . $orders["Person"]['mail'];
                                $row['tel'] = $orders["Person"]['tel_code'] . ' ' . $orders["Person"]['tel_name'];
                                $row['street'] = $orders["Person"]['adr_name'];
                                $row['org_name'] = $orders["Person"]['org_name'];
                                $row['org_inn'] = $orders["Person"]['org_inn'];
                                $row['org_kpp'] = $orders["Person"]['org_kpp'];
                                $row['user'] = $orders["Person"]['mail'];
                            }
                        }

                        $csv_line .= '"' . PHPShopSecurity::CleanOut($row[$cols_name]) . '"' . $delim;
                    }
                }

                $csv .= substr($csv_line, 0, (strlen($csv_line) - 1)) . "\n";
            }
        }

        // Дописываем поля для характеристик
        if (!empty($GLOBALS['sort_col_name']) and is_array($GLOBALS['sort_col_name'])) {
            foreach ($GLOBALS['sort_col_name'] as $k => $v)
                $csv_title .= '"@' . $k . '"' . $delim;
        }

        $csv_title = substr($csv_title, 0, (strlen($csv_title) - 1)) . "\n";
    }

    $sorce = "./csv/export_" . $subpath[2] . "_" . date("d_m_y_His") . "." . $ext_file;

    // Имя файла для модуля Задачи
    if (!empty($_REQUEST['file']))
        $sorce = $_classPath . "admpanel/csv/" . $_REQUEST['file'] . "." . $ext_file;


    // Сохранение настройки
    if ($_POST['exchanges'] == 'new' and ! empty($_POST['exchanges_new'])) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges']);
        $_POST['subpath'] = $subpath[2];
        $PHPShopOrm->insert(array('name_new' => $_POST['exchanges_new'], 'option_new' => serialize($_POST), 'type_new' => 'export'));
    }

    // Кодировка
    if (($_POST['export_code'] == 'utf' and $_POST['export_format'] == 'csv') or $_POST['export_format'] == 'xlsx') {

        if ($GLOBALS['PHPShopBase']->codBase == 'utf-8')
            $content = PHPShopString::win_utf8($csv_title, true) . $csv;
        else
            $content = PHPShopString::win_utf8($csv_title . $csv);
    }
    else {

        if ($GLOBALS['PHPShopBase']->codBase == 'utf-8')
            $content = $csv_title . PHPShopString::utf8_win1251($csv, true);
        else
            $content = $csv_title . $csv;
    }

    $result = PHPShopFile::write($sorce, $content);

    // XLSX
    if ($_POST['export_format'] == 'xlsx') {

        require_once '../lib/simplexlsx/SimpleXLSXGen.php';

        $handle = fopen($sorce, "r");
        $i=0;
        while ($data = fgetcsv($handle, 0, $delim)) {
            
            // Заголовок
            if($i == 0){
               foreach($data as $val)
                   $title[]='<b><style bgcolor="#FFFF00">'.$val.'</style></b>';
               $tmp_content[]=$title;
               
            }
            else 
                $tmp_content[]=$data;
            
            $i++;
        }

        if (empty($GLOBALS['exchanges_cron']))
            $sorce = "./csv/export_" . $subpath[2] . "_" . date("d_m_y_His") . ".xlsx";
        else
            $sorce = '../../../admpanel/csv/export_' . $subpath[2] . "_" . date("d_m_y_His") . ".xlsx";

        SimpleXLSXGen::fromArray($tmp_content)->saveAs($sorce);
    }

    if (empty($_REQUEST['file'])) {
        if ($gz) {
            $result = PHPShopFile::gzcompressfile($sorce);

            if ($result)
                header("Location: " . $sorce . '.gz');
            else
                echo $PHPShopGUI->setAlert(__('Нет прав на запись файла') . ' ' . $sorce . '.gz', 'danger');
        }
        elseif ($result)
            header("Location: " . $sorce);
        else
            echo $PHPShopGUI->setAlert(__('Нет прав на запись файла') . ' ' . $sorce, 'danger');
    }
}

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage, $PHPShopOrm, $key_name, $subpath, $key_base, $key_stop, $hideCatalog;

    // Выбранные данные
    $select_action_path = $subpath[2];
    if (empty($select_action_path))
        $select_action_path = 'product';

    // Выбрать настройку
    if (!empty($_GET['exchanges'])) {

        $PHPShopOrmExchanges = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges']);
        $data_exchanges = $PHPShopOrmExchanges->select(array('*'), array('id' => '=' . intval($_GET['exchanges'])), false, array("limit" => 1));


        if (is_array($data_exchanges)) {
            $_POST = unserialize($data_exchanges['option']);

            $exchanges_name = ": " . $data_exchanges['name'];

            $memory[$_GET['path']]['export_sortdelim'] = @$_POST['export_sortdelim'];
            $memory[$_GET['path']]['export_sortsdelim'] = @$_POST['export_sortsdelim'];
            $memory[$_GET['path']]['export_imgdelim'] = @$_POST['export_imgdelim'];
            $memory[$_GET['path']]['export_imgpath'] = @$_POST['export_imgpath'];
            $memory[$_GET['path']]['export_uniq'] = @$_POST['export_uniq'];
            $memory[$_GET['path']]['export_action'] = @$_POST['export_action'];
            $memory[$_GET['path']]['export_delim'] = @$_POST['export_delim'];
            $memory[$_GET['path']]['export_imgproc'] = @$_POST['export_imgproc'];
            $memory[$_GET['path']]['export_code'] = @$_POST['export_code'];
            $memory[$_GET['path']]['export_limit'] = @$_POST['export_limit'];
            $memory[$_GET['path']]['export_format'] = @$_POST['export_format'];

            $export_sortdelim = @$memory[$_GET['path']]['export_sortdelim'];
            $export_sortsdelim = @$memory[$_GET['path']]['export_sortsdelim'];
            $export_imgvalue = @$memory[$_GET['path']]['export_imgdelim'];
            $export_code = $memory[$_GET['path']]['export_code'];
            $export_format = $memory[$_GET['path']]['export_format'];
            $export_limit = $memory[$_GET['path']]['export_limit'];

            $_SESSION['select'][$select_action_path] = $_POST['export_select'][$select_action_path];
        }
    } else {

        if ($GLOBALS['PHPShopBase']->codBase == 'utf-8')
            $export_code = 'utf';
        else
            $export_code = 'ansi';

        $export_limit = '0,10000';
    }

    $PHPShopGUI->action_button['Экспорт'] = array(
        'name' => __('Выполнить'),
        'action' => 'saveID',
        'class' => 'btn  btn-primary btn-sm navbar-btn',
        'type' => 'submit',
        'icon' => 'glyphicon glyphicon-open'
    );

    $sel_left = $sel_right = null;

    $data = $PHPShopOrm->select(array('*'), false, false, array('limit' => 1));

    // URL
    if (empty($subpath[2])) {
        $data['url'] = null;
        $data['path'] = null;
    }

    if (is_array($data))
        foreach ($data as $key => $val) {

            if (!empty($key_name[$key]))
                $name = $key_name[$key];
            else
                $name = $key;

            if (@in_array($key, $key_base))
                $sel_left .= '<option value="' . $key . '" selected class="">' . __(ucfirst($name)) . '</option>';
            elseif (!in_array($key, $key_stop))
                $sel_right .= '<option value="' . $key . '" class="">' . __(ucfirst($name)) . '</option>';
        }


    // Размер названия поля
    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->addJSFiles('./exchange/gui/exchange.gui.js');

    // Товары
    if (empty($subpath[2])) {
        $class = false;
        $class_xml = false;
        $select_action = ' ' . __('товаров');
        $TitlePage .= $select_action;
        $select_path = 'catalog';
        $PHPShopGUI->_CODE = '<p></p><p class="text-muted">' . __('Ниже приведен список полей, которые могут быть экспортированы в формат Excel. Выделенные поля являются обязательными для последующей загрузки файла, остальные поля можно добавить или убрать из блока доступных полей по желанию.</p><p><kbd>Id</kbd> или <kbd>Артикул</kbd>') . '</p>';
    }

    // Каталоги
    elseif ($subpath[2] == 'catalog') {
        $class = $class_xml = 'hide';
        $select_action = ' ' . __('каталогов');
        $TitlePage .= $select_action;
        $PHPShopGUI->_CODE = '<p></p><p class="text-muted">' . __('Ниже приведен список полей, которые могут быть экспортированы. Выделенные поля являются обязательными для последующей загрузки файла, остальные поля можно добавить или убрать из блока доступных полей по желанию') . '.</p><p><kbd>Id</kbd></p>';
    }

    // Пользователи
    elseif ($subpath[2] == 'user') {
        $class = $class_xml = 'hide';
        $select_path = 'shopusers';
        $select_action = ' ' . __('пользователей');
        $TitlePage .= $select_action;
        $PHPShopGUI->_CODE = '<p></p><p class="text-muted">' . __('Ниже приведен список полей, которые могут быть экспортированы. Выделенные поля являются обязательными для последующей загрузки файла, остальные поля можно добавить или убрать из блока доступных полей по желанию') . '.</p><p><kbd>Id</kbd> ' . __('или') . ' <kbd>' . __('Логин') . '</kbd></p>';
    }

    // Заказы
    elseif ($subpath[2] == 'order') {
        $class = 'hide';
        $class_xml = false;
        $select_path = $subpath[2];
        $sel_right .= '<option value="orders_email" class="">Email</option>';
        $sel_right .= '<option value="orders_cart" class="">Корзина</option>';
        $PHPShopGUI->_CODE = '<p></p><p class="text-muted">' . __('Ниже приведен список полей, которые могут быть экспортированы в формат Excel. Выделенные поля являются обязательными для последующей загрузки файла, остальные поля можно добавить или убрать из блока доступных полей по желанию') . '.</p><p><kbd>Id</kbd></p>';
    }


    $PHPShopGUI->_CODE .= '
    <table width="100%" style="margin-bottom:20px">
        <tr>
        <td class="text-center" width="48%"><label for="pattern_default">' . __('Экспортируемые поля в Excel') . '</label></td>
        <td> </td>
        <td class="text-center"><label for="pattern_more">' . __('Доступные поля') . '</label></td>
        </tr>
        <tr>
        <td>
        <select id="pattern_default" style="height:250px" name="pattern_cols[]" multiple class="form-control">
             ' . $sel_left . '                                 
        </select>
        </td>
        <td class="text-center"><a class="btn btn-default btn-sm" href="#" id="send-default" data-toggle="tooltip" data-placement="top" title="' . __('Добавить поле') . '"><span class="glyphicon glyphicon-chevron-left"></span></a><br><br>
        <a class="btn btn-default btn-sm" id="send-more" href="#" data-toggle="tooltip" data-placement="top" title="' . __('Убрать поле') . '"><span class="glyphicon glyphicon-chevron-right"></span></a><br><br>
<a class="btn btn-default btn-sm" id="send-all" href="#" data-toggle="tooltip" data-placement="top" title="' . __('Выбрать все поля') . '"><span class="glyphicon glyphicon-backward"></span></a><br><br>
<a class="btn btn-default btn-sm" id="remove-all" href="#" data-toggle="tooltip" data-placement="top" title="' . __('Удалить все поля') . '"><span class="glyphicon glyphicon-forward"></span></a></td>
        <td width="48%">
        <select id="pattern_more" style="height:250px" multiple class="form-control">
             ' . $sel_right . '                                    
        </select>
</td>
        </tr>
   </table>';

    $PHPShopGUI->setActionPanel($TitlePage . $exchanges_name, false, array('Экспорт'));

    $delim_value[] = array(__('Точка с запятой'), ';', @$memory[$_GET['path']]['export_delim']);
    $delim_value[] = array(__('Запятая'), ',', @$memory[$_GET['path']]['export_delim']);

    $delim_sortvalue[] = array('#', '#', $export_sortdelim);
    $delim_sortvalue[] = array(__('Колонка'), ';', $export_sortdelim);

    $delim_imgvalue[] = array(__('Выключить'), 0, $export_imgvalue);
    $delim_imgvalue[] = array(__('Запятая'), ',', $export_imgvalue);
    $delim_imgvalue[] = array('#', '#', $export_imgvalue);
    $delim_imgvalue[] = array(__('пробел'), ' ', $export_imgvalue);

    $code_value[] = array('ANSI', 'ansi', $export_code);
    $code_value[] = array('UTF-8', 'utf', $export_code);

    $format_value[] = array('Excel (CSV)', 'csv', $export_format);


    if (empty($subpath[2])) {
        $format_value[] = array('Excel (XLSX)', 'xlsx', $export_format);
        $format_value[] = array('Яндекс.Маркет (YML)', 'yml', $export_format);
        $format_value[] = array('Яндекс.Вебмастер (YML)', 'webmaster', $export_format);
        $format_value[] = array('Google (RSS)', 'rss', $export_format);
    }

    $format_value[] = array('1C (CML)', 'cml', $export_format);

    $Tab1 = $PHPShopGUI->setField('CSV-разделитель', $PHPShopGUI->setSelect('export_delim', $delim_value, 200)) .
            $PHPShopGUI->setField('Разделитель для характеристик', $PHPShopGUI->setSelect('export_sortdelim', $delim_sortvalue, 200), 1, 'Колонка с характеристиками только для общего каталога. Для формата Excel', $class) .
            $PHPShopGUI->setField('Полный путь для изображений', $PHPShopGUI->setCheckbox('export_imgpath', 1, null, @$memory[$_GET['path']]['export_imgpath']), 1, 'Добавляет к изображениям адрес сайта для формата Excel', $class) .
            $PHPShopGUI->setField('Разделитель для изображений', $PHPShopGUI->setSelect('export_imgdelim', $delim_imgvalue, 200), 1, 'Дополнительные изображения для формата Excel', $class) .
            $PHPShopGUI->setField('Кодировка текста', $PHPShopGUI->setSelect('export_code', $code_value, 200)) .
            $PHPShopGUI->setField('Формат файла', $PHPShopGUI->setSelect('export_format', $format_value, 200, true), 1, false, $class_xml) .
            $PHPShopGUI->setField('GZIP сжатие', $PHPShopGUI->setCheckbox('export_gzip', 1, null, 0), 1, 'Сокращает размер создаваемого файла') .
            $PHPShopGUI->setField('Лимит строк', $PHPShopGUI->setInputText(null, 'export_limit', $export_limit, 200), 1, 'Запись c 1 по 10000');

    // Закладка 3
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges']);
    $data = $PHPShopOrm->select(array('*'), array('type' => '="export"'), array('order' => 'id DESC'), array("limit" => "1000"));
    $exchanges_value[] = array(__('Создать новую настройку'), 'new');
    if (is_array($data)) {
        foreach ($data as $row) {
            $exchanges_value[] = array($row['name'], $row['id'], $_REQUEST['exchanges']);
            $exchanges_remove_value[] = array($row['name'], $row['id']);
        }
    } else
        $exchanges_remove_value = null;

    $Tab3 = $PHPShopGUI->setField('Выбрать настройку', $PHPShopGUI->setSelect('exchanges', $exchanges_value, 300, false));
    $Tab3 .= $PHPShopGUI->setField('Сохранить настройку', $PHPShopGUI->setInputArg(array('type' => 'text', 'placeholder' => 'Имя настройки', 'size' => '300', 'name' => 'exchanges_new', 'class' => 'vendor_add')));
    $Tab3 .= $PHPShopGUI->setField('Удалить настройки', $PHPShopGUI->setSelect('exchanges_remove[]', $exchanges_remove_value, 300, false, false, false, false, 1, true));

    $PHPShopGUI->tab_return = true;
    $PHPShopGUI->setTab(array('Настройки', $Tab1, true), array('Сохраненные настройки', $Tab3, true));

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);


    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", true, "right", 70, "", "but") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.exchange.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.exchange.edit");

    $PHPShopGUI->setFooter($ContentFooter);


    if (!empty($_SESSION['select']) and is_array($_SESSION['select'][$select_action_path])) {

        if (!empty($_GET['return']))
            $select_path = $_GET['return'];

        foreach ($_SESSION['select'][$select_action_path] as $val)
            $select_message = '<span class="label label-default">' . count($_SESSION['select'][$select_action_path]) . '</span> ' . $select_action . ' ' . __('выбрано') . '<hr><a href="?path=' . $select_path . '""><span class="glyphicon glyphicon-ok"></span> ' . __('Изменить интервал') . '</a><br><a href="#" class="text-danger select-remove"><span class="glyphicon glyphicon-remove"></span> ' . __('Удалить диапазон') . '</a>';
    } else
        $select_message = '<p class="text-muted">' . __('Вы можете выбрать конкретные объекты для экспорта, отметив их галочками и выбрав в меню <span class="glyphicon glyphicon-cog"></span><span class="caret"></span> <em>"Экспортировать выбранные"</em>. По умолчанию будут экспортированы все позиции') . '. <a href="?path=' . $select_path . '"><span class="glyphicon glyphicon-share-alt"></span> ' . __('Выбрать') . '</a></p>';

    $sidebarleft[] = array('title' => 'Тип данных', 'content' => $PHPShopGUI->loadLib('tab_menu', false, './exchange/'));

    if (empty($hideCatalog) and $select_action_path == 'product')
        $sidebarleft[] = array('title' => 'Прайс-лист', 'content' => $PHPShopGUI->loadLib('tab_menu_xml', false, './exchange/'));

    if (!empty($select_path))
        $sidebarleft[] = array('title' => 'Подсказка', 'content' => $select_message, 'class' => 'hidden-xs');

    $PHPShopGUI->setSidebarLeft($sidebarleft, 2);

    // Футер
    $PHPShopGUI->Compile(2);
    return true;
}

/**
 * @param $pattern_cols
 * @return array
 */
function prepareCols($pattern_cols) {
    // Если есть "виртуальные поля" - удаляем их и добавляем выборку "настощего" поля
    if (in_array('orders_cart', $pattern_cols) || in_array('orders_email', $pattern_cols)) {
        $pattern_cols[] = 'orders';
    }

    if (in_array('orders_cart', $pattern_cols)) {
        unset($pattern_cols[array_search('orders_cart', $pattern_cols)]);
    }
    if (in_array('orders_email', $pattern_cols)) {
        unset($pattern_cols[array_search('orders_email', $pattern_cols)]);
    }


    array_walk($pattern_cols, 'patternCheck');

    return $pattern_cols;
}

// Обработка событий
$PHPShopGUI->getAction();
?>