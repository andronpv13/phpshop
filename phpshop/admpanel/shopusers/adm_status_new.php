<?php

$TitlePage = __('Создание статуса');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers_status']);
PHPShopObj::loadClass('user');

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $TitlePage, $PHPShopModules,$hideCatalog,$hideSite;

    // Начальные данные
    $data['enabled'] = $data['warehouse'] = 1;
    $data = $PHPShopGUI->valid($data, 'name', 'discount', 'price', 'cumulative_discount', 'cumulative_discount_check','warehouse');

    // Размер названия поля
    $PHPShopGUI->field_col = 4;
    $PHPShopGUI->addJSFiles('./shopusers/gui/shopusers.gui.js');
    $PHPShopGUI->setActionPanel(__("Покупатели") . ' / ' . $TitlePage, false, array('Сохранить и закрыть', 'Создать и редактировать'));
    // Содержание закладки 1
    $Tab1 = $PHPShopGUI->setCollapse('Информация', $PHPShopGUI->setField("Название", $PHPShopGUI->setInput('text.required', "name_new", $data['name'])) .
            $PHPShopGUI->setField("Скидка", $PHPShopGUI->setInputText('%', "discount_new", $data['discount'], 100)) .
            $PHPShopGUI->setField("Минимальная сумма заказа", $PHPShopGUI->setInputText(false, 'cart_min_new', intval($data['cart_min']), 100)) .
            $PHPShopGUI->setField("Колонка цен", $PHPShopGUI->setSelect('price_new', $PHPShopGUI->setSelectValue($data['price'], 5), 100)) .
            $PHPShopGUI->setField("Статус", $PHPShopGUI->setRadio("enabled_new", 1, "Вкл.", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new", 0, "Выкл.", $data['enabled'])) .
            $PHPShopGUI->setField("Склад", $PHPShopGUI->setRadio("warehouse_new", 1, "Вкл.", $data['warehouse']) . $PHPShopGUI->setRadio("warehouse_new", 0, "Выкл.", $data['warehouse']))
    );

    if(empty($hideCatalog))
    $Tab1 .= $PHPShopGUI->setCollapse('Накопительные скидки', '<p class="text-muted hidden-xs">' . __('Для учета мгновенной скидки от текущей стоимости заказа без привязки к статусу пользователя и накопления перейдите в раздел') . ' <a href="?path=shopusers.discount"><span class="glyphicon glyphicon-share-alt"></span> ' . __('Скидки от заказа') . '</a>.<br>' . __('Для учета накопительной скидки требуется включить опцию учета скидки покупателя в нужном статусе заказа, например "Выполнен"') . '.</p>' .
            $PHPShopGUI->setCheckbox('cumulative_discount_check_new', 1, 'Использование накопительной скидки', $data['cumulative_discount_check']) .
            $PHPShopGUI->loadLib('tab_discount', $data['cumulative_discount'], 'shopusers/')
    );

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1,true,false,true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "ОК", "right", 70, "", "but", "actionInsert.shopusers.create");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция записи
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules;

    // Накопительные скидки
    foreach ($_POST['cumulative_sum_ot'] as $key => $value) {
        if ($_POST['cumulative_discount'][$key] != '') {
            $cumulative_array[$key]['cumulative_sum_ot'] = $value;
            $cumulative_array[$key]['cumulative_sum_do'] = $_POST['cumulative_sum_do'][$key];
            $cumulative_array[$key]['cumulative_discount'] = $_POST['cumulative_discount'][$key];
            $cumulative_array[$key]['cumulative_enabled'] = intval($_POST['cumulative_enabled'][$key]);
        }
    }

    // Сериализация
    $_POST['cumulative_discount_new'] = serialize($cumulative_array);

    // Корректировка пустых значений
    $PHPShopOrm->updateZeroVars('cumulative_discount_check_new');

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->insert($_POST);

    if ($_POST['saveID'] == 'Создать и редактировать')
        header('Location: ?path=' . $_GET['path'] . '&id=' . $action);
    else
        header('Location: ?path=' . $_GET['path']);

    return $action;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>