<?php

// Снятие ограничение для больших папок
if (function_exists('set_time_limit'))
    set_time_limit(0);

/**
 * Резервная копия БД
 * @param string $dbname имя БД
 * @param string $file имя файла
 * @param bool $structure_only только структуру
 * @param bool $pattern_table отбор таблиц
 */
function mysqlbackup($dbname, $file, $structure_only = false, $pattern_table = false) {
    global $link_db;

    $crlf = PHP_EOL;
    $stat = null;

    $res = mysqli_query($link_db, "SHOW TABLES FROM `" . $dbname . "`");
    $nt = mysqli_num_rows($res);

    for ($a = 0; $a < $nt; $a++) {
        $row = mysqli_fetch_row($res);
        $tablename = $row[0];

        if ($pattern_table)
            if (!in_array($tablename, $pattern_table))
                continue;

        //$sql=$crlf."# ----------------------------------------".$crlf."# table structure for table '$tablename' ".$crlf;
        $sql = "DROP TABLE IF EXISTS $tablename;" . $crlf;

        $result = mysqli_query($link_db, "SHOW CREATE TABLE $tablename");
        if ($result != FALSE && mysqli_num_rows($result) > 0) {
            $tmpres = mysqli_fetch_array($result);
            $pos = strpos($tmpres[1], ' (');
            $tmpres[1] = substr($tmpres[1], 0, 13)
                    . $tmpres[0]
                    . substr($tmpres[1], $pos);

            $sql .= $tmpres[1] . ";" . $crlf . $crlf;
        }
        mysqli_free_result($result);

        $stat .= out($file, $sql);
        $sql = null;
        $count = 0;
        if ($structure_only == FALSE) {

            // here we get table content
            $result = mysqli_query($link_db, "SELECT * FROM  $tablename");
            $fields_cnt = mysqli_num_fields($result);
            while ($row = mysqli_fetch_row($result)) {
                $table_list = '(';
                for ($j = 0; $j < $fields_cnt; $j++) {
                    $finfo = mysqli_fetch_field_direct($result, $j);
                    $table_list .= $finfo->name . ', ';
                }
                $table_list = substr($table_list, 0, -2);
                $table_list .= ')';

                $sql .= 'INSERT INTO ' . $tablename
                        . ' VALUES (';
                for ($j = 0; $j < $fields_cnt; $j++) {
                    if (!isset($row[$j])) {
                        $sql .= ' NULL, ';
                    } else if ($row[$j] == '0' || $row[$j] != '') {
                        $finfo = mysqli_fetch_field_direct($result, $j);
                        $type = $finfo->type;
                        // a number
                        if ($type == 'tinyint' || $type == 'smallint' || $type == 'mediumint' || $type == 'int' ||
                                $type == 'bigint' || $type == 'timestamp') {
                            $sql .= $row[$j] . ', ';
                        }
                        // a string
                        else {
                            $dummy = '';
                            $srcstr = $row[$j];
                            for ($xx = 0; $xx < strlen($srcstr); $xx++) {
                                $yy = strlen($dummy);
                                if ($srcstr[$xx] == '\\')
                                    $dummy .= '\\\\';
                                if ($srcstr[$xx] == '\'')
                                    $dummy .= '\\\'';
                                if ($srcstr[$xx] == "\x00")
                                    $dummy .= '\0';
                                if ($srcstr[$xx] == "\x0a")
                                    $dummy .= '\n';
                                if ($srcstr[$xx] == "\x0d")
                                    $dummy .= '\r';
                                if ($srcstr[$xx] == "\x1a")
                                    $dummy .= '\Z';
                                if (strlen($dummy) == $yy)
                                    $dummy .= $srcstr[$xx];
                            }
                            $sql .= "'" . $dummy . "', ";
                        }
                    } else {
                        $sql .= "'', ";
                    } // end if
                } // end for
                $sql = preg_replace('/, $/', '', $sql);
                $sql .= ");" . $crlf;

                // Лимит строк
                if ($count == 100) {
                    $stat .= out($file, $sql);
                    $count = 0;
                    $sql=null;
                } else
                    $count++;
            }
            $stat .= out($file, $sql);
            mysqli_free_result($result);
        }
    }

    // Статистика в 1 строку
    $file_data = $stat . $crlf;
    $file_data .= file_get_contents($file);
    file_put_contents($file, $file_data);

    return true;
}

// Сохранение в файл
function out($file, $content) {

    if (file_exists($file))
        $file_start = file($file);
    else $file_start=null;

    if (is_array($file_start))
        $start = count($file_start);
    else
        $start = 1;

    $fp = fopen($file, "a+");
    if ($fp) {
        fputs($fp, $content);
        fclose($fp);
    }

    $file_end = file($file);

    if (is_array($file_end))
        $end = count($file_end);
    else
        $end = 1;

    return '#' . $start . '-' . ($end - $start + 1);
}
