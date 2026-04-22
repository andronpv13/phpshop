<?php

/**
 * Оптовые акции
 */
function getPrice_wholesale_hook($obj, $row) {



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
                return $price;
            }
        }
    }
}

$addHandler = array
    (
    'getPrice' => 'getPrice_wholesale_hook',
);
?>