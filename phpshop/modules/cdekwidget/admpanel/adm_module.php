<?php

PHPShopObj::loadClass("order");
PHPShopObj::loadClass("delivery");

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.cdekwidget.cdekwidget_system"));

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

// Функция обновления
function actionUpdate() {
    global $PHPShopModules;

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    if (!isset($_POST['paid_new'])) {
        $_POST['paid_new'] = '0';
    }

    // Доставки
    if (isset($_POST['delivery_id_new'])) {
        if (is_array($_POST['delivery_id_new'])) {
            foreach ($_POST['delivery_id_new'] as $val) {
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['delivery']);
                $PHPShopOrm->update(array('is_mod_new' => 2), array('id' => '=' . intval($val)));
            }
            $_POST['delivery_id_new'] = @implode(',', $_POST['delivery_id_new']);
        }
    }
    if (empty($_POST['delivery_id_new']))
        $_POST['delivery_id_new'] = '';

    if (empty($_POST['test_new']))
        $_POST['test_new'] = 0;
    if (empty($_POST['russia_only_new']))
        $_POST['russia_only_new'] = 0;


    include_once dirname(__FILE__) . '/../class/CDEKWidget.php';
    $CDEKWidget = new CDEKWidget();

    if (!isset($_POST['webhook_new'])) {
        $_POST['webhook_new'] = '0';
        $CDEKWidget->delWebhook();
    }
    else {
        $CDEKWidget->setWebhook();
    }


    $getCityCode = $CDEKWidget->getCityCode($_POST['city_from_new'])[0]['code'];

    if (!empty($getCityCode))
        $_POST['city_from_code_new'] = $getCityCode;

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.cdekwidget.cdekwidget_system"));
    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);

    header('Location: ?path=modules&id=' . $_GET['id']);

    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopSystem;


    $PHPShopGUI->addJSFiles('../modules/cdekwidget/admpanel/gui/script.gui.js?v=1.5');
    // Подсказки
    if ($PHPShopSystem->ifSerilizeParam('admoption.dadata_enabled')) {
        $PHPShopGUI->addJSFiles('./js/jquery.suggestions.min.js', './order/gui/dadata.gui.js');
        $PHPShopGUI->addCSSFiles('./css/suggestions.min.css');
    }

    // Выборка
    $data = $PHPShopOrm->select();

    // Доступые статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();

    $status[] = array(__('Новый заказ'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status) {
            $status[] = array($order_status['name'], $order_status['id'], $data['status']);
        }

    // Доставка
    $PHPShopDeliveryArray = new PHPShopDeliveryArray(array('is_folder' => "!='1'", 'enabled' => "='1'"));

    $DeliveryArray = $PHPShopDeliveryArray->getArray();
    if (is_array($DeliveryArray)) {
        foreach ($DeliveryArray as $delivery) {

            if (strpos($delivery['city'], '.')) {
                $name = explode(".", $delivery['city']);
                $delivery['city'] = $name[0];
            }

            if (in_array($delivery['id'], @explode(",", $data['delivery_id'])))
                $delivery_id = $delivery['id'];
            else
                $delivery_id = null;

            $delivery_value[] = array($delivery['city'], $delivery['id'], $delivery_id);
        }
    }

    $Tab1 = $PHPShopGUI->setField('Аккаунт интеграции', $PHPShopGUI->setInputText(false, 'account_new', $data['account'], 300));
    $Tab1 .= $PHPShopGUI->setField('Пароль интеграции', $PHPShopGUI->setInputText(false, 'password_new', $data['password'], 300));
    $Tab1 .= $PHPShopGUI->setField('Вебхуки', $PHPShopGUI->setCheckbox("webhook_new", 1, "Отправлять сообщения в мессенджеры покупателя о смене статуса доставки", $data["webhook"]));
    $Tab1 .= $PHPShopGUI->setField('Только регионы РФ', $PHPShopGUI->setCheckbox("russia_only_new", 1, "Отображать в виджете только города России", $data["russia_only"]));
    $Tab1 .= $PHPShopGUI->setField('Статус для отправки', $PHPShopGUI->setSelect('status_new', $status, 300));
    $Tab1 .= $PHPShopGUI->setField('Доставка', $PHPShopGUI->setSelect('delivery_id_new[]', $delivery_value, 300, null, false, $search = false, false, $size = 1, true));
    $Tab1 .= $PHPShopGUI->setField('Город отправки отправлений', $PHPShopGUI->setInputText(false, 'city_from_new', $data['city_from'], 300));
    $Tab1 .= $PHPShopGUI->setField('Почтовый индекс города отправителя', '<input class="form-control input-sm " onkeypress="cdekvalidate(event)" type="text" value="' . $data['index_from'] . '" name="index_from_new" style="width:300px; ">');
    $Tab1 .= $PHPShopGUI->setField('Город на карте по умолчанию', $PHPShopGUI->setInputText(false, 'default_city_new', $data['default_city'], 300));
    $Tab1 .= $PHPShopGUI->setField('Добавить наценку', $PHPShopGUI->setInputText(false, 'fee_new', $data['fee'], 100));
    $Tab1 .= $PHPShopGUI->setField('Тип наценки', $PHPShopGUI->setSelect('fee_type_new', array(array('%', 1, $data['fee_type']), array('Руб.', 2, $data['fee_type'])), 100, true, false, $search = false, false, $size = 1));
    $Tab1 .= $PHPShopGUI->setField('Дополнительный сбор за доставку', $PHPShopGUI->setInputText(false, 'cost_new', $data['cost'], 100,'%'));
    $Tab1 .= $PHPShopGUI->setField('Статус оплаты', $PHPShopGUI->setCheckbox('paid_new', 1, 'Заказ оплачен', $data["paid"]));

    $Tab1 = $PHPShopGUI->setCollapse('Настройки', $Tab1);
    $Tab1 .= $PHPShopGUI->setCollapse('Вес и габариты по умолчанию', $PHPShopGUI->setField('Вес, гр.', '<input class="form-control input-sm " onkeypress="cdekvalidate(event)" type="number" step="1" min="1" value="' . $data['weight'] . '" name="weight_new" style="width:300px; ">') .
            $PHPShopGUI->setField('Ширина, см.', '<input class="form-control input-sm " onkeypress="cdekvalidate(event)" type="number" step="1" min="1" value="' . $data['width'] . '" name="width_new" style="width:300px;">') .
            $PHPShopGUI->setField('Высота, см.', '<input class="form-control input-sm " onkeypress="cdekvalidate(event)" type="number" step="1" min="1" value="' . $data['height'] . '" name="height_new" style="width:300px;">') .
            $PHPShopGUI->setField('Длина, см.', '<input class="form-control input-sm " onkeypress="cdekvalidate(event)" type="number" step="1" min="1" value="' . $data['length'] . '" name="length_new" style="width:300px;">')
    );

    // Инструкция
    $Tab2 = $PHPShopGUI->loadLib('tab_info', $data, '../modules/' . $_GET['id'] . '/admpanel/');

    // Форма регистрации
    $Tab4 = $PHPShopGUI->setPay($serial = false, false, $data['version'], true);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("Инструкция", $Tab2), array("О Модуле", $Tab4));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
