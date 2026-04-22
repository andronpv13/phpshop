<?php

/**
 * Промоакции
 * @package PHPShopAjaxElements
 */
session_start();
$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("product");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("string");
PHPShopObj::loadClass("cart");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("user");
PHPShopObj::loadClass("bonus");

$_REQUEST['promocode'] = PHPShopString::utf8_win1251($_REQUEST['promocode']);
$_REQUEST['sum'] = PHPShopString::utf8_win1251($_REQUEST['sum']);
$_REQUEST['ssum'] = PHPShopString::utf8_win1251($_REQUEST['ssum']);
$_REQUEST['tipoplcheck'] = PHPShopString::utf8_win1251($_REQUEST['tipoplcheck']);
$_REQUEST['wsum'] = PHPShopString::utf8_win1251($_REQUEST['wsum']);

// Подключаем библиотеку доставки
require_once $_classPath . "core/order.core/delivery.php";

// Функции для заказа
$PHPShopOrder = new PHPShopOrderFunction();

// Модули
$PHPShopModules = new PHPShopModules($_classPath . "modules/");

// Системные настройки
$PHPShopSystem = new PHPShopSystem();

// Дополнительные функции из promotion/inc
require_once($_classPath . 'modules/promotions/inc/promotionselement.inc.php');

$currency = $PHPShopSystem->getDefaultValutaCode();

// Генератор кодов 10 знаков
if (trim(mb_strlen($_REQUEST['promocode']) == 10)) {
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.promotions.promotions_codes"));
    $PHPShopOrm->debug = false;
    $where['code'] = '="' . PHPShopSecurity::TotalClean(trim($_REQUEST['promocode'])) . '"';
    $where['enabled'] = '="1"';
    $data_code = $PHPShopOrm->select(array('*'), $where, array('order' => 'id'));
    $PHPShopOrm->clean();
    unset($where);
}
if (!empty($data_code['promo_id'])) {

    $_SESSION['multicodes'] = 1; // Если используем таблицу кодов

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.promotions.promotions_forms"));
    $PHPShopOrm->debug = false;
    $where['id'] = '="' . $data_code['promo_id'] . '"';
    $where['enabled'] = '="1"';
    $data = $PHPShopOrm->select(array('*'), $where, array('order' => 'id'));

    $PHPShopOrm->clean();
    unset($where);
}
// Обычный промокод
else {
    $_SESSION['multicodes'] = 0;
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.promotions.promotions_forms"));
    $PHPShopOrm->debug = false;
    $where['code'] = '="' . PHPShopSecurity::TotalClean(trim($_REQUEST['promocode'])) . '"';
    $where['enabled'] = '="1"';
    $data = $PHPShopOrm->select(array('*'), $where, array('order' => 'id'));
    $PHPShopOrm->clean();
    unset($where);
}

if (!empty($data_code['code']))
    $data['code'] = $data_code['code'];


//Если промо код уникальный
if ($_REQUEST['promocode'] != '*') {

    // Если код сходится
    if ($data['code'] != ''):
        
        //есть ли скидка
        if ($data['discount_check'] == 1):
            $data['products'] = getProductsInPromo($data['products']);
        
            //Проверяем схождения типа оплаты
            if ($_REQUEST['tipoplcheck'] != $data['delivery_method'] and $data['delivery_method_check'] == 1) {

                //узнаем тип оплаты
                $sq_pay = 'select name from ' . $SysValue['base']['payment_systems'] . ' where id=' . $data['delivery_method'];
                $qu_pay = mysqli_query($link_db, $sq_pay);
                $ro_pay = mysqli_fetch_array($qu_pay);

                $messageinfo = '<b style="color:#7e7a13;">Не подходит тип оплаты!</b><br> Для данного промо-кода тип оплаты может быть только <b>' . $ro_pay['name'] . '</b>. Выберите этот тип оплаты и нажмите снова кнопку ОК для применения скидки';
                $action = '1'; //выполним перенаправление на список оплат
                $status = '0'; //не применена скидка
            } else {

                //Проверим активность по дате
                $date_act = promotion_check_activity($data['active_check'], $data['active_date_ot'], $data['active_date_do']);
                $user_act = promotion_check_userstatus($data['status_check'], unserialize($data['statuses']));

                if ($date_act == 1 && $user_act) {

                    //Массив категорий для промо кода
                    if ($data['categories_check'] == 1):
                        //категории массив
                        $category_ar = explode(',', $data['categories']);
                    endif;

                    if ($data['products_check'] == 1):
                        //категории массив
                        $products_ar = explode(',', $data['products']);
                    endif;
                    
                    
                    // Перевод скидки суммы в процент
                    if($data['discount_tip'] == 0){
                        
                       // Сумма товаров
                       foreach ($_SESSION['cart'] as $valuecart) {
                           $sum += $valuecart['num'] * $valuecart['price'];
                       }
                       
                       if($sum < 0)
                           $sum=0;
                       
                       $data['discount']=round($data['discount']*100/$sum,2);
                       
                       if($data['discount'] > 100)
                           $data['discount']=100;
                       
                       if($data['discount'] > 0)
                           $data['discount_tip']=1;
                    }
                    
                    foreach ($_SESSION['cart'] as $rs => $valuecart) {

                        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
                        $row = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($valuecart['id'])), array('order' => 'id desc'), array('limit' => 1));

                        $sumche = $sumchep = 0;

                        // Не нулевая цена или выключен режим проверки нулевой цены
                        if (empty($row['price_n']) or empty($data['block_old_price'])) {

                            //узнаем по каким категориям брать товары из корзины
                            if (isset($category_ar)) {
                                foreach ($category_ar as $val_c) {
                                    if ($val_c == $row['category']) {
                                        $sumche = 1;
                                        $info_prod_d_f .= $row['name'] . ', ';
                                        break;
                                    } else {
                                        $sumche = 0;
                                    }
                                }
                            }

                            //узнаем по каким товарам брать товары из корзины
                            if (isset($products_ar)) {
                                foreach ($products_ar as $val_p) {
                                    if ($val_p == $row['id']) {
                                        $sumchep = 1;
                                        $info_prod_d_f .= $row['name'] . ', ';
                                        break;
                                    } else {
                                        $sumchep = 0;
                                    }
                                }
                            }
                        }

                        if (($sumche == 1 or $sumchep == 1)):
                            $sumnew += $valuecart['price'] * $valuecart['num'];
                        
                          
                            //если процент
                            if ($data['discount_tip'] == 1) {
                                // скидка и тип
                                $discount = $data['discount'];
                                $tip_disc = '%';
                                $idgg = intval($valuecart['id']);
                                if ($idgg >= 1) {
                                    // скидку в сессию (кол-во | тип)
                                    $_SESSION['cart'][$rs]['discount'] = $discount;
                                    $_SESSION['cart'][$rs]['discount_tip'] = $tip_disc;
                                    $_SESSION['cart'][$rs]['test'] = 1;
                                    $_SESSION['cart'][$rs]['promo_price'] = $valuecart['price'];
                                }
                            } 
                            // Больше не используется, все переводится в процент на все товары
                            else { //если сумма
                                //скидка и тип
                                $discount_sum = $data['discount'];
                                $tip_disc = $currency;
                                $idgg = intval($valuecart['id']);
                                if ($idgg >= 1) {

                                    // скидку в сессию (кол-во | тип)
                                    $_SESSION['cart'][$rs]['promo_sum'] = $discount_sum;
                                    $_SESSION['cart'][$rs]['discount_tip_sum'] = $tip_disc;
                                    $_SESSION['cart'][$rs]['promo_code'] = $_REQUEST['promocode'];
                                    $_SESSION['cart'][$rs]['test'] = 2;
                                    $_SESSION['cart'][$rs]['promo_price'] = $valuecart['price'];
                                }
                            }
                        else:
                            $sumoldi += $valuecart['price'] * $valuecart['num'];
                        endif;
                    }
                    //информация о товарах к которым применена скидка
                    if ($info_prod_d_f != '' and $GLOBALS['PHPShopBase']->codBase != 'utf-8') {
                        $info_prod_d = '<hr><b>Скидка применена для товаров:</b> ' . substr($info_prod_d_f, 0, strlen($info_prod_d_f) - 2);
                    }

                    //если процент
                    if ($data['discount_tip'] == 1) {
                        //считаем скидку
                        $discount = $data['discount'] / 100;
                        //сумма на которую производим скидку
                        $sumtot_new = $sumnew - ($sumnew * $discount);
                        //сумма без скидки
                        $sumtot_old = $sumoldi;
                        //тип скидки
                        $tip_disc = '%';
                        //информация в корзину
                        $discountAll = $data['discount'] . ' ' . $tip_disc;
                        //скидку в сессию
                        $_SESSION['discpromo'] = $data['discount'];
                        $_SESSION['tip_disc'] = 1;
                    } else { //если сумма
                    
                        //сумма скидки
                        $discount_sum = $data['discount'];
                        
                        //сумма на которую производим скидку
                        $sumtot_new = $sumnew - $discount_sum;
                        
                        //если вдруг сумма ушла в минус, то ставим нуль
                        if ($sumtot_new < 0) {
                            $sumtot_new = 0;
                        }
                        //сумма без скидки
                        $sumtot_old = $sumoldi;
                        //тип скидки
                        $tip_disc = $currency;
                        //информация в корзину
                        $discountAll = $data['discount'] . ' ' . $tip_disc;
                        $_SESSION['discpromo'] = $data['discount'];
                        $_SESSION['tip_disc'] = 0;
                    }

                    
                    //если не применена скидка
                    if (isset($sumtot_new)):
     
                        //общая сумма
                        $totalsumma_t = $sumtot_new + $sumtot_old;
                        //проверяем сумму от
                        if ($data['sum_order_check'] == 1):
                            if ($totalsumma_t >= $data['sum_order']) {
                                $sumordercheck = 1;
                            } else {
                                $sumordercheck = 0;
                            }
                        else:
                            $sumordercheck = 1; //ставим активной сумму от если галочка в настройках не установлена
                        endif;

                        //проверяем бесплатную доставку
                        if ($data['free_delivery'] == 1):
                            $freedelivery = 0;
                            $_SESSION['freedelivery'] = 0;
                            $delivery = 0;
                        else:
                            //галочка убрана для бесплатной доставки
                            $_SESSION['freedelivery'] = 1;
                            if ($_REQUEST['dostavka'] > 0)
                                $delivery = intval($_REQUEST['dostavka']);
                        endif;

                        if ($sumordercheck == 1):
                            $status = '1'; //скидка применена
                            //система оплаты
                            if ($data['delivery_method_check'] == 1) {
                                $delivery_method_check = $data['delivery_method'];

                                //$totalsummainput = $sumtot_new + $sumtot_old;
                            } else {
                                $delivery_method_check = 0;
                            }
                            $totalsumma = $sumtot_new + $sumtot_old;

                            if ($_REQUEST['sum'] > $totalsumma) {

                                $totalsummainput = $sumtot_new + $sumtot_old;
                                $_SESSION['totalsumma'] = $totalsumma;
                                $_SESSION['promocode'] = $data['code'];
                                $_SESSION['codetip'] = $data['code_tip'];


                                //обнулим если код верный
                                foreach ($_SESSION['cart'] as $is => $valcar) {

                                    // запомним для учета в done.hook.php
                                    $_SESSION['cart'][$is]['promo_percent'] = $valcar['discount'];
                                    $_SESSION['cart'][$is]['promo_code'] = $_REQUEST['promocode'];

                                    //сбросим инфу о скидка
                                    unset($_SESSION['cart'][$is]['discount']);
                                    unset($_SESSION['cart'][$is]['discount_tip']);
                                    unset($_SESSION['cart'][$is]['id_sys']);
                                }
                                $messageinfo = '<b>Поздравляем с приобретением!</b><br> Промо код указан верно! Ваша скидка ' . $data['discount'] . ' ' . $tip_disc . $info_cat_d . $info_prod_d;
                            } else {
                                $totalsumma = $_REQUEST['sum'];
                                $totalsummainput = $_REQUEST['sum'];
                            }
                        else:
                            $messageinfo = '<b>Не применена!</b><br> Промо код указан верно.<br> Но сумма заказа должна быть от ' . $data['sum_order'] . ' ' . $currency;
                            $status = '0'; //скидка не применена
                            $_SESSION['totalsumma'] = '0';
                        endif;
                    else:
                        $messageinfo = '<b>Не применена!</b><br> Промо код указан верно.<br> Но ни один из товаров в вашей корзине не участвует в акции';
                        $status = '0'; //скидка не применена
                        $_SESSION['totalsumma'] = '0';
                    endif;
                } else { //если дата не сошлась
                    $messageinfo = '<b>Не применена!</b><br> Промо код указан верно.<br> Но срок действия акции закончен';
                    $status = '0'; //скидка не применена
                    $_SESSION['totalsumma'] = '0';
                }
            }
        else:
            $messageinfo = '<b>Ошибка!</b><br> Скидка для промокода не установлена, свяжитесь с нами для подробной информации!';
            $status = '0'; //не применена скидка
            $_SESSION['totalsumma'] = '0';
        endif;
    else:
        $messageinfo = '<b>Ошибка!</b><br> Данного промо-кода в базе данных не обнаружено!';
        $status = '0'; //не применена скидка
    endif;

    //соберем массив скидок для JS
    $numc = 3; //для пересчета таблицы корзины
    if (is_array($_SESSION['cart']))
        foreach ($_SESSION['cart'] as $cartjs) {
            $discountcart[$cartjs['id']]['n'] = $numc;
            $numc++;
        }
} else {
    unset($_SESSION['discpromo']);
    unset($_SESSION['freedelivery']);
    unset($_SESSION['tip_disc']);
    unset($_SESSION['totalsumma']);
    unset($_SESSION['promocode']);
    unset($_SESSION['codetip']);
    unset($_SESSION['discpromo']);

    // Чистка скидки при перезагрузке страницы
    if (is_array($_SESSION['cart']))
        foreach ($_SESSION['cart'] as $k => $cart) {
            unset($_SESSION['cart'][$k]['promo_sum']);
            unset($_SESSION['cart'][$k]['promo_code']);
            unset($_SESSION['cart'][$k]['discount_tip_sum']);
            unset($_SESSION['cart'][$k]['promo_percent']);
            if (!isset($_SESSION['cart'][$k]['promotion_discount']) && !isset($_SESSION['cart'][$k]['order_discount_disabled'])) {
                unset($_SESSION['cart'][$k]['promo_price']);
            }
        }
}

$PHPShopCart = new PHPShopCart();

$promoSum = 0;

foreach ($PHPShopCart->getArray() as $product) {
    
    // Сумма в промокоде
    if (!empty($product['promo_sum'])) {
        $promoSum += $product['num'] * number_format($product['price'] - $product['promo_sum'] / $product['num'], $PHPShopOrder->format, '.', '');
    }
    // Процент от суммы в промокоде
    else if (!empty($product['promo_percent'])) {
        $promoSum += $product['num'] * number_format($product['price'] - ($product['price'] * $product['promo_percent'] / 100), $PHPShopOrder->format, '.', '');
    }
    // Учавствует в промоакции
    elseif(!empty($product['promo_price'])) {
        $promoSum += $product['price'];
    }
}

// Округление
$totalsummainput = number_format($totalsummainput, $PHPShopSystem->format, '.', '');

// Итого товары по акции
if ($promoSum > 0){
    
    $total = $promoSum;
    
    // Итого товары без промоакции, применяем скидку статуса пользователя\суммы заказа
    $total += (float) $PHPShopOrder->returnSumma($PHPShopCart->getSumWithoutPromo(true), $PHPShopOrder->ChekDiscount($totalsumma), '', (float) $delivery);

    // Итого с учетом бонусов
    $total -= (float) (new PHPShopBonus((int) $_SESSION['UsersId']))->getUserBonus($total);
    
    // Процент
    if (strstr($discountAll, '%')) {
        $discount = ($data['discount'] * $_REQUEST['sum'] / 100);
        $discount = "- " . number_format($_REQUEST['ssum'] + $discount, $PHPShopSystem->format, '.', '');
    } else
        $discount = "- " . $_REQUEST['ssum'] + $data['discount'];
}
else{
    $total = $totalsummainput;
    $discount=0;
}



// Результат
$_RESULT = array(
    'delivery' => $delivery,
    'total' => number_format($total, $PHPShopSystem->format, '.', ' '),
    'discount' => $discount,
    'discountall' => $discountAll,
    'mes' => $messageinfo,
    'action' => $action,
    'status' => $status,
    'freedelivery' => $freedelivery,
    'totalsummainput' => $totalsummainput,
    'deliverymethodcheck' => $delivery_method_check,
    'success' => 1
);

// Сумма корзины для смены доставки
$_SESSION['totalsummainput'] = $totalsummainput;

// JSON 
if ($_REQUEST['type'] == 'json') {
    $_RESULT['mes'] = PHPShopString::win_utf8($_RESULT['mes'],true);
    $_RESULT['discountall'] = PHPShopString::win_utf8($_RESULT['discountall']);
    $_RESULT['mesclean'] = strip_tags($_RESULT['mes']);
}

header("Content-Type: application/json");
exit(json_encode($_RESULT));