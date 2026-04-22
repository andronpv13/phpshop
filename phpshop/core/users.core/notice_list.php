<?php

/**
 * Вывод списка уведомлений пользователя
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopCoreFunction
 * @param obj $obj объект класса
 */
function notice_list($obj) {
    $tr = $table = $table_archive = null;
    $PHPShopOrm = new PHPShopOrm($obj->getValue('base.notice'));

    // Текущие уведомлений
    $PHPShopOrm->debug = $obj->debug;
    $data = $PHPShopOrm->select(array('*'), array('user_id' => '=' . $obj->UsersId, 'enabled' => "='0'"), array('order' => 'datas desc'), array('limit' => 100));

    if (is_array($data)) {
        foreach ($data as $row) {

            if (PHPShopSecurity::true_num($row['product_id'])) {
                $link = '/shop/UID_' . $row['product_id'] . '.html';
                $PHPShopProduct = new PHPShopProduct($row['product_id']);
                $td1 = PHPShopText::a($link, $PHPShopProduct->getName(), $PHPShopProduct->getName(), false, false, false, 'b');
            }

            $td2 = PHPShopDate::dataV($row['datas_start']) . ' - ' . PHPShopDate::dataV($row['datas']);
            $td3 = PHPShopText::a('./notice.html?noticeId=' . $row['id'], __('Удалить'));
            $tr.=$obj->tr($td1, $td2, $td3);
        }

        $title = PHPShopText::h4(__('Текущие заявки'));
        $caption = $obj->caption(__('Наименование'), __('Период'), __('Статус'));

        $table = PHPShopText::table($caption . $tr, 3, 1, 'center', '100%', false, 0, 'allspecwhite', 'list table table-striped table-bordered table-hover');
    }

    // Архив уведомлений
    $PHPShopOrm->clean();
    $data = $PHPShopOrm->select(array('*'), array('user_id' => '=' . $obj->UsersId, 'enabled' => "='1'"), array('order' => 'datas desc'), array('limit' => 100));
    $tr = null;
    if (is_array($data)) {
        foreach ($data as $row) {

            if (PHPShopSecurity::true_num($row['product_id'])) {
                $link = '/shop/UID_' . $row['product_id'] . '.html';
                $PHPShopProduct = new PHPShopProduct($row['product_id']);
                $td1 = PHPShopText::a($link, $PHPShopProduct->getName(), $PHPShopProduct->getName(), false, false, false, 'b');
            }

            $td2 = PHPShopDate::dataV($row['datas_start']) . ' - ' . PHPShopDate::dataV($row['datas']);
            $td3 = __('Выполнено');
            $tr.=$obj->tr($td1, $td2, $td3);
        }

        $title = PHPShopText::h4(__('Архив'));
        $caption = $obj->caption(__('Наименование'), __('Период'), __('Статус'));

        $table_archive = $title . PHPShopText::table($caption . $tr, 3, 1, 'center', '100%', false, 0, 'allspecwhite', 'list table table-striped table-bordered table-hover');
    }

    if ($table . $table_archive)
        $disp = $table . $table_archive;
    else
        $disp = __("У Вас нет уведомлений.");

    $obj->set('formaTitle', __('Уведомления'));
    $obj->set('formaContent', $disp);
    $obj->ParseTemplate($obj->getValue('templates.users_page_list'));
}

?>