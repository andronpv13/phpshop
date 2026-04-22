<?php

session_start();

$_classPath = "../";
include_once($_classPath . "class/obj.class.php");
include_once($_classPath . "inc/elements.inc.php");
include_once($_classPath . "class/mail.class.php");
include_once($_classPath . "core/users.core.php");
include_once($_classPath . "core/users.core/notice_add.php");
PHPShopObj::loadClass(['base', 'system', 'security', 'valuta', 'lang', 'security', 'product', 'parser', 'user']);

// Подключение к БД
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
// Объекты, нужные как глобальные в разных частях системы.
$PHPShopSystem = new PHPShopSystem();
$PHPShopValutaArray = new PHPShopValutaArray();
$PHPShopNav = new PHPShopNav();
$PHPShopRecaptchaElement = new PHPShopRecaptchaElement();
$PHPShopLang = new PHPShopLang(['locale' => $_SESSION['lang'], 'path' => 'shop']);
$ajaxNotice = new AjaxNotice();

try {
    if (isset($_REQUEST['loadForm'])) {
        $_RESULT = $ajaxNotice->getProductData((int) $_REQUEST['productId']);
        $_RESULT['success'] = true;
    } else {
        $ajaxNotice->write($_REQUEST['mail']);
        $_RESULT = [
            'message' => PHPShopString::win_utf8(__("Спасибо! Мы уведомим Вас при появлении товара в продаже.")),
            'success' => true
        ];
    }
} catch (\Exception $exception) {
    $_RESULT = [
        'message' => PHPShopString::win_utf8($exception->getMessage()),
        'success' => false
    ];
}

echo json_encode($_RESULT);

class AjaxNotice {

    public function write($email) {
        if ($this->security()) {
            $_POST['name_new'] = PHPShopString::utf8_win1251(strip_tags($_POST['name_new']));
            $_POST['name_new'] = PHPShopSecurity::TotalClean($_POST['name_new'], 4);
            $_POST['message'] = PHPShopString::utf8_win1251(strip_tags($_POST['message']));
            $_POST['tel_new'] = PHPShopString::utf8_win1251(strip_tags($_POST['tel_new']));

            $product = $this->getProductData((int) $_REQUEST['productId']);

            if (is_array($product)) {

                $PHPShopUsers = new PHPShopUsers();
                $PHPShopUsers->add_user_from_order($email);

                notice_add($PHPShopUsers);
                $this->lead($product);
            }
        } else {
            throw new Exception(__("Ошибка ключа, повторите попытку ввода ключа"));
        }
    }

    private function security() {
        global $PHPShopRecaptchaElement;

        return $PHPShopRecaptchaElement->security(['url' => false, 'captcha' => true, 'referer' => true]);
    }

    /**
     * Добавление лида
     */
    private function lead($product) {

        if (is_array($product)) {
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['notes']);
            $content = "http://" . $_SERVER['SERVER_NAME'] . "/shop/UID_" . $product['id'] . ".html\n" . PHPShopString::utf8_win1251($_POST['message']);
            $insert = array('date_new' => time(), 'message_new' => __('Уведомление') . ' ' . PHPShopString::utf8_win1251($product['title']), 'name_new' => $_POST['name_new'], 'mail_new' => $_POST['mail'], 'tel_new' => '', 'content_new' => __('Уведомление о поступлении товара') . ' ' . PHPShopSecurity::TotalClean($content));
            $PHPShopOrm->insert($insert);
        }
    }

    public function getProductData($productId) {
        $product = new PHPShopProduct($productId);

        // Проверка наличия
        if (!empty($product->getName()) and ! empty($product->getParam('sklad'))) {

            return [
                'id' => $productId,
                'link' => sprintf('/shop/UID_%s.html', $productId),
                'title' => PHPShopString::win_utf8($product->getName()),
                'image' => sprintf('<a href="' . $GLOBALS['SysValue']['dir']['dir'] . '/shop/UID_%s.html" title="%s">
                                   <img class="one-image-slider" src="%s" alt="%s" title="%s"/>
                                </a>', $productId, PHPShopString::win_utf8($product->getName()), $product->getImage(), PHPShopString::win_utf8($product->getName()), PHPShopString::win_utf8($product->getName()))
            ];
        }
    }

}

?>