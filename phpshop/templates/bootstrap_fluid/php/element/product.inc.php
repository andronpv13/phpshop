<?php

/**
 * Элемент вывода спецпредложений в переменную @showcase@ для слайдеров и т.д.
 */
class AddToTemplate extends PHPShopProductElements {

    var $debug = false;

    function __construct() {
        $this->objBase = $GLOBALS['SysValue']['base']['products'];
        parent::__construct();
    }

    function showcase($force = false, $category = null, $cell = null, $limit = null, $line = false) {

        $this->limitspec = $limit;

        if (!empty($cell))
            $this->cell = $cell;

        elseif (empty($this->cell))
            $this->cell = 1;


        switch ($GLOBALS['SysValue']['nav']['nav']) {

            // Раздел списка товаров
            case "CID":

                if (!empty($category))
                    $where['category'] = '=' . $category;

                elseif (PHPShopSecurity::true_num($this->PHPShopNav->getId())) {

                    $category = $this->PHPShopNav->getId();
                    if (!$this->memory_get('product_enabled.' . $category, true))
                        $where['category'] = '=' . $category;
                }
                break;

            // Раздел подробного описания
            case "UID":
                if (empty($force))
                    return false;
                else
                    $where['category'] = '=' . $category;

                $where['id'] = '!=' . $this->PHPShopNav->getId();
                break;
                
            default: return false;
        }

        // Поддержка SeoUrlPro
        if ($GLOBALS['PHPShopNav']->objNav['name'] == 'UID') {
            $where['id'] = '!=' . $GLOBALS['PHPShopNav']->objNav['id'];
        }

        // Кол-во товаров на странице
        if (empty($this->limitspec))
            $this->limitspec = $this->PHPShopSystem->getParam('new_num');

        if (!$this->limitspec)
            $this->limitspec = $this->num_row;

        // Завершение если отключен вывод
        if (empty($this->limitspec))
            return false;

        // Случаные товары для больших баз
        //$where['id']=$this->setramdom($limit);
        // Параметры выборки учета товара в спецах и наличия
        $where['spec'] = "='1'";
        $where['enabled'] = "='1'";
        $where['parent_enabled'] = "='0'";

        // Проверка на единичную выборку
        if ($limit == 1) {
            $array_pop = true;
            $limit++;
        }

        // Память режима выборки новинок из каталогов
        $memory_spec = $this->memory_get('product_spec.' . $category);

        if ($memory_spec != 1 and $memory_spec != 3)
            $this->dataArray = $this->select(array('*'), $where, array('order' => 'RAND()'), array('limit' => $this->limitspec), __FUNCTION__);

        // Проверка на единичную выборку
        if (!empty($array_pop) and is_array($this->dataArray)) {
            array_pop($this->dataArray);
        }

        if (!empty($this->dataArray) and is_array($this->dataArray)) {
            $this->product_grid($this->dataArray, $this->cell, $this->template, $line);
            $this->set('specMainTitle', $this->lang('specprod'));

            // Заносим в память
            $this->memory_set('product_spec.' . $category, 2);
        }


        // Собираем и возвращаем таблицу с товарами
        $this->set('showcase', $this->compile());
    }

}

// Добавляем в шаблон элемент вывода случайного товара 
$AddToTemplate = new AddToTemplate();
$AddToTemplate->showcase();
?>