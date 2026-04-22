<?php

include_once dirname(__FILE__) . '/../class/Avito.php';
PHPShopObj::loadClass('category');
$TitlePage = __('Товары из Avito');

function actionStart() {
    global $PHPShopInterface, $TitlePage, $select_name, $PHPShopModules;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("Название", "40%"), array("Категория", "25%"),array("Цена", "10%"), array("Статус", "15%"));

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $PHPShopOrm->debug = false;
    $Avito = new Avito();

    // Категория БД
    $PHPShopCategoryArray = new PHPShopCategoryArray();
    $PHPShopCategory= $PHPShopCategoryArray->getArray();
    
    if($_GET['status'] == 'NEW'){
        $_GET['status']='ALL';
        $new =true;
    }
    elseif(empty($_GET['status']))
        $_GET['status']='ALL';
    
    if(empty($_GET['limit']))
        $_GET['limit']=50;

    // Товары
    $products = $Avito->getProductList($_GET['status'],PHPShopString::utf8_win1251($_GET['offer_id']),null,(int)$_GET['limit']);

    if ($Avito->type == 2) {
        $type_name = __('Арт');
        $type = 'uid';
    } else {
        $type_name = 'ID';
        $type = 'id';
    }
    
    if (is_array($products['resources'])) {
        foreach ($products['resources'] as $products_list) {
            
            // Проверка товара в локальной базе
            $PHPShopProduct = new PHPShopProduct(PHPShopString::utf8_win1251($products_list['id']), $type);
           
            if (!empty($PHPShopProduct->getName())) {
                
                // Пропускаем
                if(!empty($new))
                    continue;
                
                $data[$products_list['id']]['id'] = $products_list['id'];
                $data[$products_list['id']]['name'] = $PHPShopProduct->getName();
                $data[$products_list['id']]['status'] = 'imported';
                $data[$products_list['id']]['link'] = '?path=product&id=' . $PHPShopProduct->getParam("id");
                $data[$products_list['id']]['price'] = $products_list['price'];
                $data[$products_list['id']]['category'] = $PHPShopCategory[$PHPShopProduct->getParam("category")]['name'];
                
            } else {
                
                
                $data[$products_list['id']]['id'] = $products_list['id'];
                $data[$products_list['id']]['name'] = PHPShopString::utf8_win1251($products_list['title']);
                $data[$products_list['id']]['link'] = '?path=modules.dir.avito.import&id=' . $products_list['id'].'&status='.$_GET['status'].'&limit='.$_GET['limit'];
                $data[$products_list['id']]['status'] = 'wait';
                $data[$products_list['id']]['price'] = $products_list['price'];
                $data[$products_list['id']]['category'] = PHPShopString::utf8_win1251($products_list['category']['name']);
            }
        }
    }
    
    //$Avito->uploadFile();
    //$Avito->getAvitoID([4457]);

    $status = [
        'imported' => '<span class="text-mutted">' . __('Загружен') . '</span>',
        'wait' => '<span class="text-success">' . __('Готов к загрузке') . '</span>',
    ];
    

    if (is_array($data))
        foreach ($data as $row) {
        
            if(empty($row['name']))
                continue;

            if (!empty($row['primary_image']))
                $icon = '<img src="' . $row['primary_image'] . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
            else
               $icon=null;

            // Артикул
            if (!empty($row['id']))
                $uid = '<div class="text-muted">ID: ' . PHPShopString::utf8_win1251($row['id']) . '</div>';
            else
                $uid = null;


            $PHPShopInterface->setRow(array('name' => $row['name'], 'addon' => $uid, 'link' => $row['link'],'target'=>'_blank'), $row['category'], $row['price'], $status[$row['status']]);
        }

        
    $status_value[] = ['Все товары', 'ALL', $_GET['status']];
    $status_value[] = ['Активные', 'active', $_GET['old']];
    $status_value[] = ['Товары в архиве', 'old', $_GET['old']];
    
    $AvitoCategories = (new PHPShopOrm('phpshop_modules_avito_categories'))->getList(['*']);
    $cat_value = [['Все категории', 0, $_GET['cat']]];

        foreach ($AvitoCategories as $category) {
            $cat_value[] = [$category['name'], $category['id'], $_GET['cat']];
        }
    
    $searchforma = $PHPShopInterface->setSelect('status', $status_value, '100%',true);
    //$searchforma .= $PHPShopInterface->setSelect('cat', $cat_value, '100%',true);
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'offer_id', 'placeholder' => 'ID', 'value' => $_GET['offer_id']));

    if(empty($_GET['limit']))
        $_GET['limit']=50;
    
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'limit', 'placeholder' => 'Лимит товаров', 'value' => $_GET['limit']));
    
    if (isset($_GET['offer_id']) or isset($_GET['product_id']))
        $searchforma .= $PHPShopInterface->setButton('Сброс', 'remove', 'btn-order-cancel pull-left',false, 'javascript:window.location.replace(\'?path=modules.dir.avito.import\')');
    
    $searchforma .= $PHPShopInterface->setButton('Показать', 'search', 'btn-order-search pull-right', false, 'document.avito_search.submit()');
    $searchforma .= $PHPShopInterface->setInput("hidden", "path", $_GET['path'], "right", 70, "", "but");

    $sidebarright[] = array('title' => 'Фильтр', 'content' => $PHPShopInterface->setForm($searchforma, false, "avito_search", false, false, 'form-sidebar'));

    $PHPShopInterface->setSidebarRight($sidebarright, 2, 'hidden-xs');

    $PHPShopInterface->Compile(2);
}
