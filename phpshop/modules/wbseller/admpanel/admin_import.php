<?php

include_once dirname(__FILE__) . '/../class/WbSeller.php';
PHPShopObj::loadClass('category');
$TitlePage = __('Товары из  Wildberries');

function actionStart() {
    global $PHPShopInterface, $TitlePage, $select_name;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("Иконка", "5%"), array("Название", "40%"), array("Категория", "30%"), array("Статус", "15%"));

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $PHPShopOrm->debug = false;

    $data = $PHPShopOrm->select(array('*'), array('export_wb' => "='1'"), array('order' => 'DESC'), array('limit' => 10000));
    $WbSeller = new WbSeller();

    // Категория БД
    $PHPShopCategoryArray = new PHPShopCategoryArray();
    $PHPShopCategory = $PHPShopCategoryArray->getArray();

    if ($_GET['status'] == 'NEW') {
        $_GET['status'] = 'ALL';
        $new = true;
    }

    // Товары
    $products = $WbSeller->getProductList($_GET['search'], $_GET['limit']);

    if ($WbSeller->type == 2) {
        $type_name = __('Арт');
        $type = 'uid';
    } else {
        $type_name = 'ID';
        $type = 'id';
    }

    if (is_array($products['cards'])) {
        foreach ($products['cards'] as $products_list) {

            // Проверка товара в локальной базе
            $PHPShopProduct = new PHPShopProduct(PHPShopString::utf8_win1251($products_list['vendorCode']), $type);
            if (!empty($PHPShopProduct->getName())) {

                // Пропускаем
                if (!empty($new))
                    continue;

                //$data[$products_list['product_id']] = $PHPShopProduct->getArray();
                $data[$products_list['nmID']]['status'] = 'imported';
                $data[$products_list['nmID']]['vendorCode'] = $products_list['vendorCode'];
                $data[$products_list['nmID']]['image'] = $PHPShopProduct->getImage();
                $data[$products_list['nmID']]['link'] = '?path=product&id=' . $PHPShopProduct->getParam("id");
                $data[$products_list['nmID']]['name'] = $PHPShopProduct->getParam("name");
                $data[$products_list['nmID']]['category'] = $PHPShopCategory[$PHPShopProduct->getParam("category")]['name'];
            } else {

                   
                $data[$products_list['nmID']]['category']=$products_list['subjectName'];  
                $data[$products_list['nmID']]['name'] = PHPShopString::utf8_win1251($products_list['title']);
                $data[$products_list['nmID']]['status'] = 'wait';
                $data[$products_list['nmID']]['link'] = '?path=modules.dir.wbseller.import&id=' . $products_list['vendorCode'];
                $data[$products_list['nmID']]['image']=$products_list['photos'][0]['tm'];
                $data[$products_list['nmID']]['vendorCode'] = $products_list['vendorCode'];
            }
        }
    }

    $status = [
        'imported' => '<span class="text-mutted">' . __('Загружен') . '</span>',
        'wait' => '<span class="text-success">' . __('Готов к загрузке') . '</span>',
    ];

    if (is_array($data))
        foreach ($data as $row) {


            if (empty($row['name']))
            continue;


            if (!empty($row['image']))
                $icon = '<img src="' . $row['image'] . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
            else
                $icon = '<img class="media-object" src="./images/no_photo.gif">';

            // Артикул
            if (!empty($row['vendorCode']))
                $uid = '<div class="text-muted">' . $type_name . ' ' . PHPShopString::utf8_win1251($row['vendorCode']) . '</div>';
            else
                $uid = '<div class="text-muted">' . $type_name . ' ' . PHPShopString::utf8_win1251($row['uid']) . '</div>';


            $PHPShopInterface->setRow($icon, array('name' => $row['name'], 'addon' => $uid, 'link' => $row['link']), PHPShopString::utf8_win1251($row['category']), $status[$row['status']]);
        }


    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'search', 'placeholder' => 'Артикул', 'value' => $_GET['search']));

    if (empty($_GET['limit']))
        $_GET['limit'] = 50;

    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'limit', 'placeholder' => 'Лимит товаров', 'value' => $_GET['limit']));

    if (isset($_GET['search']))
        $searchforma .= $PHPShopInterface->setButton('Сброс', 'remove', 'btn-order-cancel pull-left', false, 'javascript:window.location.replace(\'?path=modules.dir.wbseller.import\')');

    $searchforma .= $PHPShopInterface->setButton('Показать', 'search', 'btn-order-search pull-right', false, 'document.avito_search.submit()');
    $searchforma .= $PHPShopInterface->setInput("hidden", "path", $_GET['path'], "right", 70, "", "but");

    $sidebarright[] = array('title' => 'Фильтр', 'content' => $PHPShopInterface->setForm($searchforma, false, "avito_search", false, false, 'form-sidebar'));

    $PHPShopInterface->setSidebarRight($sidebarright, 2, 'hidden-xs');

    $PHPShopInterface->Compile(2);
}
