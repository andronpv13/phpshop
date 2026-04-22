<?php

include_once dirname(__FILE__) . '/../class/OzonSeller.php';
$TitlePage = __('Заказ из Ozon') . ' #' . $_GET['id'];
PHPShopObj::loadClass("product");

// Озон
$OzonSeller = new OzonSeller();

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopInterface, $OzonSeller;

    // Данные по заказу Озон
    if ($_GET['type'] == 'fbs')
        $order_info = $OzonSeller->getOrderFbs($_GET['id']);
    else
        $order_info = $OzonSeller->getOrderFbo($_GET['id']);

    $PHPShopGUI->field_col = 4;

    if (!empty($order_info['result']['order_id']))
        $PHPShopGUI->action_button['Загрузить заказ'] = array(
            'name' => 'Загрузить заказ',
            'locale' => true,
            'action' => 'saveID',
            'class' => 'btn  btn-default btn-sm navbar-btn' . $GLOBALS['isFrame'],
            'type' => 'submit',
            'icon' => 'glyphicon glyphicon-save'
        );


    $PHPShopGUI->setActionPanel(__('Заказ') . ' &#8470;' . $_GET['id'], false, array('Загрузить заказ'));

    // Знак рубля
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = '<span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();

    // Переводим в читаемый вид
    ob_start();
    print_r($order_info);
    $log = ob_get_clean();

    $Tab3 = $PHPShopGUI->setTextarea(null, PHPShopString::utf8_win1251($log), $float = "none", $width = '100%', $height = '500');
    $Tab1 = $PHPShopGUI->setField("&#8470; отправления", $PHPShopGUI->setText($order_info['result']['posting_number']));
    $Tab1 .= $PHPShopGUI->setField("ID заказа", $PHPShopGUI->setText($order_info['result']['order_id']));
    $Tab1 .= $PHPShopGUI->setField("&#8470; заказа", $PHPShopGUI->setText($order_info['result']['order_number']));
    $Tab1 .= $PHPShopGUI->setField("Статус", $PHPShopGUI->setText($OzonSeller->getStatus($order_info['result']['status'])));
    $Tab1 .= $PHPShopGUI->setField("Дата поступления", $PHPShopGUI->setText($order_info['result']['in_process_at']));
    $Tab1 .= $PHPShopGUI->setField("Дата доставки", $PHPShopGUI->setText($order_info['result']['shipment_date']));
    $Tab1 .= $PHPShopGUI->setField("Склад", $PHPShopGUI->setText(PHPShopString::utf8_win1251($order_info['result']['delivery_method']['warehouse']), "left", false, false));
    $Tab1 .= $PHPShopGUI->setField("Доставка", $PHPShopGUI->setText(PHPShopString::utf8_win1251($order_info['result']['delivery_method']['name']) . ' - ' . (int) $order_info['result']['delivery_price'] . $currency, "left", false, false));

    $Tab1 = $PHPShopGUI->setCollapse('Данные', $Tab1);
    $Tab3 = $PHPShopGUI->setCollapse('JSON данные', $Tab3);

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setCaption(array("Наименование", "50%"), array("Цена", "15%"), array("Кол-во", "10%"), array("Сумма", "15%", array('align' => 'right')));

    // Ключ обновления
    if ($OzonSeller->type == 2) {
        $type_name = __('Арт');
        $type = 'uid';
    } else {
        $type_name = 'ID';
        $type = 'id';
    }

    $data = $order_info['result']['products'];


    if (is_array($data))
        foreach ($data as $row) {

            $product = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['id,uid,name,pic_small'], [$type => '="' . (string) PHPShopString::utf8_win1251($row['offer_id']) . '"']);

            if (empty($product)) {
                $product_info = $OzonSeller->getProductAttribures($row['offer_id'], 'offer_id')['result'][0];
                $image = $product_info['primary_image'];
                $link = 'https://www.ozon.ru/product/' .$row['sku'];
            } else{
                $image = $product['pic_small'];
                $link = '?path=product&id=' . $product['id'] . '&return=modules.dir.ozonseller';
            }

            if (!empty($image))
                $icon = '<img src="' . $image . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
            else
                $icon = '<img class="media-object" src="./images/no_photo.gif">';

            $name = '
<div class="media">
  <div class="media-left">
    <a href="'.$link.'" target="_blank">
      ' . $icon . '
    </a>
  </div>
   <div class="media-body">
    <div class="media-heading"><a href="'.$link.'" target="_blank">' . PHPShopString::utf8_win1251($row['name']) . '</a></div>
    ' . $type_name . ': ' . $row['offer_id'] . '
  </div>
</div>';

            $PHPShopInterface->setRow($name, (1 * $row['price']), array('name' => $row['quantity'], 'align' => 'center'), array('name' => number_format($row['price'] * $row['quantity'], 0, '', ' ') . $currency, 'align' => 'right'));
        }

    $Tab2 = $PHPShopGUI->setCollapse("Корзина", '<table class="table table-hover cart-list">' . $PHPShopInterface->getContent() . '</table>');

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Информация", $Tab1 . $Tab2, true, false, true), array('Дополнительно', $Tab3));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $_GET['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.order.edit");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);

    return true;
}

/**
 * Экшен загрузки заказа
 */
function actionSave() {
    global $OzonSeller;

    // Данные по заказу Озон
    $order_info = $OzonSeller->getOrderFbs($_POST['rowID']);

    $name = 'OZON';
    $phone = null;
    $mail = null;
    $comment = null;

    // таблица заказов
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
    $qty = $sum = $weight = 0;

    // Ключ обновления
    if ($OzonSeller->type == 2)
        $type = 'uid';
    else
        $type = 'id';

    $data = $order_info['result']['products'];
    
    if (is_array($data))
        foreach ($data as $row) {

            // Данные по товару
            $product = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['id,uid,name,pic_small'], [$type => '="' . (string) PHPShopString::utf8_win1251($row['offer_id']) . '"']);


            if (empty($product) and ! empty($OzonSeller->create_products)) {

                // Создание товара
                $product_id = $OzonSeller->addProduct($row['offer_id']);
                $product = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['id,uid,name,pic_small'], ['id' => '=' . (int) $product_id]);
            }


            if (empty($product))
                continue;

            $order['Cart']['cart'][$product['id']]['id'] = $product['id'];
            $order['Cart']['cart'][$product['id']]['uid'] = $product['uid'];
            $order['Cart']['cart'][$product['id']]['name'] = $product['name'];
            $order['Cart']['cart'][$product['id']]['price'] = $row['price'];
            $order['Cart']['cart'][$product['id']]['num'] = $row['quantity'];
            $order['Cart']['cart'][$product['id']]['weight'] = '';
            $order['Cart']['cart'][$product['id']]['ed_izm'] = '';
            $order['Cart']['cart'][$product['id']]['pic_small'] = $product['pic_small'];
            $order['Cart']['cart'][$product['id']]['parent'] = 0;
            $order['Cart']['cart'][$product['id']]['user'] = 0;
            $qty += $row['quantity'];
            $sum += $row['price'] * $row['quantity'];
            $weight += $product['weight'];
        }

    $order['Cart']['num'] = $qty;
    $order['Cart']['sum'] = $sum;
    $order['Cart']['weight'] = $weight;
    $order['Cart']['dostavka'] = (int) $order_info['result']['delivery_price'];

    $order['Person']['ouid'] = '';
    $order['Person']['data'] = time();
    $order['Person']['time'] = '';
    $order['Person']['mail'] = $mail;
    $order['Person']['name_person'] = $name;
    $order['Person']['org_name'] = '';
    $order['Person']['org_inn'] = '';
    $order['Person']['org_kpp'] = '';
    $order['Person']['tel_code'] = '';
    $order['Person']['tel_name'] = '';
    $order['Person']['adr_name'] = '';
    $order['Person']['dostavka_metod'] = (int) $OzonSeller->delivery;
    $order['Person']['discount'] = 0;
    $order['Person']['user_id'] = '';
    $order['Person']['dos_ot'] = '';
    $order['Person']['dos_do'] = '';
    $order['Person']['order_metod'] = '';
    $insert['dop_info_new'] = $comment;
    
    // данные для записи в БД
    $insert['datas_new'] = time();
    $insert['uid_new'] = $OzonSeller->setOrderNum();
    $insert['orders_new'] = serialize($order);
    $insert['fio_new'] = $name;
    $insert['tel_new'] = $phone;
    $insert['city_new'] = PHPShopString::utf8_win1251($order_info['result']['delivery_method']['name'], true);
    $insert['statusi_new'] = $OzonSeller->status;
    $insert['status_new'] = serialize(array("maneger" => __('OZON заказ') . ' &#8470;' . $_POST['rowID']));
    $insert['sum_new'] = $order['Cart']['sum'];
    $insert['ozonseller_order_data_new'] = $_POST['rowID'];

    // Запись в базу
    $orderId = $PHPShopOrm->insert($insert);

    // Оповещение пользователя о новом статусе и списание со склада
    if (!empty($insert['statusi_new'])) {
        PHPShopObj::loadClass("order");
        $PHPShopOrderFunction = new PHPShopOrderFunction($orderId);
        $PHPShopOrderFunction->changeStatus($insert['statusi_new'], 0);
    }

    header('Location: ?path=order&id=' . $orderId . '&return=' . $_GET['path']);
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>