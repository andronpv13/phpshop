<?php

// API https://yandex.ru/support/partnermarket/elements/promo-gift.html

function setProducts_gift_hook($obj, $row) {

    $gift_array = $obj->PHPShopGift->getGift($row['val']);

    // Есть подарок
    if (is_array($gift_array)) {

        // Несколько подарков
        if (strpos($row['val']['gift'], ',')) {
            $gift_prod_array = explode(",", $row['val']['gift']);
        } else
            $gift_prod_array[] = $row['val']['gift'];



        // Добавляем подарки
        if (is_array($gift_prod_array)) {
            foreach ($gift_prod_array as $val) {
                if (!empty($val)) {
                    $PHPShopProduct = new PHPShopProduct($val);
                    if ($PHPShopProduct->getParam('items') > 0 or $obj->PHPShopSystem->getSerilizeParam("admoption.sklad_status") == 1) {

                        $obj->promo_product[$gift_array['id']][$row['val']['id']] = '<product offer-id="' . $row['val']['id'] . '"/>';

                        // A+B
                        if ($gift_array['gift'] == 0) {
                            
                            
                             if (!$obj->gift_product_array[$val]) {
                                $obj->promo_gift[$gift_array['id']][$val] = '<promo-gift gift-id="' . $val . '"/>';
                                $obj->gift_product .= '<gift id="' . $val . '">
                              <name><![CDATA[' . $PHPShopProduct->getName() . ']]></name>
                              <picture>' . $obj->ssl . $_SERVER['SERVER_NAME'] . $PHPShopProduct->getImage() . '</picture>
                        </gift>';
                                $obj->gift_product_array[$val] = true;
                            }
                            
                            
                        }
                        // NA+MA
                        else {
                            $obj->promo_option[$gift_array['id']] = '<required-quantity>' . $row['val']['gift_check'] . '</required-quantity>'
                                    . '<free-quantity>' . $row['val']['gift_items'] . '</free-quantity>';
                        }

                       
                    }
                }
            }
        }

    }
}

function serFooter_gift_hook($obj, $data) {
    $add = $list = $vemdorSort = null;

    // Информация об акции 
    if (is_array($obj->PHPShopGift->giftlist)) {

        if ($obj->gift_product) {
            $add = '<gifts>' .
                    $obj->gift_product .
                    '</gifts>';
        }

        $add .= '<promos>';
        foreach ($obj->PHPShopGift->giftlist as $giftlist) {

            // A+B
            if ($giftlist['discount_tip'] == 0 and is_array($obj->promo_product[$giftlist['id']]) and is_array($obj->promo_gift[$giftlist['id']])) {
                $add .= '<promo id="promo' . $giftlist['id'] . '" type="gift with purchase">
        <description><![CDATA[' . $giftlist['description'] . ']]></description>
            <purchase>
            <required-quantity>1</required-quantity>';

                foreach ($obj->promo_product[$giftlist['id']] as $product) {
                    $add .= $product;
                }

                $add .= '</purchase>
                 <promo-gifts>';

                foreach ($obj->promo_gift[$giftlist['id']] as $gift) {
                    $add .= $gift;
                }

                $add .= '</promo-gifts>
    </promo>';
            }
            // NA+MA
            elseif (is_array($obj->promo_product[$giftlist['id']])) {
                $add .= '<promo id="promo' . $giftlist['id'] . '" type="n plus m">
        <description><![CDATA[' . $giftlist['description'] . ']]></description>
               <purchase>';

                $add .= $obj->promo_option[$giftlist['id']];


                foreach ($obj->promo_product[$giftlist['id']] as $product) {
                    $add .= $product;
                }

                $add .= '</purchase>
    </promo>';
            }
        }

        return $add . '</promos>';
    }
}

function PHPShopYml_gift_hook($obj) {

    include_once '.' . $GLOBALS['SysValue']['class']['gift'];
    $obj->PHPShopGift = new PHPShopGift();
}

$addHandler = array
    (
    'setProducts' => 'setProducts_gift_hook',
    'serFooter' => 'serFooter_gift_hook',
    '__construct' => 'PHPShopYml_gift_hook'
);
?>