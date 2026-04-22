<?php

function productsproperty_UID_hook($obj, $row, $rout) {

    if ($rout == 'MIDDLE') {

        $productsproperty_array = unserialize($row['productsproperty_array']);

        if (is_array($productsproperty_array)) {

            $list = $forma = null;

            foreach ($productsproperty_array as $n => $property) {

                if (!empty($property['name'])) {
                    $obj->set('productsproperty_title', $property['name']);
                    $list = null;
                }
                else continue;

                foreach ($property['property'] as $k => $val) {


                    if (!empty($val)) {

                        $PHPShopProduct = new PHPShopProduct($property['id'][$k]);

                        if ($row['id'] == $property['id'][$k]) {
                            $class = 'active property-active';
                            $link = 'javascript:void(0);';
                        } else {
                            $class = null;

                            $seo_name = $PHPShopProduct->getParam('prod_seo_name');
                            if (!empty($seo_name))
                                $link = '/id/' . $seo_name . '-' . $property['id'][$k] . '.html';
                            else
                                $link = '/shop/UID_' . $property['id'][$k] . '.html';
                        }

                        $price = $PHPShopProduct->getPrice();
                        $items = $PHPShopProduct->getParam('items');
                        $unit = $PHPShopProduct->getParam('ed_izm');
                        
                        $obj->set('productsproperty_link', $link);
                        $obj->set('productsproperty_class', $class);
                        $obj->set('productsproperty_num', ($n + 1));
                        $obj->set('productsproperty_name', $val);
                        $obj->set('productsproperty_price', $price);
                        $obj->set('productsproperty_items', (int)$items);
                        $obj->set('productsproperty_unit', $unit);
                        $list .= PHPShopParser::file($GLOBALS['SysValue']['templates']['productsproperty']['product'], true, false, true);
                    }
                }
                $obj->set('productsproperty_list', $list);
                $forma .= PHPShopParser::file($GLOBALS['SysValue']['templates']['productsproperty']['forma'], true, false, true);
            }

            if (!empty($list)) {
                $obj->set('productsproperty_forma', $forma);
                $obj->set('productsproperty', PHPShopParser::file($GLOBALS['SysValue']['templates']['productsproperty']['productsproperty'], true, false, true));
            }
        }
    }
}

$addHandler = array
    (
    'UID' => 'productsproperty_UID_hook',
);