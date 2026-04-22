<?php

function promotionsValueEdit($data) {
    global $PHPShopGUI, $PHPShopSystem;

    $order = (new PHPShopOrm($GLOBALS['SysValue']['base']['orders']))->getOne(['*'], ['id' => '=' . (int) $_REQUEST['id']]);

    $cart = unserialize($order['orders']);
    $productID = urldecode($_REQUEST['selectID']);

    if(isset($cart['Cart']['cart'][$productID]) && is_array($cart['Cart']['cart'][$productID])) {
        $product = $cart['Cart']['cart'][$productID];
    }

    foreach ($cart['Cart']['cart'] as $val) {
        if ($val['id'] == $productID) {
            $product = $val;
        }
    }

    if(isset($product['promo_sum']) && !empty($product['promo_sum'])) {
        $PHPShopGUI->_CODE .= $PHPShopGUI->setField('Промоакция', $PHPShopGUI->setInputArg(['name' => 'promo_discount_sum_value', 'type' => 'text', 'value' => $product['promo_sum'], 'size' => 150, 'description' => $PHPShopSystem->getDefaultValutaCode()]));
    }

    if(isset($product['promo_percent']) && !empty($product['promo_percent'])) {
        $PHPShopGUI->_CODE .= $PHPShopGUI->setField('Промоакция', $PHPShopGUI->setInputArg(['name' => 'promo_discount_percent_value', 'type' => 'text', 'value' => $product['promo_percent'], 'size' => 150, 'description' => '%']));
    }
}

function promotionsCartUpdate($data) {

    $productID = PHPShopString::utf8_win1251($_REQUEST['selectID']);

    $system = new PHPShopSystem();
    $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);

    // Изменение товара и есть поле суммы или % скидки
    if($_POST['selectAction'] === 'productUpdate' && (isset($_REQUEST['promo_discount_sum_value']) or isset($_REQUEST['promo_discount_percent_value']))) {
        $newCart = unserialize($data['new']['orders_new']);

        if(isset($newCart['Cart']['cart'][$productID]) && is_array($newCart['Cart']['cart'][$productID])) {
            $product = $newCart['Cart']['cart'][$productID];
            $cartKey = $productID;
        }

        foreach ($newCart['Cart']['cart'] as $key => $val) {
            if ($val['id'] == $productID) {
                $product = $val;
                $cartKey = $key;
            }
        }

        // Сумма
        if (isset($_REQUEST['promo_discount_sum_value']) && (float) $_REQUEST['promo_discount_sum_value'] !== (float) $product['promo_sum']) {
            if((float) $_REQUEST['promo_discount_sum_value'] > 0) {
                if ($product['promo_price'] > (float) $_REQUEST['promo_discount_sum_value']) {
                    $newCart['Cart']['cart'][$cartKey]['price'] = number_format($product['promo_price'] - (float) $_REQUEST['promo_discount_sum_value'] / $product['num'], (int) $system->getSerilizeParam("admoption.price_znak"), '.', '');
                    $newCart['Cart']['cart'][$cartKey]['name'] = str_replace(
                        '['.__('скидка').' ' . $product['promo_sum'] / $product['num'] . ' ' . $system->getDefaultValutaCode() . ']',
                        '['.__('скидка').' ' . (float) $_REQUEST['promo_discount_sum_value'] / $product['num'] . ' ' . $system->getDefaultValutaCode() . ']',
                        $newCart['Cart']['cart'][$cartKey]['name']
                    );
                    $newCart['Cart']['cart'][$cartKey]['promo_sum'] = (float) $_REQUEST['promo_discount_sum_value'];
                }
            } else {
                if ($product['promo_price'] > (float) $_REQUEST['promo_discount_sum_value']) {
                    $newCart['Cart']['cart'][$cartKey]['price'] = $product['promo_price'];
                    $newCart['Cart']['cart'][$cartKey]['name'] = str_replace(
                        '['.__('скидка').' ' . $product['promo_sum'] / $product['num'] . ' ' . $system->getDefaultValutaCode() . ']',
                        '',
                        $newCart['Cart']['cart'][$cartKey]['name']
                    );
                    unset($newCart['Cart']['cart'][$cartKey]['promo_sum']);
                    unset($newCart['Cart']['cart'][$cartKey]['promo_code']);
                }
            }
        }
        // Процент от суммы
        else if (isset($_REQUEST['promo_discount_percent_value']) && (float) $_REQUEST['promo_discount_percent_value'] !== (float) $product['promo_percent']) {
            if((float) $_REQUEST['promo_discount_percent_value'] > 0) {
                $newCart['Cart']['cart'][$cartKey]['price'] = number_format($product['promo_price'] - ($product['promo_price'] * (float) $_REQUEST['promo_discount_percent_value'] / 100), (int) $system->getSerilizeParam("admoption.price_znak"), '.', '');
                $newCart['Cart']['cart'][$cartKey]['name'] = str_replace(
                    ' ['.__('скидка').' ' . $product['promo_percent'] . '%]',
                    ' ['.__('скидка').' ' . (float) $_REQUEST['promo_discount_percent_value'] . '%]',
                    $newCart['Cart']['cart'][$cartKey]['name']
                );
                $newCart['Cart']['cart'][$cartKey]['promo_percent'] = (float) $_REQUEST['promo_discount_percent_value'];
            } else {
                $newCart['Cart']['cart'][$cartKey]['price'] = $product['promo_price'];
                $newCart['Cart']['cart'][$cartKey]['name'] = str_replace(
                    ' ['.__('скидка').' ' . $product['promo_percent'] . '%]',
                    '',
                    $newCart['Cart']['cart'][$cartKey]['name']
                );
                unset($newCart['Cart']['cart'][$cartKey]['promo_percent']);
                unset($newCart['Cart']['cart'][$cartKey]['promo_code']);
            }
        }

        $PHPShopOrder = new PHPShopOrderFunction(false, $newCart['Cart']['cart']);

        // Библиотека корзины
        $PHPShopCart = new PHPShopCart($newCart['Cart']['cart']);

        // Перерасчет скидки и промоакций
        $sum = $sum_promo = 0;
        if (is_array($PHPShopCart->_CART))
            foreach ($PHPShopCart->_CART as $val) {

                // Сумма товаров с акциями
                if (!empty($val['promo_price'])) {
                    $sum_promo += $val['num'] * $val['price'];
                }
                // Сумма товаров без акций
                else
                    $sum += $val['num'] * $val['price'];
            }

        // Итого товары по акции
        $newCart['Cart']['sum'] = $PHPShopOrder->returnSumma($sum_promo);

        // Итого товары без акции
        $newCart['Cart']['sum'] += $PHPShopOrder->returnSumma($sum, $newCart['Person']['discount']);

        // Сериализация данных заказа
        $update['orders_new'] = serialize($newCart);
        $update['sum_new'] = $newCart['Cart']['sum'] + $newCart['Cart']['dostavka'];

        $orm->update($update, ['id' => '=' . (int) $_REQUEST['id']]);
    }
}

$addHandler = [
    'actionValueEdit'  => 'promotionsValueEdit',
    'actionCartUpdate' => 'promotionsCartUpdate'
];
?>