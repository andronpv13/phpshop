<?php

/**
 * Кеширование значений фильтра, под которые нет товаров.
 * Быстрая версия без учета дополнительных каталогов
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopCron
 */

// Включение для SSH Cron
$enabled = false;

if (function_exists('set_time_limit'))
    set_time_limit(0);

if (empty($_SERVER['DOCUMENT_ROOT'])){
    $_classPath = realpath(dirname(__FILE__)) . "/../../../";
    $enabled = true;
}
else
    $_classPath = "../../../";

include_once($_classPath . "class/obj.class.php");
PHPShopObj::loadClass(["base", "system", "orm"]);
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);

// Авторизация
if ($_GET['s'] == md5($PHPShopBase->SysValue['connect']['host'] . $PHPShopBase->SysValue['connect']['dbase'] . $PHPShopBase->SysValue['connect']['user_db'] . $PHPShopBase->SysValue['connect']['pass_db']))
    $enabled = true;

if (empty($enabled))
    exit("Ошибка авторизации!");

class CacheFilter {

    // Отладка
    private $debug = false;

    public function __construct() {

        // Все каталоги
        $getCategoriesAll = $this->getCategories();

        // Каталоги с товарами
        $getCategoriesWithProducts = (new PHPShopOrm('phpshop_products'))->getList(['DISTINCT category'], ['enabled' => "='1'", 'parent_enabled' => "='0'"], false, ['limit' => '10000']);

        // Переводим массив в ключи
        if (is_array($getCategoriesWithProducts))
            foreach ($getCategoriesWithProducts as $cat) {
                $getCategoriesWithProductsCheck[$cat['category']] = $cat['category'];
            }

        // Переводим массив в ключи
        if (is_array($getCategoriesAll))
            foreach ($getCategoriesAll as $cat) {
                $getCategoriesAllCheck[$cat['id']] = $cat;
                $getCategoriesAllCheck[$cat['id']]['sort'] = unserialize($cat['sort']);
            }

        unset($getCategoriesWithProducts);
        unset($getCategoriesAll);

        if (is_array($getCategoriesAllCheck))
            foreach ($getCategoriesAllCheck as $key => $cat) {

                // Есть товары
                if (isset($getCategoriesWithProductsCheck[$cat['id']])) {
                    $getCategoriesWithProducts[$key] = $cat;

                    // Убираем из массива всех категорий
                    unset($getCategoriesAll[$cat['id']]);
                }
            }

        $this->getCategoriesAll = $getCategoriesAllCheck;
        $this->createCache($getCategoriesWithProducts);

        // Обход конечных каталогов с товарами
        if (is_array($this->cache)) {
            foreach ($this->cache as $cat => $cache) {

                // Запись в БД
                $this->writeCache($cat, $cache);

                // Обход родительских каталогов
                $this->setParentCache($cache['parent_to'], $cache);
            }
        }

        //print_r($this->cache);
        echo "Кеш обновлен у " . count($this->getCategoriesAll) . " категорий";
    }

    private function setParentCache($cat, $cache) {

        if (is_array($this->getCategoriesAll[$cat])) {

            if (is_array($cache['filter_cache']))
                foreach ($cache['filter_cache'] as $key => $val) {
                    foreach ($val as $k => $v) {
                        if (empty($this->cache[$cat]['filter_cache'][$key][$k]))
                            $this->cache[$cat]['filter_cache'][$key][$k] = $v;
                    }
                }


            // Количество
            if (is_array($cache['products']))
                foreach ($cache['products'] as $key => $val) {
                    foreach ($val as $k => $v) {

                        if (!empty($v)) {

                            if ($this->debug)
                                echo 'Каталог ' . $cat . ' нашел ' . $key . '-' . $k . ' было ' . (int) $this->cache[$cat]['products'][$key][$k] . ' добавил ' . $v . ' стало ' . ((int) $this->cache[$cat]['products'][$key][$k] + $v) . '<br>';

                            $this->cache[$cat]['products'][$key][$k] += $v;
                        }
                    }
                }

            if (is_array($this->cache[$cat]['products']))
                foreach ($this->cache[$cat]['products'] as $key => $val) {
                    foreach ($val as $k => $v) {
                        if (!empty($v))
                            unset($this->cache[$cat]['filter_cache'][$key][$k]);
                    }
                }

            // Запись в БД
            $this->writeCache($cat, $this->cache[$cat]);

            $this->setParentCache($this->getCategoriesAll[$cat]['parent_to'], $cache);
        }
    }

    private function getCategories() {
        $orm = new PHPShopOrm('phpshop_categories');

        return $orm->getList(['id', 'sort', 'parent_to', 'name'], ['skin_enabled' => "!='1'"]);
    }

    private function createCache($categories) {
        foreach ($categories as $category) {
            $sorts = $category['sort'];
            $total = 0;

            if (is_array($sorts) && count($sorts) > 0) {
                $cache = [];
                foreach ($sorts as $sort) {
                    $values = $this->getSortValues((int) $sort);

                    if (is_array($values)) {
                        foreach ($values as $value) {
                            $count = $this->countProducts((int) $value, (int) $sort, (int) $category['id']);
                            $total += $count;

                            if ($count === 0) {
                                $cache['filter_cache'][(int) $sort][$value] = (int) $value;
                            }

                            $cache['products'][(int) $sort][(int) $value] = $count;
                        }

                        $cache['products'][(int) $sort][(int) $value] = $count;
                    }
                }

                $this->cache[$category['id']] = $cache;
                $this->cache[$category['id']]['parent_to'] = $category['parent_to'];
                $this->cache[$category['id']]['name'] = $category['name'];
            }
        }
    }

    private function writeCache($cat, $cache) {
        $orm = new PHPShopOrm('phpshop_categories');
        $orm->debug = false;
        $orm->update(['sort_cache_new' => serialize($cache), 'sort_cache_created_at_new' => time()], ['id=' => $cat]);
    }

    private function getSortValues($sortId) {

        // Фильтр?
        $filtr = (new PHPShopOrm('phpshop_sort_categories'))->getOne(['filtr'], ['id' => sprintf('="%s"', $sortId)])['filtr'];

        if (!empty($filtr)) {
            $orm = new PHPShopOrm('phpshop_sort');

            return array_column($orm->getList(['id'], ['category' => sprintf('="%s"', $sortId)]), 'id', 'id');
        }
    }

    private function countProducts($valueId, $sortId, $categoryId) {
        $orm = new PHPShopOrm();
        $orm->debug = false;
        $result = $orm->query(sprintf('select COUNT("id") as count from `phpshop_products` where `category`="%s" and vendor REGEXP "i%s-%si" and enabled="1" and parent_enabled="0"', $categoryId, $sortId, $valueId));

        $row = mysqli_fetch_assoc($result);

        return (int) $row['count'];
    }

}

new CacheFilter();
?>