<?php

class PHPShopProductListElement extends PHPShopProductElements {

    function __construct() {
        $this->debug = false;
        $this->objBase = $GLOBALS['SysValue']['base']['productlist']['productlist_system'];
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
    function element($category) {
        $dis = null;

        // Ó÷åò ìîäóëÿ SEOURLPRO
        if (!empty($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system']))
            $seourlpro = true;
        else
            $seourlpro = false;


        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
        $PHPShopOrm->debug = $this->debug;

        $data = $PHPShopOrm->select(array('*'), array('category' => '=' . intval($category), 'enabled' => "='1'", 'parent_enabled' => "='0'", 'id' => '!=' . $this->PHPShopNav->getId()), array('order' => 'RAND()'), array('limit' => $this->data['num']));
        if (is_array($data)) {
            foreach ($data as $row) {
                
                 if (empty($row['pic_small']) or ! file_exists($_SERVER['DOCUMENT_ROOT'] . $row['pic_small']))
                    continue;

                // Ïğîìîàêöèè
                $promotions = $this->PHPShopPromotions->getPrice($row);
                if (is_array($promotions)) {
                    $row['price'] = $promotions['price'];
                    $row['price_n'] = $promotions['price_n'];
                    $row['promo_label'] = $promotions['label'];
                }

                $this->set('productlist_product_id', $row['id']);
                $this->set('productlist_product_name', $row['name']);
                $this->set('productlist_product_pic_small', $row['pic_small']);

                $this->set('productlist_product_pic_big', $row['pic_big']);
                $this->set('productlist_product_price', number_format($this->price($row, false, false), $this->format, '.', ' '));
                $this->set('productlist_product_price_old', $this->price($row, true, false));

                $oneStarWidth = 20; // øèğèíà îäíîé çâ¸çäî÷êè
                $oneSpaceWidth = 0; // ïğîáåë ìåæäó çâ¸çäî÷êàìè
                $avgRateWidth = $oneStarWidth * $row['rate'] + $oneSpaceWidth * ceil($row['rate']);
                PHPShopParser::set('productlist_product_rating', $avgRateWidth);

                if ((float) $row['price_n'] > 0)
                    PHPShopParser::set('productlist_product_price_old', number_format($row['price_n'], $this->format, '.', ' ') . ' ' . $this->PHPShopSystem->getValutaIcon());
                else
                    PHPShopParser::set('productlist_product_price_old', null);


                // Ó÷åò ìîäóëÿ SEOURLPRO
                if ($seourlpro) {

                    if (empty($row['prod_seo_name']))
                        $url = '/id/' . str_replace("_", "-", PHPShopString::toLatin($row['name'])) . '-' . $row['id'];
                    else
                        $url = '/id/' . $row['prod_seo_name'] . '-' . $row['id'];

                    PHPShopParser::set('productlist_product_url', $url);
                }
                else {
                    $url = '/shop/UID_' . $row['id'];
                    PHPShopParser::set('productlist_product_url', $url);
                }


                $dis .= PHPShopParser::file($GLOBALS['SysValue']['templates']['productlist']['productlist_product'], true, false, true);
            }

            $this->set('productlist_title', $this->data['title']);
            $this->set('productlist_list', $dis, true);
        }
    }

}

function uid_productlist_hook($obj, $row, $rout) {
    if ($rout == 'MIDDLE') {

        $PHPShopProductListElement = new PHPShopProductListElement();
        $PHPShopProductListElement->element($row['category']);
    }
}

$addHandler = array
    (
    'UID' => 'uid_productlist_hook'
);
?>