<?php

function addSeoBrandURL($data) {

    // Добавляем SEO ссылку на бренд
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
    $vendorCategory = $PHPShopOrm->select(array("*"), array("id=" => $data["category_value"]));

    if ($vendorCategory["brand"] == 1 or $vendorCategory['virtual'] == 1) {

        PHPShopObj::loadClass("string");
        $data["sort_seo_name"] = PHPShopString::toLatin($data["name_value"]);
        $_POST["sort_seo_name_value"] = str_replace("_", "-", $data["sort_seo_name"]);
    }
}

$addHandler = array(
    'actionInsert' => 'addSeoBrandURL',
    'actionDelete' => false,
    'actionUpdate' => false
);
?>