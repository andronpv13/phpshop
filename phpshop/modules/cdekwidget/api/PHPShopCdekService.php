<?php

header('Access-Control-Allow-Origin: *');
error_reporting(0);

session_start();
$_classPath = "../../../";
include_once($_classPath . "class/obj.class.php");
include_once($_classPath . "modules/cdekwidget/api/service.php");
include_once($_classPath . "modules/cdekwidget/class/CDEKWidget.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("delivery");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("cart");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("product");
PHPShopObj::loadClass("promotions");
PHPShopObj::loadClass("string");
PHPShopObj::loadClass("cache");

$PHPShopBase = new PHPShopBase("../../../../phpshop/inc/config.ini");
$PHPShopValutaArray = new PHPShopValutaArray();
$PHPShopSystem = new PHPShopSystem();
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'shop'));

class PHPShopCdekService extends ISDEKservice {

    private static $fee;
    private static $fee_type;
    private static $deliveryId;
    private static $cost;

    public static function initialize() {
        self::setTarifPriority(
                [233, 137, 139, 16, 18, 11, 1, 3, 61, 60, 59, 58, 57, 83], [234, 136, 138, 15, 17, 10, 12, 5, 62, 63]
        );

        $CDEKWidget = new CDEKWidget();

        static::$account = $CDEKWidget->option['account'];
        static::$key = $CDEKWidget->option['password'];
        static::$fee = $CDEKWidget->option['fee'];
        static::$cost = $CDEKWidget->option['cost'];
        static::$fee_type = $CDEKWidget->option['fee_type'];
        static::$deliveryId = (int) $CDEKWidget->option['delivery_id'];
    }

    public static function calc($data) {
        if (!$data['shipment']['tarifList']) {
            $data['shipment']['tariffList'] = static::$tarifPriority[$data['shipment']['type']];
        }
        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']))
            $data['shipment']['ref'] = $_SERVER['HTTP_REFERER'];

        if (!$data['shipment']['cityToId']) {
            $cityTo = static::sendToCity($data['shipment']['cityTo']);
            if ($cityTo && $cityTo['code'] === 200) {
                $pretendents = json_decode($cityTo['result']);

                if ($pretendents) {
                    $data['shipment']['cityToId'] = $pretendents[0]->code;
                }
            }
        }

        if ($data['shipment']['cityToId']) {
            $answer = static::calculate($data['shipment']);

            if ($answer) {

                if (static::$fee > 0) {
                    $answer['result']['price'] = static::plusFee($answer['result']['price']);
                }

                if (static::$cost > 0) {
                    $answer['result']['price'] = static::plusCost($answer['result']['price']);
                }

                if (static::isFreeDelivery()) {
                    $answer['result']['price'] = 0;
                }

                $answer['type'] = $data['shipment']['type'];
                if ($data['shipment']['timestamp']) {
                    $answer['timestamp'] = $data['shipment']['timestamp'];
                }
                static::toAnswer($answer);
            }
        } else {
            static::toAnswer(array('error' => 'City to not found'));
        }

        static::printAnswer();
    }

    // Наценка
    private static function plusFee($price) {
        if (self::$fee_type == 1) {
            return number_format($price + ($price * self::$fee / 100), 0, '.', '');
        }

        return number_format($price + self::$fee, 0, '.', '');
    }

    // Дополнительный сбор
    private static function plusCost($price) {
        $cost = 0;
        if (empty($_SESSION['cdek_delivery_paid'])) {
            $sum = 0;
            if (is_array($_SESSION['cart']))
                foreach ($_SESSION['cart'] as $val)
                    $sum += $val['num'] * $val['price'];

            $cost = ((int) $sum * (int) self::$cost / 100);
        }

        return number_format($price + $cost, 0, '.', '');
    }

    private static function isFreeDelivery() {
        $cart = new PHPShopCart();

        $delivery = new PHPShopDelivery((int) static::$deliveryId);

        return $delivery->isFree($cart->getSum(false));
    }

}

PHPShopCdekService::initialize();
$action = $_REQUEST['isdek_action'];
header("Content-Type: application/json");
if (method_exists('PHPShopCdekService', $action)) {

    include_once '../class/CDEKWidget.php';
    $CDEKWidget = new CDEKWidget();

    if ($action == 'getCity' or $action == 'calc')
        $_SESSION['cdek_token'] = $CDEKWidget->getToken();


    if ($action == 'getPVZ') {

        $file = 'pvz.json';
        $cache_key = md5(str_replace("www.", "", getenv('SERVER_NAME')) . $file) . '.json';
        $PHPShopCache = new PHPShopCache($cache_key);

        $PHPShopFileCache = new PHPShopFileCache($PHPShopCache->time);
        $PHPShopFileCache->dir = "/UserFiles/Cache/static/";

        $content = $PHPShopFileCache->get($cache_key);

        if (!empty($content)) {
            echo gzuncompress($content);
        } else {
            ob_start();
            PHPShopCdekService::$action($_REQUEST);
            $content = ob_get_clean();
            $PHPShopFileCache->set($cache_key, gzcompress($content, $PHPShopCache->level));
            echo $content;
        }
    } else
        PHPShopCdekService::$action($_REQUEST);
}