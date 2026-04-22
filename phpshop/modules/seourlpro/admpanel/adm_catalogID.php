<?php

// Настройки модуля
class PHPShopSeourlOption extends PHPShopArray {

    function __construct() {
        $this->objType = 3;
        $this->checkKey = true;

        // Память настроек
        $this->memory = __CLASS__;

        $this->objBase = $GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'];
        parent::__construct('redirect_enabled', 'seo_page_enabled', 'html_enabled');
    }

}

// Добавляем значения в функцию actionStart
function addSeoUrlPro($data) {
    global $PHPShopGUI;

    $PHPShopGUI->field_col = 3;
    $PHPShopSeourlOption = new PHPShopSeourlOption();

    // Каталоги товаров
    if (isset($data['cat_seo_name'])) {

        if (empty($data['cat_seo_name'])) {
            PHPShopObj::loadClass("string");
            $data['cat_seo_name'] = PHPShopString::toLatin($data['name']);
            $data['cat_seo_name'] = str_replace(array("_", "+", '&#43;', '"'), array("-", "", "", ""), $data['cat_seo_name']);
        }

        $data['cat_seo_name'] = str_replace(array("+", '&#43;', '"'), array("", "", ""), $data['cat_seo_name']);

        // Добавление /cat/ для сложных ссылок
        $true_link = str_replace('cat/', '', $data['cat_seo_name']);
        if (stristr($true_link, '/')) {
            $data['cat_seo_name'] = 'cat/' . $true_link;
        }

        if ($PHPShopSeourlOption->getParam('html_enabled') == 2)
            $html = null;
        else
            $html = '.html';

        $Tab3 = $PHPShopGUI->setField("Ссылка:", $PHPShopGUI->setInput("text", "cat_seo_name_new", $data['cat_seo_name'], "left", false, false, false, false, '/', $html), 1, 'Можно использовать вложенные ссылки /sony/plazma/televizor');


        if ($PHPShopSeourlOption->getParam('redirect_enabled') == 2)
            $Tab3 .= $PHPShopGUI->setField("Старая ссылка:", $PHPShopGUI->setInput("text", "cat_seo_name_old_new", $data['cat_seo_name_old'], "left", false, false, false), 1, 'Старая ссылка для 301 редиректа');

        $PHPShopGUI->addTab(array("SEO", $Tab3));
    }
    // Каталог страниц
    elseif (isset($data['page_cat_seo_name'])) {

        if (empty($data['page_cat_seo_name']))
            $data['news_seo_name'] = PHPShopString::toLatin($data['name']);

        $Tab3 = $PHPShopGUI->setField("SEO ссылка:", $PHPShopGUI->setInput("text", "page_cat_seo_name_new", $data['page_cat_seo_name'], "left", false, false, false, false, '/', '.html'), 1);

        if ($PHPShopSeourlOption->getParam('redirect_enabled') == 2)
            $Tab3 .= $PHPShopGUI->setField("Старая ссылка:", $PHPShopGUI->setInput("text", "cat_seo_name_old_new", $data['cat_seo_name_old'], "left", false, false, false, false), 1, 'Старая ссылка для 301 редиректа');

        if ($PHPShopSeourlOption->getParam('seo_page_enabled') == 2)
            $PHPShopGUI->addTab(array("SEO", $Tab3, 450));
    }
}

// Замена числа в конце ссылки
function checkSeoUrlPro($a) {
    $r = 'abcdefghijklmnopqrstuvwxyz';
    if (is_numeric(substr($a, -1))) {
        $array = explode("-", $a);
        $error = $array[count($array) - 1];
        if (is_numeric($error)) {
            $array[count($array) - 1] = $r[rand(1, 27)];
        }
        $a = implode("-", $array);
    }
    return $a;
}

// Проверка уникальности сео-ссылки
function checkSeoUrlProName($data) {
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
    $PHPShopOrm->sql = 'select id,name,cat_seo_name from ' . $GLOBALS['SysValue']['base']['categories'] . ' where (name="' . addslashes($data['name_new']) . '" or cat_seo_name="' . $data['cat_seo_name_new'] . '") and id!=' . $_POST['rowID'] . ' limit 1';
    $result = $PHPShopOrm->select();

    if (is_array($result)) {
        if ($result[0]['cat_seo_name'] == $data['cat_seo_name'])
            $update = true;

        if (empty($data['cat_seo_name']) and $result[0]['name'] == $data['name'])
            $update = true;
    }

    if ($update) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
        $result = $PHPShopOrm->select(array('id', 'name'), array('id' => '=' . $data['parent_to_new']), false, array('limit' => 1));

        if (empty($data['cat_seo_name_new']))
            return PHPShopString::toLatin($_POST['name_new']);

        if (!empty($result['name']))
            return PHPShopString::toLatin($result['name']) . '-' . $data['cat_seo_name_new'];
    } else
        return $data['cat_seo_name_new'];
}

function updateSeoUrlPro($data) {

    // Каталоги товаров
    if (isset($data['cat_seo_name_new'])) {
        $data['cat_seo_name_new'] = checkSeoUrlPro($data['cat_seo_name_new']);
        $_POST['cat_seo_name_new'] = checkSeoUrlProName($data);
    }
    // Каталоги товаров
    else if (isset($data['page_cat_seo_name_new'])) {
        $_POST['page_cat_seo_name_new'] = checkSeoUrlPro($data['page_cat_seo_name_new']);
    }
}

$addHandler = array(
    'actionStart' => 'addSeoUrlPro',
    'actionDelete' => false,
    'actionUpdate' => 'updateSeoUrlPro'
);
?>