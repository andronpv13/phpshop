<?php

function setProducts_yandexcart_hook($obj, $data) {
    $add = $list = $vemdorSort = null;

    if (isset($_GET['marketplace'])) {
        if ($_GET['marketplace'] == 'webmaster') {

            // Adult
            if ($data['val']['adult'] == 1)
                $add .= '<adult>true</adult>';
        } else
            return;
    } else {

        // Характеристики
        if (!empty($obj->vendor) or ! empty($obj->param)) {

            if (is_array($data['val']['vendor_array']))
                foreach ($data['val']['vendor_array'] as $v) {

                    // Vendor
                    if ($obj->brand_array[$v[0]] != "") {
                        $add .= '<vendor>' . str_replace('&', '&amp;', $obj->brand_array[$v[0]]) . '</vendor>';
                        $vemdorSort = true;
                    }

                    // Param
                    if ($obj->param_array[$v[0]]['name'] != "" and $obj->param_array[$v[0]]['yandex_param_unit'] != "")
                        $add .= '<param name="' . str_replace('&', '&amp;', $obj->param_array[$v[0]]['param']) . '" unit="' . $obj->param_array[$v[0]]['yandex_param_unit'] . '">' . str_replace('&', '&amp;', $obj->param_array[$v[0]]['name']) . '</param>';
                    elseif ($obj->param_array[$v[0]]['param'] != "")
                        $add .= '<param name="' . str_replace('&', '&amp;', $obj->param_array[$v[0]]['param']) . '">' . str_replace('&', '&amp;', $obj->param_array[$v[0]]['name']) . '</param>';
                }
        }

        // Vendor из карточки товара
        if (empty($vemdorSort) and ! empty($data['val']['vendor_name']))
            $add .= '<vendor>' . str_replace('&', '&amp;', $data['val']['vendor_name']) . '</vendor>';

        // Подтип
        if (!empty($data['val']['group_id'])) {

            // Размер
            if (!empty($data['val']['size']))
                $add .= '<param name="Размер" unit="RU">' . $data['val']['size'] . '</param>';

            // Цвет
            if (!empty($data['val']['color']))
                $add .= '<param name="Цвет">' . $data['val']['color'] . '</param>';
        }
        
        // Oldprice
        if (!empty($data['val']['oldprice']))
            $data['xml'] = str_replace('<price>' . $data['val']['price'] . '</price>', '<price>' . (int) $obj->YandexMarket->getPrice($data['val'], $_GET['campaign']) . '</price><oldprice>' . (int) $obj->YandexMarket->getOldPrice($data['val'], $_GET['campaign']). '</oldprice>', $data['xml']);
        else $data['xml'] = str_replace('<price>' . $data['val']['price'] . '</price>', '<price>' . (int) $obj->YandexMarket->getPrice($data['val'], $_GET['campaign']) . '</price>', $data['xml']);

        // description template
        if (!empty($obj->yandex_module_options['description_template'])) {
            $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
            $obj->yandex_categories = array_column($orm->getList(['id', 'name', 'parent_to'], false, false, ['limit' => 100000]), null, 'id');
            $data['xml'] = str_replace(
                    '<description>' . $data['val']['description'] . '</description>', '<description><![CDATA[' . 
                            yandexReplaceDescriptionVariables($obj, $data['val'], $obj->yandex_module_options['description_template'])
                     . ']]></description>', $data['xml']
            );
        }

        // Доставка
        if ($data['val']['delivery'] == 1)
            $add .= '<delivery>true</delivery>';
        else
            $add .= '<delivery>false</delivery>';

        $i = 0;
        $delivery = $GLOBALS['delivery'];
        if (is_array($delivery)) {
            foreach ($delivery as $row) {
                if ($i < 5) {
                    if ($data['val']['p_enabled'] == 'true')
                        $list .= '<option cost="' . $row['price'] . '" days="' . $row['yandex_day_min'] . '-' . $row['yandex_day'] . '" order-before="' . $row['yandex_order_before'] . '"/>';
                    else
                        $list .= '<option cost="' . $row['price'] . '" days=""/>';
                } else
                    continue;
                $i++;
            }
        }

        if (!empty($list))
            $add .= '<delivery-options>' . $list . '</delivery-options>';

        // Pickup
        if ($data['val']['pickup'] == 1)
            $add .= '<pickup>true</pickup>';
        else
            $add .= '<pickup>false</pickup>';

        // Гарантия
        if ($data['val']['manufacturer_warranty'] == 1)
            $add .= '<manufacturer_warranty>true</manufacturer_warranty>';

        // Страна
        if (!empty($data['val']['country_of_origin']))
            $add .= '<country_of_origin>' . $data['val']['country_of_origin'] . '</country_of_origin>';

        // Штрихкод
        if (!empty($data['val']['barcode']))
            $add .= '<barcode>' . $data['val']['barcode'] . '</barcode>';

        // Adult
        if ($data['val']['adult'] == 1)
            $add .= '<adult>true</adult>';

        // market-sku
        if (!empty($data['val']['market_sku']))
            $add .= '<market-sku>' . $data['val']['market_sku'] . '</market-sku>';

        // min-quantity
        if (!empty($data['val']['yandex_min_quantity']))
            $add .= '<min-quantity>' . $data['val']['yandex_min_quantity'] . '</min-quantity>';

        // step-quantity
        if (!empty($data['val']['yandex_step_quantity']))
            $add .= '<step-quantity>' . $data['val']['yandex_step_quantity'] . '</step-quantity>';
        
        // Категория в маркете
        if (!empty($data['val']['market_category_id']))
            $add .= '<market_category_id>' . $data['val']['market_category_id'] . '</market_category_id>';

        // Склад
        $items = (int) $obj->YandexMarket->getWarehouse($data['val'], $_GET['campaign']);
        $add .= '<count>' . $items . '</count>';


        // Компания, которая произвела товар
        if (!empty($data['val']['manufacturer']))
            $add .= '<manufacturer>' . $data['val']['manufacturer'] . '</manufacturer>';

        // vendorCode
        if (!empty($data['val']['vendor_code']))
            $add .= '<vendorCode>' . $data['val']['vendor_code'] . '</vendorCode>';

        // condition
        if ($data['val']['condition'] > 1) {

            $condition = [null, null, 'preowned', 'showcasesample', 'reduction'];
            $quality = [null, null, 'perfect', 'excellent', 'good'];

            $add .= '<condition type="' . $condition[$data['val']['condition']] . '"><quality>' . $quality[$data['val']['quality']] . '</quality><reason>' . $data['val']['condition_reason'] . '</reason></condition>';
        }

        // Срок годности
        if (!empty($data['val']['yandex_service_life_days']))
            $add .= '<period-of-validity-days>' . $data['val']['yandex_service_life_days'] . '</period-of-validity-days>';

        // Ключ обновления артикул
        if ($obj->yandex_module_options['type'] == 2) {
            $data['xml'] = str_replace('<offer id="' . $data['val']['id'] . '"', '<offer id="' . $data['val']['uid'] . '"', $data['xml']);
        }
    }

    if (!empty($add))
        $data['xml'] = str_replace('</offer>', $add . '</offer>', $data['xml']);

    return $data['xml'];
}

function setDelivery_yandexcart_hook($obj, $data) {

    if (isset($_GET['marketplace'])) {
        return;
    }

    // Доставка
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['delivery']);
    $delivery = $PHPShopOrm->select(array('price', 'yandex_day', 'yandex_day_min', 'yandex_order_before'), array('enabled' => "='1'", 'is_folder' => "!='1'", 'yandex_enabled' => "='2'", 'yandex_check' => "='2'", 'yandex_type' => "='1'"), false, array('limit' => 300));
    $GLOBALS['delivery'] = $delivery;

    /*
      if (!empty($delivery))
      $data['xml'] = str_replace('<local_delivery_cost>' . $data['val']['price'] . '</local_delivery_cost>', '<delivery-options/>', $data['xml']); */

    // Бренды
    $PHPShopOrm = new PHPShopOrm();
    $PHPShopOrm->sql = 'SELECT b.id, b.name FROM ' . $GLOBALS['SysValue']['base']['sort_categories'] . ' AS a LEFT JOIN ' . $GLOBALS['SysValue']['base']['sort'] . ' AS b ON a.id = b.category where a.brand="1" limit 1000';
    $vendor = $PHPShopOrm->select();
    if (is_array($vendor)) {
        $obj->vendor = true;
        foreach ($vendor as $brand) {
            $obj->brand_array[$brand['id']] = $brand['name'];
        }
    }

    // Параметры
    $PHPShopOrm = new PHPShopOrm();
    $PHPShopOrm->sql = 'SELECT a.yandex_param_unit,a.name as param, b.id, b.name FROM ' . $GLOBALS['SysValue']['base']['sort_categories'] . ' AS a LEFT JOIN ' . $GLOBALS['SysValue']['base']['sort'] . ' AS b ON a.id = b.category where a.yandex_param="2" and a.brand!="1" limit 10000';
    $param = $PHPShopOrm->select();
    if (is_array($param)) {
        $obj->param = true;
        foreach ($param as $par) {
            $obj->param_array[$par['id']] = $par;
        }
    }

    return $data['xml'];
}

function PHPShopYml_yandexcart_hook($obj) {

    if (isset($_GET['marketplace'])) {
        return;
    }

    // Настройки модуля
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['yandexcart']['yandexcart_system']);
    $obj->yandex_module_options = $PHPShopOrm->select();
    $options = unserialize($obj->yandex_module_options['options']);

    include_once dirname(__FILE__) . '/../class/YandexMarket.php';
    $obj->YandexMarket = new YandexMarket();
    
    $_GET['image_source']=true;

    // Пароль
    if (!empty($obj->yandex_module_options['password']))
        if ($_GET['pas'] != $obj->yandex_module_options['password'])
            exit('Login error!');

    // Колонка цен
    if ($options['price'] > 1)
        $obj->price = 'price' . $options['price'];

    if (isset($obj->yandex_module_options['use_params'])) {
        $obj->vendor = (bool) $obj->yandex_module_options['use_params'];
    }
}

function yandexReplaceDescriptionVariables($obj, $product, $template) {
    if (stripos($template, '@Content@') !== false) {
        $template = str_replace('@Content@', $product['raw_content'], $template);
    }
    if (stripos($template, '@Description@') !== false) {
        $template = str_replace('@Description@', $product['raw_description'], $template);
    }
    if (stripos($template, '@Attributes@') !== false) {
        $template = str_replace('@Attributes@', yandexSortTable($product), $template);
    }
    if (stripos($template, '@Catalog@') !== false) {
        $template = str_replace('@Catalog@', $obj->yandex_categories[$product['category']]['name'], $template);
    }
    if (stripos($template, '@Product@') !== false) {
        $template = str_replace('@Product@', $product['name'], $template);
    }
    if (stripos($template, '@Subcatalog@') !== false) {
        $getPath = function ($categories, $id, $path = []) use(&$getPath) {
            if (!empty($id)) {
                if (isset($categories[$id])) {
                    $path[] = $categories[$id];
                    if (!empty($categories[$id]['parent_to']))
                        return $getPath($categories, $categories[$id]['parent_to'], $path);
                }
            }

            return $path;
        };

        $path = $getPath($obj->yandex_categories, $obj->yandex_categories[$product['category']]['id']);

        $subcat = '';
        array_shift($path);
        foreach ($path as $subcategory) {
            $subcat .= $subcategory['name'] . ' - ';
        }


        $subcat = substr($subcat, 0, strlen($subcat) - 3);

        $template = str_replace('@Subcatalog@', $subcat, $template);
    }

    return $template;
}

function yandexSortTable($product) {

    $category = new PHPShopCategory((int) $product['category']);

    $sort = $category->unserializeParam('sort');
    $dis = $sortCat = $sortValue = null;
    $arrayVendorValue = [];

    if (is_array($sort))
        foreach ($sort as $v) {
            $sortCat .= (int) $v . ',';
        }

    if (!empty($sortCat)) {

        // Массив имен характеристик
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
        $arrayVendor = array_column($PHPShopOrm->getList(['*'], ['id' => sprintf(' IN (%s 0)', $sortCat)], ['order' => 'num']), null, 'id');

        if (is_array($product['vendor_array']))
            foreach ($product['vendor_array'] as $v) {
                foreach ($v as $value)
                    if (is_numeric($value))
                        $sortValue .= (int) $value . ',';
            }

        if (!empty($sortValue)) {

            // Массив значений характеристик
            $PHPShopOrm = new PHPShopOrm();
            $result = $PHPShopOrm->query("select * from " . $GLOBALS['SysValue']['base']['sort'] . " where id IN ( $sortValue 0) order by num");
            while (@$row = mysqli_fetch_array($result)) {
                $arrayVendorValue[$row['category']]['name'][$row['id']] = $row['name'];
                $arrayVendorValue[$row['category']]['id'][] = $row['id'];
            }

            if (is_array($arrayVendor))
                foreach ($arrayVendor as $idCategory => $value) {

                    if (!empty($arrayVendorValue[$idCategory]['name'])) {
                        if (!empty($value['name'])) {
                            $arr = array();
                            foreach ($arrayVendorValue[$idCategory]['id'] as $valueId) {
                                $arr[] = $arrayVendorValue[$idCategory]['name'][(int) $valueId];
                            }

                            $sortValueName = implode(', ', $arr);

                            $dis .= PHPShopText::li($value['name'] . ': ' . $sortValueName, null, '');
                        }
                    }
                }

            return PHPShopText::ul($dis, '');
        }
    }
}

$addHandler = array
    (
    'setProducts' => 'setProducts_yandexcart_hook',
    'setDelivery' => 'setDelivery_yandexcart_hook',
    '__construct' => 'PHPShopYml_yandexcart_hook'
);
?>