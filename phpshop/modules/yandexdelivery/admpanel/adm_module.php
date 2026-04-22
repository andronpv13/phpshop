<?php

include_once dirname(__DIR__) . '/class/include.php';

// SQL
$PHPShopOrm = new PHPShopOrm('phpshop_modules_yandexdelivery_system');

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

    if (!isset($_POST['paid_new'])) {
        $_POST['paid_new'] = '0';
    }

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.yandexdelivery.yandexdelivery_system"));
    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);

    header('Location: ?path=modules&id=' . $_GET['id']);

    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;


    // Выбор ПВЗ отправки
    $PHPShopGUI->addJSFiles('../modules/yandexdelivery/admpanel/gui/warehouse.gui.js');


    // Выборка
    $data = $PHPShopOrm->select();

    if (empty($data['warehouse_id']))
        $buttonText = 'Выбрать';
    else
        $buttonText = 'Изменить';

    $Tab1 .= $PHPShopGUI->setField('Токен Яндекс.OAuth', $PHPShopGUI->setInputText(false, 'token_new', $data['token'], 400));
    $Tab1 .= $PHPShopGUI->setField('Станция отгрузки', $PHPShopGUI->setInputText(false, 'warehouse_id_new', $data['warehouse_id'], 400, '<a id="yandexdelivery-select-warehouse" href="#">' . __($buttonText) . '</a>'));
    $Tab1 .= $PHPShopGUI->setField('Статус для отправки', $PHPShopGUI->setSelect('status_new', YandexDelivery::getDeliveryStatuses($data['status']), 300));
    $Tab1 .= $PHPShopGUI->setField('Доставка', $PHPShopGUI->setSelect('delivery_id_new', YandexDelivery::getDeliveryVariants($data['delivery_id']), 300));
    $Tab1 .= $PHPShopGUI->setField('Город на карте по умолчанию', $PHPShopGUI->setInputText(false, 'city_new', $data['city'], 300));
    $Tab1 .= $PHPShopGUI->setField('Добавить наценку', '<input class="form-control input-sm " onkeypress="yadeliveryvalidate(event)" type="number" step="0.1" min="0" value="' . $data['fee'] . '" name="fee_new" style="width:300px;">');
    $Tab1 .= $PHPShopGUI->setField('Тип наценки', $PHPShopGUI->setSelect('fee_type_new', array(array('%', 1, $data['fee_type']), array('Руб.', 2, $data['fee_type'])), 300, null, false, $search = false, false, 1));
    $Tab1 .= $PHPShopGUI->setField('Статус оплаты', $PHPShopGUI->setCheckbox('paid_new', 1, 'Заказ оплачен', $data["paid"]));
    
    $Tab1 = $PHPShopGUI->setCollapse('Настройки',$Tab1);


    $Tab1 .= $PHPShopGUI->setCollapse('Вес и габариты по умолчанию', $PHPShopGUI->setField('Вес, гр.', '<input class="form-control input-sm " onkeypress="yadeliveryvalidate(event)" type="number" step="1" min="1" value="' . $data['weight'] . '" name="weight_new" style="width:300px; ">') .
            $PHPShopGUI->setField('Ширина, см.', '<input class="form-control input-sm " onkeypress="yadeliveryvalidate(event)" type="number" step="1" min="1" value="' . $data['width'] . '" name="width_new" style="width:300px;">') .
            $PHPShopGUI->setField('Высота, см.', '<input class="form-control input-sm " onkeypress="yadeliveryvalidate(event)" type="number" step="1" min="1" value="' . $data['height'] . '" name="height_new" style="width:300px;">') .
            $PHPShopGUI->setField('Длина, см.', '<input class="form-control input-sm " onkeypress="yadeliveryvalidate(event)" type="number" step="1" min="1" value="' . $data['length'] . '" name="length_new" style="width:300px;">')
    );

    PHPShopParser::set('yandexdelivery_weight', $data['weight']);
    PHPShopParser::set('yandexdelivery_city', $data['city']);
    PHPShopParser::set('yandexdelivery_station', $data['warehouse_id']);
    //$Tab1 .= ParseTemplateReturn('../modules/yandexdelivery/templates/template.tpl', true);
    
    $GLOBALS['modalExternalModules'] = ParseTemplateReturn(dirname(__DIR__) . '/templates/template.tpl', true);


    $info = '<h4>Получение учетных данных и настройка</h4>
       <ol>
        <li>Зарегистрируйтесь в <a href="https://dostavka.yandex.ru" target="_blank">Яндекс.Доставке</a> заполните все необходимые данные.
        <li>Получите  <a href="https://dostavka.yandex.ru/account2/integration" target="_blank">Токен Яндекс.OAuth</a> и впишите его в поле <kbd>Токен Яндекс.OAuth</kbd>.</li>
        <li>Выбрать станцию отгрузки в настройках настроек модуля.</li>
        <li>Выбрать способ доставки для работы модуля.</li>
        <li>Выбрать статус для передачи заказа в личный кабинет Яндекс.Доставки.</li>
        </ol>
        
       <h4>Настройка доставки</h4>
        <ol>
        <li>В карточке редактирования доставки в закладке <kbd>Изменение стоимости доставки</kbd> настроить дополнительный параметр сохранения стоимости доставки для модуля. Опция "Не изменять стоимость" должна быть активна.</li>
         <li>В карточке редактирования доставки в закладке <kbd>Адреса пользователя</kbd> отметить <kbd>Телефон</kbd> "Вкл." и "Обязательное".</li>
        </ol>

';

    $Tab2 = $PHPShopGUI->setInfo($info);

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
?>