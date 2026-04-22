<?php

/**
 * Вывод сортировок для товаров таблицей
 * @author PHPShop Software
 * @version 2.0
 * @package PHPShopCoreFunction
 * @param obj $obj объект класса
 * @return mixed
 */
function sort_table($obj, $row) {
    global $SysValue;

    $sort = $obj->PHPShopCategory->unserializeParam('sort');
    $vendor_array = unserialize($row['vendor_array']);
    $dis = $sortCat = $sortValue = null;
    $arrayVendorValue = array();
    $odnotip = $row['odnotip'];

    if (is_array($sort))
        foreach ($sort as $v) {
            $sortCat .= intval($v) . ',';
        }

    if (!empty($sortCat)) {

        // Массив имен характеристик
        $PHPShopOrm = new PHPShopOrm();
        $PHPShopOrm->debug = $obj->debug;
        $result = $PHPShopOrm->query("select * from " . $SysValue['base']['sort_categories'] . " where id IN ( $sortCat 0) order by num");
        while (@$row = mysqli_fetch_assoc($result)) {
            $arrayVendor[$row['id']] = $row;
        }

        if (is_array($vendor_array))
            foreach ($vendor_array as $v) {
                foreach ($v as $value)
                    if (is_numeric($value))
                        $sortValue .= intval($value) . ',';
            }

        // SeoUrl настройки
        if (class_exists('PHPShopSeourlOption')) {
            $seourl_option = (new PHPShopSeourlOption())->getArray();
        }
        else {
            $seourl_option["seo_brands_enabled"]=1;
        }

        if (!empty($sortValue)) {

            // Массив значений характеристик
            $PHPShopOrm = new PHPShopOrm();
            $PHPShopOrm->debug = $obj->debug;
            $result = $PHPShopOrm->query("select * from " . $SysValue['base']['sort'] . " where id IN ( $sortValue 0) order by num");
            while ($row = mysqli_fetch_array($result)) {

                // Определение цвета
                if ($row['name'][0] == '#')
                    $arrayVendorValue[$row['category']]['name'][$row['id']] = '  <div class="sort-color" style="width:25px;height:25px;background:' . $row['name'] . ';float:left;padding:3px;margin:3px;"></div>  ';
                else
                    $arrayVendorValue[$row['category']]['name'][$row['id']] = $row['name'];

                if ($seourl_option["seo_brands_enabled"] == 2)
                    $arrayVendorValue[$row['category']]['seo_name'][$row['id']] = $row['sort_seo_name'];

                $arrayVendorValue[$row['category']]['id'][] = $row['id'];

                if (!empty($row['page'])) {
                    $arrayVendorValue[$row['category']]['page'][$row['id']] = $row['page'];
                }

                // Бренд
                if ($arrayVendor[$row['category']]['brand']) {
                    $obj->set('brandIcon', $row['icon']);
                    $obj->set('brandName', $row['name']);
                    if ($row['page']) {
                        $PHPShopOrm->clean();
                        $res = $PHPShopOrm->query("select content from " . $SysValue['base']['page'] . " where link = '$row[page]' LIMIT 1");
                        $page = mysqli_fetch_array($res);
                        $desc = stripslashes($page['content']);
                    }

                    // SEOUrl
                    if (!empty($row['sort_seo_name']) and ! empty($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'])) {
                        if ($seourl_option["seo_brands_enabled"] == 2)
                            $obj->set('brandPageLink', $GLOBALS['SysValue']['dir']['dir'].'/brand/' . $row['sort_seo_name'] . '.html');
                        else
                            $obj->set('brandPageLink', '/selection/?v[' . $row['category'] . ']=' . $row['id']);
                    } else
                        $obj->set('brandPageLink', '/selection/?v[' . $row['category'] . ']=' . $row['id']);

                    if (!empty($desc)) {
                        $obj->set('brandDescr', $desc);
                    } else {
                        $obj->set('brandDescr', '');
                    }

                    $obj->set('brandUidDescription', ParseTemplateReturn('product/brand_uid_description.tpl'), true);
                }
            }


            // Создаем таблицу характеристик с учетом сортировки
            if (is_array($arrayVendor))
                foreach ($arrayVendor as $idCategory => $value) {

                    if (!empty($value['product']) and strstr($odnotip, ',')) {

                        $where['id'] = ' IN (' . $odnotip . ')';
                        $where['enabled'] = "='1'";

                        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
                        $PHPShopOrm->debug = false;
                        $PHPShopOrm->mysql_error = false;
                        $data = $PHPShopOrm->select(array('*'), $where, false, array('limit' => 100));
                        if (is_array($data)) {
                            $sortValueName = null;
                            foreach ($data as $row) {

                                if (!isset($row['prod_seo_name']))
                                    $p_link = '/shop/UID_' . $row['id'] . '.html';
                                else
                                    $p_link = '/id/' . str_replace("_", "-", PHPShopString::toLatin($row['name'])) . '-' . $row['id'] . '.html';

                                $sortValueName .= PHPShopText::a($p_link, $row['name'], false, false, false, false, 'sort-table-product-link', $row['pic_small']) . '<br>';
                            }
                        }

                        $sortName = PHPShopText::b($value['name']);
                        $dis .= PHPShopText::tr($sortName . ': ', substr($sortValueName, 0, strlen($sortValueName) - 2));
                    }

                    elseif (!empty($arrayVendorValue[$idCategory]['name'])) {
                        if (!empty($value['name'])) {

                            // Описание
                            if (!empty($value['page']))
                                $sortName = PHPShopText::a('/page/' . $value['page'] . '.html', $value['name']);
                            else
                                $sortName = PHPShopText::b($value['name'],false,'itemprop="name"');

                            if (!empty($value['brand'])) {
                                $arr = array();
                                foreach ($arrayVendorValue[$idCategory]['id'] as $valueId) {

                                    if (!empty($arrayVendorValue[$idCategory]['seo_name'][$valueId]))
                                        $arr[] = PHPShopText::a($GLOBALS['SysValue']['dir']['dir'].'/brand/' . $arrayVendorValue[$idCategory]['seo_name'][$valueId] . '.html', PHPShopText::span($arrayVendorValue[$idCategory]['name'][$valueId],'itemprop="value"'),$arrayVendorValue[$idCategory]['name'][$valueId]);
                                    else
                                        $arr[] = PHPShopText::a('/selection/?v[' . $idCategory . ']=' . $valueId, PHPShopText::span($arrayVendorValue[$idCategory]['name'][$valueId],'itemprop="value"'));
                                }
                                $sortValueName = implode(', ', $arr);
                            } else if (isset($arrayVendorValue[$idCategory]['page'])) {
                                $arr = array();
                                foreach ($arrayVendorValue[$idCategory]['id'] as $valueId) {
                                    $arr[] = PHPShopText::a('/page/' . $arrayVendorValue[$idCategory]['page'][$valueId] . '.html', $arrayVendorValue[$idCategory]['name'][$valueId]);
                                }
                                $sortValueName = implode(', ', $arr);
                            } else {
                                $arr = array();
                                foreach ($arrayVendorValue[$idCategory]['id'] as $valueId) {
                                    $arr[] = $arrayVendorValue[$idCategory]['name'][$valueId];
                                }
                                $sortValueName = PHPShopText::span(implode(', ', $arr),'itemprop="value"');
                            }
                           
                                    
                            $dis .= PHPShopText::tr($sortName . ': ', $sortValueName);
                        }
                    }
                }

            $disp = PHPShopText::table($dis, $cellpadding = 3, $cellspacing = 3, $align = '', $width = '100%', 'itemprop="additionalProperty" itemscope itemtype="http://schema.org/PropertyValue"', false, false, 'vendorenabled');
            $obj->set('vendorDisp', $disp);
        }
    }
}

?>