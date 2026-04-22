<?php

function payment_mod_paypal_hook($obj) {

    // Настройки модуля
    include_once(dirname(__FILE__) . '/mod_option.hook.php');
    $option = new PHPShopPaypalArray();
    $obj->value[10003] = array($option->getParam('title'), 10003, false);
}

/**
 * Добавление кнопки быстрого заказа
 */
function order_mod_paypal_hook($obj, $row, $rout) {
    /*
      if ($rout == "MIDDLE-END")
      if ($obj->temp)
      $obj->set('orderContent', parseTemplateReturn('phpshop/modules/paypal/templates/main_order_forma_nt.tpl', true));
      else
      $obj->set('orderContent', parseTemplateReturn('phpshop/modules/paypal/templates/main_order_forma.tpl', true));*/
    
    
     if ($rout == 'MIDDLE') {
           $order_action_add = "
           <script type=\"text/javascript\" src='phpshop/modules/paypal/templates/paypal.js'></script>
           ";
                       // Добавляем JS в форму заказа
            $obj->set('order_action_add', $order_action_add, true);
     }
}

$addHandler = array
    (
    'payment' => 'payment_mod_paypal_hook',
    'order' => 'order_mod_paypal_hook'
);
?>