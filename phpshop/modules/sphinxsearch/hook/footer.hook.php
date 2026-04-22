<?php

function footer_sphinxsearch_hook()
{
    $dis = '<link rel="stylesheet" href="/phpshop/modules/sphinxsearch/templates/style.css">';
    echo $dis;
}

$addHandler = [
    'footer' => 'footer_sphinxsearch_hook'
];