<?php

/**
 * Обмен по CommerceML
 * @package PHPShopExchange
 * @author PHPShop Software
 * @todo https://hmarketing.ru/blog/bitrix/zaprosy-obmena/
 * @version 4.3
 */
class CommerceMLLoader {

    private static $session_name = "CommerceMLLoader";
    private static $upload1c = 'upload/';
    var $result_path = 'sklad/';
    var $log_path = 'log/';
    var $exchange_path = '';
    var $cleanup_import_directory = true;
    var $cleanup_time = 3600;
    var $exchange_image_path = "/UserFiles/Image/";
    var $exchange_file_path = "/UserFiles/Files/";

    public function __construct() {
        global $PHPShopSystem;

        // Параметры обмена
        $this->exchange_zip = $PHPShopSystem->getSerilizeParam("1c_option.exchange_zip");
        $this->exchange_key = $PHPShopSystem->getSerilizeParam("1c_option.exchange_key");
        $this->exchange_create = $PHPShopSystem->getSerilizeParam("1c_option.exchange_create");
        $this->exchange_create_category = $PHPShopSystem->getSerilizeParam("1c_option.exchange_create_category");
        $this->exchange_load_status = $PHPShopSystem->getSerilizeParam('1c_option.1c_load_status');
        $this->exchange_auth_path = $PHPShopSystem->getSerilizeParam("1c_option.exchange_auth_path");
        $this->exchange_auth = $PHPShopSystem->getSerilizeParam("1c_option.exchange_auth");
        $this->image_result_path = $PHPShopSystem->getSerilizeParam('1c_option.exchange_image_result_path');
        $this->exchange_log = $PHPShopSystem->getSerilizeParam("1c_option.exchange_log");
        $this->exchange_image = $PHPShopSystem->getSerilizeParam("1c_option.exchange_image");
        $this->exchange_price1 = $PHPShopSystem->getSerilizeParam("1c_option.exchange_price1");
        $this->exchange_price2 = $PHPShopSystem->getSerilizeParam("1c_option.exchange_price2");
        $this->exchange_price3 = $PHPShopSystem->getSerilizeParam("1c_option.exchange_price3");
        $this->exchange_price4 = $PHPShopSystem->getSerilizeParam("1c_option.exchange_price4");
        $this->exchange_price5 = $PHPShopSystem->getSerilizeParam("1c_option.exchange_price5");
        $this->exchange_sort_ignore = $PHPShopSystem->getSerilizeParam("1c_option.exchange_sort_ignore");
        $this->exchange_clean = $PHPShopSystem->getSerilizeParam("1c_option.exchange_clean");
        $this->exchange_product_ignore = $PHPShopSystem->getSerilizeParam("1c_option.exchange_product_ignore");
        $this->exchange_change = $PHPShopSystem->getSerilizeParam("1c_option.exchange_change");
        $this->exchange_name = $PHPShopSystem->getSerilizeParam("1c_option.exchange_name");

        // Параметры ресайзинга
        $this->img_tw = $PHPShopSystem->getSerilizeParam('admoption.img_tw');
        $this->img_th = $PHPShopSystem->getSerilizeParam('admoption.img_th');
        $this->width_kratko = $PHPShopSystem->getSerilizeParam('admoption.width_kratko');
        $this->img_w = $PHPShopSystem->getSerilizeParam('admoption.img_w');
        $this->img_h = $PHPShopSystem->getSerilizeParam('admoption.img_h');
        $this->image_save_source = $PHPShopSystem->getSerilizeParam('admoption.image_save_source');

        // Замена спецсимволов в значениях характеристик
        $this->replace = ['≥' => '>='];
    }

    private function checkauth() {
        global $_classPath;

        // Авторизация по ссылке
        if ($this->exchange_auth == 1 and $this->exchange_auth_path != "" and $_SERVER['PHP_SELF'] == $GLOBALS['SysValue']['dir']['dir'] . '/1cManager/' . $this->exchange_auth_path . '.php') {

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['users']);
            $data = $PHPShopOrm->select(array('token'), array('enabled' => "='1'", 'token' => '!=""'), false, array('limit' => 1));
            $_SESSION['token'] = $data['token'];

            return true;
        }

        if ($this->exchange_auth == 0 and ! empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])) {

            include($_classPath . "lib/phpass/passwordhash.php");
            $hasher = new PasswordHash(8, false);

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['users']);
            $data = $PHPShopOrm->select(array('login,password'), array('enabled' => "='1'"), false, array('limit' => 100));

            if (is_array($data))
                foreach ($data as $row) {
                    if ($_SERVER['PHP_AUTH_USER'] == $row['login']) {
                        if ($hasher->CheckPassword($_SERVER['PHP_AUTH_PW'], $row['password'])) {

                            $_SESSION['login'] = $data['login'];
                            $_SESSION['password'] = $data['password'];

                            return true;
                        }
                    }
                }
        }
    }

    private function crc16($str) {
        $data = trim($str);

        // Внешний код
        if (!is_numeric($data)) {
            $crc = 0xFFFF;
            for ($i = 0; $i < strlen($data); $i++) {
                $x = (($crc >> 8) ^ ord($data[$i])) & 0xFF;
                $x ^= $x >> 4;
                $crc = (($crc << 8) ^ ($x << 12) ^ ($x << 5) ^ $x) & 0xFFFF;
            }
        } else
            $crc = $data;

        return $crc;
    }

    private function crc32($str) {
        $data = trim($str);
        return crc32($data);
    }

    public function exchange($type, $mode) {
        session_name(self::$session_name);
        session_start();
        $upload_path = dirname(__FILE__) . $this->exchange_path . '/';
        header("Content-Type: text/plain");
        $response = 'failure';
        if (empty($type) || empty($mode)) {
            $response .= "\nEmpty command type or mode.";
        }
        if (($mode != 'checkauth') && (!isset($_COOKIE[self::$session_name]) || ($_COOKIE[self::$session_name] != $_SESSION['fixed_session_id']))) {
            $response .= "\nUnauthorized.";
        } else
            switch ($mode) {
                case 'checkauth': // Authorization query
                    if ($this->checkauth()) {
                        $response = "success";
                        $response .= "\n" . session_name();
                        $response .= "\n" . session_id();
                        $response .= "\n" . self::sessid_get();
                        $response .= "\ntimestamp=" . time();
                    } else {
                        $response .= "\nAccess denied.";
                    }
                    break;
                case 'init': // Initialize query

                    if ($type == "sale")
                        $this->exchange_zip = 0;

                    if (!is_dir($upload_path . self::$upload1c)) {
                        mkdir($upload_path . self::$upload1c, 0777, true);
                    } elseif ($this->cleanup_import_directory) {
                        self::cleanup_import_directory($upload_path . self::$upload1c);
                    }
                    $response = "zip=" . ((intval($this->exchange_zip) == 1) ? 'yes' : 'no');
                    $response .= "\nfile_limit=" . self::parse_size(ini_get("upload_max_filesize"));

                    break;
                case 'file': // Upload files from 1C
                    $filepath = $upload_path . self::$upload1c . $_GET['filename'];

                    if ($this->data != '') {
                        $data = $this->data;
                        $data_length = 100000;
                    } else {
                        $data = file_get_contents("php://input");
                        $data_length = $_SERVER['CONTENT_LENGTH'];
                    }
                    if (isset($data) && $data !== false) {

                        if (dirname($_GET['filename']) != '.') {
                            @mkdir($upload_path . self::$upload1c . '/' . dirname($_GET['filename']), 0777, true);
                        }
                        $file = fopen($filepath, "w+");
                        if ($file) {
                            $bytes_writed = fwrite($file, $data);
                            if ($bytes_writed == $data_length or $this->data != '') {
                                if (mime_content_type($filepath) == 'application/zip') {
                                    $zip = new ZipArchive();
                                    $zip->open($filepath);
                                    $zip->extractTo($upload_path . self::$upload1c);
                                    $zip->close();
                                }
                                $response = "success";
                            }
                            fclose($file);
                        }
                    }

                    // Статусы заказов
                    if ($type == "sale") {

                        if (file_exists($upload_path . self::$upload1c . $_GET['filename'])) {
                            $move_path = 'orders/';

                            if (!is_dir($upload_path . $move_path)) {
                                @mkdir($upload_path . $move_path, 0777, true);
                            }
                            preg_match_all('/^(orders)(?:.*)(\.xml)$/', $_GET['filename'], $new_name_parts);
                            $new_name = count($new_name_parts) ? $new_name_parts[1][0] . $new_name_parts[2][0] : $_GET['filename'];

                            $xml = simplexml_load_file($upload_path . self::$upload1c . $_GET['filename']);
                            rename($upload_path . self::$upload1c . $_GET['filename'], $upload_path . $move_path . $new_name);

                            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
                            $PHPShopOrm->debug = false;
                            foreach ($xml->Документ as $order) {

                                $status_id = null;

                                // Номер заказа
                                $order_uid = preg_replace('/[^0-9\-]+/', '', (string) $order->Комментарий[0]);

                                foreach ($order->ЗначенияРеквизитов[0]->ЗначениеРеквизита as $status) {

                                    // Статуса заказа ИД
                                    if ($status->Наименование == "Статуса заказа ИД" and ! empty($status->Значение)) {

                                        $status_id = (new PHPShopOrm($GLOBALS['SysValue']['base']['order_status']))->getOne(['*'], ['external_code' => '="' . PHPShopString::utf8_win1251($status->Значение) . '"']);
                                    }

                                    // Статус заказа
                                    if (empty($status_id) and $status->Наименование == "Статус заказа") {
                                        $status_id = (new PHPShopOrm($GLOBALS['SysValue']['base']['order_status']))->getOne(['*'], ['external_code' => '="' . PHPShopString::utf8_win1251($status->Значение) . '"']);
                                    }
                                }

                                if (!empty($status_id['id'])) {
                                    $order = $PHPShopOrm->getOne(array('*'), array('uid' => '="' . $order_uid . '"'));
                                    if (is_array($order)) {
                                        PHPShopObj::loadClass(["payment", "lang", "order", "file", "parser"]);
                                        $PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'shop'));
                                        (new PHPShopOrderFunction((int) $order['id']))->changeStatus((int) $status_id['id'], $order['statusi']);
                                    }
                                }
                            }

                            $response = "success";
                        } else {
                            $response = "failure";
                        }
                    }

                    break;
                case 'import': // Processing data
                    if (file_exists($upload_path . 'completed.lock')) {
                        unlink($upload_path . 'completed.lock');
                    }
                    if (file_exists($upload_path . self::$upload1c . $_GET['filename'])) {
                        $move_path = 'goods/';
                        if (preg_match('/^import(.*)\.xml$/', $_GET['filename'])) {
                            $import_xml = simplexml_load_file($upload_path . self::$upload1c . $_GET['filename']);
                            if (isset($import_xml->Классификатор->Группы)) {
                                $move_path = 'goods/';
                            } elseif (isset($import_xml->Каталог->Товары)) {
                                $move_path = 'goods/';
                            } elseif (isset($import_xml->Классификатор->Свойства)) {
                                $move_path = 'goods/';
                            }
                        } elseif (preg_match('/^offers(.*)\.xml$/', $_GET['filename'])) {

                            $import_xml = simplexml_load_file($upload_path . self::$upload1c . $_GET['filename']);
                            if (isset($import_xml->ПакетПредложений->Предложения)) {
                                $move_path = 'goods/';
                            } elseif (isset($import_xml->Классификатор)) {
                                $move_path = 'goods/';
                            }
                        } else {
                            $move_path = 'goods/';
                        }
                        if (!is_dir($upload_path . $move_path)) {
                            @mkdir($upload_path . $move_path, 0777, true);
                        }
                        preg_match_all('/^(offers|import|prices|rests)(?:.*)(\.xml)$/', $_GET['filename'], $new_name_parts);
                        $new_name = count($new_name_parts) ? $new_name_parts[1][0] . $new_name_parts[2][0] : $_GET['filename'];
                        rename($upload_path . self::$upload1c . $_GET['filename'], $upload_path . $move_path . $new_name);

                        // Парсер
                        $this->parser($import_xml);

                        $response = "success";
                    } else {
                        $response = "failure";
                    }
                    break;
                case 'complete':
                    $complete_file = fopen($upload_path . 'completed.lock', 'w');
                    fputs($complete_file, time());
                    fclose($complete_file);

                    // Выключить товары, отсутствующие в файле импорта
                    if ($this->exchange_clean == 1) {
                        $time = time() - $this->cleanup_time; // -1 час
                        (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->update(['enabled_new' => 0], ['datas' => '<' . $time]);
                    }

                    $response = "success";
                    break;

                case 'query':// Вырузка

                    PHPShopObj::loadClass(array("cml", "order"));
                    $PHPShopCommerceML = new PHPShopCommerceML();

                    switch ($type) {

                        case 'sale': // Вырузка заказов
                            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
                            $where['seller'] = "!='1'";

                            if ($this->exchange_load_status > 0)
                                $where['statusi'] = '=' . intval($this->exchange_load_status);

                            $data = $PHPShopOrm->select(array('*'), $where, array('order' => 'id desc'), array('limit' => 10));

                            header("Content-Type: text/xml;charset=windows-1251");
                            $response = $PHPShopCommerceML->getOrders($data);

                            if (is_array($data)) {

                                // Смена флага загрузки
                                if (is_array($PHPShopCommerceML->update_status))
                                    foreach ($PHPShopCommerceML->update_status as $id) {
                                        $PHPShopOrm->update(array('seller_new' => '1'), array('id' => '=' . $id));
                                    }
                            }

                            break;

                        case 'get_catalog': // Выгрузка товаров
                            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);

                            $data = $PHPShopOrm->select(array('*'), false, array('order' => 'id desc'), array('limit' => 100000));

                            header("Content-Type: text/xml;charset=windows-1251");
                            $response = $PHPShopCommerceML->getProducts($data);
                            if (empty($response))
                                $response = "success";

                            break;
                    }

                    break;
                default:
                    $response = "failure";
            }

        // Лог
        $this->log($type, $mode, $response);

        return $response . "\n";
    }

    private static function sessid_get($varname = 'sessid') {
        $sessid = null;
        if (!is_array($_SESSION) || !isset($_SESSION['fixed_session_id'])) {
            $_SESSION["fixed_session_id"] = session_id();
        } else {
            $sessid = $_SESSION["fixed_session_id"];
        }
        return $varname . "=" . $sessid;
    }

    private static function parse_size($size) {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        $size = preg_replace('/[^0-9\.]/', '', $size);
        if ($unit) {
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        } else {
            return round($size);
        }
    }

    public static function cleanup_import_directory($path) {
        $elements = scandir($path);
        foreach ($elements as $element) {
            if (in_array($element, array('.', '..')))
                continue;
            if (is_dir($path . '/' . $element)) {
                if (@!rmdir($path . '/' . $element)) {
                    self::cleanup_import_directory($path . '/' . $element);
                    @rmdir($path . '/' . $element);
                }
            } else {
                @unlink($path . '/' . $element);
            }
        }
    }

    // Смена кодировки
    private function array2iconv(&$value) {
        $value = iconv("UTF-8", "CP1251", $value);
    }

    // Каталоги
    private function parser_category($parent, $item) {

        $this->category_array[] = array($this->crc16((string) $item->Ид[0]), trim((string) $item->Наименование[0]), (string) $parent);

        if (isset($item->Группы[0]))
            foreach ($item->Группы[0] as $items) {

                $this->category_array[] = array($this->crc16((string) $items->Ид[0]), trim((string) $items->Наименование[0]), $this->crc16((string) $item->Ид[0]));

                if (isset($items->Группы[0]))
                    $this->parser_category($this->crc16((string) $items->Ид[0]), $items);
            }
    }

    private function writeCsv($file, $csv, $error = false) {
        $fp = @fopen($file, "w+");
        if ($fp) {
            foreach ($csv as $value) {
                fputcsv($fp, $value, ';', '"');
            }
            fclose($fp);
        } elseif ($error)
            echo 'No file ' . $file;
    }

    private function parser($xml) {
        global $parent_array, $sort_array, $properties_array;

        $upload_path = dirname(__FILE__) . $this->exchange_path . '/';

        if ($xml) {

            // Создание папки
            $date = date("j-m-Y-H-i-s");
            if (!is_dir($this->result_path . $date)) {
                mkdir($this->result_path . $date, 0777, true);
            }

            // Блокировка характеристик
            if (!empty($this->exchange_sort_ignore)) {
                if (strstr($this->exchange_sort_ignore, ','))
                    $sort_ignore = explode(',', $this->exchange_sort_ignore);
                else
                    $sort_ignore[] = $this->exchange_sort_ignore;
            } else
                $sort_ignore = [];

            $properties = null;
            $this->product_array = [];
            $this->product_array[] = array("Артикул", "Наименование", "Краткое описание", "Имя картинки", "Подробное описание", "Кол-во картинок", "Остаток", "Цена1", "Цена2", "Цена3", "Цена4", "Цена5", "Вес", "Ед.измерения", "ISO", "Каталог", "Подчиненные товары", "Внешний код", "Подтип", "Характеристика", "Значение");

            // import.xml
            if (isset($xml->Классификатор->Группы) or $_GET['filename'] == 'import.xml') {

                $this->category_array[0] = array('Id', 'Наименование', 'Родитель');

                // Категории
                foreach ($xml->Классификатор->Группы[0] as $item) {

                    $this->parser_category(0, $item);
                }

                // Свойства
                if (isset($xml->Классификатор->Свойства))
                    foreach ($xml->Классификатор->Свойства[0] as $item) {

                        // Справочник 2.08
                        if (isset($item->ВариантыЗначений)) {
                            foreach ($item->ВариантыЗначений->Справочник as $directory) {

                                if (!in_array(PHPShopString::utf8_win1251((string) $directory->Значени[0]), $sort_ignore)) {

                                    // Замена спецсимвлов
                                    foreach ($this->replace as $k => $v) {
                                        if (stristr((string) $directory->Значение[0], $k))
                                            $directory_array[(string) $directory->ИдЗначения[0]] = str_replace($k, $v, (string) $directory->Значение[0]);
                                        else
                                            $directory_array[(string) $directory->ИдЗначения[0]] = (string) $directory->Значение[0];
                                    }
                                }
                            }
                        }
                        // Справочник 2.04
                        elseif (isset($item->ТипыЗначений)) {


                            foreach ($item->ТипыЗначений[0]->ТипЗначений[0]->ВариантыЗначений[0]->ВариантЗначения as $directory) {

                                if (!in_array(PHPShopString::utf8_win1251((string) $directory->Значени[0]), $sort_ignore)) {

                                    $directory_array[(string) $directory->Ид[0]] = (string) $directory->Значение[0];
                                }
                            }
                        }

                        if (!in_array(PHPShopString::utf8_win1251((string) $item->Наименование[0]), $sort_ignore))
                            $properties_array[(string) $item->Ид[0]] = (string) $item->Наименование[0];
                    }

                // Загрузка дополнительных полей справочника из МойСклад
                /*
                  $file = 'http://priceexport.sklad24.online';
                  $handle = fopen($file, "r");
                  $i = 0;
                  while ($data = fgetcsv($handle, 0, ';')) {
                  if (empty($i))
                  $csv_name = $data;
                  else
                  $csv_data[$data[0]] = $data;
                  $i++;
                  } */

                // Запись в файл
                if (count($this->category_array) > 1) {

                    // Очистка дублей
                    foreach ($this->category_array as $k => $val) {
                        if ($val[0] == $val[2])
                            unset($this->category_array[$k]);
                    }


                    if ($GLOBALS['PHPShopBase']->codBase != 'utf-8')
                        array_walk_recursive($this->category_array, 'self::array2iconv');

                    $this->writeCsv('sklad/' . $date . '/tree.csv', $this->category_array, true);
                }

                // Товары
                foreach ($xml->Каталог->Товары[0] as $item) {

                    $description = $weight = $length = $width = $height = null;

                    // Краткое описание и габариты
                    if (isset($item->ЗначенияРеквизитов[0])) {
                        foreach ($item->ЗначенияРеквизитов[0]->ЗначениеРеквизита as $req) {


                            // Наименование
                            if ($this->exchange_name == 'print') {
                                if ($req->Наименование == 'Полное наименование') {
                                    $title = $description = nl2br((string) $req->Значение);
                                }
                            } else
                                $title = (string) $item->Наименование[0];

                            if ($req->Наименование == 'Вес') {

                                $weight = (string) $req->Значение * 1000;
                            }

                            if ($req->Наименование == 'Длина') {
                                $length = '#' . round((string) $req->Значение);
                            }

                            if ($req->Наименование == 'Ширина') {
                                $width = '#' . round((string) $req->Значение);
                            }

                            if ($req->Наименование == 'Высота') {
                                $height = '#' . round((string) $req->Значение);
                            }

                            if ($req->Наименование == 'Код') {
                                $code = trim((string) $req->Значение);
                            }
                        }

                        $weight .= $length . $width . $height;
                    }

                    // Подробное описание
                    if (isset($item->Описание)) {
                        $content = nl2br((string) $item->Описание);
                    } else
                        $content = $description;

                    // Свойства
                    $properties = [];

                    // Изготовитель
                    if (isset($item->Изготовитель)) {
                        $properties[] = ['Производитель', (string) $item->Изготовитель[0]->Наименование];
                    }

                    $weight_prop = $length_prop = $width_prop = $height_prop = null;
                    if (isset($item->ЗначенияСвойств[0])) {
                        foreach ($item->ЗначенияСвойств[0] as $req) {

                            // Проверка в справочнике
                            if (isset($directory_array[(string) $req->Значение[0]]))
                                $req->Значение[0] = $directory_array[(string) $req->Значение[0]];
                            else if (isset($directory_array[(string) $req->ИдЗначения[0]]))
                                $req->Значение[0] = $directory_array[(string) $req->ИдЗначения[0]];

                            $properties[] = [$properties_array[(string) $req->Ид[0]], (string) $req->Значение[0]];

                            // Габариты из характеристик
                            if (stristr($properties_array[(string) $req->Ид], 'Вес') and empty($weight)) {
                                $weight_prop = (string) $req->Значение;
                                if (strpos($weight_prop, ','))
                                    $weight_prop = str_replace(',', '.', $weight_prop);
                                $weight_prop = round((float) $weight_prop) * 1000;
                            }
                            if (stristr($properties_array[(string) $req->Ид], 'Длина') and empty($length)) {
                                $length_prop = (string) $req->Значение;
                                if (strpos($length_prop, ','))
                                    $length_prop = str_replace(',', '.', $length_prop);
                                $length_prop = '#' . round((float) $length_prop);
                            }
                            if (stristr($properties_array[(string) $req->Ид], 'Ширина') and empty($width)) {
                                $width_prop = (string) $req->Значение;
                                if (strpos($width_prop, ','))
                                    $width_prop = str_replace(',', '.', $width_prop);
                                $width_prop = '#' . round((float) $width_prop);
                            }
                            if (stristr($properties_array[(string) $req->Ид], 'Высота') and empty($height)) {
                                $height_prop = (string) $req->Значение;
                                if (strpos($height_prop, ','))
                                    $height_prop = str_replace(',', '.', $height_prop);
                                $height_prop = '#' . round((float) $height_prop);
                            }
                        }

                        if (empty($weight) and ! empty($weight_prop))
                            $weight = $weight_prop . $length_prop . $width_prop . $height_prop;
                    }

                    // Категория
                    if (isset($item->Группы))
                        $category = $this->crc16((string) $item->Группы[0]->Ид);
                    else
                        $category = 0;


                    // Картинка
                    $image_count = 0;
                    $image = null;
                    $files = [];

                    if (isset($item->Картинка) and ! empty($this->exchange_image)) {

                        if (!is_array((array) $item->Картинка))
                            (array) $item->Картинка[] = (string) $item->Картинка;


                        foreach ((array) $item->Картинка as $i => $img) {

                            if ((string) $i == '@attributes')
                                continue;

                            if (!file_exists($upload_path . self::$upload1c . $img))
                                continue;

                            $ext = pathinfo($img, PATHINFO_EXTENSION);

                            // Картинки
                            if (in_array($ext, array('gif', 'png', 'jpg', 'jpeg', 'webp'))) {

                                $new_name = 'img' . $this->crc32((string) $item->Ид[0]) . '_' . ($i + 1) . '.' . $ext;
                                $new_name_s = 'img' . $this->crc32((string) $item->Ид[0]) . '_' . ($i + 1) . 's.' . $ext;
                                $new_name_big = 'img' . $this->crc32((string) $item->Ид[0]) . '_' . ($i + 1) . '_big.' . $ext;

                                // Тубнейл
                                $thumb = new PHPThumb(dirname(__FILE__) . $this->exchange_path . '/' . self::$upload1c . $img);
                                $thumb->setOptions(array('jpegQuality' => $this->width_kratko));
                                $thumb->resize($this->img_tw, $this->img_th);
                                $thumb->save($_SERVER['DOCUMENT_ROOT'] . $this->exchange_image_path . $this->image_result_path . $new_name_s);

                                // Основное
                                $thumb = new PHPThumb(dirname(__FILE__) . $this->exchange_path . '/' . self::$upload1c . $img);
                                $thumb->setOptions(array('jpegQuality' => $this->width_kratko));
                                $thumb->resize($this->img_w, $this->img_h);
                                $thumb->save($_SERVER['DOCUMENT_ROOT'] . $this->exchange_image_path . $this->image_result_path . $new_name);

                                // Исходное
                                if (!empty($this->image_save_source))
                                    copy(dirname(__FILE__) . $this->exchange_path . '/' . self::$upload1c . $img, $this->exchange_image_path . $this->image_result_path . $new_name_big);

                                $image = $this->image_result_path . 'img' . $this->crc32((string) $item->Ид[0]);

                                if ($ext != 'jpg')
                                    $image .= '#' . $ext;

                                $image_count++;
                            }
                            // Файлы
                            else {
                                $file_name = 'file' . $this->crc32((string) $item->Ид[0]) . '_' . ($i + 1) . '.' . $ext;
                                copy(dirname(__FILE__) . $this->exchange_path . '/' . self::$upload1c . $img, $_SERVER['DOCUMENT_ROOT'] . $this->exchange_file_path . $file_name);
                                $files[] = $file_name;
                            }
                        }
                    }

                    // Поле артикул
                    if ($this->exchange_key == 'code') {

                        if (!empty((string) $item->Код[0]))
                            $uid = (string) $item->Код[0];
                        elseif (!empty($code))
                            $uid = $code;
                        else
                            $uid = null;
                    }
                    else if ($this->exchange_key == 'external')
                        $uid = (string) $item->Ид[0];
                    else if ($this->exchange_key == 'barcode')
                        $uid = (string) $item->Штрихкод[0];
                    else
                        $uid = (string) $item->Артикул[0];

                    // Подтипы 2.07
                    if (strstr((string) $item->Ид[0], '#')) {

                        // Ид подтипа
                        $p = explode("#", (string) $item->Ид[0]);
                        (string) $item->Ид[0] = $p[1];

                        // Список подтипов у главного товара
                        $parent_array[$p[0]]['ids'] .= $p[1] . ',';

                        // Имя главного товара
                        if (empty($parent_array[$p[0]]['name'])) {

                            $name = preg_replace_callback('/\([^)]+\)/', function($match) {
                                return str_replace($match[0], '', $match[0]);
                            }, (string) $item->Наименование[0]);

                            $parent_array[$p[0]]['name'] = $name;
                            $parent_array[$p[0]]['category'] = $category;
                            $parent_array[$p[0]]['uid'] = $uid;
                            $parent_array[$p[0]]['properties'] = $properties;
                            $parent_array[$p[0]]['image'] = $image;
                        }

                        $parent_enabled = 1;

                        // Артикул для подтипа
                        $uid = (string) $item->Ид[0];
                    } else {
                        $parent_enabled = 0;
                    }

                    // Дополнительные файлы
                    if (count($files) > 0)
                        $image .= '|' . implode(',', $files);

                    $this->product_array[(string) $item->Ид[0]] = array($uid, $title, $description, $image, $content, $image_count, "", "", "", "", "", "", $weight, "", "", $category, "", (string) $item->Ид[0], $parent_enabled);

                    // Свойства
                    if (is_array($properties))
                        foreach ($properties as $val)
                            if (is_array($val))
                                foreach ($val as $value)
                                    $this->product_array[(string) $item->Ид[0]][] = $value;
                }


                // Запись в файл
                if (count($this->product_array) > 1) {

                    // Подтипы 2.07, добавляем главные товары для подтипов
                    $parent = null;
                    if (is_array($parent_array)) {

                        $parent = null;
                        foreach ($parent_array as $id => $prod) {

                            // Подтипы
                            if (!empty($prod['ids'])) {
                                $parent = substr($prod['ids'], 0, strlen($prod['ids']) - 1);
                            }

                            $this->product_array[$id] = array($prod['uid'], $prod['name'], null, $prod['image'], null, null, 0, 0, "", "", "", "", "", "", "", $prod['category'], $parent, $id, 0);

                            // Свойства
                            if (is_array($prod['properties']))
                                foreach ($prod['properties'] as $val)
                                    if (is_array($val))
                                        foreach ($val as $value)
                                            $this->product_array[$id][] = $value;
                        }
                    }

                    if ($GLOBALS['PHPShopBase']->codBase != 'utf-8')
                        array_walk_recursive($this->product_array, 'self::array2iconv');

                    $this->writeCsv('sklad/' . $date . '/upload_0.csv', $this->product_array, true);

                    // Выполнение
                    $this->load($date, true);
                }
            }

            // offers.xml
            else if (isset($xml->ПакетПредложений->Предложения) or $_GET['filename'] = 'offers.xml') {

                // Обработка измененных данных
                if (!empty($this->exchange_change) and isset($xml->ИзмененияПакетаПредложений)) {
                    unset($xml);
                    $xml = simplexml_load_string(str_replace(['ИзмененияПакетаПредложений'], ['ПакетПредложений'], file_get_contents('./goods/' . $_GET['filename'])));
                }

                // Блокировка обновления товаров
                if (!empty($this->exchange_product_ignore)) {
                    if (strstr($this->exchange_product_ignore, ','))
                        $product_ignore = explode(',', $this->exchange_product_ignore);
                    else
                        $product_ignore[] = $this->exchange_product_ignore;
                } else
                    $product_ignore = [];


                foreach ($xml->ПакетПредложений->Предложения->Предложение as $item) {

                    // Блокировка товаров
                    if (in_array(PHPShopString::utf8_win1251((string) $item->Ид[0]), $product_ignore)) {
                        continue;
                    }

                    // Дополнительные склады 10/A#20/B
                    if (isset($item->Склад)) {

                        foreach ($item->Склад as $items) {
                            $warehouses[(string) $items['ИдСклада']] = (int) $items['КоличествоНаСкладе'];
                        }

                        if (is_array($warehouses)) {
                            $warehouse = null;

                            foreach ($warehouses as $k => $v) {
                                $warehouse .= $v . '/' . $k . '#';
                            }

                            $warehouse = substr($warehouse, 0, strlen($warehouse) - 1);
                        }
                    } else
                        $warehouse = (int) $item->Количество[0];

                    // Картинка
                    $image_count = null;
                    $image = null;

                    // Подтипы 18#141
                    if (strstr((string) $item->Ид[0], '#')) {

                        // Ид подтипа
                        $p = explode("#", (string) $item->Ид[0]);
                        (string) $item->Ид[0] = $p[1];

                        // Блокировка подтипов
                        if (in_array(PHPShopString::utf8_win1251((string) $item->Ид[0]), $product_ignore)) {
                            continue;
                        }

                        // Список подтипов у главного товара
                        $parent_array[$p[0]]['ids'] .= $p[1] . ',';

                        // Цена главного товара - внешние коды цены
                        if (!empty($this->exchange_price1)) {

                            if (isset($item->Цены)) {
                                foreach ($item->Цены->Цена as $prices) {

                                    if ($this->exchange_price1 == (string) $prices->ИдТипаЦены[0])
                                        $parent_price1 = (string) $prices->ЦенаЗаЕдиницу[0];

                                    elseif ($this->exchange_price2 == (string) $prices->ИдТипаЦены[0])
                                        $parent_price2 = (string) $prices->ЦенаЗаЕдиницу[0];

                                    elseif ($this->exchange_price3 == (string) $prices->ИдТипаЦены[0])
                                        $parent_price3 = (string) $prices->ЦенаЗаЕдиницу[0];

                                    elseif ($this->exchange_price4 == (string) $prices->ИдТипаЦены[0])
                                        $parent_price4 = (string) $prices->ЦенаЗаЕдиницу[0];

                                    elseif ($this->exchange_price5 == (string) $prices->ИдТипаЦены[0])
                                        $parent_price5 = (string) $prices->ЦенаЗаЕдиницу[0];
                                }
                            }
                        }

                        // Наименьшая цена 1
                        if (empty($parent_array[$p[0]]['price']) and ! empty((int) $item->Количество[0])) {
                            $parent_array[$p[0]]['price'] = $parent_price1;
                        } else if ($parent_array[$p[0]]['price'] > $parent_price1 and ! empty($parent_price1) and ! empty((int) $item->Количество[0])) {
                            $parent_array[$p[0]]['price'] = $parent_price1;
                        }


                        // Наименьшая цена 2
                        if (empty($parent_array[$p[0]]['price2']) and ! empty((int) $item->Количество[0])) {
                            $parent_array[$p[0]]['price2'] = $parent_price2;
                        } else if ($parent_array[$p[0]]['price2'] > $parent_price2 and ! empty($parent_price2) and ! empty((int) $item->Количество[0])) {
                            $parent_array[$p[0]]['price2'] = $parent_price2;
                        }

                        // Наименьшая цена 3
                        if (empty($parent_array[$p[0]]['price3']) and ! empty((int) $item->Количество[0])) {
                            $parent_array[$p[0]]['price3'] = $parent_price3;
                        } else if ($parent_array[$p[0]]['price3'] > $parent_price3 and ! empty($parent_price3) and ! empty((int) $item->Количество[0])) {
                            $parent_array[$p[0]]['price3'] = $parent_price3;
                        }

                        // Наименьшая цена 4
                        if (empty($parent_array[$p[0]]['price4']) and ! empty((int) $item->Количество[0])) {
                            $parent_array[$p[0]]['price4'] = $parent_price4;
                        } else if ($parent_array[$p[0]]['price4'] > $parent_price4 and ! empty($parent_price4) and ! empty((int) $item->Количество[0])) {
                            $parent_array[$p[0]]['price4'] = $parent_price4;
                        }

                        // Наименьшая цена 5
                        if (empty($parent_array[$p[0]]['price5']) and ! empty((int) $item->Количество[0])) {
                            $parent_array[$p[0]]['price5'] = $parent_price5;
                        } else if ($parent_array[$p[0]]['price5'] > $parent_price5 and ! empty($parent_price5) and ! empty((int) $item->Количество[0])) {
                            $parent_array[$p[0]]['price5'] = $parent_price5;
                        }

                        // Наименьшая цена 1 без остатка
                        if (empty($parent_array[$p[0]]['price_no_items'])) {
                            $parent_array[$p[0]]['price_no_items'] = $parent_price1;
                        } else if ($parent_array[$p[0]]['price_no_items'] > $parent_price1 and ! empty($parent_price1)) {
                            $parent_array[$p[0]]['price_no_items'] = $parent_price1;
                        }

                        // Наименьшая цена 2 без остатка
                        if (empty($parent_array[$p[0]]['price2_no_items'])) {
                            $parent_array[$p[0]]['price2_no_items'] = $parent_price2;
                        } else if ($parent_array[$p[0]]['price2_no_items'] > $parent_price2 and ! empty($parent_price2)) {
                            $parent_array[$p[0]]['price2_no_items'] = $parent_price2;
                        }

                        // Наименьшая цена 3 без остатка
                        if (empty($parent_array[$p[0]]['price3_no_items'])) {
                            $parent_array[$p[0]]['price3_no_items'] = $parent_price3;
                        } else if ($parent_array[$p[0]]['price3_no_items'] > $parent_price3 and ! empty($parent_price3)) {
                            $parent_array[$p[0]]['price3_no_items'] = $parent_price3;
                        }

                        // Наименьшая цена 4 без остатка
                        if (empty($parent_array[$p[0]]['price4_no_items'])) {
                            $parent_array[$p[0]]['price4_no_items'] = $parent_price4;
                        } else if ($parent_array[$p[0]]['price4_no_items'] > $parent_price4 and ! empty($parent_price4)) {
                            $parent_array[$p[0]]['price4_no_items'] = $parent_price4;
                        }

                        // Наименьшая цена 5 без остатка
                        if (empty($parent_array[$p[0]]['price5_no_items'])) {
                            $parent_array[$p[0]]['price5_no_items'] = $parent_price5;
                        } else if ($parent_array[$p[0]]['price5_no_items'] > $parent_price5 and ! empty($parent_price5)) {
                            $parent_array[$p[0]]['price5_no_items'] = $parent_price5;
                        }

                        // Валюта
                        if (empty($parent_array[$p[0]]['currensy']))
                            $parent_array[$p[0]]['currensy'] = (string) $item->Цены->Цена[0]->Валюта[0];

                        // Склад главного товара
                        $parent_array[$p[0]]['warehouse'][] = $warehouses;

                        // Имя подтипа
                        $Наименование = $parent_name = null;
                        if (isset($item->ХарактеристикиТовара)) {
                            foreach ($item->ХарактеристикиТовара->ХарактеристикаТовара as $sorts) {

                                // Блокировка характеристик
                                if (in_array(PHPShopString::utf8_win1251((string) $sorts->Наименование), $sort_ignore)) {
                                    continue;
                                }

                                $Наименование .= ' ' . (string) $sorts->Значение;
                                $parent_name .= (string) $sorts->Значение . '@';
                            }

                            if (!empty($Наименование))
                                (string) $item->Наименование[0] .= $Наименование;
                        }

                        $parent_enabled = 1;

                        // Картинка
                        if (isset($item->Картинка) and ! empty($this->exchange_image)) {

                            if (!is_array((array) $item->Картинка))
                                (array) $item->Картинка[] = (string) $item->Картинка;

                            foreach ((array) $item->Картинка as $i => $img) {

                                $ext = pathinfo($img, PATHINFO_EXTENSION);

                                $new_name = 'img' . $this->crc32((string) $item->Ид[0]) . '_' . ($i + 1) . '.' . $ext;
                                $new_name_s = 'img' . $this->crc32((string) $item->Ид[0]) . '_' . ($i + 1) . 's.' . $ext;
                                $new_name_big = 'img' . $this->crc32((string) $item->Ид[0]) . '_' . ($i + 1) . '_big.' . $ext;

                                // Тубнейл
                                $thumb = new PHPThumb(dirname(__FILE__) . $this->exchange_path . '/' . self::$upload1c . $img);
                                $thumb->setOptions(array('jpegQuality' => $this->width_kratko));
                                $thumb->resize($this->img_tw, $this->img_th);
                                $thumb->save($_SERVER['DOCUMENT_ROOT'] . $this->exchange_image_path . $this->image_result_path . $new_name_s);

                                // Основное
                                $thumb = new PHPThumb(dirname(__FILE__) . $this->exchange_path . '/' . self::$upload1c . $img);
                                $thumb->setOptions(array('jpegQuality' => $this->width_kratko));
                                $thumb->resize($this->img_w, $this->img_h);
                                $thumb->save($_SERVER['DOCUMENT_ROOT'] . $this->exchange_image_path . $this->image_result_path . $new_name);

                                // Исходное
                                if (!empty($this->image_save_source))
                                    copy(dirname(__FILE__) . $this->exchange_path . '/' . self::$upload1c . $img, $this->exchange_image_path . $this->image_result_path . $new_name_big);

                                $image = $this->image_result_path . 'img' . $this->crc32((string) $item->Ид[0]);

                                if ($ext != 'jpg')
                                    $image .= '#' . $ext;

                                $image_count++;
                            }
                        }
                    }
                    else {
                        $parent_enabled = 0;
                        $parent_name = null;
                    }

                    // Артикул для подтипа
                    if ($parent_enabled == 1)
                        $uid = (string) $item->Ид[0];
                    else
                        $uid = null;

                    // Внешние коды цены
                    if (!empty($this->exchange_price1)) {

                        $price1 = $price2 = $price3 = $price4 = $price5 = 0;

                        if (isset($item->Цены)) {
                            foreach ($item->Цены->Цена as $prices) {

                                if ($this->exchange_price1 == (string) $prices->ИдТипаЦены[0])
                                    $price1 = (string) $prices->ЦенаЗаЕдиницу[0];

                                elseif ($this->exchange_price2 == (string) $prices->ИдТипаЦены[0])
                                    $price2 = (string) $prices->ЦенаЗаЕдиницу[0];

                                elseif ($this->exchange_price3 == (string) $prices->ИдТипаЦены[0])
                                    $price3 = (string) $prices->ЦенаЗаЕдиницу[0];

                                elseif ($this->exchange_price4 == (string) $prices->ИдТипаЦены[0])
                                    $price4 = (string) $prices->ЦенаЗаЕдиницу[0];

                                elseif ($this->exchange_price5 == (string) $prices->ИдТипаЦены[0])
                                    $price5 = (string) $prices->ЦенаЗаЕдиницу[0];
                            }
                        }
                    } else {
                        $price1 = (string) $item->Цены->Цена[0]->ЦенаЗаЕдиницу[0];
                        $price2 = (string) $item->Цены->Цена[1]->ЦенаЗаЕдиницу[0];
                        $price3 = (string) $item->Цены->Цена[2]->ЦенаЗаЕдиницу[0];
                        $price4 = (string) $item->Цены->Цена[3]->ЦенаЗаЕдиницу[0];
                        $price5 = (string) $item->Цены->Цена[4]->ЦенаЗаЕдиницу[0];
                    }

                    // Форматирование цены 2.04
                    if (!strpos($price1, '.') and ! strpos($price2, '.') and ! strpos($price3, '.') and ! strpos($price4, '.') and ! strpos($price5, '.')) {
                        $price1 = preg_replace('/\D+/', '', $price1);
                        $price2 = preg_replace('/\D+/', '', $price2);
                        $price3 = preg_replace('/\D+/', '', $price3);
                        $price4 = preg_replace('/\D+/', '', $price4);
                        $price5 = preg_replace('/\D+/', '', $price5);
                    }

                    // Наименование
                    if ($this->exchange_name == 'print') {
                        $title = null;
                        
                    } else
                        $title = (string) $item->Наименование[0];

                    $this->product_array[(string) $item->Ид[0]] = array($uid, $title, null, $image, null, $image_count, $warehouse, $price1, $price2, $price3, $price4, $price5, "", "", (string) $item->Цены->Цена[0]->Валюта[0], null, $parent_name, (string) $item->Ид[0], $parent_enabled);
                }

                // Запись в файл
                if (count($this->product_array) > 1) {

                    // Добавляем главные товары для подтипов
                    $parent = null;

                    if (is_array($parent_array)) {

                        $parent = null;
                        foreach ($parent_array as $id => $prod) {

                            // Подтипы
                            if (!empty($prod['ids']))
                                $parent = substr($prod['ids'], 0, strlen($prod['ids']) - 1);

                            // Цена 1 без остатка
                            if (empty($prod['price']) and ! empty($prod['price_no_items']))
                                $prod['price'] = $prod['price_no_items'];

                            // Цена 2 без остатка
                            if (empty($prod['price2']) and ! empty($prod['price2_no_items']))
                                $prod['price2'] = $prod['price2_no_items'];

                            // Цена 3 без остатка
                            if (empty($prod['price3']) and ! empty($prod['price3_no_items']))
                                $prod['price3'] = $prod['price3_no_items'];

                            // Цена 4 без остатка
                            if (empty($prod['price4']) and ! empty($prod['price4_no_items']))
                                $prod['price4'] = $prod['price4_no_items'];

                            // Цена 5 без остатка
                            if (empty($prod['price5']) and ! empty($prod['price5_no_items']))
                                $prod['price5'] = $prod['price5_no_items'];

                            // Форматирование цены 2.04
                            if (!strpos($prod['price'], '.'))
                                $prod['price'] = preg_replace('/\D+/', '', $prod['price']);
                            if (!strpos($prod['price2'], '.'))
                                $prod['price2'] = preg_replace('/\D+/', '', $prod['price2']);
                            if (!strpos($prod['price3'], '.'))
                                $prod['price3'] = preg_replace('/\D+/', '', $prod['price3']);
                            if (!strpos($prod['price4'], '.'))
                                $prod['price4'] = preg_replace('/\D+/', '', $prod['price4']);
                            if (!strpos($prod['price5'], '.'))
                                $prod['price5'] = preg_replace('/\D+/', '', $prod['price5']);

                            // Склад
                            if (is_array($prod['warehouse']))
                                foreach ($prod['warehouse'] as $stocks) {

                                    if (is_array($stocks))
                                        foreach ($stocks as $k => $stock) {
                                            $warehouses_parent[$k] += $stock;
                                        }
                                }

                            if (is_array($warehouses_parent)) {
                                $warehouse = null;

                                foreach ($warehouses_parent as $k => $v) {
                                    $warehouse .= $v . '/' . $k . '#';
                                }

                                $warehouse = substr($warehouse, 0, strlen($warehouse) - 1);
                                unset($warehouses_parent);
                            }
                            $this->product_array[$id] = array(null, null, null, null, null, null, $warehouse, $prod['price'], $prod['price2'], $prod['price3'], $prod['price4'], $prod['price5'], "", "", $prod['currensy'], null, $parent, $id, 0);
                        }
                    }

                    if ($GLOBALS['PHPShopBase']->codBase != 'utf-8')
                        array_walk_recursive($this->product_array, 'self::array2iconv');


                    $this->writeCsv('sklad/' . $date . '/upload_0.csv', $this->product_array, true);

                    // Выполнение
                    $this->load($date, false);
                }
            }
        } else {
            echo "Ошибка загрузки XML\n";
            foreach (libxml_get_errors() as $error) {
                echo "\t", $error->message;
            }
        }
    }

    private function encode($pas) {
        $encode = null;
        for ($i = 0; $i < (strlen($pas)); $i++)
            $encode .= ord($pas[$i]) . "O";

        $encode = str_replace(11, "I", $encode);
        return $encode . "I10O";
    }

    private function load($date, $create_category = true) {
        $protocol = 'http://';
        if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])) {
            $protocol = 'https://';
        }

        if ($this->exchange_create_category == 1 and ! empty($create_category))
            $create_category = 'true';

        if ($this->exchange_create == 1)
            $create = 'true';

        if (!empty($_SESSION['token']))
            $token = '&token=' . $_SESSION['token'];
        else
            $token = null;

        $url = $protocol . $_SERVER['SERVER_NAME'] . '/1cManager/result.php?date=' . $date . '&files=all&log=' . $_SERVER['PHP_AUTH_USER'] . '&pas=' . $this->encode($_SERVER['PHP_AUTH_PW']) . '&create=' . $create . '&create_category=' . $create_category . $token . '&cml=true';

        $сurl = curl_init();
        curl_setopt_array($сurl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false
        ));
        $action = curl_exec($сurl);
        curl_close($сurl);
        return $action;
    }

    private function log($type, $mode, $response) {

        if (!empty($this->exchange_log)) {
            $file = $this->log_path . '/cml_' . date("d_m_y") . '.log';

            if (isset($_GET['filename']))
                $filename .= '&filename=' . $_GET['filename'];
            else
                $filename = null;

            $content = '
==== ' . date('d-m-y H:i:s') . '=====
IN: ' . $_SERVER['PHP_SELF'] . '?type=' . $type . '&mod=' . $mode . $filename . '
OUT: ' . $response . '
' . $this->error;

            $fp = fopen($file, "a+");
            if ($fp) {
                fputs($fp, $content);
                fclose($fp);
            }
        }
    }

}

$_classPath = "../phpshop/";
include($_classPath . "class/obj.class.php");
include($_classPath . 'lib/thumb/phpthumb.php');
PHPShopObj::loadClass(array("base", "system", "array", "valuta"));

// Подключение к БД
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopSystem = new PHPShopSystem();
$CommerceMLLoader = new CommerceMLLoader();
echo $CommerceMLLoader->exchange($_GET['type'], $_GET['mode']);
?>