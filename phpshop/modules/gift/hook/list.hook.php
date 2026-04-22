<?php

/**
 * Цены меняем
 */
function UID_gift_hook($obj, $row, $rout) {

    if ($rout == "MIDDLE") {

        $gift = $GLOBALS['PHPShopGift']->getGift($row);

        // Есть подарок
        if (is_array($gift)) {

            // Несколько подарков
            if (strpos($row['gift'], ',')) {
                $gift_prod_array = explode(",", $row['gift']);
            } else
                $gift_prod_array[] = $row['gift'];

            // A+B
            if ($gift['gift'] == 0) {

                $PHPShopProduct = new PHPShopProduct($gift_prod_array[0]);
                if ($PHPShopProduct->getParam('items') > 0 or $obj->PHPShopSystem->getSerilizeParam("admoption.sklad_status") == 1) {
                    $obj->set('giftInfo', $gift['description']);

                    if (!empty($gift['label'])) {
                        $obj->set('giftLabel', $gift['label']);
                        $obj->set('giftIcon', PHPShopParser::file($GLOBALS['SysValue']['templates']['gift']['icon'], true, false, true));
                    } else
                        $obj->set('giftIcon', null);
                }
            }
            // NA+MA
            else {
                $obj->set('giftInfo', $gift['description']);

                if (!empty($gift['label'])) {
                    $obj->set('giftLabel', $gift['label']);
                    $obj->set('giftIcon', PHPShopParser::file($GLOBALS['SysValue']['templates']['gift']['icon'], true, false, true));
                } else
                    $obj->set('giftIcon', null);
            }
        }
    }
}

$addHandler = array
    (
    'UID' => 'UID_gift_hook',
);
?>