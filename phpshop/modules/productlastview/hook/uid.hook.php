<?php

function uid_productlastview_hook($obj, $row, $rout) {

    if ($rout == 'END') {

        $GLOBALS['ProductLastView']->add($row['id']);

        // Чистка
        $GLOBALS['ProductLastView']->clean_memory();

        // Запись в БД
        $GLOBALS['ProductLastView']->add_memory();

        // Ключ памяти в куку
        $GLOBALS['ProductLastView']->add_cookie();
    }
}

$addHandler = array
    (
    'UID' => 'uid_productlastview_hook'
);
?>