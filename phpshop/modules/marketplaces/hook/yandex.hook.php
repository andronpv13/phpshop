<?php

include_once dirname(__DIR__) . '/class/Marketplaces.php';

function setProducts_marketplaces_hook($obj, $data) {
    $add = $list = $vemdorSort = null;

    if (!Marketplaces::isMarketplace()) {
        return;
    }

    // Характеристики
    if (!empty($obj->vendor)) {

        if (is_array($data['val']['vendor_array']))
            foreach ($data['val']['vendor_array'] as $v) {
                // Vendor
                if ($obj->brand_array[$v[0]] != "") {
                    $add .= '<vendor>' . str_replace('&', '&amp;', $obj->brand_array[$v[0]]) . '</vendor>';
                    $vemdorSort = true;
                }
            }
    }

    // Vendor из карточки товара
    if (empty($vemdorSort) and ! empty($data['val']['vendor_name']))
        $add .= '<vendor>' . str_replace('&', '&amp;', $data['val']['vendor_name']) . '</vendor>';

    // Подтип
    if (!empty($data['val']['group_id'])) {

        if (Marketplaces::isAliexpress()) {

            // Размер
            if (!empty($data['val']['size']))
                $add .= '<size>' . $data['val']['size'] . '</size>';

            // Цвет
            if (!empty($data['val']['color']))
                $add .= '<cus_skucolor>' . $data['val']['color'] . '</cus_skucolor>';
        } else {

            // Размер
            if (!empty($data['val']['size']))
                $add .= '<param name="Размер" unit="RU">' . $data['val']['size'] . '</param>';

            // Цвет
            if (!empty($data['val']['color']))
                $add .= '<param name="Цвет">' . $data['val']['color'] . '</param>';
        }
    }

    // Oldprice
    if (!empty($data['val']['oldprice']))
        $data['xml'] = str_replace('<price>' . $data['val']['price'] . '</price>', '<price>' . $data['val']['price'] . '</price><oldprice>' . $data['val']['oldprice'] . '</oldprice>', $data['xml']);

    // description template
    if (!empty($obj->marketplaces_options['description_template'])) {
        $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
        $obj->marketplaces_categories = array_column($orm->getList(['id', 'name', 'parent_to'], false, false, ['limit' => 100000]), null, 'id');

        $data['xml'] = str_replace(
                '<description>' . $data['val']['description'] . '</description>', '<description><![CDATA[' .
                marketplacesReplaceDescriptionVariables($obj, $data['val'], $obj->marketplaces_options['description_template'])
                . ']]></description>', $data['xml']
        );
    }

    $options = unserialize($obj->marketplaces_options['options']);

    // price columns
    $price = $data['val']['price'];
    $fee = 0;
    $markup = 0;

    // Цена Сбермаркет
    if (Marketplaces::isSbermarket()) {
        if (!empty($data['val']['price_sbermarket'])) {
            $price = $data['val']['price_sbermarket'];
        } elseif (isset($options['price_sbermarket']) && (int) $options['price_sbermarket'] > 1 && !empty($data['val']['price' . (int) $options['price_sbermarket']])) {
            $price = $data['val']['price' . (int) $options['price_sbermarket']];
        }
        if (isset($options['price_sbermarket_fee']) && (float) $options['price_sbermarket_fee'] > 0) {
            $fee = (float) $options['price_sbermarket_fee'];
            $markup = (int) $options['price_sbermarket_markup'];
        }
    }

    // Цена СДЭК.МАРКЕТ
    if (Marketplaces::isCdek()) {
        if (!empty($data['val']['price_cdek'])) {
            $price = $data['val']['price_cdek'];
        } elseif (isset($options['price_cdek']) && (int) $options['price_cdek'] > 1 && !empty($data['val']['price' . (int) $options['price_cdek']])) {
            $price = $data['val']['price' . (int) $options['price_cdek']];
        }
        if (isset($options['price_cdek_fee']) && (float) $options['price_cdek_fee'] > 0) {
            $fee = (float) $options['price_cdek_fee'];
            $markup = (int) $options['price_cdek_markup'];
        }
    }

    // Цена AliExpress
    if (Marketplaces::isAliexpress()) {
        if (!empty($data['val']['price_aliexpress'])) {
            $price = $data['val']['price_aliexpress'];
        } elseif (isset($options['price_ali']) && (int) $options['price_ali'] > 1 && !empty($data['val']['price' . (int) $options['price_ali']])) {
            $price = $data['val']['price' . (int) $options['price_ali']];
        }
        if (isset($options['price_ali_fee']) && (float) $options['price_ali_fee'] > 0) {
            $fee = (float) $options['price_ali_fee'];
            $markup = (int) $options['price_ali_markup'];
        }

        if (!empty($data['val']['length']))
            $add .= '<length>' . $data['val']['length'] . '</length>';
        if (!empty($data['val']['width']))
            $add .= '<width>' . $data['val']['width'] . '</width>';
        if (!empty($data['val']['height']))
            $add .= '<height>' . $data['val']['height'] . '</height>';
    }

    // Наценка руб.
    $price = $price + (int) $markup;

    // Наценка %
    if ($fee > 0) {
        $price = $price + ($price * $fee / 100);
    }

    $data['xml'] = str_replace('<price>' . $data['val']['price'] . '</price>', '<price>' . $price . '</price>', $data['xml']);

    // Доставка
    if ($data['val']['delivery'] == 1)
        $add .= '<delivery>true</delivery>';
    else
        $add .= '<delivery>false</delivery>';

    if (!empty($list))
        $add .= '<delivery-options>' . $list . '</delivery-options>';

    // Pickup
    if ($data['val']['pickup'] == 1)
        $add .= '<pickup>true</pickup>';
    else
        $add .= '<pickup>false</pickup>';

    // Store
    if ($data['val']['store'] == 1)
        $add .= '<store>true</store>';
    else
        $add .= '<store>false</store>';

    // Notes
    if (!empty($data['val']['sales_notes']))
        $add .= '<sales_notes>' . $data['val']['sales_notes'] . '</sales_notes>';

    // Гарантия
    if ($data['val']['manufacturer_warranty'] == 1)
        $add .= '<manufacturer_warranty>true</manufacturer_warranty>';

    // Страна
    if (!empty($data['val']['country_of_origin']))
        $add .= '<country_of_origin>' . $data['val']['country_of_origin'] . '</country_of_origin>';

    // Модель
    if (!empty($data['val']['model']))
        $add .= '<model>' . $data['val']['model'] . '</model>';

    // Штрихкод
    if (!empty($data['val']['barcode']))
        $add .= '<barcode>' . $data['val']['barcode'] . '</barcode>';

    // Adult
    if ($data['val']['adult'] == 1)
        $add .= '<adult>true</adult>';

    // min-quantity
    if (!empty($data['val']['yandex_min_quantity']))
        $add .= '<min-quantity>' . $data['val']['yandex_min_quantity'] . '</min-quantity>';

    // step-quantity
    if (!empty($data['val']['yandex_step_quantity']))
        $add .= '<step-quantity>' . $data['val']['yandex_step_quantity'] . '</step-quantity>';

    // Компания, которая произвела товар
    if (!empty($data['val']['manufacturer']))
        $add .= '<manufacturer>' . $data['val']['manufacturer'] . '</manufacturer>';

    // vendorCode
    if (!empty($data['val']['vendor_code']))
        $add .= '<vendorCode>' . $data['val']['vendor_code'] . '</vendorCode>';

    // condition
    if ($data['val']['condition'] > 1 and ! empty($data['val']['condition_reason'])) {

        if ($data['val']['condition'] == 2)
            $condition = 'likenew';
        else
            $condition = 'used';

        $add .= '<condition type="' . $condition . '"><reason>' . $data['val']['condition_reason'] . '</reason></condition>';
    }

    if (Marketplaces::isCdek()) {
        $add .= '<amount>' . $data['val']['items'] . '</amount>';
        $ndsEnabled = $obj->PHPShopSystem->getParam('nds_enabled');
        $nds = $obj->PHPShopSystem->getParam('nds');
        if (empty($ndsEnabled)) {
            $ndsValue = 'NO_VAT';
        } else {
            $ndsValue = 'VAT_' . $nds;
        }

        $add .= '<vat>' . $ndsValue . '</vat>';
    }

    if (Marketplaces::isAliexpress()) {
        $add .= '<quantity>' . $data['val']['items'] . '</quantity>';
        if (!empty($data['val']['uid'])) {
            $add .= '<sku_code>' . $data['val']['uid'] . '</sku_code>';
        }
    }

    if (Marketplaces::isSbermarket())
        $add .= '<outlets><outlet id="1" instock="' . $data['val']['items'] . '"></outlet></outlets>';

    if (Marketplaces::isCdek())
        $add .= '<count>' . $data['val']['items'] . '</count>';

    if (Marketplaces::isRetailCRM()) {
        $add .= '<xmlId>' . $data['val']['uid'] . '</xmlId>';
    }

    if (!empty($add))
        $data['xml'] = str_replace('</offer>', $add . '</offer>', $data['xml']);

    return $data['xml'];
}

function setDelivery_marketplaces_hook($obj, $data) {

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

    return $data['xml'];
}

function PHPShopYml_marketplaces_hook($obj) {

    // Настройки модуля
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['marketplaces']['marketplaces_system']);
    $obj->marketplaces_options = $PHPShopOrm->select();

    // Пароль
    if (!empty($obj->marketplaces_options['password']))
        if ($_GET['pas'] != $obj->marketplaces_options['password'])
            exit('Login error!');

    if (isset($obj->marketplaces_options['use_params'])) {
        $obj->vendor = (bool) $obj->marketplaces_options['use_params'];
    }
}

function marketplacesReplaceDescriptionVariables($obj, $product, $template) {
    if (stripos($template, '@Content@') !== false) {
        $template = str_replace('@Content@', $product['raw_content'], $template);
    }
    if (stripos($template, '@Description@') !== false) {
        $template = str_replace('@Description@', $product['raw_description'], $template);
    }
    if (stripos($template, '@Attributes@') !== false) {
        $template = str_replace('@Attributes@', marketplacesSortTable($product), $template);
    }
    if (stripos($template, '@Catalog@') !== false) {
        $template = str_replace('@Catalog@', $obj->marketplaces_categories[$product['category']]['name'], $template);
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

        $path = $getPath($obj->marketplaces_categories, $obj->marketplaces_categories[$product['category']]['id']);

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

function marketplacesSortTable($product) {

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

$addHandler = [
    'setProducts' => 'setProducts_marketplaces_hook',
    'setDelivery' => 'setDelivery_marketplaces_hook',
    '__construct' => 'PHPShopYml_marketplaces_hook'
];
?>