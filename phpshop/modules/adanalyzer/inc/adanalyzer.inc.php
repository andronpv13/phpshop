<?php
// Тест
//setcookie("ps_adanalyzer", 'test123456', time() + 60 * 60 * 24 * 90, "/", $_SERVER['SERVER_NAME'], 0);

$url = parse_url($_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
$referal = $url["host"];

// Поиск UTM меток
parse_str($url['query'], $query);

if (is_array($query)) {
    if (!empty($query['utm_campaign']))
        $utm = PHPShopSecurity::TotalClean($query['utm_campaign']);
}

// Запись UTM метки
if (strlen($_SERVER['HTTP_REFERER']) > 5 and !strpos($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME']) and ! empty($utm)) {
    setcookie("ps_adanalyzer", $utm, time() + 60 * 60 * 24 * 90, "/", $_SERVER['SERVER_NAME'], 0);
}
?>