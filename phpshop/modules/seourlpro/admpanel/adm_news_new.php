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

function addSeoUrlPro($data) {
    global $PHPShopGUI;

    $PHPShopSeourlOption = new PHPShopSeourlOption();
    if ($PHPShopSeourlOption->getParam('html_enabled') == 2)
        $html = null;
    else
        $html = '.html';

    $Tab3 = $PHPShopGUI->setField("SEO ссылка:", $PHPShopGUI->setInput("text", "news_seo_name_new", @$data['news_seo_name'], "left", false, false, false, false, '/', $html), 1);

    $PHPShopGUI->addTab(array("SEO", $Tab3, 450));
}

function updateSeoUrlPro($data) {
    if (empty($data['news_seo_name_new']))
        $data['news_seo_name_new'] = PHPShopString::toLatin($data['zag']);
}

$addHandler = array(
    'actionStart' => 'addSeoUrlPro',
    'actionDelete' => false,
    'actionUpdate' => 'updateSeoUrlPro'
);
