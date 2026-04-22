<?php

/**
 * Добавление поля промоакции
 */
function order_promotions_hook($obj, $row, $rout) {

    if ($rout == 'MIDDLE') {
        global $promotionslistCode;
        
        $code_check = $html = null;

        if(isset($promotionslistCode)) {
            if(is_array($promotionslistCode))
                foreach ($promotionslistCode as $value) {
                    if($value['code_check']==1) {
                        $code_check = 1;
                        break;
                    }
                    else {
                        $code_check = 0;
                    }
                }
        }

        if($code_check==1) {
            $html = PHPShopParser::file('./phpshop/modules/promotions/templates/order/cart_input.tpl', true, false, true);
        }

        $order_action_add = '
        <script>
        // Promotions
        $(document).ready(function() {
            $(\'' . $html . '\').insertAfter(".img_fix");
        });</script><script src="phpshop/modules/promotions/js/promotions-main.js"></script>';


        if(!empty($_GET['promoselect']) and $_GET['promoselect']!='yes')
            $order_action_add .= '<script>setInterval(UpdatePromotion("*"), 2000);</script>';


        // Добавляем JS в форму заказа
        $obj->set('order_action_add', $order_action_add, true);
    }
}

$addHandler = array
    (
    'order' => 'order_promotions_hook'
);
?>