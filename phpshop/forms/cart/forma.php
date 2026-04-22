<?php
session_start();

// Библиотеки
$_classPath="../../";
include($_classPath."class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("inwords");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("text");
PHPShopObj::loadClass("cart");
PHPShopObj::loadClass("lang");

// Подключение к БД
$PHPShopBase = new PHPShopBase("../../inc/config.ini");
$PHPShopSystem = new PHPShopSystem();
$PHPShopLang = new PHPShopLang(array('locale'=>$_SESSION['lang'],'path'=>'shop'));
$PHPShopOrder = new PHPShopOrderFunction();
$PHPShopValutaArray= new PHPShopValutaArray();
$PHPShopCart = new PHPShopCart();

/**
 * Шаблон вывода таблицы корзины
 * Основной шаблон печатной формы расположен в phpshop/lib/templates/print/cart.tpl
 */
function printforma($val) {
    static $n;
    if(empty($val['ed_izm'])) $val['ed_izm']='шт.';
    $dis=PHPShopText::tr($n+1,$val['name'],$val['ed_izm'],$val['num'],$val['price'],($val['num']*$val['price']));
    @$n++;
    return $dis;
}

// Перевод цифр в слова
$iw = new inwords;

PHPShopParser::set('name', $PHPShopSystem->getName());
PHPShopParser::set('total',$PHPShopCart->getTotal());
PHPShopParser::set('discount',$PHPShopOrder->ChekDiscount($PHPShopCart->getSum()));
PHPShopParser::set('date',date("d-m-y"));
PHPShopParser::set('logo',$PHPShopSystem->getLogo(true));
PHPShopParser::set('cart',$PHPShopCart->display('printforma'));
PHPShopParser::set('item',$PHPShopCart->getNum());
PHPShopParser::set('totaltext',$iw->get($PHPShopCart->getTotal()));
PHPShopParser::set('currency',$PHPShopOrder->default_valuta_code);
PHPShopParser::file('../../lib/templates/print/cart.tpl');

?>