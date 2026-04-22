<?php

function showcaseelement_suggestIP() {
    global $PHPShopSystem;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/iplocate/address');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'ip' => $_SERVER['REMOTE_ADDR'],//"46.39.57.47"
    ]));
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Token ' . $PHPShopSystem->getSerilizeParam('admoption.dadata_token'),
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = json_decode(curl_exec($ch), true)['location'];
    curl_close($ch);

    if (is_array($result))
        return PHPShopString::utf8_win1251($result['data']['region_with_type']);
    else
        return true;
}

function showcaseelement_hook($obj, $row, $rout) {
    global $PHPShopCache;

    if ($rout == 'START') {
        if (!isset($_SESSION['showcase_region'])) {

            if ($PHPShopCache->checkBot())
                $_SESSION['showcase_region'] = true;
            else
                $_SESSION['showcase_region'] = showcaseelement_suggestIP();
        }
        
    }

    if ($rout == 'MIDDLE') {
        if (!empty($row['selector_name'])) {

            $obj->set('ShowcaseName', $row['selector_name']);
            $obj->set('ShowcasePath', parse_url($_SERVER['REQUEST_URI'])['path'].'?from=showcase');

            if ($row['selector_name'] == $_SESSION['showcase_region'])
                $obj->set('ShowcaseRegionUrl', $row['host']);
        }
    }

    if ($rout == 'END') {

        if (!empty($_SESSION['showcase_region']) and $_SESSION['showcase_region'] != 1 and !empty($obj->get('ShowcaseRegionUrl'))) {
            $obj->set('ShowcaseRegionName', $_SESSION['showcase_region']);
            $obj->set('ShowcaseRegionCheck', null);
        } elseif (empty($_SESSION['showcase_region']) or $_SESSION['showcase_region'] == 1 or empty($obj->get('ShowcaseRegionUrl')))
            $obj->set('ShowcaseRegionCheck', 'hidden d-none');

        if ($_GET['from'] == 'showcase')
            setcookie("showcaseselect", 1, time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);

        $showcase_menu = $obj->parseTemplate($obj->getValue('templates.showcase_menu'));
        $obj->set('visualcart_lib', $showcase_menu . '<script type="text/javascript" src="phpshop/modules/showcaseselect/js/showcaseselect.js"></script>', true);
    }
}

$addHandler = array
    (
    'index' => 'showcaseelement_hook',
);
