<?php

session_start();
$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass(array("base", "file"));

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
if (empty($_SESSION['idPHPSHOP'])) {
    header("Location: " . $GLOBALS['SysValue']['dir']['dir'] . "/phpshop/admpanel/");
    exit("No access");
}

// Восстановление бекапа
function mysqlrestore() {
    global $link_db;

    if (!empty($_POST['file'])) {
        $csv_file = $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . $_POST['file'];

        $path_parts = pathinfo($csv_file);

        // GZIP
        if ($path_parts['extension'] == 'gz') {

            $tmp_csv_file = str_replace('.sql.gz', '.sql', $csv_file);

            // Создание темпового файла
            if (!file_exists($tmp_csv_file)) {
                ob_start();
                readgzfile($csv_file);
                $file_content = ob_get_clean();
                file_put_contents($tmp_csv_file, $file_content);
            }

            $csv_file = $tmp_csv_file;
        }

        $file = new SplFileObject($csv_file);

        // Настройки
        $fileIterator = new LimitIterator($file, 0, 1);
        foreach ($fileIterator as $line) {
            if (strstr($line, "#"))
                $option = explode("#", $line);
        }

        if (is_array($option))
            array_shift($option);

        // Новый формат
        if (is_array($option)) {

            foreach ($option as $k => $v)
                $option_restore[$k] = explode("-", $v);


            $start = $option_restore[(int) $_POST['option']][0];
            $end = $option_restore[(int) $_POST['option']][1];

            if (!empty($start) and ! empty($end))
                $fileIterator2 = new LimitIterator($file, $start, $end);
            $sql_query = null;

            if ($fileIterator2)
                foreach ($fileIterator2 as $line) {
                    $sql_query .= $line;
                }

            // Кодировка UTF
            if ($GLOBALS['PHPShopBase']->codBase == 'utf-8') {
                $sql_query = str_replace("CHARSET=cp1251", "CHARSET=utf8", $sql_query);
                $sql_query = PHPShopString::win_utf8($sql_query, true);
            }

            $sql_array = explode(";" . PHP_EOL, trim($sql_query));

            $result = true;
            foreach ($sql_array as $v) {
                if (!empty($v))
                    $result = mysqli_query($link_db, trim($v));
            }

            $bar = round($_POST['option'] * 100 / count($option_restore));

            if (count($option_restore) > $_POST['option']) {
                return array("success" => $result, 'option' => $_POST['option'] + 1, 'bar' => $bar);
            } else {

                $file = null;

                // Удаление временного файла
                if (isset($tmp_csv_file)) {
                    @unlink($tmp_csv_file);
                }

                return array("success" => 'done', 'bar' => $bar);
            }
        }
        // Обычный формат
        else {

            $result_error_tracer = $error_line = null;

            // GZIP
            if ($path_parts['extension'] == 'gz') {
                ob_start();
                readgzfile($csv_file);
                $sql_file_content = ob_get_clean();
            } else
                $sql_file_content = file_get_contents($csv_file);

            // Кодировка UTF
            if ($GLOBALS['PHPShopBase']->codBase == 'utf-8') {
                $sql_file_content = str_replace("CHARSET=cp1251", "CHARSET=utf8", $sql_file_content);
                $sql_file_content = PHPShopString::win_utf8($sql_file_content, true);
            }

            $sql_query = PHPShopFile::sqlStringToArray($sql_file_content);

            foreach ($sql_query as $k => $v) {

                if (strlen($v) > 10) {
                    $result = mysqli_query($link_db, $v);


                    if (!$result) {
                        $error_line .= '[Line ' . $k . '] ';
                        $result_error_tracert .= 'Запрос: ' . $v . '
Ошибка: ' . mysqli_error($link_db);
                    } //else @mysqli_free_result($result);
                }
            }


            // Выполнено успешно
            if (empty($result_error_tracert)) {
                return array("success" => 'done', 'bar' => 100);
            } else {
                return array("success" => false, "error" => mysqli_error($link_db) . ' -> ' . $error_line);
            }
        }
    }
}

$result = mysqlrestore();
if ($result) {
    header("Content-Type: application/json");
    echo json_encode($result);
}
