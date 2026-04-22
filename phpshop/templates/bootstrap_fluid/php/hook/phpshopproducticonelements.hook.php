<?php


function specMainIcon_hook($obj) {
    $obj->limitspec = 3;
}

$addHandler = array
    (
    'specMainIcon' => 'specMainIcon_hook',
);
?>