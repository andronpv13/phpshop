<?php

/**
 * Запись нового уведомления от пользователя
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopCoreFunction
 * @param obj $obj объект класса
 */
function notice_add($obj) {
    $PHPShopOrm = new PHPShopOrm($obj->getValue('base.notice'));

    // Проверка на уникальность
    $row = $PHPShopOrm->select(array('id'), array('user_id' => '=' . $obj->UsersId, 'product_id' => '=' . $_POST['productId'], 'enabled' => "='0'"), false, array('limit' => 1));

    // Запись в БД
    if (empty($row)) {

        $PHPShopOrm->debug = $obj->debug;
        $PHPShopOrm->insert(array('user_id_new' => $obj->UsersId, 'product_id_new' => $_POST['productId'], 'datas_new' => time() + ($_POST['date'] * 60 * 60 * 24 * 30),
            'datas_start_new' => time(), 'enabled_new' => '0'));

        // Сообщение
        notice_mail($obj);
    }

    // выводим сообщение об успешном добавлении уведомления в базу
    $obj->set('formaTitle', __('Уведомления'));
    $obj->set('formaContent', ParseTemplateReturn('phpshop/lib/templates/users/notice_done.tpl', true));
    $obj->ParseTemplate($obj->getValue('templates.users_page_list'));
}

/**
 * Отправление по почте нового уведомления от пользователя
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopCoreFunction
 * @param obj $obj объект класса
 */
function notice_mail($obj) {

    $PHPShopUser = new PHPShopUser($obj->UsersId);
    if (PHPShopSecurity::true_num($_POST['productId'])) {

        $PHPShopProduct = new PHPShopProduct($_POST['productId']);

        $obj->set('product_name', $PHPShopProduct->getName());
        $obj->set('product_art', $PHPShopProduct->getParam('uid'));
        $obj->set('product_id', $_POST['productId']);
        $obj->set('date', date("d-m-y H:i a"));
        $active = date("U") + ($_POST['date'] * 60 * 60 * 24 * 30);
        $obj->set('date_active', PHPShopDate::dataV($active));
        $obj->set('user_ip', $_SERVER['REMOTE_ADDR']);
        $obj->set('user_mail', $PHPShopUser->getValue('mail'));
        $obj->set('user_name', $PHPShopUser->getValue('name'));
        $obj->set('user_company', $PHPShopUser->getValue('company'));
        $obj->set('user_tel', $PHPShopUser->getValue('tel'));
        $obj->set('user_message', $_POST['message']);

        // Адрес для сообщений о регистрации
        $admin_mail = $obj->PHPShopSystem->getParam('adminmail2');

        // Заголовок e-mail
        $title = __('Поступила заявка на уведомление о товаре') . " " . $PHPShopProduct->getName();

        // Отправка e-mail пользователя
        $PHPShopMail = new PHPShopMail($admin_mail, $admin_mail, $title, '', true, true,array('replyto'=>$PHPShopUser->getValue('mail')));

        // Содержание e-mail
        $content = ParseTemplateReturn(dirname(dirname(__DIR__)) . '/lib/templates/users/mail_notice_add.tpl', true);

        // отправляем письмо
        $PHPShopMail->sendMailNow($content);
    }
    else
        $obj->setError404();
}

?>