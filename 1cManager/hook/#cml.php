<?php

/**
 * Модуль расширения для учета внешнего кода и файлов в CML
 * @author PHPShop Software
 * @version 1.1
 */
// Персонализация настроек
function mod_option($option) {
    $GLOBALS['option']['sort'] = 19;
}

// Персонализация вставки
function mod_insert(&$CsvToArray, $class_name, $func_name) {

    if (strstr($CsvToArray[3], '|')) {
        
        $image = explode("|", $CsvToArray[3])[0];
        $file = explode("|", $CsvToArray[3])[1];

        $CsvToArray[3] = $image;

        if (strstr($file, ','))
            $files = explode(',', $file);
        else
            $files[] = $file;

        foreach ($files as $f) {
            $path_parts = pathinfo($f);
            $files_path[] = array('name' => $path_parts['basename'], 'path' => $GLOBALS['SysValue']['dir']['dir'] . "/UserFiles/Files/" . $f);
        }

        $return = " files='" . serialize($files_path) . "', ";
    }

    $return .=" external_code='" . $CsvToArray[17] . "', ";
    
    return $return;
}

// Персонализация обновления
function mod_update(&$CsvToArray, $class_name, $func_name) {

    if (strstr($CsvToArray[3], '|')) {
        
        $image = explode("|", $CsvToArray[3])[0];
        $file = explode("|", $CsvToArray[3])[1];

        $CsvToArray[3] = $image;

        if (strstr($file, ','))
            $files = explode(',', $file);
        else
            $files[] = $file;

        foreach ($files as $f) {
            $path_parts = pathinfo($f);
            $files_path[] = array('name' => $path_parts['basename'], 'path' => $GLOBALS['SysValue']['dir']['dir'] . "/UserFiles/Files/" . $f);
        }

        $return = " files='" . serialize($files_path) . "', ";
    }
   
    return $return;
}
