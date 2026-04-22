<?php
/*
 * Счет в банк заказ
 */

session_start();

$_classPath="../../";
include($_classPath."class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("delivery");
PHPShopObj::loadClass("date");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("inwords");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("cart");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("text");
PHPShopObj::loadClass("lang");

$PHPShopBase = new PHPShopBase($_classPath."inc/config.ini");
$PHPShopSystem = new PHPShopSystem();
$PHPShopLang = new PHPShopLang(array('locale'=>$_SESSION['lang'],'path'=>'admin'));

if(PHPShopSecurity::true_param($_GET['tip'],$_GET['orderId'],$_GET['datas'])) {

    $orderId=PHPShopSecurity::TotalClean($_GET['orderId'],5);
    $datas=PHPShopSecurity::TotalClean($_GET['datas'],5);

    $PHPShopOrm = new PHPShopOrm();
    $result=$PHPShopOrm->query("select id from ".$SysValue['base']['orders']." where id='$orderId' and datas=".$datas);
    $n=mysqli_num_rows($result);

    if(empty($n)) exit("Неавторизованный пользователь!");
    else $PHPShopOrder = new PHPShopOrderFunction($orderId);


    // Перевод цифр в слова
    $iw = new inwords;

    PHPShopParser::set('totaltext',$iw->get($PHPShopOrder->getTotal()));
    PHPShopParser::set('item',$PHPShopOrder->getNum());
    PHPShopParser::set('currency',$PHPShopOrder->default_valuta_code);
    PHPShopParser::set('total',round($PHPShopOrder->getTotal()));
    PHPShopParser::set('totalnds',$PHPShopOrder->getTotal($nds=true));
    PHPShopParser::set('nds',$PHPShopOrder->PHPShopSystem->getParam('nds'));
    PHPShopParser::set('discount',$PHPShopOrder->getDiscount());
    PHPShopParser::set('ouid',$PHPShopOrder->getValue('uid'));
    PHPShopParser::set('user',$PHPShopOrder->getSerilizeParam('orders.Person.name_person') . $PHPShopOrder->getParam('fio'));
    PHPShopParser::set('org_bank_acount',$PHPShopSystem->getSerilizeParam('bank.org_bank_schet'));
    PHPShopParser::set('org_bank_schet',$PHPShopSystem->getSerilizeParam('bank.org_bank_schet'));
    PHPShopParser::set('org_bic',$PHPShopSystem->getSerilizeParam('bank.org_bic'));
    PHPShopParser::set('org_bank',$PHPShopSystem->getSerilizeParam('bank.org_bank'));
    PHPShopParser::set('org_name',$PHPShopSystem->getSerilizeParam('bank.org_name'));
    PHPShopParser::set('org_schet',$PHPShopSystem->getSerilizeParam('bank.org_schet'));
    PHPShopParser::set('org_kpp',$PHPShopSystem->getSerilizeParam('bank.org_kpp'));
    PHPShopParser::set('org_inn',$PHPShopSystem->getSerilizeParam('bank.org_inn'));
    PHPShopParser::set('org_adres',$PHPShopSystem->getSerilizeParam('bank.org_adres'));
    PHPShopParser::set('org_ur_adres',$PHPShopSystem->getSerilizeParam('bank.org_ur_adres'));
    PHPShopParser::set('org_name',$PHPShopSystem->getSerilizeParam('bank.org_name'));
    PHPShopParser::set('date',date("d-m-y"));
    PHPShopParser::set('name',$PHPShopSystem->getName());
    
    // Монитор
    if($_GET['tip'] == 2 and empty($_SESSION['logPHPSHOP'])){
         PHPShopParser::set('comment_start','<!--');
         PHPShopParser::set('comment_end','-->');
    }
    
    PHPShopParser::file('../../lib/templates/print/receipt.tpl');
    writeLangFile();
}
else header('Location: /');
?>