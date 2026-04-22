<?php

$TitlePage = __("Обновить товары");
PHPShopObj::loadClass('valuta');
PHPShopObj::loadClass('category');

$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);

// Определение визульного вывода поля
function getKeyView($val) {
    global $key_placeholder, $key_format;

    if (strpos($val['Type'], "(")) {
        $a = explode("(", $val['Type']);
        $b = $a[0];
    } else
        $b = $val['Type'];

    $key_view = array(
        'varchar' => array('type' => 'text', 'name' => $val['Field'] . '_new', 'placeholder' => $key_placeholder[$val['Field']]),
        'text' => array('type' => 'textarea', 'height' => 150, 'name' => $val['Field'] . '_new', 'placeholder' => $key_placeholder[$val['Field']]),
        'int' => array('type' => 'text', 'size' => 100, 'name' => $val['Field'] . '_new', 'placeholder' => $key_placeholder[$val['Field']]),
        'float' => array('type' => 'text', 'size' => 200, 'name' => $val['Field'] . '_new', 'placeholder' => $key_placeholder[$val['Field']]),
        'enum' => array('type' => 'checkbox', 'name' => $val['Field'] . '_new', 'value' => 1, 'caption' => 'Вкл.'),
        'radio' => array('type' => 'checkbox', 'name' => $val['Field'] . '_new', 'value' => 1, 'caption' => 'Вкл.'),
        'editor' => array('type' => 'editor', 'name' => $val['Field'] . '_new',)
    );

    if (!empty($key_format[$val['Field']])) {
        return $key_view[$key_format[$val['Field']]];
    } else if (!empty($key_view[$b]))
        return $key_view[$b];
    else
        return array('type' => 'text', 'name' => $val['Field'] . '_new');
}

// Описания полей
$key_name = array(
    'id' => 'Id',
    'name' => '<b>Наименование</b>',
    'uid' => 'Артикул',
    'price' => '<b>Цена 1</b>',
    'price2' => 'Цена 2',
    'price3' => 'Цена 3',
    'price4' => 'Цена 4',
    'price5' => 'Цена 5',
    'price_n' => 'Старая цена',
    'sklad' => 'Нет в наличии',
    'newtip' => 'Новинка',
    'spec' => 'Спецпредложение',
    'items' => '<b>Склад</b>',
    'weight' => 'Вес',
    'num' => 'Приоритет',
    'enabled' => '<b>Вывод</b>',
    'content' => 'Подробное описание',
    'description' => 'Краткое описание',
    'pic_small' => 'Маленькое изображение',
    'pic_big' => 'Большое изображение',
    'category' => 'Категория',
    'yml' => 'Яндекс.Маркет',
    'icon' => 'Иконка',
    'parent_to' => 'Родитель',
    'category' => 'Каталог',
    'title' => 'Meta Title',
    'login' => 'Логин',
    'tel' => 'Телефон',
    'datas' => 'Дата',
    'cumulative_discount' => 'Накопительная скидка',
    'seller' => 'Статус загрузки в 1С',
    'statusi' => 'Статус состояния заказа',
    'fio' => 'Ф.И.О',
    'city' => 'Город',
    'street' => 'Улица',
    'orders' => 'Сумма',
    'odnotip' => 'Сопутствующие товары (IDS)',
    'page' => 'Страницы',
    'parent' => 'Подчиненные товары (IDS)',
    'dop_cat' => 'Дополнительные каталоги',
    'ed_izm' => 'Единица измерения',
    'baseinputvaluta' => 'Валюта (ID)',
    'p_enabled' => 'Яндекс.Маркет в наличии',
    'rate' => 'Рейтинг',
    'rate_count' => 'Голоса в рейтинге',
    'descrip' => 'Meta description',
    'keywords' => 'Meta keywords',
    'parent_enabled' => 'Подтип товара',
    'price_search' => 'Цена для поиска',
    'prod_seo_name' => 'SEO ссылка',
    'vendor_array' => 'Характеристики',
    'vendor_name' => 'Производитель',
    'items1' => 'Склад 2',
    'items2' => 'Склад 3',
    'items3' => 'Склад 4',
    'items4' => 'Склад 5',
    'files' => 'Файлы',
    'width' => 'Ширина',
    'height' => 'Высота',
    'color' => 'Цвет',
    'length' => 'Длина',
    'price_purch' => 'Закупочная цена',
    'hit' => 'Хит',
    'external_code' => 'Внешний код',
    'productservices_products' => 'Услуги'
);

$key_placeholder = array(
    'dop_cat' => '#10#11#',
    'odnotip' => '10,11,12',
    'parent' => '10,11,12',
);

// Стоп лист
$key_stop = array('id', 'password', 'wishlist', 'datas', 'data_adres', 'sort', 'yml_bid_array', 'vendor', 'status', 'user', 'title_enabled', 'descrip_enabled', 'title_shablon', 'descrip_shablon', 'title_shablon', 'keywords_enabled', 'keywords_shablon', 'parent2', 'type', 'prod_seo_name_old');

// Настраиваемые поля
if (!empty($GLOBALS['SysValue']['base']['productoption']['productoption_system'])) {
    $PHPShopOrmOptions = new PHPShopOrm($GLOBALS['SysValue']['base']['productoption']['productoption_system']);
    $m_data = $PHPShopOrmOptions->select();
    $vendor = unserialize($m_data['option']);

    if (!empty($vendor['option_1_name'])) {
        $key_name['option1'] = ucfirst($vendor['option_1_name']);
        $key_format['option1'] = $vendor['option_1_format'];
    } else
        $key_stop[] = 'option1';

    if (!empty($vendor['option_2_name'])) {
        $key_name['option2'] = ucfirst($vendor['option_2_name']);
        $key_format['option2'] = $vendor['option_2_format'];
    } else
        $key_stop[] = 'option2';

    if (!empty($vendor['option_3_name'])) {
        $key_name['option3'] = ucfirst($vendor['option_3_name']);
        $key_format['option3'] = $vendor['option_3_format'];
    } else
        $key_stop[] = 'option3';

    if (!empty($vendor['option_4_name'])) {
        $key_name['option4'] = ucfirst($vendor['option_4_name']);
        $key_format['option4'] = $vendor['option_4_format'];
    } else
        $key_stop[] = 'option4';

    if (!empty($vendor['option_5_name'])) {
        $key_name['option5'] = ucfirst($vendor['option_5_name']);
        $key_format['option5'] = $vendor['option_5_format'];
    } else
        $key_stop[] = 'option5';
}

/**
 * Объединить выбранные
 */
function actionJoin() {
    // Выбранные товары
    if (!empty($_POST['select'])) {
        if (is_array($_POST['select'])) {
            foreach ($_POST['select'] as $k => $v)
                if (!empty($v))
                    $odnotip[intval($k)] = intval($v);

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);

            if (is_array($odnotip)) {
                foreach ($odnotip as $value) {
                    if ((int) $value > 0) {
                        $PHPShopOrm->update(['odnotip_new' => implode(',', $odnotip)], ['id' => '=' . (int) $value]);
                    }
                }

            return array('success' => true);
            }
        }
    }
}

/**
 * Редактировать с выбранными Шаг 1 /
 */
function actionSelect() {
    global $PHPShopGUI, $key_name, $key_stop;

    // Выбранные товары
    if (!empty($_POST['select'])) {
        unset($_SESSION['select']['product']);
        if (is_array($_POST['select'])) {
            foreach ($_POST['select'] as $k => $v)
                if (!empty($v))
                    $select[intval($k)] = intval($v);
            $_SESSION['select']['product'] = $select;
        }
    }

    // Наборы
    $command[] = array('Прайс-лист', 1, false);
    $command[] = array('База Excel', 2, false);

    $PHPShopGUI->_CODE .= '<p class="text-muted">' . __('Вы можете редактировать одновременно несколько записей. Выберите записи из списка выше, отметьте галочкой поля, которые нужно отредактировать, и нажмите на кнопку "Редактировать выбранные".</p><p class="text-muted"><a href="#" id="select-all">Выбрать все</a> | <a href="#" id="select-none">Снять выделение со всех</a>') . '</p>';

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $data = $PHPShopOrm->select(array('*'), false, false, array('limit' => 1));

    if (is_array($data))
        foreach ($data as $key => $val) {

            if ((!in_array($key, $key_stop))) {
                if (!empty($key_name[$key])) {
                    $name = $key_name[$key];
                    $select = 0;
                } else {
                    $name = $key;
                    $select = 0;
                }

                // Память выбранных полей
                if (!empty($_COOKIE['check_memory'])) {
                    $memory = json_decode($_COOKIE['check_memory'], true);
                    if (is_array($memory[$_GET['path']])) {
                        if ($memory[$_GET['path']][$key] == 1)
                            $select = 1;
                        else
                            $select = 0;
                    }
                }

                $PHPShopGUI->_CODE .= '<div class="pull-left" style="width:200px;>' . $PHPShopGUI->setCheckBox($key, 1, ucfirst($name), $select, null, true, false) . '</div>';
            }
        }

    exit($PHPShopGUI->_CODE . '<p class="clearfix"> </p>');
}

// Добавление полей товара в выбор
function actionSelectEdit() {

    unset($_SESSION['select_col']);
    if (!empty($_POST['select_col'])) {
        $_SESSION['select_col'] = $_POST['select_col'];
    }
    return array("success" => true);
}

// Персональное изменение характеристики у товара
function sortParse($current_sort) {
    $current_sort = unserialize($current_sort);

    if (is_array($current_sort))
        foreach ($current_sort as $k => $v) {
            if (empty($_POST['vendor_array_new'][$k]))
                $_POST['vendor_array_new'][$k] = $v;
        }
}

/**
 * Экшен сохранения
 */
function actionSave() {
    global $PHPShopOrm, $PHPShopSystem, $PHPShopModules, $_classPath;

    if (is_array($_SESSION['select']['product'])) {
        $val = array_values($_SESSION['select']['product']);
        $where = array('id' => ' IN (' . implode(',', $val) . ')');
    } else
        $where = null;

    $PHPShopOrm->debug = false;

    // Коррекция подтипов при смене каталога у главного товара
    if (!empty($_POST['category_new'])) {

        $update_option = $PHPShopSystem->ifSerilizeParam('1c_option.update_option');

        if (is_array($val))
            foreach ($val as $id) {

                $PHPShopProduct = new PHPShopProduct($id);
                $parent_enabled = $PHPShopProduct->getParam('parent_enabled');
                $parent = @explode(",", $PHPShopProduct->getParam('parent'));
                if (empty($parent_enabled) and ! empty($parent)) {

                    $category = $PHPShopProduct->getParam('category');

                    if ($category != $_POST['category_new'])
                        $category_update = true;
                    else
                        $category_update = false;

                    // Подтипы из 1С
                    if ($update_option) {

                        if ($category_update) {
                            $PHPShopOrm->update(array('category_new' => $_POST['category_new']), array('uid' => ' IN ("' . @implode('","', $parent) . '")', 'parent_enabled' => "='1'"));
                        }
                    } else {

                        if ($category_update) {
                            $PHPShopOrm->update(array('category_new' => $_POST['category_new']), array('id' => ' IN ("' . @implode('","', $parent) . '")', 'parent_enabled' => "='1'"));
                        }
                    }
                }
            }
    }

    // Сохранение характеристик при смене каталога
    if (!empty($_POST['category_new'])) {

        if (is_array($val))
            foreach ($val as $id) {

                $PHPShopProduct = new PHPShopProduct($id);
                $category = $PHPShopProduct->getParam('category');

                $PHPShopCategory = new PHPShopCategory($category);
                $sort_old = $PHPShopCategory->unserializeParam('sort');

                if (is_array($sort_old)) {
                    $PHPShopCategory = new PHPShopCategory($_POST['category_new']);
                    $sort_new = $PHPShopCategory->unserializeParam('sort');

                    if (is_array($sort_new)) {
                        foreach ($sort_old as $val) {
                            if (!in_array($val, $sort_new))
                                $sort_new[] = $val;
                        }
                    } else
                        $sort_new = $sort_old;

                    $PHPShopCategory->updateParam(array('sort_new' => serialize($sort_new)));
                }
            }
    }

    // Добавление характеристик
    if (is_array($_POST['vendor_array_add'])) {
        foreach ($_POST['vendor_array_add'] as $k => $valS) {

            if (!empty($valS)) {
                $PHPShopOrmSort = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);
                $result = $PHPShopOrmSort->insert(array('name_new' => $valS, 'category_new' => $k));
                if (!empty($result))
                    $_POST['vendor_array_new'][$k][] = $result;
            } else
                unset($_POST['vendor_array_add'][$k]);
        }
    }

    // Изменение характеристик
    if (!empty($_POST['vendor_array_new'])) {

        // Сохранение старых значений характеристик
        $data = $PHPShopOrm->select(array('id,vendor_array'), $where, array('order' => ' FIELD (id, ' . implode(',', $val) . ') '), array('limit' => 1000));
        $vendor_array_new_memory = $_POST['vendor_array_new'];
        if (is_array($data))
            foreach ($data as $val) {
                sortParse($val['vendor_array']);

                $_POST['vendor_new'] = null;
                if (is_array($_POST['vendor_array_new']))
                    foreach ($_POST['vendor_array_new'] as $k => $v) {
                        if (is_array($v)) {
                            foreach ($v as $key => $p) {
                                $_POST['vendor_new'] .= "i" . $k . "-" . $p . "i";
                                if (empty($p))
                                    unset($_POST['vendor_array_new'][$k][$key]);
                            }
                        } else
                            $_POST['vendor_new'] .= "i" . $k . "-" . $v . "i";
                    }


                $_POST['vendor_array_new'] = serialize($_POST['vendor_array_new']);
                $PHPShopOrm->update($_POST, array('id' => '=' . $val['id']));

                // Возвращаем значение из памяти
                $_POST['vendor_array_new'] = $vendor_array_new_memory;
            }
    }

    // Память выбранных полей
    if (is_array($_POST)) {
        $memory = json_decode($_COOKIE['check_memory'], true);
        unset($memory[$_GET['path']]);
        foreach ($_POST as $k => $v) {
            if (strstr($k, '_new'))
                $memory[$_GET['path']][str_replace('_new', '', $k)] = 1;
        }
        if (is_array($memory))
            setcookie("check_memory", json_encode($memory), time() + 3600000, '/phpshop/admpanel/');
    }

    $PHPShopOrm->clean();
    unset($_POST['vendor_array_new']);
    unset($_POST['vendor_new']);

    // Списывание со склада
    if (isset($_POST['items_new'])) {
        switch ($PHPShopSystem->getSerilizeParam('admoption.sklad_status')) {

            case(3):
                if ($_POST['items_new'] < 1) {
                    $_POST['sklad_new'] = 1;
                } else {
                    $_POST['sklad_new'] = 0;
                }
                break;

            case(2):
                if ($_POST['items_new'] < 1) {
                    $_POST['enabled_new'] = 0;
                } else {
                    $_POST['enabled_new'] = 1;
                }
                break;

            default:
                break;
        }
    }

    // Доп каталоги
    if (is_array($_POST['dop_cat']) and $_POST['dop_cat'][0] != 'null') {

        $_POST['dop_cat_new'] = "#";
        foreach ($_POST['dop_cat'] as $v)
            if ($v != 'null' and ! strstr($v, ','))
                $_POST['dop_cat_new'] .= $v . "#";

        // Дополнить
        if ($_POST['action'] == 1) {

            $val = array_values($_SESSION['select']['product']);

            if (is_array($val)) {
                foreach ($val as $id) {
                    $dop_cat = $PHPShopOrm->select(['dop_cat'], ['id' => '=' . $id])['dop_cat'];
                    $PHPShopOrm->update(['dop_cat_new' => $dop_cat . $_POST['dop_cat_new']], ['id' => '=' . $id]);
                }
            }

            unset($_POST['dop_cat_new']);
        }
    } else if (isset($_POST['dop_cat']))
        $_POST['dop_cat_new'] = '';

    // Файлы
    if (is_array($_POST['files_new'])) {
        foreach ($_POST['files_new'] as $k => $files)
            $files_new[] = @array_map("urldecode", $files);

        $_POST['files_new'] = serialize($files_new);
    }

    // Помощь AI
    if (isset($_POST['help_ai_content']) or isset($_POST['help_ai_description']) or isset($_POST['help_ai_title']) or isset($_POST['help_ai_descrip']) or isset($_POST['help_ai_pic_big'])) {

        PHPShopObj::loadClass('yandexcloud');
        $YandexGPT = new YandexGPT();
        require_once $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/parsedown/Parsedown.php';

        $YandexSearch = new YandexSearch();
        $yandexsearch_image_num = (int) $PHPShopSystem->getSerilizeParam('ai.yandexsearch_image_num');

        require_once $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/thumb/phpthumb.php';
        $width_kratko = $PHPShopSystem->getSerilizeParam('admoption.width_kratko');
        $img_tw = $PHPShopSystem->getSerilizeParam('admoption.img_tw');
        $img_th = $PHPShopSystem->getSerilizeParam('admoption.img_th');

        if (is_array($val))
            foreach ($val as $id) {

                $product = $PHPShopOrm->getOne(['name', 'content', 'description'], ['id' => '=' . (int) $id]);
                $name = $product['name'];

                // Подробное описание
                if (isset($_POST['content_new']) and isset($_POST['help_ai_content'])) {

                    $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_product_content_role');
                    $length = 500;
                    $result = $YandexGPT->text($name, $system, $PHPShopSystem->getSerilizeParam('ai.yandexgpt_temperature'), $length);
                    $text = $YandexGPT->html($result['result']['alternatives'][0]['message']['text']);

                    // Добавить или заменить
                    if (!empty($_POST['action']))
                        $content = PHPShopString::utf8_win1251($text) . $product['content'];
                    else
                        $content = PHPShopString::utf8_win1251($text);

                    $PHPShopOrm->update(['content_new' => $content], ['id' => '=' . (int) $id]);
                }

                // Краткое описание
                if (isset($_POST['description_new']) and isset($_POST['help_ai_description'])) {

                    $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_product_description_role');
                    $length = 200;
                    $result = $YandexGPT->text($name, $system, $PHPShopSystem->getSerilizeParam('ai.yandexgpt_temperature'), $length);
                    $text = str_replace(['*', '\n', '\r'], ['', '', ''], $result['result']['alternatives'][0]['message']['text']);
                    $text = preg_replace("/\r|\n/", ' ', $text);

                    // Добавить или заменить
                    if (!empty($_POST['action']))
                        $description = PHPShopString::utf8_win1251($text) . $product['description'];
                    else
                        $description = PHPShopString::utf8_win1251($text);

                    $PHPShopOrm->update(['description_new' => $description], ['id' => '=' . (int) $id]);
                }

                // Title Meta
                if (isset($_POST['title_new']) and isset($_POST['help_ai_title'])) {

                    $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_product_title_role');
                    $length = 70;
                    $result = $YandexGPT->text($name, $system, $PHPShopSystem->getSerilizeParam('ai.yandexgpt_temperature'), $length);
                    $text = str_replace(['*', '\n', '\r'], ['', '', ''], $result['result']['alternatives'][0]['message']['text']);
                    $text = preg_replace("/\r|\n/", ' ', $text);

                    $PHPShopOrm->update(['title_new' => PHPShopString::utf8_win1251($text), 'title_enabled_new' => 1], ['id' => '=' . (int) $id]);
                }

                // Descrip Meta
                if (isset($_POST['descrip_new']) and isset($_POST['help_ai_descrip'])) {

                    $system = $PHPShopSystem->getSerilizeParam('ai.yandexgpt_product_descrip_role');
                    $length = 80;
                    $result = $YandexGPT->text($name, $system, $PHPShopSystem->getSerilizeParam('ai.yandexgpt_temperature'), $length);
                    $text = str_replace(['*', '\n', '\r'], ['', '', ''], $result['result']['alternatives'][0]['message']['text']);
                    $text = preg_replace("/\r|\n/", ' ', $text);

                    $PHPShopOrm->update(['descrip_new' => PHPShopString::utf8_win1251($text), 'descrip_enabled_new' => 1], ['id' => '=' . (int) $id]);
                }

                // Поиск в Яндексе
                if (isset($_POST['pic_big_new']) and isset($_POST['help_ai_pic_big'])) {

                    if ($YandexSearch->init())
                        $result = $YandexSearch->search_img($name);

                    $data_img = [];
                    $set_main = false;
                    if (is_array($result)) {

                        $i = 0;

                        foreach ($result as $images) {

                            if ($i < $yandexsearch_image_num)
                                $data_img[] = $images['url'];
                            else
                                continue;

                            $i++;
                        }
                    }

                    // Замена изображений
                    if (!empty($_POST['export_imgfunc'])) {
                        $PHPShopOrmImg = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
                        $PHPShopOrmImg->delete(['parent' => '=' . (int) $id]);
                    }

                    foreach ($data_img as $k => $img) {
                        if (!empty($img)) {

                            // Проверка изображения
                            $checkImage = checkImage($img, (int) $id, false);
                            $img_save = $checkImage['img'];

                            // Создаем новую
                            if (empty($checkImage['check'])) {

                                // Файл загружен
                                if (downloadFile($img, $_SERVER['DOCUMENT_ROOT'] . $img_save))
                                    $img_load++;
                                else
                                    continue;


                                // Новое имя
                                $img = $img_save;
                                $path_parts = pathinfo($img);

                                // Сохранение в webp
                                if ($PHPShopSystem->ifSerilizeParam('admoption.image_webp_save') and $path_parts['extension'] != 'webp') {

                                    $thumb = new PHPThumb($_SERVER['DOCUMENT_ROOT'] . $img);
                                    $thumb->setFormat('WEBP');
                                    $name_webp = str_replace([".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF", ".WEBP"], '.webp', $img);

                                    $thumb->save($_SERVER['DOCUMENT_ROOT'] . $name_webp);
                                    @unlink($_SERVER['DOCUMENT_ROOT'] . $img);
                                    $img = $name_webp;
                                }

                                // Запись в фотогалерее
                                $PHPShopOrmImg = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
                                $PHPShopOrmImg->insert(array('parent_new' => (int) $id, 'name_new' => $img, 'num_new' => $k));

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
                                if (empty($set_main) and ! empty($img)) {

                                    $pic_big = $img;

                                    // Главное превью
                                    $pic_small = str_replace(array(".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF", ".webp", ".WEBP"), array("s.png", "s.jpg", "s.jpeg", "s.gif", "s.png", "s.jpg", "s.jpeg", "s.gif", "s.webp", "s.webp"), $img);

                                    $PHPShopOrm->update(['pic_big_new' => $pic_big, 'pic_small_new' => $pic_small], ['id' => '=' . (int) $id]);
                                    $set_main = true;
                                }
                            } else
                                continue;
                        }
                    }
                }
            }

        if (isset($_POST['help_ai_content']))
            unset($_POST['content_new']);

        if (isset($_POST['help_ai_description']))
            unset($_POST['description_new']);

        if (isset($_POST['help_ai_title']))
            unset($_POST['title_new']);

        if (isset($_POST['help_ai_descrip']))
            unset($_POST['descrip_new']);

        if (isset($_POST['help_ai_pic_big']))
            unset($_POST['pic_big_new']);
    }

    // Дата обновления
    $_POST['datas_new'] = time();

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $where);

    if (is_array($where) and $PHPShopOrm->update($_POST, $where)) {
        if (!empty($_GET['cat']))
            header('Location: ?path=catalog&cat=' . intval($_GET['cat']));
        else
            header('Location: ?path=catalog');
        return true;
    } else
        return true;
}

// Построение дерева категорий
function treegenerator($array, $i, $parent, $multi) {
    global $tree_array;
    $del = '&brvbar;&nbsp;&nbsp;&nbsp;';
    $tree = $tree_select = $check = false;
    $del = str_repeat($del, $i);
    if (is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {

            $check = treegenerator($tree_array[$k], $i + 1, $k, $multi);

            if ($k == $_GET['parent_to'])
                $selected = 'selected';
            else
                $selected = null;

            if (empty($check['select'])) {
                $tree_select .= '<option value="' . $k . '" ' . $selected . '>' . $del . $v . '</option>';
                $i = 1;
            } elseif (!empty($multi)) {
                $tree_select .= '<option value="' . $k . '" ' . $selected . '>' . $del . $v . '</option>';
                //$i++;
            } else {
                $tree_select .= '<option value="' . $k . '" ' . $selected . ' disabled>' . $del . $v . '</option>';
                //$i++;
            }

            $tree .= '<tr class="treegrid-' . $k . ' treegrid-parent-' . $parent . ' data-tree">
		<td><a href="?path=catalog&id=' . $k . '">' . $v . '</a></td>
                    </tr>';

            $tree_select .= $check['select'];
            $tree .= $check['tree'];
        }
    }
    return array('select' => $tree_select, 'tree' => $tree);
}

// Выбор каталога
function viewCatalog($name = 'category_new', $multi = false) {

    $PHPShopCategoryArray = new PHPShopCategoryArray();
    $CategoryArray = $PHPShopCategoryArray->getArray();

    $CategoryArray[0]['name'] = '- Кореневой уровень -';
    $tree_array = array();

    foreach ($PHPShopCategoryArray->getKey('parent_to.id', true) as $k => $v) {
        foreach ($v as $cat) {
            $tree_array[$k]['sub'][$cat] = $CategoryArray[$cat]['name'];
        }
        $tree_array[$k]['name'] = $CategoryArray[$k]['name'];
        $tree_array[$k]['id'] = $k;
    }


    $GLOBALS['tree_array'] = &$tree_array;

    $tree_select = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container="" data-width="100%" data-style="btn btn-default btn-sm" name="' . $name . '" ' . $multi . '>';

    $tree_select .= '<option value="" selected>Ничего не выбрано</option>';

    if (is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator($tree_array[$k], 1, $data['category'], $multi);

            if ($k == $data['category'])
                $selected = 'selected';
            else
                $selected = null;

            if (empty($tree_array[$k]))
                $disabled = null;
            else
                $disabled = ' disabled';

            $tree_select .= '<option value="' . $k . '" ' . $selected . $disabled . '>' . $v . '</option>';

            $tree_select .= $check['select'];
        }
    $tree_select .= '</select>';

    return $tree_select;
}

/**
 * Редактировать с выбранными Шаг 2
 */
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopModules, $key_name, $key_stop;

    $PHPShopGUI->setActionPanel(__("Обновить Товары"), false, array('Сохранить и закрыть'));
    $PHPShopGUI->addJSFiles('./catalog/gui/catalog.gui.js');
    $PHPShopGUI->field_col = 3;
    $select_error = null;

    // Помощь AI
    $help_ai = ['content', 'description', 'title', 'descrip'];

    $PHPShopGUI->_CODE .= $PHPShopGUI->setHelp('Вы можете редактировать одновременно несколько записей. Выберите записи из списка товаров, отметьте галочкой товары, которые нужно отредактировать, и нажмите на кнопку "Редактировать выбранные".<hr>', false);

    $PHPShopOrm->sql = 'show fields  from ' . $GLOBALS['SysValue']['base']['products'];
    $select = array_values($_SESSION['select_col']);
    $data = $PHPShopOrm->select();
    if (is_array($data))
        foreach ($data as $val) {

            if (in_array($val['Field'], $select) and ! in_array($val['Field'], $key_stop)) {

                // Каталоги
                if ($val['Field'] == 'category') {
                    $PHPShopGUI->_CODE .= $PHPShopGUI->setField("Каталог", viewCatalog());
                }
                // Каталоги
                elseif ($val['Field'] == 'dop_cat') {
                    $PHPShopGUI->_CODE .= $PHPShopGUI->setField("Дополнительные каталоги", viewCatalog('dop_cat[]', 'multiple') .
                            $PHPShopGUI->setRadio('action', 0, 'Заменить', 1) .
                            $PHPShopGUI->setRadio('action', 1, 'Добавить', 1)
                    );
                }
                // Помощь AI
                elseif (in_array($val['Field'], $help_ai)) {
                    $name = $key_name[$val['Field']];
                    $PHPShopGUI->_CODE .= $PHPShopGUI->setField(ucfirst($name), $PHPShopGUI->setInputArg(getKeyView($val)) .
                            $PHPShopGUI->setCheckbox('help_ai_' . $val['Field'], 1, 'Помощь AI', 0, $PHPShopGUI->disabled_yandexcloud) . '&nbsp;&nbsp;' .
                            $PHPShopGUI->setRadio('action', 0, 'Заменить', 0, true, false, false, $PHPShopGUI->disabled_yandexcloud) .
                            $PHPShopGUI->setRadio('action', 1, 'Добавить', 0, true, false, false, $PHPShopGUI->disabled_yandexcloud)
                    );
                }
                // Поиск в Яндексе
                elseif ($val['Field'] == 'pic_big') {
                    $name = $key_name[$val['Field']];
                    $PHPShopGUI->_CODE .= $PHPShopGUI->setField(ucfirst($name), $PHPShopGUI->setInputArg(getKeyView($val)) .
                            $PHPShopGUI->setCheckbox('help_ai_' . $val['Field'], 1, 'Поиск в Яндексе', 0, $PHPShopGUI->disabled_yandexcloud) .
                            $PHPShopGUI->setCheckbox('export_imgproc', 1, 'Обработка изображений', 0, $PHPShopGUI->disabled_yandexcloud) .
                            $PHPShopGUI->setCheckbox('export_imgfunc', 1, 'Заменить старые изображения', 0, $PHPShopGUI->disabled_yandexcloud)
                    );
                }
                // Характеристики
                elseif ($val['Field'] == 'vendor_array') {
                    if (!empty($_GET['cat']) and $_GET['cat'] != 'undefined') {
                        PHPShopObj::loadClass("sort");
                        $PHPShopSort = new PHPShopSort((int) $_GET['cat'], false, false, 'sorttemplate', false, false, true, false, null, true);
                        $PHPShopGUI->_CODE .= $PHPShopSort->disp;
                    } else {
                        $select_error = 'Редактировать характеристики можно только у товаров из общей категории: <a href="?path=catalog"><span class="glyphicon glyphicon-share-alt"></span> Выбрать</a>';
                    }
                }
                // Файлы
                elseif ($val['Field'] == 'files') {
                    $PHPShopGUI->addJSFiles('./js/jquery.tagsinput.min.js', './catalog/gui/catalog.gui.js', './js/jquery.waypoints.min.js', './product/gui/product.gui.js', './js/bootstrap-colorpicker.min.js');
                    $PHPShopGUI->_CODE .= $PHPShopGUI->setCollapse('Файлы', $PHPShopGUI->loadLib('tab_files', null, './product/'));
                } elseif (!empty($key_name[$val['Field']])) {
                    $name = $key_name[$val['Field']];
                    $PHPShopGUI->_CODE .= $PHPShopGUI->setField(ucfirst($name), $PHPShopGUI->setInputArg(getKeyView($val)));
                } else {
                    $name = $val['Field'];
                    $PHPShopGUI->_CODE .= $PHPShopGUI->setField(ucfirst($name), $PHPShopGUI->setInputArg(getKeyView($val)));
                }
            }
        }


    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.catalog.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.catalog.edit");


    // Выбранные данные
    $select_action_path = 'product';

    if (is_array($_SESSION['select'][$select_action_path])) {
        foreach ($_SESSION['select'][$select_action_path] as $val)
            $select_message = '<span class="label label-default">' . count($_SESSION['select']['product']) . '</span> ' . __('товаров выбрано') . '<hr><a href="#" class="back"><span class="glyphicon glyphicon-ok"></span> ' . __('Изменить интервал') . '</a>';
    } else
        $select_message = '<p class="text-muted">Вы можете выбрать конкретные объекты для экспорта. По умолчанию будут экспортированы все позиции.: <a href="?path=catalog"><span class="glyphicon glyphicon-share-alt"></span> Выбрать</a></p>';

    $sidebarleft[] = array('title' => 'Подсказка', 'content' => $select_message);

    // Ошибки
    if (!empty($select_error))
        $sidebarleft[] = array('title' => 'Ошибка', 'content' => $select_error, 'class' => 'text-danger');


    $PHPShopGUI->setSidebarLeft($sidebarleft, 2);

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);

    $PHPShopGUI->Compile(2);
    return true;
}

/**
 * Шаблон вывода характеристик
 */
function sorttemplate($value, $n, $title, $vendor) {
    global $PHPShopGUI;
    $i = 1;
    //$value_new[0]=array(__('Нет данных'),false, 'none');
    if (is_array($value)) {
        sort($value);
        foreach ($value as $p) {
            $sel = null;
            if (is_array($vendor[$n])) {
                foreach ($vendor[$n] as $value) {

                    if ($value == $p[1])
                        $sel = "selected";
                }
            }elseif ($vendor[$n] == $p[1])
                $sel = "selected";

            $value_new[$i] = array($p[0], $p[1], $sel);
            $i++;
        }
    }

    $value = $PHPShopGUI->setSelect('vendor_array_new[' . $n . '][]', $value_new, 300, null, false, $search = true, false, $size = 1, $multiple = true);

    $disp = $PHPShopGUI->setField($title, $value, 1, null, null, 'control-label', false) .
            $PHPShopGUI->setField(null, $PHPShopGUI->setInputArg(array('type' => 'text', 'placeholder' => 'Ввести другое', 'size' => '300', 'name' => 'vendor_array_add[' . $n . ']')));

    return $disp;
}

/**
 * Настройка полей - 1 шаг
 */
function actionOption() {
    global $PHPShopInterface, $PHPShopModules, $PHPShopSystem;

    // Память выбранных полей
    if (!empty($_COOKIE['check_memory'])) {
        $memory = json_decode($_COOKIE['check_memory'], true);
    }
    if (!is_array($memory['catalog.option']) or count($memory['catalog.option']) < 3) {
        $memory['catalog.option']['icon'] = 1;
        $memory['catalog.option']['name'] = 1;
        $memory['catalog.option']['price'] = 1;
        $memory['catalog.option']['item'] = 1;
        $memory['catalog.option']['menu'] = 1;
        $memory['catalog.option']['status'] = 1;
        $memory['catalog.option']['label'] = 1;
        $memory['catalog.option']['sort'] = 0;
        $memory['catalog.option']['price_n'] = 0;
        $memory['catalog.option']['price_purch'] = 0;
        $memory['catalog.option']['join'] = 0;
    }

    // Режим каталога
    $shop_type = (int) $PHPShopSystem->getParam("shop_type");
    if ($shop_type == 1) {
        $memory['catalog.option']['item'] = 0;
    }

    $message = '<p class="text-muted">' . __('Вы можете изменить перечень полей в таблице отображения товаров в категориях') . '.</p>';

    $searchforma = $message .
            $PHPShopInterface->setCheckbox('icon', 1, 'Иконка', $memory['catalog.option']['icon']) .
            $PHPShopInterface->setCheckbox('name', 1, 'Название', $memory['catalog.option']['name']) .
            $PHPShopInterface->setCheckbox('uid', 1, 'Артикул', $memory['catalog.option']['uid']) .
            $PHPShopInterface->setCheckbox('id', 1, 'ID', $memory['catalog.option']['id']) .
            $PHPShopInterface->setCheckbox('price', 1, 'Цена', $memory['catalog.option']['price']) .
            $PHPShopInterface->setCheckbox('price2', 1, 'Цена 2', $memory['catalog.option']['price2']) .
            $PHPShopInterface->setCheckbox('price3', 1, 'Цена 3', $memory['catalog.option']['price3']) . '<br>' .
            $PHPShopInterface->setCheckbox('price4', 1, 'Цена 4', $memory['catalog.option']['price4']) .
            $PHPShopInterface->setCheckbox('price5', 1, 'Цена 5', $memory['catalog.option']['price5']) .
            $PHPShopInterface->setCheckbox('price_n', 1, 'Старая цена', $memory['catalog.option']['price_n']) .
            $PHPShopInterface->setCheckbox('price_purch', 1, 'Закупочная цена', $memory['catalog.option']['price_purch']) .
            $PHPShopInterface->setCheckbox('status', 1, 'Статус', $memory['catalog.option']['status']) .
            $PHPShopInterface->setCheckbox('item', 1, 'Кол-во', $memory['catalog.option']['item'], $shop_type) . '<br>' .
            $PHPShopInterface->setCheckbox('menu', 1, 'Экшен меню', $memory['catalog.option']['menu']) .
            $PHPShopInterface->setCheckbox('num', 1, 'Сортировка', $memory['catalog.option']['num']) .
            $PHPShopInterface->setCheckbox('label', 1, 'Лейблы статусов', $memory['catalog.option']['label']) .
            $PHPShopInterface->setCheckbox('join', 1, 'Кол-во аналогов', $memory['catalog.option']['join']) . '<br>' .
            $PHPShopInterface->setCheckbox('sort', 1, 'Характеристики', $memory['catalog.option']['sort']);


    // Дополнительный склад
    $PHPShopOrmWarehouse = new PHPShopOrm($GLOBALS['SysValue']['base']['warehouses']);
    $dataWarehouse = $PHPShopOrmWarehouse->select(array('*'), array('enabled' => "='1'"), array('order' => 'num DESC'), array('limit' => 100));
    if (is_array($dataWarehouse)) {
        $searchforma .= '<br>';
        foreach ($dataWarehouse as $row) {
            $searchforma .= $PHPShopInterface->setCheckbox('items' . $row['id'], 1, $row['name'], $memory['catalog.option']['items' . $row['id']]);
        }
    }

    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'path', 'value' => 'catalog'));
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'cat', 'value' => $_REQUEST['cat']));

    $searchforma .= '<p class="clearfix"> </p>';


    $PHPShopInterface->_CODE .= $searchforma;

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, null);

    exit($PHPShopInterface->getContent() . '<p class="clearfix"> </p>');
}

/**
 * Настройка полей - 2 шаг
 */
function actionOptionSave() {

    // Память выбранных полей
    if (is_array($_POST['option'])) {

        $memory = json_decode($_COOKIE['check_memory'], true);
        unset($memory['catalog.option']);
        foreach ($_POST['option'] as $k => $v) {
            $memory['catalog.option'][$k] = $v;
        }
        if (is_array($memory))
            setcookie("check_memory", json_encode($memory), time() + 3600000 * 6, $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/admpanel/');
    }

    return array('success' => true);
}

/*
 * Сброс кэша характеристик всех каталогов
 */

function actionResetCache() {
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
    $PHPShopOrm->update(array('sort_cache_new' => '', 'sort_cache_created_at_new' => 0));

    return array('success' => true);
}

/*
 * Удалить неиспользуемые характеристики
 */

function actionCleanSort() {
    $PHPShopSort = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);
    $PHPShopSort->debug = false;
    $PHPShopProduct = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $PHPShopProduct->debug = false;
    $PHPShopSortCat = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
    $PHPShopSortCat->debug = false;
    $count = 0;

    // Проверка значений характеристик
    $data = $PHPShopSort->getList(['id', 'category', 'name'], false, ['order' => 'id'], ['limit' => 300000]);
    if (is_array($data))
        foreach ($data as $row) {
            $value = $row['category'] . '-' . $row['id'];
            $check = $PHPShopProduct->getOne(['id'], ['vendor' => " REGEXP 'i" . $value . "i'"]);

            // Удаление
            if (count($check) < 1) {
                //echo "Нет " . $row['name'] . ', удаляю id=' . $row['id'] . ' из ' . $GLOBALS['SysValue']['base']['sort'] . '<br>';
                $PHPShopSort->delete(['id' => '=' . $row['id']]);
                $PHPShopSort->clean();
                $count++;
            }
        }


    // Проверка характеристик
    $data = $PHPShopSortCat->getList(['id', 'category', 'name'], ['category' => '!=0'], ['order' => 'id'], ['limit' => 300000]);
    if (is_array($data))
        foreach ($data as $row) {
            $check = $PHPShopSort->getOne(['id'], ['category' => '=' . $row['id']]);

            // Удаление
            if (count($check) < 1) {
                //echo "Нет " . $row['name'] . ', удаляю id=' . $row['id'] . ' из ' . $GLOBALS['SysValue']['base']['sort_categories'] . '<br>';
                $PHPShopSortCat->delete(['id' => '=' . $row['id']]);
                $PHPShopSortCat->clean();
                $count++;
            }
        }

    return array('success' => true, 'count' => $count);
}

// Проверка изображения
function checkImage($img, $id, $uniq) {
    global $PHPShopSystem;

    // Перевод в латиницу
    $path_parts = pathinfo($img);
    $path_parts['basename'] = PHPShopFile::toLatin($path_parts['basename']);

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
    if (!is_array($check)) {

        // Проверка имени файла
        if (empty($uniq) and file_exists($_SERVER['DOCUMENT_ROOT'] . $img_check)) {

            // Соль
            $rand = '_' . substr(abs(crc32($img)), 0, 5);
            $path_parts['basename'] = str_replace([".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF", ".WEBP"], [$rand . ".png", $rand . ".jpg", $rand . ".jpeg", $rand . ".gif", $rand . ".PNG", $rand . ".JPG", $rand . ".JPEG", $rand . ".GIF", $rand . ".WEBP"], $path_parts['basename']);
        }
    }

    // Новое имя
    $img = $GLOBALS['dir']['dir'] . '/UserFiles/Image/' . $path . $path_parts['basename'];

    return ['img' => $img, 'check' => $check];
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

// Обработка событий
$PHPShopGUI->getAction();
