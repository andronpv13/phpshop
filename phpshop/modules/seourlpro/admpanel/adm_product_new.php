<?php

// Настройки модуля
class PHPShopSeourlOption extends PHPShopArray {

    function __construct() {
        $this->objType = 3;
        $this->checkKey = true;

        // Память настроек
        $this->memory = __CLASS__;

        $this->objBase = $GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'];
        parent::__construct('redirect_enabled','html_enabled');
    }

}

function addSeoUrl($data) {
    global $PHPShopGUI;

    // Копирование товара
    $data['prod_seo_name'] = null;
    
        $PHPShopSeourlOption = new PHPShopSeourlOption();
    if($PHPShopSeourlOption->getParam('html_enabled') == 2)
        $html=null;
    else $html='.html';

    $Tab3 = $PHPShopGUI->setField("SEO ссылка:", $PHPShopGUI->setInput("text", "prod_seo_name_new", $data['prod_seo_name'], "left", false, false, false, false, '/id/', '-' . $data['id'] . $html), 1, 'Можно использовать вложенные ссылки /sony/plazma/televizor');

    if ($PHPShopSeourlOption->getParam('redirect_enabled') == 2)
        $Tab3 .= $PHPShopGUI->setField("Редирект", $PHPShopGUI->setInput("text", "prod_seo_name_old_new", $data['prod_seo_name_old'], "left", false, false, false, false), 1, 'Старая ссылка для 301 редиректа');

    $PHPShopGUI->addTab(array("SEO", $Tab3, 450));
}

$addHandler = array(
    'actionStart' => 'addSeoUrl',
    'actionDelete' => false,
    'actionUpdate' => false
);
?>