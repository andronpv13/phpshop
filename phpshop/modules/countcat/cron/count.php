<?php

/**
 * Подсчет количества товаров в каталогах
 * Расширенная версия с учетом дополнительных каталогов
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopCron
 */
// Включение для SSH Cron
$enabled = false;

if (function_exists('set_time_limit'))
    set_time_limit(0);

if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_classPath = realpath(dirname(__FILE__)) . "/../../../";
    $enabled = true;
} else
    $_classPath = "../../../";

include_once($_classPath . "class/obj.class.php");
PHPShopObj::loadClass(["base", "system", "orm"]);
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);

// Авторизация
if ($_GET['s'] == md5($PHPShopBase->SysValue['connect']['host'] . $PHPShopBase->SysValue['connect']['dbase'] . $PHPShopBase->SysValue['connect']['user_db'] . $PHPShopBase->SysValue['connect']['pass_db']))
    $enabled = true;

if (empty($enabled))
    exit("Ошибка авторизации!");

class CountCat {

    // Отладка
    private $debug = false;

    public function __construct() {

        echo "<pre>";

        // Все каталоги
        $getCategoriesAll = $this->getCategories();

        // Каталоги с товарами
        $getCategoriesWithProducts = (new PHPShopOrm('phpshop_products'))->getList(['DISTINCT category'], ['enabled' => "='1'", 'parent_enabled' => "='0'"], false, ['limit' => '10000']);

        // Дополнительные каталоги с товарами
        $getCategoriesDopWithProducts = (new PHPShopOrm('phpshop_products'))->getList(['DISTINCT dop_cat'], ['dop_cat' => "!=''", 'enabled' => "='1'", 'parent_enabled' => "='0'"], false, ['limit' => '10000']);

        // Переводим массив категорий в ключи
        if (is_array($getCategoriesWithProducts))
            foreach ($getCategoriesWithProducts as $cat) {
                $getCategoriesWithProductsCheck[$cat['category']] = $cat['category'];
            }

        // Переводим массив дополнительных категорий в ключи
        if (is_array($getCategoriesDopWithProducts))
            foreach ($getCategoriesDopWithProducts as $cat) {
                $dop_cat_array = explode('#', $cat['dop_cat']);

                if (is_array($dop_cat_array)) {

                    foreach ($dop_cat_array as $dop_cat) {

                        // Убираем дубли
                        foreach ($dop_cat_array as $dop_cat) {
                            $check_dop_cat[$dop_cat] = $dop_cat;
                        }
                    }

                    if (is_array($check_dop_cat))
                        foreach ($check_dop_cat as $dop_cat) {
                            if (!empty($dop_cat))
                                $getCategoriesWithProductsCheck[$dop_cat] = $dop_cat;
                        }

                    unset($check_dop_cat);
                }
            }

        // Переводим массив в ключи
        if (is_array($getCategoriesAll))
            foreach ($getCategoriesAll as $cat) {
                $getCategoriesAllCheck[$cat['id']] = $cat;
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

                // Обход дополнительных каталогов
                if (!empty($cache['dop_cat'])) {
                    $dop_cat_array = explode('#', $cache['dop_cat']);

                    if (is_array($dop_cat_array)) {

                        // Убираем дубли
                        foreach ($dop_cat_array as $dop_cat) {
                            $check_dop_cat[$dop_cat] = $dop_cat;
                        }

                        if (is_array($check_dop_cat))
                            foreach ($check_dop_cat as $dop_cat) {
                                if (!empty($dop_cat))
                                    $this->setParentCache($dop_cat, $cache);
                            }

                        unset($check_dop_cat);
                    }
                }
            }
        }

        echo "Товары подсчитаны у " . count($this->getCategoriesAll) . " категорий";
    }

    private function setParentCache($cat, $cache) {

        if (is_array($this->getCategoriesAll[$cat])) {

            $this->cache[$cat]['total'] += $cache['total'];

            if (empty($this->cache[$cat]['id'])) {
                $this->cache[$cat]['id'] = $cat;
                $this->cache[$cat]['parent_to'] = $this->getCategoriesAll[$cat]['parent_to'];
                $this->cache[$cat]['name'] = $this->getCategoriesAll[$cat]['name'];
            }

            // Запись в БД
            $this->writeCache($cat, $this->cache[$cat]);

            $this->setParentCache($this->getCategoriesAll[$cat]['parent_to'], $cache);
        }
    }

    private function getCategories() {
        $orm = new PHPShopOrm('phpshop_categories');

        return $orm->getList(['id', 'sort', 'parent_to', 'name', 'dop_cat'], ['skin_enabled' => "!='1'"]);
    }

    private function createCache($categories) {

        foreach ($categories as $category) {

            $total = $this->countProducts((int) $category['id']);

            $this->cache[$category['id']]['parent_to'] = $category['parent_to'];
            $this->cache[$category['id']]['name'] = $category['name'];
            $this->cache[$category['id']]['dop_cat'] = $category['dop_cat'];
            $this->cache[$category['id']]['total'] = $total;
        }
    }

    private function writeCache($cat, $cache) {
        $orm = new PHPShopOrm('phpshop_categories');
        $orm->debug = false;
        $orm->update(['count_new' => $cache['total']], ['id=' => $cat]);
    }

    private function countProducts($categoryId) {
        $orm = new PHPShopOrm();
        $orm->debug = false;
        $result = $orm->query(sprintf('select COUNT("id") as count from `phpshop_products` where (`category`="%s" or `dop_cat` REGEXP "#%s#") and enabled="1" and parent_enabled="0"', $categoryId, $categoryId));

        $row = mysqli_fetch_assoc($result);

        return (int) $row['count'];
    }

}

new CountCat();
?>