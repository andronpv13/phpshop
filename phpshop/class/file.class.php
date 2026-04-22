<?php

/**
 * Библиотека для работы с файлами
 * @author PHPShop Software
 * @version 1.4
 * @package PHPShopClass
 */
class PHPShopFile {

    /**
     * Перевод в латиницу имени файла
     * @param string $str
     * @return string
     */
    static function toLatin($str) {
 
        // UTF Fix
        if ($GLOBALS['PHPShopBase']->codBase == 'utf-8')
            $str = iconv("utf-8", "windows-1251//IGNORE", $str);

        $str = str_replace("%20", "_", $str);
        $str = str_replace("/", "-", $str); 
        $str = str_replace("\\", "", $str);
        $str = str_replace("(", "", $str);
        $str = str_replace(")", "", $str);
        $str = str_replace(":", "", $str);
        $str = str_replace(" ", "_", $str);
        $str = str_replace(",", "", $str);
        $str = str_replace("!", "", $str);
        $str = str_replace("|", "_", $str);
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
        $str = str_replace("%", "", $str);
        $str = str_replace("*", "", $str);

        $new_str = '';
        $_Array = array(" " => "_", "а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d", "е" => "e", "ё" => "e", "є" => "e", "ї" => "yi", "Є" => "e", "Ї" => "yi", "ж" => "zh", "з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l", "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r", "с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h", "ц" => "c", "ч" => "ch", "ш" => "sh", "щ" => "sch", "ъ" => "i", "ы" => "y", "ь" => "i", "э" => "e", "ю" => "u", "я" => "ya", "А" => "a", "Б" => "b", "В" => "v", "Г" => "g", "Д" => "d", "Е" => "e", "Ё" => "e", "Ж" => "zh", "З" => "z", "И" => "i", "Й" => "y", "К" => "k", "Л" => "l", "М" => "m", "Н" => "n", "О" => "o", "П" => "p", "Р" => "r", "С" => "s", "Т" => "t", "Ы" => "Y", "У" => "u", "Ф" => "f", "Х" => "h", "Ц" => "c", "Ч" => "ch", "Ш" => "sh", "Щ" => "sch", "Э" => "e", "Ю" => "u", "Я" => "ya",  "$" => "i", "%" => "i", "&" => "_and_");

        $chars = preg_split('//', $str, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($chars as $val)
            if (empty($_Array[$val]))
                $new_str .= $val;
            else
                $new_str .= $_Array[$val];

        return $new_str;
    }

    /**
     * Права на запись файла
     * @param string $file имя файла
     */
    static function chmod($file, $error = false, $rul = 0775) {
        if (function_exists('chmod')) {
            if (@chmod($file, $rul))
                return true;
            elseif ($error)
                return 'Нет файла ' . $file;
        }
        elseif ($error)
            return __FUNCTION__ . '() запрещена';
    }

    /**
     * Запись данных в файл
     * @param string $file путь до файла
     * @param string $csv данные для записи
     * @param string $type параметр записи
     * @param bool $error вывод ошибки
     */
    static function write($file, $csv, $type = 'w+', $error = false) {
        $fp = @fopen($file, $type);
        if ($fp) {
            //stream_set_write_buffer($fp, 0);
            fputs($fp, $csv);
            fclose($fp);
            return true;
        } elseif ($error)
            echo 'Нет файла ' . $file;
    }

    /**
     * Запись данных в csv файл
     * @param string $file путь до файла
     * @param array $csv данные для записи
     * @param bool $error вывод ошибки
     */
    static function writeCsv($file, $csv, $error = false) {
        $fp = fopen($file, "w+");
        if ($fp) {
            if (is_array($csv))
                foreach ($csv as $value) {
                    fputcsv($fp, $value, ';', '"');
                }
            fclose($fp);
        } elseif ($error)
            echo 'Нет файла ' . $file;
    }

    /**
     * Чтение CSV файла
     * @param string $file адрес файла
     * @param string $function имя функции обработчика
     * @param string $delim разделитель
     * @return bool
     */
    static function readCsv($file, $function, $delim = ';') {

        $opts = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );

        $fp = @fopen($file, "r", false, stream_context_create($opts));
        if ($fp) {
            while (($data = @fgetcsv($fp, 0, $delim)) !== FALSE) {
                call_user_func($function, $data);
            }
            fclose($fp);
            return true;
        }
    }

    /**
     * Генератор
     */
    static function getLines($file, $delim) {
        $fp = fopen($file, 'r');
        try {
            while ($line = fgetcsv($fp, 0, $delim)) {
                yield $line;
            }
        } finally {
            fclose($fp);
        }
    }

    /**
     * Чтение CSV файла с генератором
     * @param string $file адрес файла
     * @param string $function имя функции обработчика
     * @param string $delim разделитель
     * @param string $limit массив значений интервала (0,500)
     * @return bool
     */
    static function readCsvGenerators($file, $function, $delim = ';', $limit = array(0, 500)) {

        foreach (self::getLines($file, $delim) as $n => $line) {
            if ($n == 0 or ( $limit[0] <= $n and $n < $limit[1]))
                call_user_func($function, $line);
            else
                continue;
        }

        if (file_exists($file))
            return true;
    }

    /**
     * GZIP компрессия файла
     * @param string $source путь до файла
     * @param int $level степень сжатия
     * @return bool
     */
    static function gzcompressfile($source, $level = false) {
        $dest = $source . '.gz';
        $mode = 'wb' . $level;
        $error = false;
        if ($fp_out = @gzopen($dest, $mode)) {
            if ($fp_in = @fopen($source, 'rb')) {
                while (!feof($fp_in))
                    gzwrite($fp_out, fread($fp_in, 1024 * 512));
                fclose($fp_in);
            } else
                $error = true;
            gzclose($fp_out);
            unlink($source);
            //rename($dest, $source . '.bz2');
        } else
            $error = true;
        if ($error)
            return false;
        else
            return $dest;
    }

    /**
     * Поиск файлов
     * @param string $dir папка
     * @param string $function функция обработки
     * @param bool $return функция возвратить первый найденный файл
     * @return mixed
     */
    static function searchFile($dir, $function, $return = false) {
        $user_func_result = null;
        if (is_dir($dir))
            if (@$dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file != '.' and $file != '..') {
                        $user_func_result .= call_user_func_array($function, array($file));
                        if ($return)
                            return $user_func_result;
                    }
                }

                return $user_func_result;
                closedir($dh);
            }
    }

    /**
     * @param string $sql
     * @return array
     */
    public static function sqlStringToArray($sql) {
        $sql = trim($sql);

        if (strpos($sql, "\r\n")) {
            $eol = "\r\n";
        } elseif (strpos($sql, "\n")) {
            $eol = "\n";
        } else {
            $eol = "\r";
        }

        $array = explode(";" . $eol, $sql);

        foreach ($array as $key => $element) {
            $array[$key] = trim($element);
            if (substr($element, -1) !== ';') {
                $array[$key] = $element . ';';
            }
        }

        return $array;
    }

}

/**
 * Поиск лицензии
 * @param string $file имя файла
 * @return string
 */
function getLicense($file) {
    $fstat = explode(".", $file);
    if ($fstat[1] == "lic")
        return $file;
}

?>