<?php

session_start();

$_classPath = "../";
include_once($_classPath . "class/obj.class.php");
include_once($_classPath . "core/pricemail.core.php");
include_once($_classPath . "inc/elements.inc.php");
PHPShopObj::loadClass(['base', 'system', 'security', 'valuta', 'lang', 'security', 'product', 'parser']);

// Подключение к БД
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
// Объекты, нужные как глобальные в разных частях системы.
$PHPShopSystem = new PHPShopSystem();
$PHPShopValutaArray = new PHPShopValutaArray();
$PHPShopNav = new PHPShopNav();
$PHPShopRecaptchaElement = new PHPShopRecaptchaElement();
$PHPShopLang = new PHPShopLang(['locale'=>$_SESSION['lang'],'path'=>'shop']);
$ajaxPricemail = new AjaxPricemail();

try {
    PHPShopParser::set('serverName', $_SERVER['SERVER_NAME']);
    PHPShopParser::set('user_ip', $_SERVER['REMOTE_ADDR']);
    PHPShopParser::set('date', date("d-m-y H:i a"));
    PHPShopParser::set('serverPath', $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir']);
    PHPShopParser::set('logo', $PHPShopSystem->getLogo(true));

    $ajaxPricemail->send();
    $_RESULT = [
        'message'   => PHPShopString::win_utf8(__("Спасибо, мы рассмотрим Вашу заявку в ближайшее время.")),
        'success' => true
    ];
} catch (\Exception $exception) {
    $_RESULT = [
        'message'   => PHPShopString::win_utf8($exception->getMessage()),
        'success' => false
    ];
}

echo json_encode($_RESULT);

class AjaxPricemail
{
    private $pricemail;

    public function __construct()
    {
        $this->pricemail = new PHPShopPricemail((int) $_REQUEST['product_id']);
    }

    public function send()
    {
        if($this->pricemail->security()) {
            $this->pricemail->send();
            $this->pricemail->lead();
        } else {
            throw new Exception(__("Ошибка ключа, повторите попытку ввода ключа"));
        }
    }
}

?>