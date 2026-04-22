<?php

// Настройки модуля
class PHPShopSeourlOption extends PHPShopArray {

    function __construct() {
        $this->objType = 3;
        $this->checkKey = true;

        // Память настроек
        $this->memory = __CLASS__;

        $this->objBase = $GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'];
        parent::__construct('html_enabled');
    }

}

function addSeoBrandURL($data) {
    global $PHPShopGUI;

    // Добавляем SEO ссылку на бренд
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
    $vendorCategory = $PHPShopOrm->select(array("*"), array("id=" => $data["category"]));

    if ($vendorCategory["brand"] == 1) {
        if (empty($data["sort_seo_name"])) {
            PHPShopObj::loadClass("string");
            $data["sort_seo_name"] = PHPShopString::toLatin($data["name"]);
            $data["sort_seo_name"] = str_replace("_", "-", $data["sort_seo_name"]);
        }

        $PHPShopSeourlOption = new PHPShopSeourlOption();
        if ($PHPShopSeourlOption->getParam('html_enabled') == 2)
            $html = null;
        else
            $html = '.html';

        $Tab = $PHPShopGUI->setField("Ссылка:", $PHPShopGUI->setInput("text", "sort_seo_name_value", $data['sort_seo_name'], "left", false, false, "form-control", false, '/brand/', $html), 1);


        $PHPShopGUI->tab_key = 105;
        $PHPShopGUI->addTab(['SEO', $Tab, true]);
    }
    elseif ($vendorCategory['virtual'] == 1) {
        if (empty($data["sort_seo_name"])) {
            PHPShopObj::loadClass("string");
            $data["sort_seo_name"] = PHPShopString::toLatin($data["name"]);
            $data["sort_seo_name"] = str_replace("_", "-", $data["sort_seo_name"]);
        }
        $Tab = $PHPShopGUI->setField("Ссылка:", $PHPShopGUI->setInput("text", "sort_seo_name_value", $data['sort_seo_name'], "left", false, false, "form-control", false, false, '.html'), 1);


        $PHPShopGUI->tab_key = 105;
        $PHPShopGUI->addTab(['SEO', $Tab, true]);
    }
}

$addHandler = array(
    'actionValueEdit' => 'addSeoBrandURL',
    'actionDelete' => false,
    'actionUpdate' => false
);
?>