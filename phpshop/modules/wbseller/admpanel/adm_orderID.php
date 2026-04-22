<?php

include_once dirname(__FILE__) . '/../class/WbSeller.php';
$TitlePage = __('Заказ из WB') . ' #' . $_GET['id'];
PHPShopObj::loadClass("product");

// Озон
$WbSeller = new WbSeller();

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopInterface, $WbSeller;

    // Данные по заказу
    $orders = $WbSeller->getOrderList($_GET['date_start'], $_GET['date_end'], $_GET['status'])['orders'];

    if (is_array($orders)) {
        foreach ($orders as $order) {
            if ($order['id'] == $_GET['id']) {
                $order_info = $order;
            }
        }
    }

    $PHPShopGUI->field_col = 4;

    if (!empty($order_info['id']))
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
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();

    // Переводим в читаемый вид
    ob_start();
    print_r($order_info);
    $log = ob_get_clean();

    $Tab3 = $PHPShopGUI->setTextarea(null, PHPShopString::utf8_win1251($log), $float = "none", $width = '100%', $height = '500');
    $Tab1 = $PHPShopGUI->setField("&#8470; заказа", $PHPShopGUI->setText($order_info['id']));
    $Tab1 .= $PHPShopGUI->setField("Дата поступления", $PHPShopGUI->setText($order_info['createdAt']));
    $Tab1 .= $PHPShopGUI->setField("Доставка", $PHPShopGUI->setText(PHPShopString::utf8_win1251($order_info['prioritySc'][0]), "left", false, false));
    $Tab1 .= $PHPShopGUI->setField("Склад", $PHPShopGUI->setText(PHPShopString::utf8_win1251($order_info['offices'][0]), "left", false, false));

    $Tab1 .= $PHPShopGUI->setInput("hidden", "date_start", $_GET['date_start']);
    $Tab1 .= $PHPShopGUI->setInput("hidden", "date_end", $_GET['date_end']);

    $Tab1 = $PHPShopGUI->setCollapse('Данные', $Tab1);
    $Tab3 = $PHPShopGUI->setCollapse('JSON данные', $Tab3);

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setCaption(array("Наименование", "50%"), array("Цена", "15%"), array("Кол-во", "10%"), array("Сумма", "15%", array('align' => 'right')));

    // Ключ обновления
    if ($WbSeller->type == 2) {
        $type_name = __('Арт');
        $type = 'uid';
    } else {
        $type_name = 'ID';
        $type = 'id';
    }

    // Данные по товару в БД
    $prod = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['id,uid,name,pic_small'], [$type => '="' . (string) $order_info['article'] . '"']);

    // Даннеы по товары из WB
    if (empty($prod)) {
        $product_info = $WbSeller->getProductList($order_info['skus'][0], 1)['cards'][0];
        $prod['pic_small'] = $product_info['photos'][0]['tm'];
        $prod['uid'] = PHPShopString::utf8_win1251($product_info['vendorCode']);
        $prod['name'] = PHPShopString::utf8_win1251($product_info['title']);

        $link = 'https://www.wildberries.ru/catalog/' . $product_info['nmID'] . '/detail.aspx';
    }
    else {
        $link = '?path=product&id=' . $prod['id'];
    }



    if (!empty($prod['pic_small']))
        $icon = '<img src="' . $prod['pic_small'] . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
    else
        $icon = '<img class="media-object" src="./images/no_photo.gif">';

    $name = '
<div class="media">
  <div class="media-left">
    <a href="' . $link . '" target="_blank" >
      ' . $icon . '
    </a>
  </div>
   <div class="media-body">
    <div class="media-heading"><a href="' . $link . '" target="_blank">' . $prod['name'] . '</a></div>
    ' . $type_name . ': ' . $prod['uid'] . '
  </div>
</div>';

    $PHPShopInterface->setRow($name, number_format(round($order_info['price'] / 100), 0, '', ' '), array('name' => 1, 'align' => 'center'), array('name' => number_format($order_info['price'] / 100, 0, '', ' ') . $currency, 'align' => 'right'));


    $Tab2 = $PHPShopGUI->setCollapse("Корзина", '<table class="table table-hover cart-list">' . $PHPShopInterface->getContent() . '</table>');

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Информация", $Tab1 . $Tab2, true, false, true), array('Дополнительно', $Tab3));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $_GET['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("hidden", "status", $_GET['status'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.order.edit");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);

    return true;
}

/**
 * Экшен загрузки заказа
 */
function actionSave() {
    global $WbSeller;

    // Данные по заказу
    $orders = $WbSeller->getOrderList($_POST['date_start'], $_POST['date_end'],$_POST['status'])['orders'];

    if (is_array($orders)) {
        foreach ($orders as $order) {
            if ($order['id'] == $_POST['rowID']) {
                $order_info = $order;
            }
        }
    }
    

    
    $name = 'WB';
    $phone = null;
    $mail = null;
    $comment = null;

    // таблица заказов
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
    $qty = $sum = $weight = 0;

    // Ключ обновления
    if ($WbSeller->type == 2) {
        $type_name = __('Арт');
        $type = 'uid';
    } else {
        $type_name = 'ID';
        $type = 'id';
    }

    // Данные по товару
    $product = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['id,uid,name,pic_small'], [$type => '="' . (string) PHPShopString::utf8_win1251($order_info['article']) . '"']);

    if (empty($product) and ! empty($WbSeller->create_products)) {

        // Создание товара
        $product_id = $WbSeller->addProduct($order_info['skus'][0]);
        $product = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['id,uid,name,pic_small'], ['id' => '=' . (int) $product_id]);
    }


    $order['Cart']['cart'][$product['id']]['id'] = $product['id'];
    $order['Cart']['cart'][$product['id']]['uid'] = $product["uid"];
    $order['Cart']['cart'][$product['id']]['name'] = $product['name'];
    $order['Cart']['cart'][$product['id']]['price'] = $order_info['price'] / 100;
    $order['Cart']['cart'][$product['id']]['num'] = 1;
    $order['Cart']['cart'][$product['id']]['weight'] = '';
    $order['Cart']['cart'][$product['id']]['ed_izm'] = '';
    $order['Cart']['cart'][$product['id']]['pic_small'] = $product['pic_small'];
    $order['Cart']['cart'][$product['id']]['parent'] = 0;
    $order['Cart']['cart'][$product['id']]['user'] = 0;
    $qty = 1;
    $sum = $order_info['price'] / 100;
    $weight += $product['weight'];


    $order['Cart']['num'] = $qty;
    $order['Cart']['sum'] = $sum;
    $order['Cart']['weight'] = $weight;
    $order['Cart']['dostavka'] = 0;

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
    $order['Person']['dostavka_metod'] = '';
    $order['Person']['discount'] = 0;
    $order['Person']['user_id'] = '';
    $order['Person']['order_metod'] = '';
    $insert['dop_info_new'] = $comment;

    // данные для записи в БД
    $insert['datas_new'] = time();
    $insert['uid_new'] = $WbSeller->setOrderNum();
    $insert['orders_new'] = serialize($order);
    $insert['fio_new'] = $name;
    $insert['tel_new'] = $phone;
    $insert['city_new'] = PHPShopString::utf8_win1251($order_info['prioritySc'][0]);
    $insert['statusi_new'] = $WbSeller->status;
    $insert['status_new'] = serialize(array("maneger" => __('WB заказ &#8470;' . $_POST['rowID'])));
    $insert['sum_new'] = $order['Cart']['sum'];
    $insert['wbseller_order_data_new'] = $_POST['rowID'];

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