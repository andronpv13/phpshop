<?php

if (!defined("OBJENABLED"))
    exit(header('Location: /?error=OBJENABLED'));

// Заглушка на 404 ошибку при пустом пути для SEO Pro
if (empty($PHPShopNav)) {
    PHPShopObj::loadClass('nav');
    $PHPShopNav = new PHPShopNav();
}

if ($PHPShopNav->notPath(array('page')) and ! strpos($PHPShopNav->getName(true), '/', 1))
    $SysValue['nav']['path'] = 'index';

class PHPShopCategorySeoProArray extends PHPShopArray {

    /**
     * Конструктор
     * @param string $sql SQL условие выборки
     */
    function __construct($sql = false) {
        $this->objSQL = $sql;
        $this->cache = false;
        $this->order = array('order' => 'num');
        $this->objBase = $GLOBALS['SysValue']['base']['categories'];
        parent::__construct("id", "name", "cat_seo_name");
    }

}

class PHPShopPageCategorySeoProArray extends PHPShopArray {

    /**
     * Конструктор
     * @param string $sql SQL условие выборки
     */
    function __construct($sql = false) {
        $this->objSQL = $sql;
        $this->cache = false;
        $this->order = array('order' => 'num');
        $this->objBase = $GLOBALS['SysValue']['base']['page_categories'];
        parent::__construct("id", "name", "page_cat_seo_name");
    }

}

class PHPShopSeoPro {

    var $cat_pre = 'shop/CID_';
    var $prod_pre = 'shop/UID_';
    var $prod_pre_target = 'id/';
    var $cat_page_pre = 'page/CID_';
    var $true_dir = false;
    var $settings;
    var $add_html;

    function __construct() {

        if (!empty($GLOBALS['modules']['seourlpro']['map']))
            $this->memory = $GLOBALS['modules']['seourlpro']['map'];

        if (!empty($GLOBALS['modules']['seourlpro']['map_prod']))
            $this->memory_prod = $GLOBALS['modules']['seourlpro']['map_prod'];

        $url = explode("?", $_SERVER["REQUEST_URI"]);
        $this->url = pathinfo($url[0]);
    }

    /*
     *  Расчет пагинации и ссылки
     */

    function getNav() {
        $url = $this->url;

        // Поддержка / в ссылках, пример sony/audio-video-foto-super.html => /cat/sony/audio-video-foto-super.html
        if ($this->true_dir) {
            if (strpos($GLOBALS['PHPShopNav']->getName(true), '/', 2)) {
                $GLOBALS['SysValue']['nav']['path'] = 'index';
                $url['filename'] = substr($url['dirname'], 1, strlen($url['dirname']) - 1) . '/' . $url['filename'];
            }
        }

        // Поддержка виртуальных каталогов /filters/
        if (strpos($GLOBALS['SysValue']['nav']['truepath'], '/filters/') !== false) {
            $url['filename'] = preg_replace('#^/(.*).html/filters/.*$#', '$1', $GLOBALS['SysValue']['nav']['truepath']);
        }

        $array_seo_name = explode('-', $url['filename']);
        $page = $array_seo_name[count($array_seo_name) - 1];

        if (!is_numeric($page))
            $page = null;

        if ($page >= 1)
            $file = substr($url['filename'], 0, strlen($url['filename']) - strlen($page) - 1);
        else
            $file = $url['filename'];

        return array('page' => $page, 'file' => $file, 'name' => $url['filename']);
    }

    /**
     * Получение реального ID каталога 
     */
    function getCID() {
        $getNav = $this->getNav();

        if (is_array($this->memory))
            $array_true = array_flip($this->memory);

        if (!empty($array_true[$getNav['file']]))
            return str_replace($this->cat_pre, '', $array_true[$getNav['file']]);
    }

    /**
     * Получение реального ID товара
     */
    function getID() {
        $getNav = $this->getNav();
        return $getNav['page'];
    }

    function setRout($mode = 1) {

        $getNav = $this->getNav();
        $file = $getNav['file'];
        $page = $getNav['page'];
        $true_id = null;

        $GLOBALS['PHPShopNav']->objNav['page'] = intval($page);
        $GLOBALS['PHPShopNav']->objNav['path'] = 'shop';

        if ($mode == 1) {

            $array_true = array_flip($this->memory);
            $GLOBALS['PHPShopNav']->objNav['name'] = 'CID';

            if (!empty($array_true[$file]))
                $true_id = str_replace($this->cat_pre, '', $array_true[$file]);
        } elseif ($mode == 2) {

            $GLOBALS['PHPShopNav']->objNav['name'] = 'UID';
            $true_id = $page;
        }

        $GLOBALS['PHPShopNav']->objNav['id'] = intval($true_id);
        $GLOBALS['SysValue']['nav']['id'] = intval($true_id);
    }

    function setMemory($id, $name, $mode = 1, $latin = true) {
        if ($mode == 1) {

            $this->memory[$this->cat_pre . $id . $this->add_html] = $this->setLatin($name, $latin);

            if (strstr($name, '/')) {
                $this->memory['./CID_' . $id . '_1' . $this->add_html] = '/' . $this->setLatin($name . '-1', $latin);
            } else
                $this->memory['CID_' . $id . '_1' . $this->add_html] = $this->setLatin($name . '-1', $latin);
        } elseif ($mode == 2)
            $this->memory_prod[$this->prod_pre . $id . $this->add_html] = $this->prod_pre_target . $this->setLatin($name, $latin) . '-' . $id;
        elseif ($mode == 3)
            $this->memory[$this->cat_page_pre . $id . $this->add_html] = 'page/' . $this->setLatin($name, $latin);
    }

    function setLatin($str, $enabled = true) {
        if ($enabled) {
            $str = PHPShopString::toLatin(trim(strip_tags($str)));
            $str = str_replace("_", "-", $str);
            //$str = str_replace("/", "-", $str);
        }

        return $str;
    }

    function catArrayToMemory() {

        $PHPShopCategoryArray = new PHPShopCategorySeoProArray();
        $getArray = $PHPShopCategoryArray->getArray();
        if (is_array($getArray))
            foreach ($getArray as $key => $val) {

                if (!empty($val['cat_seo_name'])) {
                    $this->setMemory($key, $val['cat_seo_name'], 1, false);
                    $this->memory['CID_' . $key . '_1' . $this->add_html] = $this->setLatin($val['cat_seo_name'] . '-1');
                } else {
                    $this->setMemory($key, $val['name']);
                    $this->memory['CID_' . $key . '_1' . $this->add_html] = $this->setLatin($val['name'] . '-1');
                }
            }
    }

    function catPageArrayToMemory() {

        $PHPShopPageCategoryArray = new PHPShopPageCategorySeoProArray();

        foreach ($PHPShopPageCategoryArray->getArray() as $key => $val) {

            if (!empty($val['page_cat_seo_name'])) {
                $this->setMemory($key, $val['page_cat_seo_name'], 3, false);
            } else {
                $this->setMemory($key, $val['name'], 3);
            }
        }
    }

    function catCacheToMemory() {

        if (is_array($GLOBALS['Cache'][$GLOBALS['SysValue']['base']['categories']]))
            foreach ($GLOBALS['Cache'][$GLOBALS['SysValue']['base']['categories']] as $key => $val) {
                $this->setMemory($key, $val['name']);
                $this->memory['CID_' . $key . '_1' . $this->add_html] = $this->setLatin($val['name'] . '-1');
            }
    }

    function stro_replace($search, $replace, $subject) {
        return strtr($subject, array_combine($search, $replace));
    }

    function AjaxCompile($result) {

        // Товары
        if (is_array($this->memory_prod)) {
            $array_str_prod = array_values($this->memory_prod);
            $array_id_prod = array_keys($this->memory_prod);
            $result = $this->stro_replace($array_id_prod, $array_str_prod, $result);
        }

        // Убираем окончание .html
        if ($GLOBALS['PHPShopSeoPro']->getSettings()['html_enabled'] == 2){
            $result = preg_replace(['/\.html"/i'],['"'],$result);
        }

        return $result;
    }

    function Compile($obj) {
        global $PHPShopModules;

        $this->catArrayToMemory();

        // Обработка массива памяти категорий
        // $this->catCacheToMemory();
        //$this->catArrayToMemory();
        //print_r($this->memory);
        //print_r($this->memory_prod);
        // Каталоги
        if (is_array($this->memory)) {
            $array_str = array_values($this->memory);
            $array_id = array_keys($this->memory);
        }

        // Товары
        if (is_array($this->memory_prod)) {
            $array_str_prod = array_values($this->memory_prod);
            $array_id_prod = array_keys($this->memory_prod);
        }

        // Убираем окончание .html
        if ($GLOBALS['PHPShopSeoPro']->getSettings()['html_enabled'] == 2)
            $replace = ['/\.html"/i' => '"', '/\.html\?/' => '?'];
        else
            $replace = false;

        ob_start();
        ob_implicit_flush(0);
        ParseTemplate($obj->getValue($obj->template), $replace);
        $result = ob_get_clean();

        if (is_array($this->memory))
            $result = $this->stro_replace($array_id, $array_str, $result);

        if (is_array($this->memory_prod))
            $result = $this->stro_replace($array_id_prod, $array_str_prod, $result);

        echo $result;
    }

    public function getSettings() {
        if (is_array($this->settings)) {
            return $this->settings;
        }

        include_once(dirname(__DIR__) . '/hook/mod_option.hook.php');

        $PHPShopSeourlOption = new PHPShopSeourlOption();
        $this->settings = $PHPShopSeourlOption->getArray();

        return $this->settings;
    }

}

if (empty($GLOBALS['PHPShopSeoPro'])) {
    $GLOBALS['PHPShopSeoPro'] = new PHPShopSeoPro();
}
?>