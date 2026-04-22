<?php

session_start();

$_classPath = "../";
include_once($_classPath . "class/obj.class.php");
include_once($_classPath . "inc/elements.inc.php");
include_once($_classPath . "class/mail.class.php");
include_once($_classPath . "core/users.core.php");
PHPShopObj::loadClass(['base', 'system', 'security', 'valuta', 'lang', 'security', 'product', 'parser', 'user']);

// Подключение к БД
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini",true,true);

// Объекты, нужные как глобальные в разных частях системы.
$PHPShopSystem = new PHPShopSystem();
$PHPShopValutaArray = new PHPShopValutaArray();
$PHPShopNav = new PHPShopNav();
$PHPShopRecaptchaElement = new PHPShopRecaptchaElement();
$PHPShopLang = new PHPShopLang(['locale'=>$_SESSION['lang'],'path'=>'shop']);
if($PHPShopSystem->ifSerilizeParam('admoption.recaptcha_enabled')) {
    $PHPShopRecaptchaElement->recaptcha = true;
}
$ajaxReview = new AjaxReview();

try {
    $ajaxReview->write($_REQUEST['mail']);
    $_RESULT = [
        'message' => PHPShopString::win_utf8(__("Спасибо! Ваш комментарий будет доступен после прохождения модерации.")),
        'success' => true
    ];
} catch (\Exception $exception) {
    $_RESULT = [
        'message'   => PHPShopString::win_utf8($exception->getMessage()),
        'success' => false
    ];
}

echo json_encode($_RESULT);

class AjaxReview
{
    public function write($email)
    {
        if($this->security()) {
            $PHPShopUsers = new PHPShopUsers();
            $PHPShopUsers->stop_redirect = true;
            
            $_POST['name_new'] = PHPShopString::utf8_win1251(strip_tags($_POST['name_new']));
            $_POST['name_new'] = PHPShopSecurity::TotalClean($_POST['name_new'], 4);
            
            $userId = $PHPShopUsers->add_user_from_order($email,$_POST['name_new']);
            $message = PHPShopString::utf8_win1251(strip_tags($_REQUEST['message']));
            $message = PHPShopSecurity::TotalClean($message, 2);
            $myRate = abs(intval($_REQUEST['rate']));

            if (!$myRate)
                $myRate = 0;
            elseif ($myRate > 5)
                $myRate = 5;
            
            if (!empty($message)) {
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['comment']);
                $PHPShopOrm->insert([
                    'datas_new'     => time(),
                    'name_new'      => $_POST['name_new'],
                    'parent_id_new' => (int) $_REQUEST['productId'],
                    'content_new'   => $message,
                    'user_id_new'   => $userId,
                    'enabled_new'   => 0,
                    'rate_new'      => $myRate
                ]);

                // Имя товара
                $product = new PHPShopProduct((int) $_REQUEST['productId']);
                $name = $product->getName();

                // Письмо администратору
                PHPShopParser::set('mail',$email);
                PHPShopParser::set('content',$message);
                PHPShopParser::set('name',$_POST['name_new']);
                PHPShopParser::set('product',$name);
                PHPShopParser::set('product_id', $product->objID);
                PHPShopParser::set('rating',$myRate);
                PHPShopParser::set('date',PHPShopDate::dataV(false, false));

                $system = new PHPShopSystem();
                $title = __("Добавлен отзыв к товару").' "'.$name.'"';

                (new PHPShopMail($system->getValue('adminmail2'), $system->getValue('adminmail2'), $title, '', true, true,['replyto' => $email]))->sendMailNow(PHPShopParser::file('../lib/templates/users/mail_admin_review.tpl', true,false));
                
                $this->lead();
            }
        } else {
            throw new Exception(__("Ошибка ключа, повторите попытку ввода ключа"));
        }
    }
    
    /**
     * Добавление лида
     */
    private function lead() {
        
        $product = $this->getProductData((int) $_REQUEST['productId']);
        
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['notes']);
        $content = "http://" . $_SERVER['SERVER_NAME'] . "/shop/UID_" . $product['id'] . ".html\n".PHPShopString::utf8_win1251($_POST['message']);
        $insert = array('date_new' => time(), 'message_new' => __('Комментарий').' '.PHPShopString::utf8_win1251($product['title']), 'name_new' => $_POST['name_new'], 'mail_new' => $_POST['mail'], 'tel_new' => '', 'content_new' => __('Комментарий о товаре').' '.PHPShopSecurity::TotalClean($content));
        $PHPShopOrm->insert($insert);
    }

    public function getProductData($productId) {
        $product = new PHPShopProduct($productId);

        return [
            'id' => $productId,
            'link' => sprintf('/shop/UID_%s.html', $productId),
            'title' => PHPShopString::win_utf8($product->getName()),
            'image' => sprintf('<a href="/shop/UID_%s.html" title="%s">
                                   <img class="one-image-slider" src="%s" alt="%s" title="%s"/>
                                </a>', $productId, PHPShopString::win_utf8($product->getName()), $product->getImage(), PHPShopString::win_utf8($product->getName()), PHPShopString::win_utf8($product->getName()))
        ];
    }

    private function security() {
        global $PHPShopRecaptchaElement;

        return $PHPShopRecaptchaElement->security(['url' => false, 'captcha' => true, 'referer' => true]);
    }
}
?>