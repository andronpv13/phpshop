<?php

function UID_kvk_product_hook($obj, $dataArray, $rout) {
    if ($rout == 'MIDDLE') {

        // Òîâàð
        $price = str_replace(' ', '', $obj->get('productPrice'));
        $dis = null;
        if ($price >= 3000 && $dataArray['kvk_enabled'] == 1) {
            $kvk_sum = number_format($price, 2, '.', '');
            $kvk_pay = ceil($price / 19);
            $kvk_promo = trim($dataArray['kvk_promo']);
            if (empty($kvk_promo))
                $kvk_promo = 'default';

            // Íàñòðîéêè ìîäóëÿ
            include_once dirname(__FILE__) . '/mod_option.hook.php';
            $PHPShopKVKArray = new PHPShopKVKArray();
            $option = $PHPShopKVKArray->getArray();

            $kvk_url = 'https://forma.tinkoff.ru/api/partners/v1/lightweight/create';
            $kvk_shop_id = $option['shop_id'];
            $kvk_showcase_id = $option['showcase_id'];


            // Ôîðìà
            $obj->set('kvk_name', iconv("windows-1251", "utf-8", htmlspecialchars($dataArray['name'], ENT_COMPAT, 'cp1251', true)));
            $obj->set('kvk_uid', $dataArray['id']);
            $obj->set('kvk_url', $kvk_url);
            $obj->set('kvk_shop_id', $kvk_shop_id);
            if (!empty($kvk_showcase_id))
                $obj->set('kvk_showcase_id', "<input name='showcaseId' value='$kvk_showcase_id' type='hidden'>");
            $obj->set('kvk_promo', $kvk_promo);
            $obj->set('kvk_sum', $kvk_sum);

            if ($kvk_promo == 'default')
                $but = "Â ÊÐÅÄÈÒ îò $kvk_pay ðóá â ìåñ";
            else
                $but = 'Â ÐÀÑÑÐÎ×ÊÓ';

            $obj->set('kvk_pay', $but);

            $dis = ParseTemplateReturn($GLOBALS['SysValue']['templates']['kupivkredit']['kupivkredit_product'], true);
        }

        $obj->set('kvk_product', $dis);
    }
}

$addHandler = array
    (
    'UID' => 'UID_kvk_product_hook',
);
?>