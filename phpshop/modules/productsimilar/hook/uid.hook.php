<?php

class PHPShopProductSimilarElement extends PHPShopProductElements {

    function __construct() {
        $this->debug = false;
        $this->objBase = $GLOBALS['SysValue']['base']['productsimilar']['productsimilar_system'];
        $this->option();
        parent::__construct();
    }

    function option() {
        $PHPShopOrm = new PHPShopOrm($this->objBase);
        $this->data = $PHPShopOrm->select();
        if ($this->data['num'] < 1)
            $this->data['num'] = 1;
    }

    // Âûâîä
    function element($vendor_array) {
        $dis = $sort = null;

        // Ó÷åò ìîäóëÿ SEOURLPRO
        if (!empty($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system']))
            $seourlpro = true;
        else
            $seourlpro = false;


        if (is_array($vendor_array)) {

            $PHPShopOrmSort = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
            $PHPShopOrmSort->debug = false;

            foreach ($vendor_array as $k => $v) {
                $data_sort = $PHPShopOrmSort->getOne(['id'], ['id' => '=' . (int) $k, 'productsimilar_enabled' => "='1'"]);
                if (!empty($data_sort['id'])) {
                    $hash = $k . "-" . $v[0];
                    $sort .= " vendor REGEXP 'i" . $hash . "i' and";
                }
            }

            if (!empty($sort))
                $sort = substr($sort, 0, strlen($sort) - 3);
            else
                return true;

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
            $PHPShopOrm->debug = false;
            $PHPShopOrm->sql = "select * from phpshop_products where enabled='1' and (" . $sort . ") and id !=" . $this->PHPShopNav->getId() . " order by RAND() limit " . $this->data['num'];

            $data = $PHPShopOrm->select();
            if (is_array($data)) {

                foreach ($data as $row) {

                    if (empty($row['pic_small']) or ! file_exists($_SERVER['DOCUMENT_ROOT'] . $row['pic_small']))
                        continue;


                    // Ñïåöïğåäëîæåíèÿ
                    if ($row['spec'])
                        $this->set('productsimilar_product_specIcon', ParseTemplateReturn('product/specIcon.tpl'));
                    else
                        $this->set('productsimilar_product_specIcon', '');

                    // Íîâèíêè
                    if ($row['newtip'])
                        $this->set('productsimilar_product_newtipIcon', ParseTemplateReturn('product/newtipIcon.tpl'));
                    else
                        $this->set('productsimilar_product_newtipIcon', '');


                    // Ïğîìîàêöèè
                    $promotions = $this->PHPShopPromotions->getPrice($row);
                    if (is_array($promotions)) {
                        $row['price'] = $promotions['price'];
                        $row['price_n'] = $promotions['price_n'];
                        $row['promo_label'] = $promotions['label'];
                    }

                    $this->set('productsimilar_product_id', $row['id']);
                    $this->set('productsimilar_product_name', $row['name']);
                    $this->set('productsimilar_product_pic_small', $row['pic_small']);
                    $this->set('productsimilar_product_pic_big', $row['pic_big']);


                    $this->set('productsimilar_product_price', number_format($this->price($row, false, false), $this->format, '.', ' '));
                    $this->set('productsimilar_product_price_old', $this->price($row, true, false));

                    $oneStarWidth = 20; // øèğèíà îäíîé çâ¸çäî÷êè
                    $oneSpaceWidth = 0; // ïğîáåë ìåæäó çâ¸çäî÷êàìè
                    $avgRateWidth = $oneStarWidth * $row['rate'] + $oneSpaceWidth * ceil($row['rate']);
                    PHPShopParser::set('productsimilar_product_rating', $avgRateWidth);

                    if ((float) $row['price_n'] > 0)
                        PHPShopParser::set('productsimilar_product_price_old', number_format($row['price_n'], $this->format, '.', ' ') . ' ' . $this->PHPShopSystem->getValutaIcon());
                    else
                        PHPShopParser::set('productsimilar_product_price_old', null);


                    // Ó÷åò ìîäóëÿ SEOURLPRO
                    if ($seourlpro) {

                        if (empty($row['prod_seo_name']))
                            $url = '/id/' . str_replace("_", "-", PHPShopString::toLatin($row['name'])) . '-' . $row['id'];
                        else
                            $url = '/id/' . $row['prod_seo_name'] . '-' . $row['id'];

                        PHPShopParser::set('productsimilar_product_url', $url);
                    }
                    else {
                        $url = '/shop/UID_' . $row['id'];
                        PHPShopParser::set('productsimilar_product_url', $url);
                    }


                    $dis .= PHPShopParser::file($GLOBALS['SysValue']['templates']['productsimilar']['productsimilar_product'], true, false, true);
                }

                $this->set('productsimilar_title', $this->data['title']);
                $this->set('productsimilar_list', $dis);
            }
        }
    }

}

function uid_productsimilar_hook($obj, $row, $rout) {
    if ($rout == 'MIDDLE') {

        $PHPShopProductSimilarElement = new PHPShopProductSimilarElement();
        $PHPShopProductSimilarElement->element(unserialize($row['vendor_array']));
    }
}

$addHandler = array
    (
    'UID' => 'uid_productsimilar_hook'
);
?>