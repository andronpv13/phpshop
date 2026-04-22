<?php

function UIDProductServices($obj, $row, $rout) {

    if ($rout === 'MIDDLE') {
        $services = explode(',', $row['productservices_products']);
        $services = array_diff($services, array(''));

        if (is_array($services) && count($services) > 0) {
            $tpl = '';
            foreach ($services as $service) {
                if ((int) $service > 0) {
                    $productService = new PHPShopProduct((int) $service);

                    $price = $productService->getPrice();

                    // Скидка %
                    if ($row['productservices_discount'] > 0) {
                        $price = $price - $row['price'] * $row['productservices_discount'] / 100;
                        $obj->set('product_services_name_price', number_format($price, $obj->format, '.', ' ').' '.$obj->currency);
                    }
                    else $obj->set('product_services_name_price',null);

                    $obj->set('product_services_id', $productService->objID);
                    $obj->set('product_services_name', $productService->getName());
                    $obj->set('product_services_price', $price);
                   
                    $tpl .= PHPShopParser::file($GLOBALS['SysValue']['templates']['productservices']['productservices_service'], true, false, true);
                    $obj->set('productservices_service', $tpl);
                }
            }

            $html = PHPShopParser::file($GLOBALS['SysValue']['templates']['productservices']['productservices_list'], true, false, true);

            $obj->set('productservices_list', $html);
        } else {
            $obj->set('productservices_list', '');
        }
    }
}

$addHandler = array(
    'UID' => 'UIDProductServices',
);
?>