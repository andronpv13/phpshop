<?php

/**
 * Вывод иконок распродажи и спецпредложений в кратком описании товаров.
 */

/**
 * Оптовые акции
 */
function elements_product_grid_wholesale_hook($obj, $row) {

    if (empty($GLOBALS['PHPShopWholesale'])) {
        include_once $GLOBALS['SysValue']['class']['wholesale'];
        $GLOBALS['PHPShopWholesale'] = new PHPShopWholesale();
    }
    
    $opt = $GLOBALS['PHPShopWholesale']->getOpt($row);

    // Есть акция
    if (is_array($opt)) {

        // Описание акции
        $obj->set('wholesaleInfo', $opt['description']);
        if (!empty($opt['label'])) {
            $obj->set('wholesaleLabel', $opt['label']);
            $obj->set('wholesaleIcon', PHPShopParser::file($GLOBALS['SysValue']['templates']['wholesale']['icon'], true, false, true));
        } else
            $obj->set('wholesaleIcon', null);

        // Проверка кол-ва товара
        if ($_SESSION['cart'][$row['id']]['num'] >= $row['wholesale_check']) {

            // Колонка
            if ($opt['tip'] == 1) {

                $column_opt = $row['wholesale_price'];

                if ($column_opt > 1) {

                    $price = PHPShopProductFunction::GetPriceValuta($row['id'], $row['price' . (int) $column_opt], $row["baseinputvaluta"], true, true);

                    $price_n = PHPShopProductFunction::GetPriceValuta($row['id'], $row['price'], $row["baseinputvaluta"], true, false);
                }
            }
            // Скидка
            else {

                $discount = $row['wholesale_discount'];

                if (!empty($discount)) {

                    $price = PHPShopProductFunction::GetPriceValuta($row['id'], $row['price'], $row["baseinputvaluta"], true, true);
                    $price = $price - ($price * intval($discount) / 100);

                    $price_n = PHPShopProductFunction::GetPriceValuta($row['id'], $row['price'], $row["baseinputvaluta"], true, false);
                }
            }

            if (!empty($price)) {
                $obj->set('productPrice', $price);
                $obj->set('productPriceOld', $price_n);
            }
        }
    }
}

/**
 * Добавление в список каталогов спецпредложения товаров в 3 ячейки, лимит 3
 */
$addHandler = array
    (
    'product_grid' => 'elements_product_grid_wholesale_hook',
);
?>