<?php

include_once dirname(__FILE__) . '/../class/CDEKFulfillment.php';
PHPShopObj::loadClass('category');
$TitlePage = __('Товары из CDEK');

function actionStart() {
    global $PHPShopInterface, $TitlePage, $select_name, $PHPShopModules;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("Иконка", "5%"), array("Название", "40%"), array("Категория", "30%"), array("Статус", "15%"));

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $PHPShopOrm->debug = false;
    $CDEKFulfillment = new CDEKFulfillment();

    // Категория Ozon
    //$PHPShopOrmCat = new PHPShopOrm($PHPShopModules->getParam("base.cdekfulfillment.cdekfulfillment_categories"));
    
    // Категория БД
    $PHPShopCategoryArray = new PHPShopCategoryArray();
    $PHPShopCategory= $PHPShopCategoryArray->getArray();
    
    if($_GET['status'] == 'NEW'){
        $_GET['status']='ALL';
        $new =true;
    }

    // Товары
    $products = $CDEKFulfillment->getProductList()['_embedded']['product_offer'];

    if ($CDEKFulfillment->type == 2) {
        $type_name = __('Арт');
        $type = 'uid';
    } else {
        $type_name = 'ID';
        $type = 'id';
    }

   
    if (is_array($products)) {
        foreach ($products as $products_list) {

            // Проверка товара в локальной базе
            $PHPShopProduct = new PHPShopProduct(PHPShopString::utf8_win1251($products_list['id']), $type);
            if (!empty($PHPShopProduct->getName())) {
                
                // Пропускаем
                if(!empty($new))
                    continue;
                
                $data[$products_list['id']] = $PHPShopProduct->getArray();
                $data[$products_list['id']]['name'] = PHPShopString::win_utf8($PHPShopProduct->getName());
                $data[$products_list['id']]['status'] = 'imported';
                $data[$products_list['id']]['offer_id'] = $products_list['offer_id'];
                $data[$products_list['id']]['image'] = $PHPShopProduct->getImage();
                $data[$products_list['id']]['link'] = '?path=product&id=' . $PHPShopProduct->getParam("id");
                
                $data[$products_list['id']]['category'] = $PHPShopCategory[$PHPShopProduct->getParam("category")]['name'];
                
            } else {
                
                $data[$products_list['id']] = $products_list;
                $data[$products_list['id']]['status'] = 'wait';
                $data[$products_list['id']]['link'] ='?path=modules.dir.cdekfulfillment.import&id='.$products_list['id'];

            }
        }
    }

    $status = [
        'imported' => '<span class="text-mutted">' . __('Загружен') . '</span>',
        'wait' => '<span class="text-success">' . __('Готов к загрузке') . '</span>',
    ];
    
   
    if (is_array($data))
        foreach ($data as $row) {
        
            if(empty($row['name']))
                continue;

            if (!empty($row['image']))
                $icon = '<img src="' . $row['image'] . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
            else
               $icon=$icon = '<img src="./images/no_photo.gif" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';

            // Артикул
            if (!empty($row['extId']))
                $uid = '<div class="text-muted">' . $type_name . ' ' . PHPShopString::utf8_win1251($row['extId']) . '</div>';
            else
                $uid = null;


            $PHPShopInterface->setRow($icon, array('name' => PHPShopString::utf8_win1251($row['name']), 'addon' => $uid, 'link' => $row['link']), $row['category'], $status[$row['status']]);
        }

        

    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'offer_id', 'placeholder' => 'Артикул', 'value' => $_GET['offer_id']));
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'product_id', 'placeholder' => 'CDEK ID', 'value' => $_GET['product_id']));
    
    if(empty($_GET['limit']))
        $_GET['limit']=50;
    
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'limit', 'placeholder' => 'Лимит товаров', 'value' => $_GET['limit']));
    
    if (isset($_GET['offer_id']) or isset($_GET['product_id']))
        $searchforma .= $PHPShopInterface->setButton('Сброс', 'remove', 'btn-order-cancel pull-left',false, 'javascript:window.location.replace(\'?path=modules.dir.ozonseller.import\')');
    
    $searchforma .= $PHPShopInterface->setButton('Показать', 'search', 'btn-order-search pull-right', false, 'document.avito_search.submit()');
    $searchforma .= $PHPShopInterface->setInput("hidden", "path", $_GET['path'], "right", 70, "", "but");

    $sidebarright[] = array('title' => 'Фильтр', 'content' => $PHPShopInterface->setForm($searchforma, false, "avito_search", false, false, 'form-sidebar'));

    $PHPShopInterface->setSidebarRight($sidebarright, 2, 'hidden-xs');

    $PHPShopInterface->Compile(2);
}
