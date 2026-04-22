<?php

use Shuchkin\SimpleXLSX;
use Shuchkin\SimpleXLS;

$TitlePage = __("Импорт данных");

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
    'yml' => 'Яндекс.Маркет',
    'icon' => 'Иконка',
    'parent_to' => 'Родитель',
    'category' => 'Каталог',
    'title' => 'Заголовок',
    'login' => 'Логин',
    'tel' => 'Телефон',
    'cumulative_discount' => 'Накопительная скидка',
    'seller' => 'Статус загрузки в 1С',
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
    'descrip' => 'Meta description',
    'keywords' => 'Meta keywords',
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
    'vendor' => '@Характеристика',
    'data_adres' => 'Адрес',
    'color' => 'Код цвета',
    'parent2' => 'Цвет',
    'rate' => 'Рейтинг',
    'productday' => 'Товар дня',
    'hit' => 'Хит',
    'sendmail' => 'Подписка на рассылку',
    'statusi' => 'Статус заказа',
    'country' => 'Страна',
    'state' => 'Область',
    'index' => 'Индекс',
    'house' => 'Дом',
    'porch' => 'Подъезд',
    'door_phone' => 'Домофон',
    'flat' => 'Квартира',
    'delivtime' => 'Время доставки',
    'org_name' => 'Организация',
    'org_inn' => 'ИНН',
    'org_kpp' => 'КПП',
    'org_yur_adres' => 'Юридический адрес',
    'dop_info' => 'Комментарий пользоватея',
    'tracking' => 'Код отслеживания',
    'path' => 'Путь каталога',
    'length' => 'Длина',
    'width' => 'Ширина',
    'height' => 'Высота',
    'moysklad_product_id' => 'МойСклад Id',
    'bonus' => 'Бонус',
    'price_purch' => 'Закупочная цена',
    'files' => 'Файлы',
    'external_code' => 'Внешний код',
    'barcode' => 'Штрихкод',
    'rate_count' => 'Голоса',
    'productservices_products' => 'Услуги'
);

//if ($GLOBALS['PHPShopBase']->codBase == 'utf-8')
//unset($key_name);
// Стоп лист
$key_stop = array('password', 'wishlist', 'sort', 'status', 'datas', 'price_search', 'vid', 'name_rambler', 'servers', 'skin', 'skin_enabled', 'secure_groups', 'icon_description', 'title_enabled', 'title_shablon', 'descrip_shablon', 'descrip_enabled', 'productsgroup_check', 'productsgroup_product', 'keywords_enabled', 'keywords_shablon', 'sort_cache', 'sort_cache_created_at', 'parent_title', 'menu', 'order_by', 'order_to', 'org_ras', 'org_bank', 'org_kor', 'org_bik', 'org_city', 'admin', 'org_fakt_adres');

if (empty($subpath[2]))
    $subpath[2] = null;

switch ($subpath[2]) {
    case 'catalog':
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
        $key_base = array('id');
        break;
    case 'user':
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
        $key_base = array('id', 'login');
        array_push($key_stop, 'tel_code', 'adres', 'inn', 'kpp', 'company', 'mail', 'token', 'token_time');
        break;
    case 'order':
        PHPShopObj::loadClass('order');
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $key_base = array('id', 'uid');
        array_push($key_stop, 'orders', 'user');
        $key_name['uid'] = __('№ Заказа');
        $TitlePage .= ' ' . __('заказов');
        break;
    default: $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
        $key_base = array('id', 'uid');
        break;
}

// Загрузка изображения по ссылке 
function downloadFile($url, $path) {

    $newfname = $path;
    $url = iconv("windows-1251", "utf-8//IGNORE", $url);

    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );

    $file = @fopen($url, 'rb', false, stream_context_create($arrContextOptions));
    if ($file) {
        $newf = fopen($newfname, 'wb');
        if ($newf) {
            while (!feof($file)) {
                fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
            }
        }
    }
    if ($file) {
        fclose($file);
    }
    if ($newf) {
        fclose($newf);
        return true;
    }
}

// Проверка изображения
function checkImage($img, $id, $uniq) {
    global $PHPShopSystem;

    // Перевод в латиницу
    $path_parts = pathinfo($img);
    $path_parts['basename'] = PHPShopFile::toLatin($path_parts['basename']);

    // Нет расширения ставим JPG
    if (empty($path_parts['extension'])) {
        $rand = '_' . substr(abs(crc32($img)), 0, 5);
        $path_parts['basename'] .= $rand . '.jpg';
    }

    // Папка картинок
    $path = $PHPShopSystem->getSerilizeParam('admoption.image_result_path');

    // Имя для проверки в фотогалерее
    $img_check = $GLOBALS['dir']['dir'] . '/UserFiles/Image/' . $path . $path_parts['basename'];

    // Сохранение в webp
    if ($PHPShopSystem->ifSerilizeParam('admoption.image_webp_save') and $path_parts['extension'] != 'webp') {
        $img_check = str_replace([".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF", ".WEBP"], '.webp', $img_check);
    }

    // Новое имя
    $img = $GLOBALS['dir']['dir'] . '/UserFiles/Image/' . $path . $path_parts['basename'];

    // Проверка существования изображения в фотогалерее
    $PHPShopOrmImg = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
    $PHPShopOrmImg->debug = false;
    $check = $PHPShopOrmImg->select(array('id'), array('name' => '="' . $img_check . '"', 'parent' => '=' . intval($id)), false, array('limit' => 1))['id'];

    // Картинки нет
    if (empty($check)) {

        // Проверка имени файла
        if (empty($uniq) and file_exists($_SERVER['DOCUMENT_ROOT'] . $img_check)) {

            // Соль
            $rand = '_' . substr(abs(crc32($img)), 0, 5);
            $path_parts['basename'] = str_replace([".png", ".jpg", ".jpeg", ".gif", ".webp", ".PNG", ".JPG", ".JPEG", ".GIF", ".WEBP"], [$rand . ".png", $rand . ".jpg", $rand . ".jpeg", $rand . ".gif", $rand . ".webp", $rand . ".PNG", $rand . ".JPG", $rand . ".JPEG", $rand . ".GIF", $rand . ".WEBP"], $path_parts['basename']);
        }
    }

    // Новое имя
    $img = $GLOBALS['dir']['dir'] . '/UserFiles/Image/' . $path . $path_parts['basename'];

    return ['img' => $img, 'check' => $check];
}

// Временная категория
function setCategory() {
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
    $row = $PHPShopOrm->getOne(array('id'), array('name' => '="Загрузка CSV ' . PHPShopDate::get() . '"'));

    if (empty($row['id'])) {
        $result = $PHPShopOrm->insert(array('name_new' => 'Загрузка CSV ' . PHPShopDate::get(), 'skin_enabled_new' => 1));
        return $result;
    } else
        return $row['id'];
}

// Генератор характеристик общие значения
function sort_encode_general($sort, $category) {

    $return = [];
    $delim = $_POST['export_sortdelim'];
    $sortsdelim = $_POST['export_sortsdelim'];
    $debug = false;
    if (!empty($sort)) {

        if (strstr($sort, $delim)) {
            $sort_array = explode($delim, $sort);
        } else
            $sort_array[] = $sort;

        if (is_array($sort_array))
            foreach ($sort_array as $sort_list) {

                if (strstr($sort_list, $sortsdelim)) {

                    $sort_list_array = explode($sortsdelim, $sort_list, 2);
                    $sort_name = PHPShopSecurity::TotalClean($sort_list_array[0]);
                    $sort_value = PHPShopSecurity::TotalClean($sort_list_array[1]);

                    if (!empty($sort_name) and ! empty($sort_value)) {
                        $sortCheck = (new sortCheck($sort_name, $sort_value, $category, $debug))->result();

                        foreach ($sortCheck as $k => $v) {
                            if (isset($sortCheck[$k]))
                                $return[$k][] = $v[0];
                            else
                                $return[$k] = $v[0];
                        }
                    }
                }
            }
    }
    return $return;
}

// Генератор характеристик уникальные значения
function sort_encode($sort, $category) {
    global $PHPShopBase;

    $return = null;
    $delim = $_POST['export_sortdelim'];
    $sortsdelim = $_POST['export_sortsdelim'];
    $debug = false;
    if (!empty($sort)) {

        if (strstr($sort, $delim)) {
            $sort_array = explode($delim, $sort);
        } else
            $sort_array[] = $sort;

        if (is_array($sort_array))
            foreach ($sort_array as $sort_list) {

                if (strstr($sort_list, $sortsdelim)) {

                    $sort_list_array = explode($sortsdelim, $sort_list, 2);
                    $sort_name = PHPShopSecurity::TotalClean($sort_list_array[0]);
                    $sort_value = PHPShopSecurity::TotalClean($sort_list_array[1]);

                    // Получить ИД набора характеристик в каталоге
                    $PHPShopOrm = new PHPShopOrm();
                    $PHPShopOrm->debug = $debug;
                    $result_1 = $PHPShopOrm->query('select sort,name from ' . $GLOBALS['SysValue']['base']['categories'] . ' where id="' . $category . '"  limit 1', __FUNCTION__, __LINE__);
                    $row_1 = mysqli_fetch_array($result_1);

                    $cat_sort = unserialize($row_1['sort']);

                    $cat_name = $row_1['name'];

                    // Отсутствует в базе
                    if (is_array($cat_sort))
                        $where_in = ' and a.id IN (' . @implode(",", $cat_sort) . ') ';
                    else
                        $where_in = null;

                    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
                    $PHPShopOrm->debug = $debug;

                    $result_2 = $PHPShopOrm->query('select a.id as parent, b.id from ' . $GLOBALS['SysValue']['base']['sort_categories'] . ' AS a 
        JOIN ' . $GLOBALS['SysValue']['base']['sort'] . ' AS b ON a.id = b.category where a.name="' . $sort_name . '" and b.name="' . $sort_value . '" ' . $where_in . ' limit 1', __FUNCTION__, __LINE__);
                    $row_2 = mysqli_fetch_array($result_2);

                    // Присутствует в  базе
                    if (!empty($where_in) and isset($row_2['id'])) {
                        $return[$row_2['parent']][] = $row_2['id'];
                    }
                    // Отсутствует в базе
                    else {

                        // Проверка характеристики
                        if (!empty($where_in))
                            $sort_name_present = $PHPShopBase->getNumRows('sort_categories', 'as a where a.name="' . $sort_name . '" ' . $where_in . ' limit 1');

                        // Создаем новую характеристику
                        if (empty($sort_name_present) and ! empty($category)) {

                            // Есть
                            if (!empty($cat_sort[0])) {
                                $PHPShopOrm = new PHPShopOrm();
                                $PHPShopOrm->debug = $debug;

                                $result_3 = $PHPShopOrm->query('select category from ' . $GLOBALS['SysValue']['base']['sort_categories'] . ' where id="' . intval($cat_sort[0]) . '"  limit 1', __FUNCTION__, __LINE__);
                                $row_3 = mysqli_fetch_array($result_3);
                                $cat_set = $row_3['category'];
                            }
                            // Нет, создать новый набор
                            elseif (!empty($cat_name)) {

                                // Создание набора характеристик
                                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
                                $PHPShopOrm->debug = $debug;
                                $cat_set = $PHPShopOrm->insert(array('name_new' => __('Для каталога') . ' ' . $cat_name, 'category_new' => 0), '_new', __FUNCTION__, __LINE__);
                            }

                            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
                            $PHPShopOrm->debug = $debug;

                            if (!empty($sort_name) and ! empty($cat_set))
                                if ($parent = $PHPShopOrm->insert(array('name_new' => $sort_name, 'category_new' => $cat_set), '_new', __FUNCTION__, __LINE__)) {

                                    // Создаем новое значение характеристики
                                    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);
                                    $PHPShopOrm->debug = $debug;
                                    $slave = $PHPShopOrm->insert(array('name_new' => $sort_value, 'category_new' => $parent, 'sort_seo_name_new' => PHPShopString::toLatin($sort_value)), '_new', __FUNCTION__, __LINE__);

                                    $return[$parent][] = $slave;
                                    $cat_sort[] = $parent;

                                    // Обновляем набор каталога товаров
                                    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
                                    $PHPShopOrm->debug = $debug;
                                    $PHPShopOrm->update(array('sort_new' => serialize($cat_sort)), array('id' => '=' . $category), '_new', __FUNCTION__, __LINE__);
                                }
                        }
                        // Дописываем значение 
                        elseif (!empty($sort_value)) {

                            // Получаем ИД существующей характеристики
                            $PHPShopOrm = new PHPShopOrm();
                            $PHPShopOrm->debug = $debug;
                            $result = $PHPShopOrm->query('select a.id  from ' . $GLOBALS['SysValue']['base']['sort_categories'] . ' AS a where a.name="' . $sort_name . '" ' . $where_in . ' limit 1', __FUNCTION__, __LINE__);
                            if ($row = mysqli_fetch_array($result)) {
                                $parent = $row['id'];
                                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);
                                $PHPShopOrm->debug = $debug;
                                $slave = $PHPShopOrm->insert(array('name_new' => $sort_value, 'category_new' => $parent), '_new', __FUNCTION__, __LINE__);

                                $return[$parent][] = $slave;
                            }
                        }
                    }
                }
            }
    }

    return $return;
}

// Обработка строки CSV
function csv_update($data) {
    global $PHPShopOrm, $PHPShopBase, $csv_load_option, $key_name, $csv_load_count, $subpath, $PHPShopSystem, $csv_load, $csv_load_totale, $img_load;

    // Кодировка UTF-8
    if ($_POST['export_code'] == 'utf' and is_array($data)) {

        if ($GLOBALS['PHPShopBase']->codBase == 'utf-8') {

            $key_name_utf = $key_name;
            unset($key_name);

            foreach ($key_name_utf as $k => $v)
                $key_name[$k] = PHPShopString::win_utf8($v, true);
        } else {
            foreach ($data as $k => $v)
                $data[$k] = PHPShopString::utf8_win1251($v);
        }
    }

    require_once $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/thumb/phpthumb.php';
    $width_kratko = $PHPShopSystem->getSerilizeParam('admoption.width_kratko');
    $img_tw = $PHPShopSystem->getSerilizeParam('admoption.img_tw');
    $img_th = $PHPShopSystem->getSerilizeParam('admoption.img_th');

    // AI
    if ($_POST['export_ai'] == 1) {
        PHPShopObj::loadClass('yandexcloud');
        require_once $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/parsedown/Parsedown.php';
        $YandexGPT = new YandexGPT();
    }

    // Поиск Яндекс
    if ($_POST['export_imgsearch'] == 1) {
        PHPShopObj::loadClass('yandexcloud');
        $YandexSearch = new YandexSearch();
        $yandexsearch_image_num = (int) $PHPShopSystem->getSerilizeParam('ai.yandexsearch_image_num');
    }

    if (is_array($data)) {

        $key_name_true = array_flip($key_name);

        // Имена полей
        if (empty($csv_load_option)) {
            $select = false;

            // Сопоставление полей
            if (is_array($_POST['select_action'])) {

                if ($GLOBALS['PHPShopBase']->codBase == 'utf-8') {
                    foreach ($_POST['select_action'] as $k => $v)
                        $_POST['select_action'][$k] = PHPShopString::utf8_win1251($v, true);
                }


                foreach ($_POST['select_action'] as $k => $name) {

                    // Автоматизация
                    if (!empty($_POST['smart'])) {
                        $_POST['select_action'][$k] = PHPShopString::utf8_win1251($name, true);
                    }

                    if (!empty($name))
                        $select = true;

                    if (substr($name, 0, 1) == '@')
                        $_POST['select_action'][$k] = '@' . $data[$k];
                }
            }

            if ($select)
                $csv_load_option = $_POST['select_action'];
            else
                $csv_load_option = $data;
        }
        // Значения
        else {
            // Простановка полей
            foreach ($csv_load_option as $k => $cols_name) {

                // base64
                if (substr($data[$k], 0, 7) == 'base64-') {

                    // Пользователи
                    if ($subpath[2] == 'user') {
                        $array = array();
                        $array['main'] = 0;
                        $array['list'][] = json_decode(base64_decode(substr($data[$k], 7, strlen($data[$k]) - 7)), true);
                        array_walk_recursive($array, 'array2iconv');

                        $data[$k] = serialize($array);
                    }
                }

                // Поля кириллические
                if (!empty($key_name_true[$cols_name])) {

                    if ($GLOBALS['PHPShopBase']->codBase == 'utf-8') {

                        if ($_POST['export_code'] == 'ansi')
                            $row[$key_name_true[$cols_name]] = PHPShopString::win_utf8($data[$k], true);
                        else
                            $row[$key_name_true[$cols_name]] = $data[$k];
                    } else
                        $row[$key_name_true[$cols_name]] = $data[$k];
                }
                // Поля характеристики в колонках
                elseif (substr($cols_name, 0, 1) == '@') {
                    $row[$cols_name] = $data[$k];
                    $sort_name = substr($cols_name, 1, (strlen($cols_name) - 1));

                    // Несколько значений
                    if (strstr($data[$k], $_POST['export_sortsdelim'])) {
                        $sort_array = explode($_POST['export_sortsdelim'], $data[$k]);
                    } else
                        $sort_array[] = $data[$k];

                    if (is_array($sort_array)) {
                        foreach ($sort_array as $v)
                            $row['vendor_array'] .= $sort_name . $_POST['export_sortsdelim'] . $v . $_POST['export_sortdelim'];
                    }

                    unset($row[$cols_name]);
                    unset($sort_array);
                }
                // Остальные
                else
                    $row[strtolower($cols_name)] = $data[$k];
            }

            // Телефон пользователя
            if (!empty($row['data_adres'])) {

                $row['enabled'] = 1;

                $tel['main'] = 0;
                $tel['list'][0]['tel_new'] = $row['data_adres'];
                $row['data_adres'] = serialize($tel);
            }

            // Яндекс Поиск картинок товаров
            if (isset($row['pic_big']) and isset($YandexSearch)) {

                if ($YandexSearch->init())
                    $result = $YandexSearch->search_img($row['name']);

                if (is_array($result)) {
                    $row['pic_big'] = null;
                    $i = 0;
                    foreach ($result as $images) {

                        if ($i < $yandexsearch_image_num)
                            $row['pic_big'] .= $images['url'] . ',';
                        else
                            continue;

                        $i++;
                    }
                }
            }

            // Яндекс Поиск картинок каталогов
            if (isset($row['icon']) and isset($YandexSearch)) {

                if ($YandexSearch->init())
                    $result = $YandexSearch->search_img($row['name']);

                if (is_array($result)) {
                    $row['icon'] = null;
                    $i = 0;
                    foreach ($result as $images) {

                        if ($i < 1)
                            $row['icon'] = $images['url'];
                        else
                            continue;

                        $i++;
                    }
                }
            }

            // AI Описание
            if (isset($row['content']) and isset($YandexGPT)) {

                if ($subpath[2] == 'catalog')
                    $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_catalog_content_role');
                else
                    $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_product_content_role');

                if (!empty($row['content']))
                    $message = $row['content'];
                else
                    $message = $row['name'];

                if ($_POST['export_code'] == 'utf')
                    $message = PHPShopString::utf8_win1251($message);

                if (!empty($message) and $YandexGPT->init()) {
                    $result = $YandexGPT->text(strip_tags($message), $system, 0.3, 500);
                    $text = $YandexGPT->html($result['result']['alternatives'][0]['message']['text']);
                    $row['content'] = PHPShopString::utf8_win1251($text);
                }
            }

            // AI Краткое описание
            if (isset($row['description']) and isset($YandexGPT)) {

                $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_product_description_role');

                if (!empty($row['description']))
                    $message = $row['description'];
                else
                    $message = $row['name'];

                if ($_POST['export_code'] == 'utf')
                    $message = PHPShopString::utf8_win1251($message);

                if (!empty($message) and $YandexGPT->init()) {
                    $result = $YandexGPT->text(strip_tags($message), $system, 0.3, 200);

                    $text = str_replace(['*', '\n', '\r'], ['', '', ''], $result['result']['alternatives'][0]['message']['text']);
                    $text = preg_replace("/\r|\n/", ' ', $text);

                    $row['description'] = PHPShopString::utf8_win1251($text);
                }
            }

            // AI Meta Title
            if (isset($row['title']) and class_exists('YandexGPT')) {

                if ($subpath[2] == 'catalog')
                    $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_catalog_title_role');
                else
                    $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_product_title_role');

                if (!empty($row['title']))
                    $message = $row['title'];
                else
                    $message = $row['name'];

                if ($_POST['export_code'] == 'utf')
                    $message = PHPShopString::utf8_win1251($message);

                if (!empty($message)) {
                    $result = $YandexGPT->text(strip_tags($message), $system, 0.3, 100);

                    $text = str_replace(['*', '\n', '\r'], ['', '', ''], $result['result']['alternatives'][0]['message']['text']);
                    $text = preg_replace("/\r|\n/", ' ', $text);

                    $row['title'] = PHPShopString::utf8_win1251($text);
                    $row['title_enabled'] = 1;
                }
            }

            // AI Meta Description
            if (isset($row['descrip']) and class_exists('YandexGPT')) {

                if ($subpath[2] == 'catalog')
                    $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_catalog_description_role');
                else
                    $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_product_descrip_role');

                if (!empty($row['descrip']))
                    $message = $row['descrip'];
                else
                    $message = $row['name'];

                if ($_POST['export_code'] == 'utf')
                    $message = PHPShopString::utf8_win1251($message);

                if (!empty($message)) {
                    $result = $YandexGPT->text(strip_tags($message), $system, 0.3, 100);

                    $text = str_replace(['*', '\n', '\r'], ['', '', ''], $result['result']['alternatives'][0]['message']['text']);
                    $text = preg_replace("/\r|\n/", ' ', $text);

                    $row['descrip'] = PHPShopString::utf8_win1251($text);
                    $row['descrip_enabled'] = 1;
                }
            }


            // Файлы
            if (!empty($row['files'])) {

                if (strstr($row['files'], ",")) {
                    $files_array = explode(",", $row['files']);
                } else
                    $files_array[] = $row['files'];

                if (is_array($files_array)) {
                    foreach ($files_array as $file) {
                        $name = pathinfo($file);
                        $files[] = ['name' => $name['basename'], 'path' => $file];
                    }

                    $row['files'] = serialize($files);
                }
            }

            // Путь каталога
            if (isset($row['path'])) {

                $search = $row['path'];
                $category = new PHPShopCategory(0);
                $category->getChildrenCategories(100, ['id', 'parent_to', 'name'], false, $search);

                while (count($category->search) != $category->found) {
                    $PHPShopOrmCat = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
                    $PHPShopOrmCat->debug = false;
                    $category->search_id = $PHPShopOrmCat->insert(array('name_new' => $category->search[$category->found], 'parent_to_new' => $category->search_id));
                    $category->found++;
                }

                $row['category'] = $category->search_id;
            }

            // Коррекция флага подтипа
            if (isset($row['parent']) and $row['parent'] == '')
                unset($row['parent']);

            // Характеристики
            if (!empty($row['vendor_array'])) {

                // Не указана категория
                if (empty($row['category'])) {

                    // Поиск категории по ИД
                    if (!empty($row['id'])) {
                        $row['category'] = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['category'], ['id' => '=' . (string) $row['id']])['category'];
                    }

                    // Поиск категории по Атикулу
                    if (empty($row['category'])) {
                        $row['category'] = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['category'], ['uid' => '="' . $row['uid'] . '"'])['category'];
                    }

                    // Временная категория
                    if (empty($row['category'])) {
                        $row['category'] = setCategory();
                    }
                }

                $row['vendor'] = null;

                // Генератор характеристик общие значения
                if ($PHPShopSystem->getSerilizeParam("admoption.update_sort_type") == 1) {
                    $vendor_array = sort_encode_general($row['vendor_array'], $row['category']);
                }
                // Генератор характеристик уникальные значения
                else {
                    $vendor_array = sort_encode($row['vendor_array'], $row['category']);
                }

                if (is_array($vendor_array)) {
                    $row['vendor_array'] = serialize($vendor_array);
                    foreach ($vendor_array as $k => $v) {
                        if (is_array($v)) {
                            foreach ($v as $p) {
                                $row['vendor'] .= "i" . $k . "-" . $p . "i";
                            }
                        } else
                            $row['vendor'] .= "i" . $k . "-" . $v . "i";
                    }
                } else
                    $row['vendor_array'] = null;
            }

            // Полный путь к изображениями
            if (!strstr($row['pic_big'], '/UserFiles/Image/') and ! strstr($row['pic_big'], 'http'))
                $_POST['export_imgpath'] = true;
            else
                $_POST['export_imgpath'] = false;


            if (!empty($_POST['export_imgpath'])) {
                if (!empty($row['pic_small']))
                    $row['pic_small'] = '/UserFiles/Image/' . $row['pic_small'];
            }

            // Разделитель для изображений
            if (empty($_POST['export_imgdelim'])) {
                $imgdelim = [' ', ',', ';', '#'];
                foreach ($imgdelim as $delim) {
                    if (strstr($row['pic_big'], $delim)) {
                        $_POST['export_imgdelim'] = $delim;
                    }
                }
            }

            // Дополнительные изображения
            if (!empty($_POST['export_imgdelim']) and strstr($row['pic_big'], $_POST['export_imgdelim'])) {
                $data_img = explode($_POST['export_imgdelim'], $row['pic_big']);
            } elseif (!empty($row['pic_big']))
                $data_img[] = $row['pic_big'];

            // Проверка уникальности товаров
            if (empty($subpath[2]) and ! empty($_POST['export_uniq']) and ! empty($row['uid'])) {
                $uniq = $PHPShopBase->getNumRows('products', "where uid = '" . $row['uid'] . "'");
            } else
                $uniq = 0;

            // Отключение изображений
            if ($_POST['export_imgload'] == 0) {
                unset($data_img);
                $row['pic_big'] = null;
            }

            if (!empty($data_img) and is_array($data_img)) {

                // Очистка начальных данных
                unset($row['pic_big']);

                // Если выключена обработка фото
                if (isset($_POST['export_imgproc']) and $_POST['export_imgload'] == 1)
                    unset($row['pic_small']);

                // Получение ID товара по артикулу при обновлении
                if ($_POST['export_action'] == 'update' and empty($row['id']) and ! empty($row['uid'])) {
                    $PHPShopOrmProd = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
                    $data_prod = $PHPShopOrmProd->getOne(array('id'), array('uid' => '="' . $row['uid'] . '"'));
                    $row['id'] = $data_prod['id'];
                }

                // Очистка изображений при проверки уникальности
                if ($_POST['export_action'] == 'insert' and ! empty($uniq)) {
                    unset($data_img);
                }

                // Замена изображений
                if ($_POST['export_imgfunc'] == 1) {
                    $PHPShopOrmImg = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
                    $PHPShopOrmImg->delete(['parent' => '=' . intval($row['id'])]);
                }

                // Удаление изображений с заменой
                if ($_POST['export_imgfunc'] == 2) {
                    fotoDelete(['parent' => '=' . intval($row['id'])]);
                }

                foreach ($data_img as $k => $img) {
                    if (!empty($img)) {

                        $img = trim($img);

                        // Полный путь к изображениям
                        if (!empty($_POST['export_imgpath']))
                            $img = '/UserFiles/Image/' . $img;


                        // Проверка изображния
                        $checkImage = checkImage($img, $row['id'], $row['parent']);
                        $img_save = $checkImage['img'];

                        // Создаем новую
                        if (empty($checkImage['check'])) {

                            // Загрузка изображений по ссылке
                            if ($_POST['export_imgload'] == 1 and strstr($img, 'http')) {

                                // Файл загружен
                                if (downloadFile($img, $_SERVER['DOCUMENT_ROOT'] . $img_save))
                                    $img_load++;
                                else
                                    continue;

                                // Новое имя
                                $img = $img_save;
                                $path_parts = pathinfo($img);

                                // Сохранение в webp
                                if ($PHPShopSystem->ifSerilizeParam('admoption.image_webp_save') and $path_parts['extension'] != 'webp' and $path_parts['extension'] != 'mp4' and $path_parts['extension'] != 'mov') {

                                    $thumb = new PHPThumb($_SERVER['DOCUMENT_ROOT'] . $img);
                                    $thumb->setFormat('WEBP');
                                    $name_webp = str_replace([".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF", ".WEBP"], '.webp', $img);

                                    $thumb->save($_SERVER['DOCUMENT_ROOT'] . $name_webp);
                                    @unlink($_SERVER['DOCUMENT_ROOT'] . $img);
                                    $img = $name_webp;
                                }
                            }


                            // Запись в фотогалерее
                            $PHPShopOrmImg = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
                            $PHPShopOrmImg->insert(array('parent_new' => intval($row['id']), 'name_new' => $img, 'num_new' => $k));

                            // Изображение
                            if (!in_array($path_parts['extension'], array('mp4', 'mov'))) {

                                $file = $_SERVER['DOCUMENT_ROOT'] . $img;
                                $name = str_replace(array(".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF", ".webp", ".WEBP"), array("s.png", "s.jpg", "s.jpeg", "s.gif", "s.png", "s.jpg", "s.jpeg", "s.gif", "s.webp", "s.webp"), $file);


                                if (!file_exists($name) and file_exists($file)) {

                                    // Генерация тубнейла 
                                    if (!empty($_POST['export_imgproc'])) {
                                        $thumb = new PHPThumb($file);
                                        $thumb->setOptions(array('jpegQuality' => $width_kratko));
                                        $thumb->resize($img_tw, $img_th);
                                        $thumb->save($name);
                                    } else
                                        copy($file, $name);
                                }

                                // Главное изображение
                                if ($k == 0 and ! empty($file)) {

                                    $row['pic_big'] = $img;

                                    // Главное превью
                                    if ($_POST['export_imgload'] == 2 and empty($row['pic_small'])) {
                                        $row['pic_small'] = $img;
                                    } else if ($_POST['export_imgload'] == 1 and isset($_POST['export_imgproc'])) {
                                        $row['pic_small'] = str_replace(array(".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF", ".webp", ".WEBP"), array("s.png", "s.jpg", "s.jpeg", "s.gif", "s.png", "s.jpg", "s.jpeg", "s.gif", "s.webp", "s.webp"), $img);
                                    }
                                }
                            }
                        } else
                            continue;
                    }
                }
            }
            // Полный путь к изображениями
            else if (isset($_POST['export_imgpath']) and ! empty($row['pic_big']))
                $row['pic_big'] = '/UserFiles/Image/' . $row['pic_big'];

            // Создание данных
            if ($_POST['export_action'] == 'insert') {


                $PHPShopOrm->debug = false;
                $PHPShopOrm->mysql_error = false;

                // Списывание со склада
                if (isset($row['items'])) {
                    switch ($GLOBALS['admoption_sklad_status']) {

                        case(3):
                            if ($row['items'] < 1) {
                                $row['sklad'] = 1;
                                $row['p_enabled'] = 0;
                            } else {
                                $row['sklad'] = 0;
                                $row['p_enabled'] = 1;
                            }
                            break;

                        case(2):
                            if ($row['items'] < 1) {
                                $row['enabled'] = 0;
                                $row['p_enabled'] = 0;
                            } else {
                                $row['enabled'] = 1;
                                $row['p_enabled'] = 1;
                            }
                            break;

                        default:
                            break;
                    }
                }

                // Дата создания
                $row['datas'] = time();

                // Проверка SEO имени каталога
                if ($subpath[2] == 'catalog' and ! empty($row['name'])) {
                    $uniq_cat_data = (new PHPShopOrm($GLOBALS['SysValue']['base']['categories']))->getOne(['*'], ['name' => '="' . $row['name'] . '"']);

                    // Есть одноименный каталог
                    if (!empty($uniq_cat_data['name'])) {
                        $parent_cat_data = (new PHPShopOrm($GLOBALS['SysValue']['base']['categories']))->getOne(['*'], ['id' => '="' . $uniq_cat_data['parent_to'] . '"']);
                        $row['cat_seo_name'] = PHPShopString::toLatin($row['name']);
                        $row['cat_seo_name'] = PHPShopString::toLatin($parent_cat_data['name']) . '-' . PHPShopString::toLatin($row['name']);
                    } else
                        $row['cat_seo_name'] = PHPShopString::toLatin($row['name']);
                }

                // Проверки пустого имени
                if (empty($row['name']))
                    $uniq = true;

                if (empty($uniq)) {

                    if (isset($row['price'])) {
                        $row['price'] = str_replace(',', '.', $row['price']);
                    }
                    if (isset($row['price_n'])) {
                        $row['price_n'] = str_replace(',', '.', $row['price_n']);
                    }
                    if (isset($row['price2'])) {
                        $row['price2'] = str_replace(',', '.', $row['price2']);
                    }
                    if (isset($row['price3'])) {
                        $row['price3'] = str_replace(',', '.', $row['price3']);
                    }
                    if (isset($row['price4'])) {
                        $row['price4'] = str_replace(',', '.', $row['price4']);
                    }
                    if (isset($row['price5'])) {
                        $row['price5'] = str_replace(',', '.', $row['price5']);
                    }

                    // ID загрузки
                    $row['import_id'] = $_SESSION['import_id'];

                    $insertID = $PHPShopOrm->insert($row, '');
                    if (is_numeric($insertID)) {

                        $PHPShopOrm->clean();

                        // Обновляем ID в фотогалереи нового товара
                        if ($PHPShopOrmImg)
                            $PHPShopOrmImg->update(array('parent_new' => $insertID), array('parent' => '=0'));

                        // Счетчик
                        $csv_load_count++;
                        $csv_load_totale++;

                        // Отчет
                        $GLOBALS['csv_load'][] = $row;
                    }
                }
            }
            // Обновление данных
            else {

                // Настраиваемый ключ
                if (!empty($_POST['export_key'])) {
                    $where = array($_POST['export_key'] => '="' . $row[$_POST['export_key']] . '"');
                    unset($row[$_POST['export_key']]);
                } else {

                    // Обновление по ID
                    if (!empty($row['id'])) {
                        $where = array('id' => '="' . intval($row['id']) . '"');
                        unset($row['id']);
                    }

                    // Обновление по артикулу
                    elseif (!empty($row['uid'])) {
                        $where = array('uid' => '="' . $row['uid'] . '"');
                        unset($row['uid']);
                    }

                    // Обновление по логину
                    elseif (!empty($row['login'])) {
                        $where = array('login' => '="' . $row['login'] . '"');
                        unset($row['login']);
                    }

                    // Ошибка
                    else {
                        unset($row);
                        return false;
                    }
                }

                // Списывание со склада
                if (isset($row['items'])) {
                    switch ($GLOBALS['admoption_sklad_status']) {

                        case(3):
                            if ($row['items'] < 1) {
                                $row['sklad'] = 1;
                                $row['p_enabled'] = 0;
                            } else {
                                $row['sklad'] = 0;
                                $row['p_enabled'] = 1;
                            }
                            break;

                        case(2):
                            if ($row['items'] < 1) {
                                $row['enabled'] = 0;
                                $row['p_enabled'] = 0;
                            } else {
                                $row['enabled'] = 1;
                                $row['p_enabled'] = 1;
                            }
                            break;

                        default:
                            break;
                    }
                }

                // Дата обновления
                $row['datas'] = time();

                // ID загрузки
                $row['import_id'] = $_SESSION['import_id'];

                if (!empty($where)) {
                    $PHPShopOrm->debug = false;
                    if ($PHPShopOrm->update($row, $where, '') === true) {

                        // Обновляем ID в фотогалереи товара по артикулу
                        if (!empty($where['uid']) and is_array($data_img) and $PHPShopOrmImg) {

                            $PHPShopOrmProduct = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
                            $data_product = $PHPShopOrmProduct->select(array('id'), array('uid' => $where['uid']), false, array('limit' => 1));
                            $PHPShopOrmImg->update(array('parent_new' => $data_product['id']), array('parent' => '=0'));
                        }

                        // Счетчик
                        $count = $PHPShopOrm->get_affected_rows();

                        $csv_load_count += $count;
                        $csv_load_totale++;

                        // Отчет
                        if (!empty($count))
                            $GLOBALS['csv_load'][] = $row;
                    }
                }
            }
        }
    }
}

// Построение пути каталогов
function createCategoryPath($category_array, $id, $path = null) {

    if (isset($category_array[$id])) {
        $path .= '/' . $category_array[$id][0];

        if (isset($category_array[$category_array[$id][1]])) {
            $path .= '/' . $category_array[$category_array[$id][1]][0];

            $path .= createCategoryPath($category_array, $category_array[$category_array[$id][1]][0], $path);
            return $path;
        }

        return $path;
    }
}

// Функция обновления
function actionSave() {
    global $PHPShopGUI, $PHPShopSystem, $key_name, $key_name, $result_message, $csv_load_count, $subpath, $csv_load, $csv_load_totale, $img_load;

    // Выбрать настройку
    if ($_POST['exchanges'] != 'new') {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges']);

        // Изменить имя настройки
        if (!empty($_POST['exchanges_new'])) {
            $PHPShopOrm->update(array('name_new' => $_POST['exchanges_new']), array('id' => '=' . intval($_POST['exchanges'])));
        }

        // Настройки для Cron
        if (!empty($_POST['exchanges_cron'])) {
            $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_POST['exchanges'])), false, array("limit" => 1));
            if (is_array($data)) {
                unset($_POST);
                $_POST = unserialize($data['option']);
                $exchanges_name = $data['name'];
                unset($_POST['exchanges_new']);
                unset($_POST['smart']);
            }
        }
    }

    // Удалить настройки
    if (!empty($_POST['exchanges_remove']) and is_array($_POST['exchanges_remove'])) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges']);
        foreach ($_POST['exchanges_remove'] as $v)
            $data = $PHPShopOrm->delete(array('id' => '=' . intval($v)));
    }

    // Раздел из памяти настроек
    if (!empty($_POST['subpath']))
        $subpath[2] = $_POST['subpath'];

    switch ($subpath[2]) {
        case 'catalog':
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
            break;
        case 'user':
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
            break;
        case 'order':
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
            break;
        default: $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
            break;
    }

    $delim = $_POST['export_delim'];

    // Настройка нулевого склада
    $GLOBALS['admoption_sklad_status'] = $PHPShopSystem->getSerilizeParam('admoption.sklad_status');

    // Память настроек
    $memory[$_GET['path']]['export_sortdelim'] = @$_POST['export_sortdelim'];
    $memory[$_GET['path']]['export_sortsdelim'] = @$_POST['export_sortsdelim'];
    $memory[$_GET['path']]['export_imgdelim'] = @$_POST['export_imgdelim'];
    $memory[$_GET['path']]['export_imgpath'] = @$_POST['export_imgpath'];
    $memory[$_GET['path']]['export_uniq'] = @$_POST['export_uniq'];
    $memory[$_GET['path']]['export_action'] = @$_POST['export_action'];
    $memory[$_GET['path']]['export_delim'] = @$_POST['export_delim'];
    $memory[$_GET['path']]['export_imgproc'] = @$_POST['export_imgproc'];
    $memory[$_GET['path']]['export_imgload'] = @$_POST['export_imgload'];
    $memory[$_GET['path']]['export_imgsearch'] = @$_POST['export_search'];
    $memory[$_GET['path']]['export_ai'] = @$_POST['export_ai'];

    // Копируем csv от пользователя
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        if (in_array($_FILES['file']['ext'], ['csv', 'xml', 'yml', 'xlsx', 'xls'])) {
            if (@move_uploaded_file($_FILES['file']['tmp_name'], "csv/" . PHPShopString::toLatin($_FILES['file']['name']) . '.' . $_FILES['file']['ext'])) {
                $csv_file_name = PHPShopString::toLatin($_FILES['file']['name']) . '.' . $_FILES['file']['ext'];
                $csv_file = "csv/" . $csv_file_name;
                $_POST['lfile'] = $GLOBALS['dir']['dir'] . "/phpshop/admpanel/csv/" . $csv_file_name;
            } else
                $result_message = $PHPShopGUI->setAlert('Ошибка сохранения файла <strong>' . $csv_file_name . '</strong> в phpshop/admpanel/csv', 'danger');
        }
    }

    // Читаем csv из URL
    elseif (!empty($_POST['furl'])) {

        // Google
        $path = parse_url($_POST['furl']);
        if ($path['host'] == 'docs.google.com') {
            $a_path = explode("/", $path['path']);
            if (is_array($a_path)) {
                $id = $a_path[3];

                if ($id == 'e') {
                    $id = $a_path[4];
                    $csv_file = $_POST['furl'];
                } else
                    $csv_file = 'https://docs.google.com/spreadsheets/d/' . $id . '/export?format=csv&' . $path['fragment'];

                $csv_file_name = 'Google Таблиц ' . $_POST['exchanges_new'] . $exchanges_name;
                $_POST['export_code'] = 'utf';
                $delim = ',';
            }
        }
        // Url
        else {
            $csv_file = $_POST['furl'];
            $path_parts = pathinfo($csv_file);
            $csv_file_name = $path_parts['basename'];
            $url = true;
        }
    }

    // Читаем csv из файлового менеджера
    elseif (!empty($_POST['lfile'])) {
        $csv_file = $_SERVER['DOCUMENT_ROOT'] . $_POST['lfile'];
        $path_parts = pathinfo($csv_file);
        $csv_file_name = $path_parts['basename'];
    }
    // Автоматизация
    elseif (!empty($_POST['csv_file'])) {
        $csv_file = $_POST['csv_file'];
        $path_parts = pathinfo($csv_file);
        $csv_file_name = $path_parts['basename'];
    }

    // Обработка csv
    if (!empty($csv_file)) {

        PHPShopObj::loadClass('file');

        // ID загрузки
        $_SESSION['import_id'] = md5($csv_file . date("m.d.y"));

        // Автоопределение расширения
        if ($_POST['export_extension'] == 'auto') {

            $_POST['export_extension'] = PHPShopSecurity::getExt($csv_file);

            if (!in_array($_POST['export_extension'], ['csv', 'xls', 'xlsx'])) {

                $find_extension = file($csv_file);

                if (strpos($find_extension['1'], 'yml_catalog') or strpos($find_extension['2'], 'yml_catalog'))
                    $_POST['export_extension'] = 'yml';
                else if (strpos($find_extension['1'], 'google') or strpos($find_extension['2'], 'channel'))
                    $_POST['export_extension'] = 'rss';
                else if (strpos($find_extension['1'], 'КоммерческаяИнформация') or strpos($find_extension['1'], PHPShopString::win_utf8('КоммерческаяИнформация')))
                    $_POST['export_extension'] = 'cml';
            }
        } elseif (empty($_POST['export_extension'])) {
            $_POST['export_extension'] = 'csv';
        }

        // Автоопределение кодировки
        if ($_POST['export_code'] == 'auto') {

            if (in_array($_POST['export_extension'], ['csv', 'xls', 'xlsx'])) {

                if (!$find_extension)
                    $find_extension = file($csv_file);

                if (stripos($find_extension['0'], 'Артикул') or stripos($find_extension['0'], 'Склад') or stripos($find_extension['0'], 'Цена 1') or stripos($find_extension['0'], 'Наименование'))
                    $_POST['export_code'] = 'ansi';
                elseif (stripos($find_extension['0'], PHPShopString::win_utf8('Артикул')) or stripos($find_extension['0'], PHPShopString::win_utf8('Склад')) or stripos($find_extension['0'], PHPShopString::win_utf8('Цена 1')) or stripos($find_extension['0'], PHPShopString::win_utf8('Наименование')))
                    $_POST['export_code'] = 'utf';
            }
        }

        if ($find_extension)
            unset($find_extension);

        // YML
        if ($_POST['export_extension'] == 'yml') {

            if ($xml = simplexml_load_file($csv_file)) {
                $_POST['export_code'] = 'ansi';

                // Товары
                if (empty($subpath[2])) {

                    $yml_array[0] = ["Артикул", "Наименование", "Большое изображение", "Подробное описание", "Склад", "Цена 1", "Вес", "ISO", "Каталог", "Путь каталога", "Характеристики", "Штрихкод", "Подтип", "Подчиненные товары", "Цвет", "Старая цена", "Длина", "Ширина", "Высота", "Внешний код"];

                    // Каталоги
                    foreach ($xml->shop[0]->categories[0]->category as $item) {
                        $category_array[(string) $item->attributes()->id] = [PHPShopString::utf8_win1251((string) $item[0], true), (string) $item->attributes()->parentId];
                    }

                    // Товары
                    foreach ($xml->shop[0]->offers[0]->offer as $item) {

                        $warehouse = 0;
                        $parent2 = $parent = '';

                        // Путь каталога
                        $category_path = createCategoryPath($category_array, (string) $item->categoryId[0]);
                        $category_path = substr($category_path, 1, strlen($category_path) - 1);
                        $category_path_array = explode("/", $category_path);
                        $category_path = implode("/", array_reverse($category_path_array));

                        // Склад
                        if (isset($item->count[0]))
                            $warehouse = (int) $item->count[0];

                        // Склад
                        if (isset($item->amount[0]))
                            $warehouse = (int) $item->count[0];

                        // Склад
                        if ((string) $item->attributes()->available == "true" and empty($warehouse))
                            $warehouse = 1;

                        // Картинки
                        if (is_array((array) $item->picture)) {
                            $images = implode(",", (array) $item->picture);
                        } else
                            $images = (string) $item->picture;

                        // Видео
                        if (is_array((array) $item->video)) {
                            $images .= ',' . implode(",", (array) $item->video);
                        } else
                            $images .= ',' . (string) $item->video;

                        // Старая цена
                        if (isset($item->oldprice[0]))
                            $oldprice = (string) $item->oldprice[0];

                        // Габариты
                        if (isset($item->dimensions[0])) {
                            $dimensions = explode("/", (string) $item->dimensions[0]);
                            $length = $dimensions[0];
                            $width = $dimensions[1];
                            $height = $dimensions[2];
                        }

                        // Характеристики
                        $sort = null;
                        $i = 0;

                        if (is_array((array) $item->param)) {
                            while ($i < (count((array) $item->param) - 1)) {

                                $sort_name = PHPShopString::utf8_win1251((string) $item->param[$i]->attributes()->name, true);
                                $sort_value = PHPShopString::utf8_win1251((string) $item->param[$i], true);
                                $i++;

                                $sort .= $sort_name . '/' . $sort_value . '#';
                            }
                        } else
                            $sort = (string) $item->param[0];

                        // Бренд
                        if (isset($item->vendor[0]))
                            $sort .= 'Бренд/' . PHPShopString::utf8_win1251((string) $item->vendor[0], true);

                        // Штрихкод
                        if (!empty((string) $item->barcode[0]))
                            $barcode = (string) $item->barcode[0];
                        else
                            $barcode = null;

                        // Артикул
                        if (!empty((string) $item->vendorCode[0])) {
                            $uid = PHPShopString::utf8_win1251((string) $item->vendorCode[0], true);

                            // Внешний код
                            $external_code = (string) $item->attributes()->id;
                        } else {
                            $uid = PHPShopString::utf8_win1251((string) $item->attributes()->id, true);
                            $external_code = null;
                        }

                        // Наименование
                        if (!empty((string) $item->name[0])) {
                            $name = $item->name[0];
                        } else if (!empty((string) $item->model[0]) and (string) $item->attributes()->type == "vendor.model") {
                            $name = $item->model[0];
                        }

                        // Подтипы
                        if (!empty((string) $item->attributes()->group_id)) {

                            $parent_enabled = 1;
                            $sort = null;

                            // Картинки
                            if (is_array((array) $item->picture)) {
                                $images = (string) $item->picture[0];
                            } else
                                $images = (string) $item->picture;

                            if (!empty((string) $item->param[0]))
                                $parent = PHPShopString::utf8_win1251((string) $item->param[0], true);

                            if (!empty((string) $item->param[1]))
                                $parent2 = PHPShopString::utf8_win1251((string) $item->param[1], true);

                            // Главный товар
                            if (!is_array($yml_array[(string) $item->attributes()->group_id])) {

                                // Название
                                $name = ucfirst(trim(str_replace([$parent, $parent2], ['', ''], PHPShopString::utf8_win1251($name, true))));

                                $yml_array[(string) $item->attributes()->group_id] = [(string) $item->attributes()->group_id, $name, $images, nl2br(PHPShopString::utf8_win1251((string) $item->description[0], true)), $warehouse, (string) $item->price[0], ($item->weight[0] * 100), (string) $item->currencyId[0], (string) $item->categoryId[0], $category_path, $sort, $barcode, 0, null, '', $oldprice, $length, $width, $height];
                            } else {

                                // Список подтипов
                                if (!empty((string) $item->vendorCode[0]))
                                    $yml_array[(string) $item->attributes()->group_id][13] .= ',' . $uid;
                                else
                                    $yml_array[(string) $item->attributes()->group_id][13] .= ',' . (string) $item->attributes()->id;

                                // Картинка
                                $yml_array[(string) $item->attributes()->group_id][2] .= ',' . $images;

                                // Минимальная цена
                                if ($yml_array[(string) $item->attributes()->group_id][6] > (string) $item->price[0])
                                    $yml_array[(string) $item->attributes()->group_id][6] = (string) $item->price[0];
                            }
                        }
                        else {
                            $parent_enabled = 0;
                            $parent = $parent2 = '';
                        }


                        $yml_array[$uid] = [$uid, PHPShopString::utf8_win1251($name, true), $images, nl2br(PHPShopString::utf8_win1251((string) $item->description[0], true)), $warehouse, (string) $item->price[0], ($item->weight[0] * 100), (string) $item->currencyId[0], (string) $item->categoryId[0], $category_path, $sort, $barcode, $parent_enabled, $parent, $parent2, $oldprice, $length, $width, $height, $external_code];
                    }

                    if (empty($GLOBALS['exchanges_cron']))
                        $csv_file = './csv/product.yml.csv';
                    else
                        $csv_file = '../../../admpanel/csv/product.yml.csv';
                }
                // Категории
                else if ($subpath[2] == 'catalog') {
                    $yml_array[] = ['Id', 'Наименование', 'Родитель'];
                    foreach ($xml->shop[0]->categories[0]->category as $item) {
                        $yml_array[] = [(string) $item->attributes()->id, PHPShopString::utf8_win1251((string) $item[0], true), (string) $item->attributes()->parentId];
                    }

                    if (empty($GLOBALS['exchanges_cron']))
                        $csv_file = './csv/category.yml.csv';
                    else
                        $csv_file = '../../../admpanel/csv/category.yml.csv';
                }

                // Временный файл
                PHPShopFile::writeCsv($csv_file, $yml_array);
            }
        }

        // RSS
        else if ($_POST['export_extension'] == 'rss') {

            $_POST['export_code'] = 'ansi';
            $feed = str_replace(['g:'], [''], file_get_contents($csv_file));
            $xml = simplexml_load_string($feed);

            // Товары
            $yml_array[] = ["Артикул", "Наименование", "Большое изображение", "Подробное описание", "Склад", "Цена 1", "ISO"];

            foreach ($xml->channel[0]->item as $item) {

                // Склад
                if ((string) $item->availability == "in stock")
                    $warehouse = 1;
                else
                    $warehouse = 0;

                // Картинки
                if (is_array((array) $item->image_link))
                    $images = implode(",", (array) $item->image_link);
                else
                    $images = (string) $item->image_link;

                // Цена
                $price = explode(" ", (string) $item->price[0]);

                $yml_array[] = [(string) $item->id[0], PHPShopString::utf8_win1251((string) $item->title[0]), $images, nl2br(PHPShopString::utf8_win1251((string) $item->description[0])), $warehouse, $price[0], $price[1], (int) $item->categoryId[0]];
            }


            if (empty($GLOBALS['exchanges_cron']))
                $csv_file = './csv/product.rss.csv';
            else
                $csv_file = '../../../admpanel/csv/product.rss.csv';

            // Временный файл
            PHPShopFile::writeCsv($csv_file, $yml_array);
        }

        // XLSX
        else if ($_POST['export_extension'] == 'xlsx') {

            require_once '../lib/simplexlsx/SimpleXLSX.php';
            $_POST['export_code'] = 'utf';

            if ($xlsx = SimpleXLSX::parse($csv_file)) {

                if (empty($GLOBALS['exchanges_cron']))
                    $csv_file = './csv/product.xlsx.csv';
                else
                    $csv_file = '../../../admpanel/csv/product.xlsx.csv';

                // Временный файл
                PHPShopFile::writeCsv($csv_file, $xlsx->rows());
            } else {
                echo SimpleXLSX::parseError();
            }
        }

        // XLS
        else if ($_POST['export_extension'] == 'xls') {

            require_once '../lib/simplexlsx/SimpleXLS.php';
            $_POST['export_code'] = 'utf';

            if ($xls = SimpleXLS::parse($csv_file)) {

                if (empty($GLOBALS['exchanges_cron']))
                    $csv_file = './csv/product.xls.csv';
                else
                    $csv_file = '../../../admpanel/csv/product.xls.csv';

                // Временный файл
                PHPShopFile::writeCsv($csv_file, $xls->rows());
            } else {
                echo SimpleXLS::parseError();
            }
        }
        // Копируем CSV файл локально для автоматизации
        else if ($_POST['export_extension'] == 'csv' and ! empty($url) and ! empty($_POST['smart'])) {

            if (!empty($_POST['furl'])) {
                $csv_file = './csv/' . $path_parts['basename'];
                @file_put_contents($csv_file, @file_get_contents($_POST['furl']));
            }
        }

        // Автоматизация
        if (!empty($_POST['smart'])) {

            $limit = intval($_POST['line_limit']);

            if (empty($_POST['end']))
                $_POST['end'] = intval($_POST['line_limit']);

            $end = $_POST['end'];

            if (isset($_POST['total']) and $_POST['end'] > $_POST['total'])
                $end = $_POST['total'];

            if (empty($_POST['start']))
                $_POST['start'] = 0;

            // Первая загрузка
            if (empty($_POST['total'])) {

                // Строк в файле
                $total = 0;
                $handle = fopen($csv_file, "r");
                while ($data = fgetcsv($handle, 0, $delim)) {
                    $total++;
                }

                $bar = 0;
                $end = 0;
                $csv_load_count = 0;
                $bar_class = "active";

                if ($_POST['export_action'] == 'insert')
                    $do = 'Создано';
                else
                    $do = 'Изменено';

                $total_min = round($total / $_POST['line_limit'] * $_POST['time_limit']);

                $result_message = $PHPShopGUI->setAlert('<div id="bot_result">' . __('Файл') . ' <strong>' . $csv_file_name . '</strong> ' . __('загружен. Обработано ') . $end . __(' из ') . $total . __(' строк. ' . $do) . ' <b id="total-update">' . intval($csv_load_count) . '</b> ' . __('записей.') . '</div>
<div class="progress bot-progress">
  <div class="progress-bar progress-bar-striped  progress-bar-success ' . $bar_class . '" role="progressbar" aria-valuenow="" aria-valuemin="0" aria-valuemax="100" style="width: ' . $bar . '%"> ' . $bar . '% 
  </div>
</div>', 'success load-result', false, false, false);
                $result_message .= $PHPShopGUI->setAlert('<b>Пожалуйста, не закрывайте окно до полной загрузки товаров</b><br>
Вы можете продолжить работу с другими разделами сайта, открывая меню в новой вкладке (нажмите <kbd>CTRL</kbd> и кликните на раздел).', 'info load-info', true, false, false);
                $result_message .= $PHPShopGUI->setInput("hidden", "csv_file", $csv_file);
                $result_message .= $PHPShopGUI->setInput("hidden", "total", $total);
                $result_message .= $PHPShopGUI->setInput("hidden", "stop", 0);
            } else {

                $result = PHPShopFile::readCsvGenerators($csv_file, 'csv_update', $delim, array($_POST['start'], $_POST['end']));
                if ($result) {

                    $total = $_POST['total'];

                    $bar = round($_POST['line_limit'] * 100 / $total);

                    // Конец
                    if ($end > $total) {
                        $end = $total;
                        $bar = 100;
                        $bar_class = null;
                    } else {
                        $bar_class = "active";
                    }

                    if ($_POST['export_action'] == 'insert')
                        $lang_do = __('Создано');
                    else
                        $lang_do = __('Изменено');

                    if ($csv_load_count < 0)
                        $csv_load_count = 0;

                    $total_min = round(($total - $csv_load_count) / $_POST['line_limit'] * $_POST['time_limit']);
                    $action = true;
                    $json_message = __('Файл') . ' <strong>' . $csv_file_name . '</strong> ' . __('загружен. Обработано ') . $end . __(' из ') . $total . __(' строк. ') . $lang_do . ' <b id="total-update">' . intval($csv_load_count) . '</b> ' . __('записей.');

                    // Файл результа
                    if ($_POST['line_limit'] >= 10) {
                        $result_csv = './csv/result_' . date("d_m_y_His") . '.csv';
                        PHPShopFile::writeCsv($result_csv, $GLOBALS['csv_load']);
                    }

                    // Данные для журнала
                    $csv_load_totale = $_POST['start'] . '-' . $_POST['end'];
                } else
                    $result_message = $PHPShopGUI->setAlert(__('Нет прав на запись файла') . ' ' . $csv_file, 'danger', false);
            }
        }
        else {

            $result = PHPShopFile::readCsv($csv_file, 'csv_update', $delim);

            if ($result) {

                if (empty($csv_load_count))
                    $result_message = $PHPShopGUI->setAlert(__('Файл') . ' <strong>' . $csv_file_name . '</strong> ' . __('загружен. Обработано ' . $csv_load_totale . ' строк. Изменено') . ' <strong>' . intval($csv_load_count) . '</strong> ' . __('записей') . '.', 'warning', false);
                else {

                    // Файл результа
                    $result_csv = 'result_' . date("d_m_y_His") . '.csv';
                    if (empty($GLOBALS['exchanges_cron']))
                        PHPShopFile::writeCsv('./csv/' . $result_csv, $csv_load);
                    else
                        PHPShopFile::writeCsv('../../../admpanel/csv/' . $result_csv, $csv_load);

                    if ($_POST['export_action'] == 'insert') {
                        $lang_do = 'Создано';
                        $lang_do2 = 'созданным';
                    } else {
                        $lang_do = 'Изменено';
                        $lang_do2 = 'обновленным';
                    }

                    $result_message = $PHPShopGUI->setAlert(__('Файл') . ' <strong>' . $csv_file_name . '</strong> ' . __('загружен. Обработано ' . $csv_load_totale . ' строк. ' . $lang_do) . ' <strong>' . intval($csv_load_count) . '</strong> ' . __('записей') . '. ' . __('Отчет по ' . $lang_do2 . ' позициям ') . ' <a href="./csv/' . $result_csv . '" target="_blank">CSV</a>.', 'success', false);
                }
            } else {
                $result = 0;
                $result_message = $PHPShopGUI->setAlert(__('Нет прав на запись файла') . ' ' . $csv_file, 'danger', false);
            }
        }
    }

    // Сохранение настройки
    if ($_POST['exchanges'] == 'new' and ! empty($_POST['exchanges_new'])) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges']);
        $PHPShopOrm->insert(array('name_new' => $_POST['exchanges_new'], 'option_new' => serialize($_POST), 'type_new' => 'import'));
    }

    if (!empty($_POST['smart']) and ( empty($_POST['total']) or $_POST['line_limit'] < 10))
        $log_off = true;

    // Журнал загрузок
    if (empty($log_off)) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges_log']);
        $_POST['exchanges'] = $_GET['exchanges'];
        $PHPShopOrm->insert(array('date_new' => time(), 'file_new' => $csv_file, 'status_new' => $result, 'info_new' => serialize([$csv_load_totale, $lang_do, (int) $csv_load_count, $result_csv, (int) $img_load]), 'option_new' => serialize($_POST), 'import_id_new' => $_SESSION['import_id']));
    }

    // Автоматизация
    if (!empty($_POST['ajax'])) {

        if ($total > $end) {

            $bar = round($_POST['end'] * 100 / $total);

            return array("success" => $action, "bar" => $bar, "count" => $csv_load_count, "result" => PHPShopString::win_utf8($json_message), 'limit' => $limit, 'img_load' => (int) $img_load, 'action' => PHPShopString::win_utf8(mb_strtolower($lang_do, $GLOBALS['PHPShopBase']->codBase)));
        } else {

            // Журнал загрузок
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges_log']);
            $_POST['exchanges'] = $_GET['exchanges'];
            $PHPShopOrm->insert(array('date_new' => time(), 'file_new' => $csv_file, 'status_new' => $result, 'info_new' => serialize([$total, $lang_do, (int) ($total - 1), $result_csv, (int) $_POST['img_load']]), 'option_new' => serialize($_POST), 'import_id_new' => $_SESSION['import_id']));


            return array("success" => 'done', "count" => $csv_load_count, "result" => PHPShopString::win_utf8($json_message), 'limit' => $limit, 'action' => PHPShopString::win_utf8(mb_strtolower($lang_do, $GLOBALS['PHPShopBase']->codBase)));
        }
    }
}

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $PHPShopSystem, $TitlePage, $PHPShopOrm, $key_name, $subpath, $key_base, $key_stop, $result_message;


    // Выбрать настройку
    if (!empty($_GET['exchanges'])) {

        $PHPShopOrmExchanges = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges']);
        $data_exchanges = $PHPShopOrmExchanges->select(array('*'), array('id' => '=' . intval($_GET['exchanges'])), false, array("limit" => 1));

        if (is_array($data_exchanges)) {
            $_POST = unserialize($data_exchanges['option']);
            $exchanges_name = ": " . $data_exchanges['name'];
        }
    }

    if (!empty($_POST['export_action'])) {


        $memory[$_GET['path']]['export_sortdelim'] = @$_POST['export_sortdelim'];
        $memory[$_GET['path']]['export_sortsdelim'] = @$_POST['export_sortsdelim'];
        $memory[$_GET['path']]['export_imgdelim'] = @$_POST['export_imgdelim'];
        $memory[$_GET['path']]['export_imgpath'] = @$_POST['export_imgpath'];
        $memory[$_GET['path']]['export_imgload'] = @$_POST['export_imgload'];
        $memory[$_GET['path']]['export_uniq'] = @$_POST['export_uniq'];
        $memory[$_GET['path']]['export_action'] = @$_POST['export_action'];
        $memory[$_GET['path']]['export_delim'] = @$_POST['export_delim'];
        $memory[$_GET['path']]['export_imgproc'] = @$_POST['export_imgproc'];
        $memory[$_GET['path']]['export_code'] = @$_POST['export_code'];
        $memory[$_GET['path']]['smart'] = @$_POST['smart'];
        $memory[$_GET['path']]['export_key'] = @$_POST['export_key'];
        $memory[$_GET['path']]['export_imgfunc'] = @$_POST['export_imgfunc'];
        $memory[$_GET['path']]['export_extension'] = @$_POST['export_extension'];
        $memory[$_GET['path']]['export_imgsearch'] = @$_POST['export_imgsearch'];
        $memory[$_GET['path']]['export_ai'] = @$_POST['export_ai'];


        $export_sortdelim = @$memory[$_GET['path']]['export_sortdelim'];
        $export_sortsdelim = @$memory[$_GET['path']]['export_sortsdelim'];
        $export_imgvalue = @$memory[$_GET['path']]['export_imgdelim'];
        $export_code = $memory[$_GET['path']]['export_code'];
        $export_extension = $memory[$_GET['path']]['export_extension'];
        $export_key = $memory[$_GET['path']]['export_key'];
        $export_imgfunc = @$memory[$_GET['path']]['export_imgfunc'];
        $export_imgload = $memory[$_GET['path']]['export_imgload'];
    }
    // Настройки по умолчанию
    else {
        $memory[$_GET['path']]['export_imgload'] = 1;
        $memory[$_GET['path']]['export_imgproc'] = 1;
        $memory[$_GET['path']]['export_imgsearch'] = 0;
        $memory[$_GET['path']]['export_ai'] = 0;
        $export_imgload = 1;

        $_POST['line_limit'] = 1;

        if ($_GET['path'] == 'exchange.import')
            $_POST['smart'] = 1;

        if ($subpath[2] == 'catalog')
            $memory[$_GET['path']]['export_action'] = 'insert';

        /*
          if ($GLOBALS['PHPShopBase']->codBase == 'utf-8')
          $export_code = 'utf';
          else
          $export_code = 'ansi'; */

        $export_code = 'auto';
        $export_sortsdelim = ',';
    }

    $PHPShopGUI->action_button['Импорт'] = array(
        'name' => __('Выполнить'),
        'action' => 'saveID',
        'class' => 'btn btn-primary btn-sm navbar-btn',
        'type' => 'submit',
        'icon' => 'glyphicon glyphicon-save'
    );

    $list = null;
    $PHPShopOrm->clean();
    $data = $PHPShopOrm->select(array('*'), false, false, array('limit' => 1));
    $select_value[] = array('Не выбрано', false, false);

    // Пустая база
    if (!is_array($data)) {
        $PHPShopOrm->insert(array('name_new' => 'Тестовый товар'));
        $PHPShopOrm->clean();
        $data = $PHPShopOrm->select(array('*'), false, false, array('limit' => 1));
        $PHPShopOrm->delete(array('name' => '="Тестовый товар"'));

        if (empty($subpath[2]))
            $memory[$_GET['path']]['export_action'] = 'insert';
    }

    if (is_array($data)) {

        // Путь каталога
        if (empty($subpath[2])) {
            $data['path'] = null;
        }

        $key_value[] = array('Id или Артикул', 0);

        foreach ($data as $key => $val) {

            if (!empty($key_name[$key]))
                $name = $key_name[$key];
            else
                $name = $key;

            if (@in_array($key, $key_base)) {
                if ($key == 'id')
                    $kbd_class = 'enabled';
                else
                    $kbd_class = null;

                $list .= '<div class="pull-left" style="width:190px;min-height: 19px;"><kbd class="' . $kbd_class . '">' . __(ucfirst($name)) . '</kbd></div>';
                $help = 'data-subtext="<span class=\'glyphicon glyphicon-flag text-success\'></span>"';
            }
            elseif (!in_array($key, $key_stop)) {
                $list .= '<div class="pull-left" style="width:190px;min-height: 19px;">' . __(ucfirst($name)) . '</div>';
                $help = null;
            }

            if (!in_array($key, $key_stop)) {

                if ($GLOBALS['PHPShopBase']->codBase == 'utf-8')
                    $select_value[] = array(ucfirst($name), $key, false, $help);
                else
                    $select_value[] = array(ucfirst($name), __(ucfirst($name)), false, $help);

                // Ключ обновления
                if ($key != 'id' and $key != 'uid' and $key != 'vendor' and $key != 'vendor_array') {
                    $key_value[] = array(ucfirst($name), $key, $export_key);
                }
            }
        }
    } else
        $list = '<span class="text-warning hidden-xs">' . __('Недостаточно данных для создания карты полей. Создайте одну запись в нужном разделе в ручном режиме для начала работы') . '.</span>';


    // Размер названия поля
    $PHPShopGUI->field_col = 4;
    $PHPShopGUI->addJSFiles('./exchange/gui/exchange.gui.js');
    $PHPShopGUI->_CODE = $result_message;

    // Товары
    if (empty($subpath[2])) {
        $class = $yml = $class_ai = false;
        $TitlePage .= ' ' . __('товаров');
        $data['path'] = null;
    }

    // Каталоги
    elseif ($subpath[2] == 'catalog') {
        $class = 'hide';
        $yml = $class_ai = false;
        $TitlePage .= ' ' . __('каталогов');
    }

    // Пользователи
    elseif ($subpath[2] == 'user') {
        $class = $yml = $class_ai = 'hide';
        $TitlePage .= ' ' . __('пользователей');
    }

    // Пользователи
    elseif ($subpath[2] == 'order') {
        $class = $yml = $class_ai = 'hide';
    }

    $PHPShopGUI->setActionPanel($TitlePage . $exchanges_name, false, array('Импорт'));

    $delim_value[] = array('Точка с запятой', ';', @$memory[$_GET['path']]['export_delim']);
    $delim_value[] = array('Запятая', ',', @$memory[$_GET['path']]['export_delim']);

    $action_value[] = array('Обновление', 'update', @$memory[$_GET['path']]['export_action']);
    $action_value[] = array('Создание', 'insert', @$memory[$_GET['path']]['export_action']);

    $delim_sortvalue[] = array('#', '#', $export_sortdelim);
    $delim_sortvalue[] = array('@', '@', $export_sortdelim);
    $delim_sortvalue[] = array('$', '$', $export_sortdelim);
    $delim_sortvalue[] = array(__('Колонка'), '-', $export_sortdelim);

    $delim_sort[] = array('/', '/', $export_sortsdelim);
    $delim_sort[] = array('|', '|', $export_sortsdelim);
    $delim_sort[] = array('-', '-', $export_sortsdelim);
    $delim_sort[] = array('&', '&', $export_sortsdelim);
    $delim_sort[] = array(';', ';', $export_sortsdelim);
    $delim_sort[] = array(',', ',', $export_sortsdelim);

    $delim_imgvalue[] = array(__('Автоматический'), 0, $export_imgvalue);
    $delim_imgvalue[] = array(__('Запятая'), ',', $export_imgvalue);
    $delim_imgvalue[] = array(__('Точка с запятой'), ';', $export_imgvalue);
    $delim_imgvalue[] = array('#', '#', $export_imgvalue);
    $delim_imgvalue[] = array(__('Пробел'), ' ', $export_imgvalue);

    $code_value[] = array('Автоматическая', 'auto', $export_code);
    $code_value[] = array('ANSI', 'ansi', $export_code);
    $code_value[] = array('UTF-8', 'utf', $export_code);

    $code_extension[] = array('Автоматический', 'auto', $export_extension);
    $code_extension[] = array('Excel (CSV)', 'csv', $export_extension);
    $code_extension[] = array('Яндекс (YML)', 'yml', $export_extension);
    $code_extension[] = array('Google (RSS)', 'rss', $export_extension);
    $code_extension[] = array('Excel (XLSX)', 'xlsx', $export_extension);
    $code_extension[] = array('Excel (XLS)', 'xls', $export_extension);

    $imgfunc_value[] = array(__('Добавить фото к существующим'), 0, $export_imgfunc);
    $imgfunc_value[] = array(__('Заменить фото в базе, без удаления с сервера'), 1, $export_imgfunc);
    $imgfunc_value[] = array(__('Заменить и удалить фото на сервере'), 2, $export_imgfunc);

    $imgload_value[] = array(__('Игнорировать'), 0, $export_imgload);
    $imgload_value[] = array(__('Загрузить по внешней ссылке'), 1, $export_imgload);
    $imgload_value[] = array(__('Прописать ссылку в базе'), 2, $export_imgload);

    // AI
    if (empty($PHPShopSystem->ifSerilizeParam('admoption.yandexcloud_enabled'))) {
        $yandexcloud = $PHPShopGUI->setField('Создание описаний с помощью AI', $PHPShopGUI->setCheckbox('export_ai', 1, null, @$memory[$_GET['path']]['export_ai'], $PHPShopGUI->disabled_yandexcloud), 1, 'Создание и обработка описаний с помощью AI. Требуется подписка Yandex Cloud.', $class_ai) .
                $PHPShopGUI->setField('Поиск изображений в Яндекс', $PHPShopGUI->setCheckbox('export_imgsearch', 1, null, @$memory[$_GET['path']]['export_imgsearch'], $PHPShopGUI->disabled_yandexcloud), 1, 'Поиск изображений в Яндексе по имени товара. Требуется подписка Yandex Cloud.', $class_ai);
    }

    // Закладка 1
    $Tab1 = $PHPShopGUI->setField("Файл", $PHPShopGUI->setFile($_POST['lfile']), 1, 'Поддерживаются файлы csv, xls, xlsx, yml, xml') .
            $PHPShopGUI->setField('Действие', $PHPShopGUI->setSelect('export_action', $action_value, 150, true)) .
            $PHPShopGUI->setField('CSV-разделитель', $PHPShopGUI->setSelect('export_delim', $delim_value, 150, true)) .
            $PHPShopGUI->setField('Разделитель для характеристик', $PHPShopGUI->setSelect('export_sortdelim', $delim_sortvalue, 150), false, 'Для формата Excel', $class) .
            $PHPShopGUI->setField('Разделитель значений характеристик', $PHPShopGUI->setSelect('export_sortsdelim', $delim_sort, 150), false, 'Для формата Excel', $class) .
            $yandexcloud .
            $PHPShopGUI->setField('Обработка изображений', $PHPShopGUI->setCheckbox('export_imgproc', 1, null, @$memory[$_GET['path']]['export_imgproc']), 1, 'Создание изображения для превью и ватермарк', $class) .
            $PHPShopGUI->setField('Загрузка изображений', $PHPShopGUI->setSelect('export_imgload', $imgload_value, 250), 1, 'Загрузить изображения или использовать ссылки', $class) .
            $PHPShopGUI->setField('Действие для изображений', $PHPShopGUI->setSelect('export_imgfunc', $imgfunc_value, 250), 1, 'Заменить на новые или дополнить изображения', $class) .
            $PHPShopGUI->setField('Разделитель для изображений', $PHPShopGUI->setSelect('export_imgdelim', $delim_imgvalue, 150), 1, 'Дополнительные изображения для формата Excel', $class) .
            $PHPShopGUI->setField('Кодировка текста', $PHPShopGUI->setSelect('export_code', $code_value, 150, true)) .
            $PHPShopGUI->setField('Тип файла', $PHPShopGUI->setSelect('export_extension', $code_extension, 150, true), 1, null, $yml) .
            $PHPShopGUI->setField('Ключ обновления', $PHPShopGUI->setSelect('export_key', $key_value, 150, true, false, true), 1, 'Изменение ключа обновления может привести к порче данных', $class) .
            $PHPShopGUI->setField('Проверка уникальности', $PHPShopGUI->setCheckbox('export_uniq', 1, null, @$memory[$_GET['path']]['export_uniq']), 1, 'Исключает дублирование данных при создании', $class);

    // Память
    if (is_array($_POST['select_action'])) {

        foreach ($_POST['select_action'] as $x => $p)
            if (is_array($select_value)) {
                $select_value_pre = [];
                foreach ($select_value as $k => $v) {

                    if ($GLOBALS['PHPShopBase']->codBase == 'utf-8') {

                        if ($v[1] == $p or ( strstr($v[0], '@') and strstr($p, '@')))
                            $v[2] = 'selected';
                        else
                            $v[2] = null;
                    } else {
                        if ($v[0] == $p or ( strstr($v[0], '@') and strstr($p, '@')))
                            $v[2] = 'selected';
                        else
                            $v[2] = null;
                    }



                    $select_value_pre[] = [$v[0], $v[1], $v[2], $v[3]];
                }
                ${'select_value' . ($x + 1)} = $select_value_pre;
            }
    }else {
        $n = 1;
        while ($n < 21) {
            ${'select_value' . ($n)} = $select_value;
            $n++;
        }
    }

    // Закладка 2
    $Tab2 = $PHPShopGUI->setField(array('Колонка A', 'Колонка B'), array($PHPShopGUI->setSelect('select_action[]', $select_value1, 150, true, false, true), $PHPShopGUI->setSelect('select_action[]', $select_value2, 150, true, false, true)), array(array(3, 2), array(2, 2)));
    $Tab2 .= $PHPShopGUI->setField(array('Колонка C', 'Колонка D'), array($PHPShopGUI->setSelect('select_action[]', $select_value3, 150, true, false, true), $PHPShopGUI->setSelect('select_action[]', $select_value4, 150, true, false, true)), array(array(3, 2), array(2, 2)));
    $Tab2 .= $PHPShopGUI->setField(array('Колонка E', 'Колонка F'), array($PHPShopGUI->setSelect('select_action[]', $select_value5, 150, true, false, true), $PHPShopGUI->setSelect('select_action[]', $select_value6, 150, true, false, true)), array(array(3, 2), array(2, 2)));
    $Tab2 .= $PHPShopGUI->setField(array('Колонка G', 'Колонка H'), array($PHPShopGUI->setSelect('select_action[]', $select_value7, 150, true, false, true), $PHPShopGUI->setSelect('select_action[]', $select_value8, 150, true, false, true)), array(array(3, 2), array(2, 2)));
    $Tab2 .= $PHPShopGUI->setField(array('Колонка I', 'Колонка J'), array($PHPShopGUI->setSelect('select_action[]', $select_value9, 150, true, false, true), $PHPShopGUI->setSelect('select_action[]', $select_value10, 150, true, false, true)), array(array(3, 2), array(2, 2)));
    $Tab2 .= $PHPShopGUI->setField(array('Колонка K', 'Колонка L'), array($PHPShopGUI->setSelect('select_action[]', $select_value11, 150, true, false, true), $PHPShopGUI->setSelect('select_action[]', $select_value12, 150, true, false, true)), array(array(3, 2), array(2, 2)));
    $Tab2 .= $PHPShopGUI->setField(array('Колонка M', 'Колонка N'), array($PHPShopGUI->setSelect('select_action[]', $select_value13, 150, true, false, true), $PHPShopGUI->setSelect('select_action[]', $select_value14, 150, true, false, true)), array(array(3, 2), array(2, 2)));
    $Tab2 .= $PHPShopGUI->setField(array('Колонка O', 'Колонка P'), array($PHPShopGUI->setSelect('select_action[]', $select_value15, 150, true, false, true), $PHPShopGUI->setSelect('select_action[]', $select_value16, 150, true, false, true)), array(array(3, 2), array(2, 2)));
    $Tab2 .= $PHPShopGUI->setField(array('Колонка Q', 'Колонка R'), array($PHPShopGUI->setSelect('select_action[]', $select_value17, 150, true, false, true), $PHPShopGUI->setSelect('select_action[]', $select_value18, 150, true, false, true)), array(array(3, 2), array(2, 2)));
    $Tab2 .= $PHPShopGUI->setField(array('Колонка S', 'Колонка T'), array($PHPShopGUI->setSelect('select_action[]', $select_value19, 150, true, false, true), $PHPShopGUI->setSelect('select_action[]', $select_value20, 150, true, false, true)), array(array(3, 2), array(2, 2)));

    // Закладка 3
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges']);
    $data = $PHPShopOrm->select(array('*'), array('type' => '="import"'), array('order' => 'id DESC'), array("limit" => "1000"));
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

    if (is_array($exchanges_remove_value))
        $Tab3 .= $PHPShopGUI->setField('Удалить настройки', $PHPShopGUI->setSelect('exchanges_remove[]', $exchanges_remove_value, 300, false, false, false, false, 1, true));

    // Закладка 4
    if (empty($_POST['time_limit']))
        $_POST['time_limit'] = 10;

    if (empty($_POST['line_limit']))
        $_POST['line_limit'] = 50;

    if (empty($_POST['smart']))
        $_POST['smart'] = null;

    $Tab4 = $PHPShopGUI->setField('Лимит строк', $PHPShopGUI->setInputText(null, 'line_limit', $_POST['line_limit'], 150), 1, 'Зависит от скорости хостинга');
    //$Tab4 .= $PHPShopGUI->setField('Временной интервал', $PHPShopGUI->setInputText(null, 'time_limit', $_POST['time_limit'], 150, __('секунд')), 1, 'Зависит от скорости хостинга');
    //$Tab4 .= $PHPShopGUI->setInput("hidden", "line_limit", $_POST['line_limit']);
    $Tab4 .= $PHPShopGUI->setField("Помощник", $PHPShopGUI->setCheckbox('smart', 1, __('Умная загрузка для соблюдения правила ограничений на хостинге'), @$_POST['smart'], false, false));

    $Tab1 = $PHPShopGUI->setCollapse('Настройки', $Tab1);
    $Tab2 = $PHPShopGUI->setCollapse('Подсказка', $PHPShopGUI->setHelp(__('Если вы загружаете файл, который скачали в меню "База" &rarr; "Экспорт базы", и он содержит') . ' <a name="import-col-name" href="#">' . __('штатные заголовки столбцов') . '</a> - ' . __('сопоставление полей делать <b>не нужно</b>. Если это сторонний прайс со своими названиями колонок, сделайте <b>Cопоставление полей</b>.') . '<div style="margin-top:10px" id="import-col-name" class="none panel panel-default"><div class="panel-body">' . $list . '</div></div>', false, false)) .
            $PHPShopGUI->setCollapse('Сопоставление полей', $Tab2);

    $Tab3 = $PHPShopGUI->setCollapse('Сохраненные настройки', $Tab3);
    $Tab4 = $PHPShopGUI->setCollapse('Автоматизация', $Tab4);

    $Tab5 = $PHPShopGUI->loadLib('tab_log', $data, 'exchange/');
    if (!empty($Tab5))
        $Tab5_status = false;
    else
        $Tab5_status = true;

    $PHPShopGUI->tab_return = true;
    $PHPShopGUI->setTab(array('Настройки', $Tab1, true), array('Сопоставление полей', $Tab2, true), array('Сохраненные настройки', $Tab3, true), array('Автоматизация', $Tab4, true), array('История импортов', $Tab5, true, $Tab5_status));

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", true, "right", 70, "", "but") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.exchange.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.exchange.edit");

    $PHPShopGUI->setFooter($ContentFooter);

    $help = '<p class="text-muted data-row">' . __('Для импорта данных нужно скачать') . ' <a href="?path=exchange.export"><span class="glyphicon glyphicon-share-alt"></span>' . __('Пример файла') . '</a>' . __(', выбрав нужные вам поля. Далее добавьте или измените нужную информацию, не нарушая структуру, и выберите меню') . ' <em> ' . __('"Импорт данных"') . '</em></p>';

    $sidebarleft[] = array('title' => 'Тип данных', 'content' => $PHPShopGUI->loadLib('tab_menu', false, './exchange/'));
    $sidebarleft[] = array('title' => 'Подсказка', 'content' => $help, 'class' => 'hidden-xs');

    $PHPShopGUI->setSidebarLeft($sidebarleft, 2);

    // Футер
    $PHPShopGUI->Compile(2);

    return true;
}

// Обработка характеристик
class sortCheck {

    var $debug = false;

    function __construct($name, $value, $category, $debug = false) {

        $this->debug = $debug;

        $this->debug('Дано характеристика "' . $name . '" = "' . $value . '" в каталоге с ID=' . $category);

        // Проверка имени характеристики 
        $check_name = (new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']))->getOne(['*'], ['name' => '="' . $name . '"']);
        if ($check_name) {

            $this->debug('Есть характеристика "' . $name . '" c ID=' . $check_name['id'] . ' и CATEGORY=' . $check_name['category']);

            // Проверка значения характеристики
            $check_value = (new PHPShopOrm($GLOBALS['SysValue']['base']['sort']))->getOne(['*'], ['name' => '="' . $value . '"', 'category' => '="' . $check_name['id'] . '"']);
            if ($check_value) {
                $this->debug('Есть значение характеристики "' . $name . '" = "' . $value . '" c ID=' . $check_value['id']);

                // Проверка категории набора характеристики
                $check_category = (new PHPShopOrm($GLOBALS['SysValue']['base']['categories']))->getOne(['*'], ['id' => '="' . $category . '"']);
                $sort = unserialize($check_category['sort']);

                if (is_array($sort) and in_array($check_name['id'], $sort)) {
                    $this->debug('Есть набор характеристики "' . $name . '" = "' . $value . '" c ID=' . $check_value['id'] . ' в каталоге ' . $check_category['name'] . '" с ID=' . $category);
                } else {
                    $sort_categories = (new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']))->getOne(['*'], ['id' => '=' . $check_name['category']]);
                    $this->debug('Нет набор характеристики "' . $sort_categories['name'] . '" c ID=' . $check_name['category'] . ' в каталоге ' . $check_category['name'] . '" с ID=' . $category);

                    // Добавление в категорию набора характеристики
                    $sort[] = $check_name['id'];
                    (new PHPShopOrm($GLOBALS['SysValue']['base']['categories']))->update(['sort_new' => serialize($sort)], ['id' => '=' . $category]);
                    $this->debug('Набор характеристик "' . $sort_categories['name'] . '" c ID=' . $check_name['category'] . ' добавлен в каталог "' . $check_category['name'] . '" с ID=' . $category);

                    $result[$check_name['id']][] = $check_value['id'];
                }
                $result[$check_name['id']][] = $check_value['id'];
            } else {
                $this->debug('Нет значения характеристики "' . $name . '" = "' . $value . '"');

                // Создание нового значения характеристики
                $new_value_id = (new PHPShopOrm($GLOBALS['SysValue']['base']['sort']))->insert(['name_new' => $value, 'category_new' => $check_name['id'], 'sort_seo_name_new' => str_replace("_", "-", PHPShopString::toLatin($value))]);

                $this->debug('Создание нового значения характеристики "' . $name . '" = "' . $value . '" c ID=' . $new_value_id);
                $result[$check_name['id']][] = $new_value_id;
            }
        } else {

            $this->debug('Нет характеристики "' . $name . '"');

            // Проверка категории набора характеристики
            $check_category = (new PHPShopOrm($GLOBALS['SysValue']['base']['categories']))->getOne(['*'], ['id' => '="' . $category . '"']);
            $sort = unserialize($check_category['sort']);

            // У каталога есть характеристики
            if (is_array($sort)) {

                // Проверка значения характеристики
                foreach ($sort as $val) {
                    $check_value = (new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']))->getOne(['*'], ['id' => '=' . $val]);
                    if (!empty($check_value['category'])) {
                        $sort_categories = $check_value['category'];
                        continue;
                    }
                }

                $this->debug('Выбран набор характеристик c ID=' . $sort_categories);
            }
            // У каталога нет набора характеристик
            else {

                // Проверка общей группы
                $sort_categories_name = __('Общая группа');
                $sort_categories = (new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']))->getOne(['*'], ['name' => '="' . $sort_categories_name . '"'])['id'];

                // Создание общего набора характеристик
                if (empty($sort_categories)) {

                    $sort_categories = (new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']))->insert(['name_new' => $sort_categories_name, 'category_new' => 0]);
                    $this->debug('Создание нового набор характеристик "' . $sort_categories_name . '" c ID=' . $sort_categories . ' ');
                }
            }

            // Создание новой характеристики 
            if (!empty($sort_categories)) {
                $new_name_id = (new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']))->insert(['name_new' => $name, 'category_new' => $sort_categories]);
                $this->debug('Создание новой характеристики "' . $name . '" c ID=' . $new_name_id . ' в группе характеристик ID=' . $sort_categories);

                // Создание нового значения характеристики
                $new_value_id = (new PHPShopOrm($GLOBALS['SysValue']['base']['sort']))->insert(['name_new' => $value, 'category_new' => $new_name_id, 'sort_seo_name_new' => str_replace("_", "-", PHPShopString::toLatin($value))]);
                $this->debug('Создание нового значения характеристики "' . $name . '" = "' . $value . '" c ID=' . $new_value_id);

                // Добавление в категорию характеристики
                $sort[] = $new_name_id;
                (new PHPShopOrm($GLOBALS['SysValue']['base']['categories']))->update(['sort_new' => serialize($sort)], ['id' => '=' . $category]);
                $this->debug('Характеристика "' . $name . '" c ID=' . $new_name_id . ' добавлен в каталог "' . $check_category['name'] . '" с ID=' . $category);

                $result[$new_name_id][] = $new_value_id;
            }
        }

        $this->result = $result;
    }

    // Отладка
    function debug($str) {
        if ($this->debug)
            echo $str . PHP_EOL . '<br>';
    }

    // Результат
    function result() {
        return $this->result;
    }

}

// Удаление фотогалереи
function fotoDelete($where = null) {

    if (!is_array($where))
        $where = array('parent' => '=' . intval($_POST['rowID']));

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
    $data = $PHPShopOrm->select(array('*'), $where, false, array('limit' => 100));
    if (is_array($data)) {
        foreach ($data as $row) {
            $name = $row['name'];
            $pathinfo = pathinfo($name);
            $oldWD = getcwd();
            $dirWhereRenameeIs = $_SERVER['DOCUMENT_ROOT'] . $pathinfo['dirname'];
            $oldFilename = $pathinfo['basename'];

            @chdir($dirWhereRenameeIs);
            @unlink($oldFilename);
            $oldFilename_s = str_replace(".", "s.", $oldFilename);
            @unlink($oldFilename_s);
            $oldFilename_big = str_replace(".", "_big.", $oldFilename);
            @unlink($oldFilename_big);
            @chdir($oldWD);
        }
        $PHPShopOrm->clean();
        $result = $PHPShopOrm->delete($where);

        // Проверка главного изображения товара
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
        $data_main = $PHPShopOrm->getOne(array('pic_big'), array('id' => '=' . intval($row['parent'])));

        if (is_array($data_main) and $name == $data_main['pic_big']) {
            $result = $PHPShopOrm->update(array('pic_small_new' => '', 'pic_big_new' => ''), array('id' => '=' . intval($row['parent'])));
        }


        return $result;
    }
}

// Обработка событий
$PHPShopGUI->getAction();
?>