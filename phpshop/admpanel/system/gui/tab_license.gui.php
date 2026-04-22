<?php

function tab_license() {
    return '<span id="license">'.  file_get_contents($GLOBALS['_classPath'] . 'locale/' . $_SESSION['lang']. '/license.tpl').'</span>';
}

?>