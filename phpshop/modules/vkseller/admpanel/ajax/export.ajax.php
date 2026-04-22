<?php

session_start();
$_classPath = "../../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass(array("base"));

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopBase->chekAdmin();

$PHPShopSystem = new PHPShopSystem();

include_once '../../class/VkSeller.php';
$VkSeller = new VkSeller();

if ($VkSeller->model == 'API') {

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);

    // Всего товаров 
    if (empty($_POST['end']) and empty($_POST['stop']))
        $end = $PHPShopBase->getNumRows('products', "where export_vk='1' and export_vk_task_status=0");
    else
        $end = (int) $_POST['end'];

    $start = (int) $_POST['start'];
    $count = (int) $_POST['count'];

    // Стоп
    if (!empty($_POST['stop'])) {
        $PHPShopOrm->update(['export_vk_task_status_new' => 0], ['export_vk_id' => '=0']);
        $return = array("success" => 'done');
    } else
        $row = $PHPShopOrm->getOne(array('*'), array('export_vk' => "='1'", 'export_vk_task_status' => "=0"));

    if (is_array($row)) {

        // Фото главное
        $row['main_photo_id'] = $VkSeller->sendImages($row['id'], $row['pic_big'])['response'][0]['id'];

        // Фото дополнительные
        $images = $VkSeller->getImages($row['id'], $row['pic_big']);
        if (is_array($images))
            foreach ($images as $image) {

                $photo_result = $VkSeller->sendImages($row['id'], $image)['response'][0]['id'];

                if (!empty($photo_result))
                    $photo_ids[] = $photo_result;
            }

        if (is_array($photo_ids))
            $row['photo_ids'] = implode(",", $photo_ids);
        unset($photo_ids);
        $export_vk_id = $result = null;

        if (!empty($row['main_photo_id'])) {
            $export_vk = $VkSeller->sendProduct($row);
            $export_vk_id = $export_vk['response']['market_item_id'];
        } else {
            $export_vk['error']['error_msg'] = 'Format images are not supported';
        }

        // Товар выгрузился
        if (!empty($export_vk_id)) {
            $PHPShopOrm->update(['export_vk_task_status_new' => time(), 'export_vk_id_new' => $export_vk_id], ['id' => '=' . (int) $row['id']]);
            $VkSeller->clean_log($row['id']);
            $result = true;
            $count++;
        }
        // Ошибка
        elseif (!empty($export_vk['error']['error_msg'])) {
            $PHPShopOrm->update(['export_vk_task_status_new' => time()], ['id' => '=' . (int) $row['id']]);
            $VkSeller->export_log($export_vk['error']['error_msg'], $row['id'], $row['name']);
            $result = true;
        }


        $start++;
        $bar = round($start * 100 / $end);

        if ($bar < 100) {
            $return = array("success" => $result, 'start' => (int) $start, 'end' => (int) $end, 'bar' => (int) $bar, 'count' => (int) $count, 'id' => (int) $row['id']);
        } else {
            $PHPShopOrm->update(['export_vk_task_status_new' => 0], ['export_vk_id' => '=0']);
            $return = array("success" => 'done', 'bar' => (int) $bar, 'count' => (int) $count, 'id' => (int) $row['id']);
        }
    } else
        $return = array("success" => 'done', 'bar' => 100, 'count' => (int) $count);
}


if ($return) {
    header("Content-Type: application/json");
    echo json_encode($return);
}
