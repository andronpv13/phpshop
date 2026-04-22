<?php

/**
 * Сообщение регистрации пользователя
 * @author PHPShop Software
 * @version 1.2
 * @package PHPShopCoreFunction
 * @param obj $obj объект класса
 */
function message_activation($obj) {

    $obj->set('user_key', $obj->user_status);
    $obj->set('user_mail', PHPShopSecurity::TotalClean($_POST['mail_new'], 3));
    $obj->set('user_name', PHPShopSecurity::TotalClean($_POST['name_new'], 4));
    $obj->set('user_login', $_POST['login_new']);
    $obj->set('user_password', $_POST['password_new']);

    // Адрес для сообщений о регистрации
    $admin_mail = $obj->PHPShopSystem->getParam('adminmail2');

    // Заголовок e-mail пользователю
    $title = $obj->lang('activation_title') . " " . PHPShopSecurity::TotalClean($_POST['name_new']);

    if ($obj->PHPShopSystem->ifSerilizeParam('admoption.user_mail_activate')) {


        // Отправка e-mail пользователю
        $PHPShopMail = new PHPShopMail($_POST['mail_new'], $admin_mail, $title, '', true, true);
        // Содержание e-mail пользователю
        $content = ParseTemplateReturn('./phpshop/lib/templates/users/mail_user_activation.tpl', true);
        $PHPShopMail->sendMailNow($content);

        $obj->set('formaContent', ParseTemplateReturn('phpshop/lib/templates/users/message_activation.tpl', true));
    } elseif ($obj->PHPShopSystem->ifSerilizeParam('admoption.user_mail_activate_pre')) {
        
        // Отправка e-mail пользователю, что он должен ожидать ручной активации от админинистратора ресурса.
        $PHPShopMail = new PHPShopMail($_POST['mail_new'], $admin_mail, $title, '', true, true);
        // Содержание e-mail пользователю
        $content = ParseTemplateReturn('./phpshop/lib/templates/users/mail_user_activation_by_admin.tpl', true);
        $PHPShopMail->sendMailNow($content);

        // Заголовок e-mail администратору
        $title = $obj->lang('activation_admin_title') . " " . $_POST['name_new'];

        // Отправка e-mail администратору
        $PHPShopMail = new PHPShopMail($admin_mail, $admin_mail, $title, '', true, true);
        
        // Содержание e-mail  администратору
        $content = ParseTemplateReturn('./phpshop/lib/templates/users/mail_admin_activation.tpl', true);
        $PHPShopMail->sendMailNow($content);
        
        $obj->set('formaContent', ParseTemplateReturn('phpshop/lib/templates/users/message_admin_activation.tpl', true), true);
    }


    $obj->set('formaTitle', $obj->lang('user_register_title'));
    $obj->ParseTemplate($obj->getValue('templates.users_page_list'));
}

?>