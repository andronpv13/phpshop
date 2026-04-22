<?php

include_once dirname(__FILE__) . '/../class/VkSeller.php';
PHPShopObj::loadClass('category');

function actionStart() {
    global $PHPShopInterface, $TitlePage, $select_name, $PHPShopModules;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("Иконка", "5%"), array("Название", "40%"), array("Категория", "30%"), array("Статус", "15%"));

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $PHPShopOrm->debug = false;

    $VkSeller = new VkSeller();

    // Категория БД
    $PHPShopCategoryArray = new PHPShopCategoryArray();
    $PHPShopCategory = $PHPShopCategoryArray->getArray();


    // Товары
    if ($VkSeller->model == 'API') {
        $products = $VkSeller->getProductList($_GET['limit'])['response']['items'];
    }
    $data = [];

    if (empty($_GET['status'])) {
        $_GET['status'] = 'new';
    }

    if (is_array($products)) {
        foreach ($products as $products_list) {

            // Проверка товара в локальной базе
            $PHPShopProduct = new PHPShopProduct($products_list['id'], 'export_vk_id');
            if (!empty($PHPShopProduct->getName())) {

                if ($_GET['status'] == 'new') {
                    continue;
                }

                $data[$products_list['id']]['id'] = $PHPShopProduct->getParam("id");
                $data[$products_list['id']]['name'] = $PHPShopProduct->getParam("name");
                $data[$products_list['id']]['status'] = '<span class="text-mutted">' . __('Загружен') . ' ' . PHPShopDate::get($PHPShopProduct->getParam('export_vk_task_status')) . '</span>';
                $data[$products_list['id']]['image'] = $PHPShopProduct->getParam("pic_small");
                $data[$products_list['id']]['link'] = '?path=product&id=' . $PHPShopProduct->getParam("id");
                $data[$products_list['id']]['uid'] = $PHPShopProduct->getParam("uid");
                $data[$products_list['id']]['category'] = $PHPShopCategory[$PHPShopProduct->getParam("category")]['name'];
            } else {

                $data[$products_list['id']]['id'] = $products_list['id'];
                $data[$products_list['id']]['name'] = PHPShopString::utf8_win1251($products_list['title']);
                $data[$products_list['id']]['status'] = '<span class="text-success">' . __('Готов к загрузке') . '</span>';
                $data[$products_list['id']]['image'] = $products_list['thumb_photo'];
                $data[$products_list['id']]['link'] = '?path=modules.dir.vkseller.import&id=' . $products_list['id'];
                $data[$products_list['id']]['uid'] = $products_list['sku'];
                $data[$products_list['id']]['category'] = PHPShopString::utf8_win1251($products_list['category']['parent']['name']) . ' &rarr; ' . PHPShopString::utf8_win1251($products_list['category']['name']);
            }
        }
    }

    if (is_array($data))
        foreach ($data as $row) {

            if (empty($row['name']))
                continue;

            if (!empty($row['image']))
                $icon = '<img src="' . $row['image'] . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
            else
                $icon = '<img class="media-object" src="./images/no_photo.gif">';

            // Артикул
            $uid = '<div class="text-muted">' . __('Арт') . ' ' . $row['uid'] . '</div>';

            $PHPShopInterface->setRow($icon, array('name' => $row['name'], 'addon' => $uid, 'link' => $row['link']), $row['category'], $row['status']);
        }

    $status_value[] = array(__('Новые товары'), 'new', $_GET['status']);
    $status_value[] = array(__('Все товары'), 'all', $_GET['status']);

    if (isset($_GET['limit']))
        $cancel = $PHPShopInterface->setButton('Сброс', 'remove', 'btn-order-cancel pull-left', false, 'javascript:window.location.replace(\'?path=modules.dir.vkseller.import\')');
    else
        $cancel = null;

    if (empty($_GET['limit']))
        $_GET['limit'] = 50;

    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'limit', 'placeholder' => 'Лимит товаров', 'value' => $_GET['limit']));
    $searchforma .= $PHPShopInterface->setSelect('status', $status_value, '100%');

    $searchforma .= $cancel;

    $searchforma .= $PHPShopInterface->setButton('Показать', 'search', 'btn-order-search pull-right', false, 'document.avito_search.submit()');
    $searchforma .= $PHPShopInterface->setInput("hidden", "path", $_GET['path'], "right", 70, "", "but");

    $sidebarright[] = array('title' => 'Фильтр', 'content' => $PHPShopInterface->setForm($searchforma, false, "avito_search", false, false, 'form-sidebar'));

    $PHPShopInterface->setSidebarRight($sidebarright, 2, 'hidden-xs');

    $PHPShopInterface->Compile(2);
}
