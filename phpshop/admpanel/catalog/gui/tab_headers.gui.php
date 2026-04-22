<?php

/**
 * Панель заголовков каталога
 * @param array $row массив данных
 * @return string 
 */
function tab_headers($row) {
    global $PHPShopGUI;

    $title = $row['title'];
    $title_enabled = $row['title_enabled'];
    $title_shablon = $row['title_shablon'];
    $descrip = $row['descrip'];
    $descrip_enabled = $row['descrip_enabled'];
    $descrip_shablon = $row['descrip_shablon'];
    $keywords = $row['keywords'];
    $keywords_enabled = $row['keywords_enabled'];
    $keywords_shablon = $row['keywords_shablon'];
    
    $t1=$t2=$t3=$d1=$d2=$d3=$k1=$k2=$k3=null;
    
    if ($title_enabled == 0) {
        $t1 = "checked";
        $t2_enabled = "none";
        $t3_enabled = "none";
    } elseif ($title_enabled == 1) {
        $t2 = "checked";
        $t2_enabled = "block";
        $t3_enabled = "none";
    } elseif ($title_enabled == 2) {
        $t3 = "checked";
        $t3_enabled = "block";
        $t2_enabled = "none";
    }

    if ($descrip_enabled == 0) {
        $d1 = "checked";
        $d2_enabled = "none";
        $d3_enabled = "none";
    } elseif ($descrip_enabled == 1) {
        $d2 = "checked";
        $d2_enabled = "block";
        $d3_enabled = "none";
    } elseif ($descrip_enabled == 2) {
        $d3 = "checked";
        $d3_enabled = "block";
        $d2_enabled = "none";
    }

    if ($keywords_enabled == 0) {
        $k1 = "checked";
        $k2_enabled = "none";
        $k3_enabled = "none";
    } elseif ($keywords_enabled == 1) {
        $k2 = "checked";
        $k2_enabled = "block";
        $k3_enabled = "none";
    } elseif ($keywords_enabled == 2) {
        $k3 = "checked";
        $k3_enabled = "block";
        $k2_enabled = "none";
    }


    $disp = '
<script src="./catalog/gui/headers.gui.js"></script>';
    
    
    
    $disp.=$PHPShopGUI->setCollapse("Title",'
<label><input type="radio" value="0" name="title_enabled_new" onclick="document.getElementById(\'titleForma\').style.display=\'none\';document.getElementById(\'titleShablon\').style.display=\'none\'" ' . $t1 . '> '.__('Автоматическая генерация').'</label>&nbsp;&nbsp;&nbsp;
<label><input type="radio" value="2" name="title_enabled_new" onclick="document.getElementById(\'titleShablon\').style.display=\'block\';document.getElementById(\'titleForma\').style.display=\'none\'" ' . $t3 . '> '.__('Мой шаблон').'</label> &nbsp;&nbsp;&nbsp;
<label><input type="radio" value="1" name="title_enabled_new"  onclick="document.getElementById(\'titleForma\').style.display=\'block\';document.getElementById(\'titleShablon\').style.display=\'none\'" ' . $t2 . '> '.__('Ручная настройка').'</label><br>
    
<div id="titleShablon" style="display:' . $t3_enabled . '">
<textarea class="form-control" name="title_shablon_new" id="Shablon">' . $title_shablon . '</textarea>
    <div class="btn-group" role="group" aria-label="...">
<input name="btnLang" type="button" value="'.__('Каталог').'" onclick="ShablonAdd(\'@Catalog@\',\'Shablon\')" class="buttonSh">
<input name="btnLang" type="button" value="'.__('Подкаталог').'" onclick="ShablonAdd(\'@Podcatalog@\',\'Shablon\')" class="buttonSh">
<input type="button" name="btnLang"  value="'.__('Общий').'" onclick="ShablonAdd(\'@System@\',\'Shablon\')" class="buttonSh">
<input type="button" value="," onclick="ShablonAdd(\',\',\'Shablon\')" class="buttonSh">
<input type="button" value="-" onclick="ShablonAdd(\'-\',\'Shablon\')" class="buttonSh">
<input type="button" value="/" onclick="ShablonAdd(\'/\',\'Shablon\')" class="buttonSh">
<input type="button" name="btnLang" value="'.__('Пробел').'" onclick="ShablonAdd(\' \',\'Shablon\')" class="buttonSh">
<input type="button" name="btnLang" value="'.__('Ввести слово').'" onclick="ShablonPromt(\'Shablon\')" class="buttonSh">
<input type="button" name="btnLang" value="'.__('Сбросить').'" onclick="ShablonDell(\'Shablon\')" class="buttonSh">
   </div>
</div>

<div id="titleForma" style="display:' . $t2_enabled . '">
<textarea class="form-control" name="title_new" style="height:150px">' . $title . '</textarea>
'.$PHPShopGUI->setAIHelpButton('title_new',70,'catalog_title').'
</div>');

   $disp.=$PHPShopGUI->setCollapse("Description",'<label>
<input type="radio" value="0" name="descrip_enabled_new" onclick="document.getElementById(\'titleFormaD\').style.display=\'none\';document.getElementById(\'titleShablonD\').style.display=\'none\'" ' . $d1 . '> '.__('Автоматическая генерация').'</label>&nbsp;&nbsp;&nbsp;
<label><input type="radio" value="2" name="descrip_enabled_new" onclick="document.getElementById(\'titleShablonD\').style.display=\'block\';document.getElementById(\'titleFormaD\').style.display=\'none\'" ' . $d3 . '> '.__('Мой шаблон').'</label>&nbsp;&nbsp;&nbsp;
<label><input type="radio" value="1" name="descrip_enabled_new"  onclick="document.getElementById(\'titleFormaD\').style.display=\'block\';document.getElementById(\'titleShablonD\').style.display=\'none\'" ' . $d2 . '> '.__('Ручная настройка').'</label><br>
    
<div id="titleShablonD" style="display:' . $d3_enabled . '">
<textarea class="form-control" name="descrip_shablon_new" id="ShablonD">' . $descrip_shablon . '</textarea>
    <div class="btn-group" role="group" aria-label="...">
<input type="button" name="btnLang" value="'.__('Каталог').'" onclick="ShablonAdd(\'@Catalog@\',\'ShablonD\')" class="buttonSh">
<input type="button"  name="btnLang" value="'.__('Подкаталог').'" onclick="ShablonAdd(\'@Podcatalog@\',\'ShablonD\')" class="buttonSh">
<input type="button" name="btnLang" value="'.__('Общий').'" onclick="ShablonAdd(\'@System@\',\'ShablonD\')" class="buttonSh">
<input type="button" value="," onclick="ShablonAdd(\',\',\'ShablonD\')" class="buttonSh">
<input type="button" value="-" onclick="ShablonAdd(\'-\',\'ShablonD\')" class="buttonSh">
<input type="button" value="/" onclick="ShablonAdd(\'/\',\'ShablonD\')" class="buttonSh">
<input type="button" name="btnLang" value="'.__('Пробел').'" onclick="ShablonAdd(\' \',\'ShablonD\')" class="buttonSh">
<input type="button" name="btnLang" value="'.__('Ввести слово').'" onclick="ShablonPromt(\'ShablonD\')" class="buttonSh">
<input type="button" name="btnLang" value="'.__('Сбросить').'" onclick="ShablonDell(\'ShablonD\')" class="buttonSh">
 </div>
</div>

<div id="titleFormaD" style="display:' . $d2_enabled . '">
<textarea class="form-control" name="descrip_new" style="height:150px">' . $descrip . '</textarea>
'.$PHPShopGUI->setAIHelpButton('descrip_new',80,'catalog_descrip').'
</div>');
   
   $disp.=$PHPShopGUI->setCollapse("Keywords",'

<label>
<input type="radio" value="0" name="keywords_enabled_new" onclick="document.getElementById(\'titleFormaK\').style.display=\'none\';document.getElementById(\'titleShablonK\').style.display=\'none\'" ' . $k1 . '> '.__('Автоматическая генерация').'</label>&nbsp;&nbsp;&nbsp;
<label><input type="radio" value="2" name="keywords_enabled_new" onclick="document.getElementById(\'titleShablonK\').style.display=\'block\';document.getElementById(\'titleFormaK\').style.display=\'none\'" ' . $k3 . '> '.__('Мой шаблон').'</label> &nbsp;&nbsp;&nbsp;
<label><input type="radio" value="1" name="keywords_enabled_new"  onclick="document.getElementById(\'titleFormaK\').style.display=\'block\';document.getElementById(\'titleShablonK\').style.display=\'none\'" ' . $k2 . '> '.__('Ручная настройка').'</label><br>
<div id="titleShablonK" style="display:' . $k3_enabled . '">
<textarea class="form-control" name="keywords_shablon_new" id="ShablonK">' . $keywords_shablon . '</textarea>
     <div class="btn-group" role="group" aria-label="...">
<input type="button" name="btnLang" value="'.__('Каталог').'" onclick="ShablonAdd(\'@Catalog@\',\'ShablonK\')" class="buttonSh">
<input type="button" name="btnLang" value="'.__('Подкаталог').'" onclick="ShablonAdd(\'@Podcatalog@\',\'ShablonK\')" class="buttonSh">
<input type="button" name="btnLang" value="'.__('Общий').'" onclick="ShablonAdd(\'@System@\',\'ShablonK\')" class="buttonSh">
<input type="button" name="btnLang" value="'.__('Автопобор').'" onclick="ShablonAdd(\'@Generator@\',\'ShablonK\')" class="buttonSh">
<input type="button" value="," onclick="ShablonAdd(\',\',\'ShablonK\')" class="buttonSh">
<input type="button" value="-" onclick="ShablonAdd(\'-\',\'ShablonK\')" class="buttonSh">
<input type="button" value="/" onclick="ShablonAdd(\'/\',\'ShablonK\')" class="buttonSh">
<input type="button" name="btnLang" value="'.__('Пробел').'" onclick="ShablonAdd(\' \',\'ShablonK\')" class="buttonSh">
<input type="button" name="btnLang" value="'.__('Слово').'" onclick="ShablonPromt(\'ShablonK\')" class="buttonSh">
<input type="button" name="btnLang" value="'.__('Сбросить').'" onclick="ShablonDell(\'ShablonK\')" class="buttonSh">
</div>
</div>
<div id="titleFormaK" style="display:' . $k2_enabled . '">
<textarea class="form-control"  name="keywords_new">' . $keywords . '</textarea>
</div>');
   
    return $disp;
}
?>