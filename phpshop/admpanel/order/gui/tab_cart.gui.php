<?php

/**
 * Панель подробного описания товара
 * @param array $row массив данных
 * @return string 
 */
function tab_cart($data, $option = false) {
    global $PHPShopInterface;


    $PHPShopInterface->action_title['cart-value-edit'] = 'Редактировать';
    $PHPShopInterface->action_title['cart-value-remove'] = 'Удалить';


    // Библиотека заказа
    $PHPShopOrder = new PHPShopOrderFunction($data['id']);

    $order = unserialize($data['orders']);
    $status = unserialize($data['status']);
    $CART = $order['Cart'];
    $PERSON = $order['Person'];
    $cart = $CART['cart'];

    $cartForSession = [];
    if (is_array($cart))
        foreach ($cart as $k => $item) {
            unset($item['name']);
            $cartForSession[$k] = $item;
        }

    $_SESSION['selectCart'] = $cartForSession;
    $num = $data_id = $sum = null;
    $n = $promo = 1;


    // Знак рубля
    if ($PHPShopOrder->default_valuta_iso == 'RUB')
        $currency = ' <span class="rubznak">p</span>';
    else
        $currency = $PHPShopOrder->default_valuta_iso;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->dropdown_action_form = false;
    $PHPShopInterface->setCaption(array("Наименование", "50%"), array("Цена", "15%"), array('<span class="hidden-xs">Кол-во</span><span class="visible-xs">Кол.</span>', "10%", array('align' => 'center')), array(null, "10%"), array('Сумма', '15%', array('align' => 'right')));

    if (is_array($cart) and sizeof($cart) != 0)
        if (is_array($cart))
            foreach ($cart as $key => $val) {

                if (!empty($val['id'])) {

                    // Проверка подтипа товара
                    if (!empty($val['parent']))
                        $val['id'] = $val['parent'];
                    if (!empty($val['parent_uid']))
                        $val['uid'] = $val['parent_uid'];

                    // Артикул
                    if (!empty($val['uid']))
                        $code = __('Артикул') . ': ' . $val['uid'];
                    else
                        $code = __('Код') . ': ' . $val['id'];

                    // Внешний код
                    //$PHPShopProduct = new PHPShopProduct($val['id']);
                    //$code .= '<br>' . __('Внешний код') . ': ' . $PHPShopProduct->getParam('external_code');

                    // Промокод
                    if (!empty($val['promo_code']))
                        $code .= '<br><span class="text-info">' . __('Купон') . ': ' . $val['promo_code'] . '</span>';

                    // Скидка не применилась
                    if (!empty($val['promotion_discount']))
                        $code .= '<br><span class="text-success">' . __('Применена скидка промоакции') . '</span>';

                    if (!empty($val['order_discount_disabled']))
                        $code .= '<br><span class="text-success">' . __('Скидка от суммы заказа не применена') . '</span>';

                    if (!empty($val['pic_small']))
                        $icon = '<img src="' . $val['pic_small'] . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
                    else
                        $icon = '<img class="media-object" src="./images/no_photo.gif">';

                    $name = '
<div class="media">
  <div class="media-left">
    <a href="?path=product&id=' . $val['id'] . '" >
      ' . $icon . '
    </a>
  </div>
   <div class="media-body">
    <div class="media-heading"><a href="?path=product&id=' . $val['id'] . '&return=order.' . $data['id'] . '" >' . $val['name'] . '</a></div>
    ' . $code . '
  </div>
</div>';
                    // Цена
                    $price = $PHPShopOrder->ReturnSumma($val['price']) . $currency;
                    if (!empty((int) $val['price_n'])) {
                        $price .= '<br><s class="text-muted">' . $PHPShopOrder->ReturnSumma($val['price_n']) . '</s>' . $currency;
                        $promo++;
                    }

                    $PHPShopInterface->setRow(array('name' => $name, 'align' => 'left'), $price, array('name' => $val['num'], 'align' => 'center'), array('action' => array('cart-value-edit', '|', 'cart-value-remove', 'id' => $key), 'align' => 'center'), array('name' => $PHPShopOrder->ReturnSumma($val['price'] * $val['num']) . $currency, 'align' => 'right'));

                    $n++;
                    $num += $val['num'];
                    $sum += $val['price'] * $val['num'];
                }
            }

    $total = '<table class="pull-right totals">
      <tbody>
      <tr>
      <td>&nbsp;</td>
      <td class="text-right"><h4>' . __('Итого') . '</h4></td>
      </tr>
      <tr>
      <td width="130">' . __('Сумма') . ':</td>
      <td class="text-right">
      ' . ($PHPShopOrder->returnSumma($sum) ) . $currency . '
      </td>
      </tr>
      <tr>
      <td>' . __('Доставка') . ':</td>
      <td class="text-right">
      ' . number_format($PHPShopOrder->getDeliverySumma(), $PHPShopOrder->format, '.', ' ') . $currency . '
      </td>
      </tr>';

    if (!empty($CART['weight']))
        $total .= '
      <tr>
      <td>' . __('Вес') . ':</td>
      <td class="text-right">
      ' . $CART['weight'] . ' ' . __('гр.') . '
      </td>
      </tr>';

    // Очистка скидки если все товары промо
    if ($n == $promo)
        $PERSON['discount'] = 0;

    $total .= '<tr>
      <td>' . __('Скидка') . ':</td>
      <td class="text-right">
      ' . (int) $PERSON['discount'] . ' %
      </td>
      </tr>';

    if (!empty($data['bonus_minus']))
        $total .= '<tr>
      <td>' . __('Списано бонусов') . ':</td>
      <td class="text-right">
      ' . (int) $data['bonus_minus'] . $currency . '
      </td>
      </tr>';

    $total .= '<tr>
      <td><h5>' . __('Итого') . ':</h5></td>
      <td class="text-right">
      <h5 class="text-success">' . ($PHPShopOrder->getTotal(false, ' ')) . $currency . '</h5>
      </td>
      </tr>
      </tbody>
      </table>';

    // Скидка
    if (!empty($PERSON['discount']))
        $discount = $PERSON['discount'];
    else
        $discount = null;

    $disp = '<table class="table table-hover cart-list">' . $PHPShopInterface->getContent() . '</table>
<div class="row">
  <div class="col-lg-9 col-md-8 col-xs-6">
   <div class="input-group-btn">
   <button  class="btn btn-default btn-sm cart-add"><span class="glyphicon glyphicon-plus"></span> ' . __('Добавить товары') . '</button>
   <div class="btn btn-default btn-sm btn-file"><span class="glyphicon glyphicon-save"></span><input type="file" id="uploadimage" name="file"></div>
   </div>
  </div>
  <div class="col-lg-3 col-md-4 col-xs-6">
    <div class="input-group">
      <span class="input-group-addon input-sm">%</span>
      <input type="text" class="form-control input-sm discount-value" placeholder="' . __('Скидка') . '" value="' . $discount . '"> 
      <span class="input-group-btn">
        <button class="btn btn-default btn-sm discount" type="button">' . __('Назначить') . '</button>
     </span>
    </div>
  </div>
</div>
<p class="clearfix"> </p>
' . $total . '
<p class="clearfix"> </p>
<div class="row">
  <div class="col-md-6">
  <label for="dop_info">' . __('Примечания покупателя') . '</label>
  <textarea class="form-control" id="dop_info" name="dop_info_new">' . $data['dop_info'] . '</textarea>
  </div>
  <div class="col-md-6">
    <label for="status_maneger">' . __('Примечания администратора') . '</label>
    <textarea class="form-control" id="status_maneger" name="status[maneger]">' . $status['maneger'] . '</textarea>
  </div>
</div>
';
    return $disp;
}

?>