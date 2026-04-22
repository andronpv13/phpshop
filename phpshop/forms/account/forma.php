<?php

/*
 * Счет в банк заказ
 */

session_start();

$_classPath = "../../";
include($_classPath . "class/obj.class.php");
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

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
$PHPShopSystem = new PHPShopSystem();
$PHPShopBase->checkMultibase("../../../");
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'admin'));

/**
 * Шаблон вывода таблицы корзины
 * Основной шаблон печатной формы расположен в phpshop/lib/templates/print/acount.tpl
 */
function printforma($val, $options) {
    global $n;
    if (empty($val['ed_izm']))
        $val['ed_izm'] = __('шт.');
    $dis = PHPShopText::tr($n, $val['name'], $val['ed_izm'], $val['num'], $val['price'] . ' ' . $options['currency'], $val['total'] . ' ' . $options['currency']);
    @$n++;
    return $dis;
}

/**
 * Шаблон вывода таблицы доставки
 * Основной шаблон печатной формы расположен в phpshop/lib/templates/print/acount.tpl
 */
function printdelivery($val, $options) {
    global $n;
    return PHPShopText::tr($n, __('Доставка') . ' - ' . $val['name'], __('шт.'), '1', $val['price'] . ' ' . $options['currency'], $val['price'] . ' ' . $options['currency']);
}

if (PHPShopSecurity::true_param($_GET['tip'], $_GET['orderId'], $_GET['datas'])) {

    $orderId = PHPShopSecurity::TotalClean($_GET['orderId'], 5);
    $datas = PHPShopSecurity::TotalClean($_GET['datas'], 5);

    $PHPShopOrm = new PHPShopOrm();
    $result = $PHPShopOrm->query("select id from " . $GLOBALS['SysValue']['base']['orders'] . " where id='" . intval($orderId) . "' and datas=" . intval($datas));
    $n = mysqli_num_rows($result);

    if (empty($n))
        exit("Неавторизованный пользователь!");
    else
        $PHPShopOrder = new PHPShopOrderFunction($orderId);

    // Юридические лица
    $company = $PHPShopOrder->getParam('company');
    $PHPShopSystem->setCompany($company);

    // Перевод цифр в слова
    $iw = new inwords;

    PHPShopParser::set('totaltext', $iw->get($PHPShopOrder->getTotal()));
    PHPShopParser::set('item', $PHPShopOrder->getNum());
    PHPShopParser::set('currency', $PHPShopOrder->default_valuta_code);
    PHPShopParser::set('total', $PHPShopOrder->getTotal());

    if ($PHPShopSystem->getValue('nds_enabled') == 0) {
        PHPShopParser::set('nds_block_start', '<!--');
        PHPShopParser::set('nds_block_end', '-->');
    }

    PHPShopParser::set('totalnds', $PHPShopOrder->getTotal(true));
    PHPShopParser::set('nds', $PHPShopOrder->PHPShopSystem->getParam('nds'));

    $discount = $PHPShopOrder->getDiscount() . '%';
    $bonus = $PHPShopOrder->getParam('bonus_minus');

    if (!empty($bonus))
        $discount = $bonus.' '.$PHPShopOrder->default_valuta_code;

    PHPShopParser::set('discount', $discount);
    PHPShopParser::set('ouid', $PHPShopOrder->getValue('uid'));

    $orgData = @$PHPShopOrder->getSerilizeParam('orders.Person.org_name');
    if (empty($orgData)) {
        $orgData = $PHPShopOrder->getParam('org_name');
    }
    if (!empty(@$PHPShopOrder->getSerilizeParam('orders.Person.org_inn')) || !empty($PHPShopOrder->getParam('org_inn'))) {
        $orgData .= ' ИНН ';
        $inn = $PHPShopOrder->getSerilizeParam('orders.Person.org_inn');
        if (empty($inn)) {
            $inn = $PHPShopOrder->getParam('org_inn');
        }
        $orgData .= $inn;
    }
    if (!empty(@$PHPShopOrder->getSerilizeParam('orders.Person.org_kpp')) || !empty($PHPShopOrder->getParam('org_kpp'))) {
        $orgData .= ' КПП ';
        $kpp = $PHPShopOrder->getSerilizeParam('orders.Person.org_kpp');
        if (empty($kpp)) {
            $kpp = $PHPShopOrder->getParam('org_kpp');
        }
        $orgData .= $kpp;
    }
    if (!empty($PHPShopOrder->getParam('org_yur_adres'))) {
        $orgData .= ' Юр. адрес ' . $PHPShopOrder->getParam('org_yur_adres');
    }

    $fio = $PHPShopOrder->getParam('fio');
    if (!empty($fio))
        $user = $PHPShopOrder->getParam('fio');
    else
        $user = $PHPShopOrder->getSerilizeParam('orders.Person.name_person');

    if (!empty($orgData)) {
        PHPShopParser::set('buyer_data', $orgData);
    } else {
        PHPShopParser::set('buyer_data', $user);
    }
    PHPShopParser::set('person_user', $user);
    PHPShopParser::set('org_bank_acount', $PHPShopSystem->getSerilizeParam('bank.org_bank_schet'));
    PHPShopParser::set('org_bic', $PHPShopSystem->getSerilizeParam('bank.org_bic'));
    PHPShopParser::set('org_bank', $PHPShopSystem->getSerilizeParam('bank.org_bank'));
    PHPShopParser::set('org_name', $PHPShopSystem->getSerilizeParam('bank.org_name'));
    PHPShopParser::set('org_schet', $PHPShopSystem->getSerilizeParam('bank.org_schet'));
    PHPShopParser::set('org_kpp', $PHPShopSystem->getSerilizeParam('bank.org_kpp'));
    PHPShopParser::set('org_inn', $PHPShopSystem->getSerilizeParam('bank.org_inn'));
    PHPShopParser::set('org_adres', $PHPShopSystem->getSerilizeParam('bank.org_adres'));
    PHPShopParser::set('org_ur_adres', $PHPShopSystem->getSerilizeParam('bank.org_ur_adres'));
    PHPShopParser::set('date', PHPShopDate::dataV($datas, false));
    PHPShopParser::set('logo', $PHPShopSystem->getLogo(true));
    PHPShopParser::set('telNum', $PHPShopSystem->getValue('tel'));
    PHPShopParser::set('name', $PHPShopSystem->getValue('name'));
    PHPShopParser::set('company', $PHPShopSystem->getValue('company'));
    PHPShopParser::set('descrip', $PHPShopSystem->getValue('descrip'));
    PHPShopParser::set('adminMail', $PHPShopSystem->getValue('adminmail2'));
    PHPShopParser::set('cart', $PHPShopOrder->cart('printforma', array('currency' => $PHPShopOrder->default_valuta_code)) . $PHPShopOrder->delivery('printdelivery', array('currency' => $PHPShopOrder->default_valuta_code)));

    // Печати и подписи
    $LoadBanc['org_sig'] = $PHPShopSystem->getSerilizeParam('bank.org_sig');
    $LoadBanc['org_sig_buh'] = $PHPShopSystem->getSerilizeParam('bank.org_sig_buh');
    $LoadBanc['org_stamp'] = $PHPShopSystem->getSerilizeParam('bank.org_stamp');

    if (!empty($LoadBanc['org_sig']))
        $org_sig = '<img src="' . $LoadBanc['org_sig'] . '">';
    else
        $org_sig = '<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>';

    if (!empty($LoadBanc['org_sig_buh']))
        $org_sig_buh = '<img src="' . $LoadBanc['org_sig_buh'] . '">';
    else
        $org_sig_buh = '<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>';

    if (!empty($LoadBanc['org_stamp']))
        $org_stamp = '<img src="' . $LoadBanc['org_stamp'] . '">';
    else
        $org_stamp = '<div style="padding:50px;border-bottom: 1px solid #000000;border-top: 1px solid #000000;border-left: 1px solid #000000;border-right: 1px solid #000000;" align="center">' . __('М.П.') . '</div>';


    // Монитор
    if ($_GET['tip'] == 2 and empty($_SESSION['logPHPSHOP'])) {
        PHPShopParser::set('comment_start', '<!--');
        PHPShopParser::set('comment_end', '-->');
        $org_stamp = '<div style="padding:50px;border-bottom: 1px solid #000000;border-top: 1px solid #000000;border-left: 1px solid #000000;border-right: 1px solid #000000;" align="center">М.П.</div>';
        $org_sig_buh = $org_sig = '<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>';
    }

    PHPShopParser::set('org_sig', $org_sig);
    PHPShopParser::set('org_sig_buh', $org_sig_buh);
    PHPShopParser::set('org_stamp', $org_stamp);

    if ($_GET['tip'] == 2)
        PHPShopParser::set('title', '№' . $PHPShopOrder->getValue('uid'));

    PHPShopParser::file('../../lib/templates/print/account.tpl');
    writeLangFile();
} else
    header('Location: /');
?>