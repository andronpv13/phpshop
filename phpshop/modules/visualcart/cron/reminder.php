<?php
session_start();

// Включение
$enabled = false;

$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("date");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("cart");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("text");
PHPShopObj::loadClass("lang");
PHPShopObj::loadClass("security");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'shop'));
$PHPShopSystem = new PHPShopSystem();
$GLOBALS['PHPShopOrder'] = new PHPShopOrderFunction();

// Авторизация
if ($_GET['s'] == md5($PHPShopBase->SysValue['connect']['host'] . $PHPShopBase->SysValue['connect']['dbase'] . $PHPShopBase->SysValue['connect']['user_db'] . $PHPShopBase->SysValue['connect']['pass_db']))
    $enabled = true;

if (empty($enabled))
    exit("Ошибка авторизации!");

function getUserName($id) {
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
    $data = $PHPShopOrm->select(array('name,tel'), array('id' => '=' . $id), false, array('limit' => 1));
    if (is_array($data))
        return array('name' => $data['name'] . ' ' . $data['tel'], 'link' => '?path=shopusers&id=' . $id);
}

/**
 * Шаблон вывода таблицы корзины
 */
function mailcartforma($val, $option) {
    global $PHPShopModules, $PHPShopOrder;

    if (empty($val['name']))
        return true;

    // Артикул
    if (!empty($val['parent_uid']))
        $val['uid'] = $val['parent_uid'];

    $val['price'] *= $option['rate'];
    $val['price'] = number_format($val['price'], $PHPShopOrder->format, '.', '');

    $dis = '<img width="50" src="http://' . $_SERVER['SERVER_NAME'] . $val['pic_small'] . '" align="left" alt=""> ' . $val['uid'] . "  " . $val['name'] . " (" . $val['num'] . " " . $val['ed_izm'] . " * " . $val['price'] . ") -- " . ($val['price'] * $val['num']) . " " . $option['currency'] . " <br>
";
    return $dis;
}

// Настройки модуля
PHPShopObj::loadClass("modules");
$PHPShopModules = new PHPShopModules($_classPath . "modules/");

$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.visualcart.visualcart_system"));
$option = $PHPShopOrm->getOne(array('sendmail'));

$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.visualcart.visualcart_memory"));
$PHPShopOrm->debug = false;
$i=0;

$where = ['sendmail' => "='0'","mail"=>'!=""'];
if (!empty($_GET['hostID']))
    $where['server'] = '=' . (int) $_GET['hostID'];

$data = $PHPShopOrm->select(array('*'), $where, array('order' => 'id'), array("limit" => $option['sendmail']));
if (is_array($data))
    foreach ($data as $row) {

        if (!empty($row['user'])) {
            $user = getUserName($row['user']);
            $row['name'] = $user['name'];
            $row['mail'] = $user['mail'];
        }
        
        if(empty($row['name']))
            $row['name']=__('Покупатель');

        if (PHPShopSecurity::true_email($row['mail'])) {
            
            // Данные по корзине 
            $cart = unserialize($row['cart']);
            $PHPShopCart = new PHPShopCart($cart);

            $currency = $PHPShopSystem->getDefaultValutaCode(true);
            $rate = 1;

            PHPShopParser::set('sum', $row['sum']);
            PHPShopParser::set('cart', $PHPShopCart->display('mailcartforma', array('currency' => $currency, 'rate' => $rate)));
            PHPShopParser::set('currency', $currency);
            PHPShopParser::set('total', $data['sum']);
            PHPShopParser::set('shop_name', $PHPShopSystem->getName());
            PHPShopParser::set('date', PHPShopDate::get());
            PHPShopParser::set('mail', $row['mail']);
            PHPShopParser::set('company', $PHPShopSystem->getParam('name'));
            PHPShopParser::set('user_name', $row['name']);
            PHPShopParser::set('serverShop', PHPShopString::check_idna($_SERVER['SERVER_NAME']));
            PHPShopParser::set('serverPath', PHPShopString::check_idna($_SERVER['SERVER_NAME']));
            PHPShopParser::set('shopName', $PHPShopSystem->getValue('company'));
            PHPShopParser::set('adminMail', $PHPShopSystem->getEmail());
            PHPShopParser::set('telNum', $PHPShopSystem->getValue('tel'));
            PHPShopParser::set('logo', $PHPShopSystem->getLogo());

            // Заголовок письма покупателю
            $title = __('Уведомление о незаконченном заказе');

            $PHPShopMail = new PHPShopMail($row['mail'], $PHPShopSystem->getEmail(), $title, PHPShopParser::file($GLOBALS['SysValue']['templates']['visualcart']['visualcart_mail'], true, false, true), true);
            
            if($PHPShopMail){
                $i++;
            }
        }
        
        $PHPShopOrm->update(array('sendmail_new'=>'1'),array('id'=>'='.$row['id']));
    }
echo "Отправлено ".$i." писем";
?>