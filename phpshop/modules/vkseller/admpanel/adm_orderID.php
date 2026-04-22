<?php

include_once dirname(__FILE__) . '/../class/VkSeller.php';
$TitlePage = __('Заказ из VK') . ' #' . $_GET['id'];
PHPShopObj::loadClass("product");

$VkSeller = new VkSeller();

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopInterface, $VkSeller;

    // Данные по заказу
    $order_info = $VkSeller->getOrder($_GET['id'])['response']['order'];

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

    $status_array = ['Новый заказ', 'Согласовывается', 'Собирается', 'Доставляется', 'Выполнен', 'Отменен', 'Возврат'];


    $Tab3 = $PHPShopGUI->setTextarea(null, PHPShopString::utf8_win1251($log), $float = "none", $width = '100%', $height = '500');
    $Tab1 = $PHPShopGUI->setField("&#8470; заказа", $PHPShopGUI->setText($order_info['id']));
    $Tab1 .= $PHPShopGUI->setField("Дата поступления", $PHPShopGUI->setText(PHPShopDate::get($order_info['date'], true)));
    $Tab1 .= $PHPShopGUI->setField("Статус", $PHPShopGUI->setText($status_array[$order_info['status']]));

    $user = $VkSeller->getUser($order_info['user_id'])['response'][0]['screen_name'];

    $Tab1 .= $PHPShopGUI->setField("Покупатель", $PHPShopGUI->setText($PHPShopGUI->setLink('https://vk.ru/' . $user, PHPShopString::utf8_win1251($order_info['recipient']['name']), '_blank', false, false, false, false, false), "left", false, false));
    $Tab1 .= $PHPShopGUI->setField("Телефон", $PHPShopGUI->setText(PHPShopString::utf8_win1251($order_info['recipient']['phone']), "left", false, false));
    $Tab1 .= $PHPShopGUI->setField("Доставка", $PHPShopGUI->setText(PHPShopString::utf8_win1251($order_info['delivery']['address']), "left", false, false));
    $Tab1 .= $PHPShopGUI->setField("Оплата", $PHPShopGUI->setText(PHPShopString::utf8_win1251($order_info['payment']['status']), "left", false, false));
    $Tab1 .= $PHPShopGUI->setField("Комментарий", $PHPShopGUI->setText(PHPShopString::utf8_win1251($order_info['comment']), "left", false, false));

    $Tab1 .= $PHPShopGUI->setInput("hidden", "date_start", $_GET['date_start']);
    $Tab1 .= $PHPShopGUI->setInput("hidden", "date_end", $_GET['date_end']);

    $Tab1 = $PHPShopGUI->setCollapse('Данные', $Tab1);
    $Tab3 = $PHPShopGUI->setCollapse('JSON данные', $Tab3);

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setCaption(array("Наименование", "50%"), array("Цена", "15%"), array("Кол-во", "10%"), array("Сумма", "15%", array('align' => 'right')));

    $data = $order_info['preview_order_items'];
    if (is_array($data))
        foreach ($data as $row) {

            // Проверка по VK ID
            $product = new PHPShopProduct($row['item_id'], 'export_vk_id');

            // Проверка по ИД и артикулу
            if (empty($product->getName())) {
                if ($VkSeller->vk_options['type'] == 1)
                    $product = new PHPShopProduct($row['item']['sku'], 'id');
                else
                    $product = new PHPShopProduct($row['item']['sku'], 'uid');
            }
            
            if (empty($product->getName()))
                continue;

            if (!empty($product->getValue('pic_small')))
                $icon = '<img src="' . $product->getValue('pic_small') . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
            else
                $icon = '<img class="media-object" src="./images/no_photo.gif">';

            $name = '
<div class="media">
  <div class="media-left">
    <a href="?path=product&id=' . $product->getValue('id') . '&return=modules.dir.ozonseller" >
      ' . $icon . '
    </a>
  </div>
   <div class="media-body">
    <div class="media-heading"><a href="?path=product&id=' . $product->getValue('id') . '&return=modules.dir.ozonseller" >' . $product->getValue('name') . '</a></div>
    ' . __('Арт') . ': ' . $product->getValue('uid') . '
  </div>
</div>';
            $price = round($row['price']['amount'] / 100);

            $PHPShopInterface->setRow($name, $price, array('name' => $row['quantity'], 'align' => 'center'), array('name' => number_format($price * $row['quantity'], 0, '', ' ') . $currency, 'align' => 'right'));
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
    global $VkSeller;

    // Данные по заказу
    $order_info = $VkSeller->getOrder($_POST['rowID'])['response']['order'];

    $name = PHPShopString::utf8_win1251($order_info['recipient']['name'], true);
    $phone = PHPShopString::utf8_win1251($order_info['recipient']['phone'], true);
    $mail = null;
    $comment = PHPShopString::utf8_win1251($order_info['comment'], true);
    $pay = PHPShopString::utf8_win1251($order_info['payment']['status'], true);

    // таблица заказов
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
    $qty = $sum = $weight = 0;

    $data = $order_info['preview_order_items'];
    if (is_array($data))
        foreach ($data as $row) {

            $product = new PHPShopProduct($row['item_id'], 'export_vk_id');

            // Проверка по ИД и артикулу
            if (empty($product->getName())) {
                if ($VkSeller->vk_options['type'] == 1)
                    $product = new PHPShopProduct($row['item']['sku'], 'id');
                else
                    $product = new PHPShopProduct($row['item']['sku'], 'uid');
            }
            
            if (empty($product->getName()))
                continue;

            $id = $product->getParam('id');
            $price = round($row['price']['amount'] / 100);
            $order['Cart']['cart'][$id]['id'] = $product->getParam('id');
            $order['Cart']['cart'][$id]['uid'] = $product->getParam("uid");
            $order['Cart']['cart'][$id]['name'] = $product->getName();
            $order['Cart']['cart'][$id]['price'] = $price;
            $order['Cart']['cart'][$id]['num'] = $row['quantity'];
            $order['Cart']['cart'][$id]['weight'] = '';
            $order['Cart']['cart'][$id]['ed_izm'] = '';
            $order['Cart']['cart'][$id]['pic_small'] = $product->getImage();
            $order['Cart']['cart'][$id]['parent'] = 0;
            $order['Cart']['cart'][$id]['user'] = 0;
            $qty += $row['quantity'];
            $sum += $price * $row['quantity'];
            $weight += $product->getParam('weight');
        }

    $order['Cart']['num'] = $qty;
    $order['Cart']['sum'] = $sum;
    $order['Cart']['weight'] = $weight;
    $order['Cart']['dostavka'] = intval($order_info['total_price']['amount']/100-$sum);

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
    $order['Person']['dostavka_metod'] = (int) $VkSeller->delivery;
    $order['Person']['discount'] = 0;
    $order['Person']['user_id'] = '';
    $order['Person']['order_metod'] = '';
    $insert['dop_info_new'] = $comment;

    // данные для записи в БД
    $insert['datas_new'] = time();
    $insert['uid_new'] = $VkSeller->setOrderNum();
    $insert['orders_new'] = serialize($order);
    $insert['fio_new'] = $name;
    $insert['tel_new'] = $phone;
    $insert['city_new'] = PHPShopString::utf8_win1251($order_info['delivery']['address'].' '.$order_info['delivery']['type'], true);
    $insert['statusi_new'] = $VkSeller->status;
    $insert['status_new'] = serialize(array("maneger" => __('VK заказ &#8470;' . $_POST['rowID']).', '.$pay));
    $insert['sum_new'] = $order['Cart']['sum'];
    $insert['vkseller_order_data_new'] = $_POST['rowID'];

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