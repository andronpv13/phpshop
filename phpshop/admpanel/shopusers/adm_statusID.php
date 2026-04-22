<?php

$TitlePage = __('Редактирование статуса') . ' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers_status']);
PHPShopObj::loadClass('user');

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopModules,$hideCatalog;

    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_REQUEST['id'])));

    // Нет данных
    if (!is_array($data)) {
        header('Location: ?path=' . $_GET['path']);
    }

    // Размер названия поля
    $PHPShopGUI->field_col = 4;
    $PHPShopGUI->addJSFiles('./shopusers/gui/shopusers.gui.js');
    $PHPShopGUI->setActionPanel(__("Покупатели") . ' / ' . __('Статусы') . ' / ' . $data['name'], array('Удалить'), array('Сохранить', 'Сохранить и закрыть'));

    // Содержание закладки 1
    $Tab1 = $PHPShopGUI->setCollapse('Информация', $PHPShopGUI->setField("Название", $PHPShopGUI->setInput('text.required', "name_new", $data['name'])) .
            $PHPShopGUI->setField("Скидка", $PHPShopGUI->setInputText('%', "discount_new", $data['discount'], 100)) .
            $PHPShopGUI->setField("Минимальная сумма заказа", $PHPShopGUI->setInputText(false, 'cart_min_new', $data['cart_min'], 100)) .
            $PHPShopGUI->setField("Колонка цен", $PHPShopGUI->setSelect('price_new', $PHPShopGUI->setSelectValue($data['price'], 5), 100)) .
            $PHPShopGUI->setField("Статус", $PHPShopGUI->setRadio("enabled_new", 1, "Вкл.", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new", 0, "Выкл.", $data['enabled'])) .
        $PHPShopGUI->setField("Склад", $PHPShopGUI->setRadio("warehouse_new", 1, "Вкл.", $data['warehouse']) . $PHPShopGUI->setRadio("warehouse_new", 0, "Выкл.", $data['warehouse'])), 'in', false);

    if(empty($hideCatalog))
    $Tab1 .= $PHPShopGUI->setCollapse('Накопительные скидки', '<p class="text-muted hidden-xs">' . __('Для учета мгновенной скидки от текущей стоимости заказа без привязки к статусу пользователя и накопления перейдите в раздел') . ' <a href="?path=shopusers.discount"><span class="glyphicon glyphicon-share-alt"></span> ' . __('Скидки от заказа') . '</a>.<br>' . __('Для учета накопительной скидки требуется включить опцию учета скидки покупателя в нужном статусе заказа, например "Выполнен"') . '.</p>' .
            $PHPShopGUI->setCheckbox('cumulative_discount_check_new', 1, 'Использование накопительной скидки', $data['cumulative_discount_check']) .
                    $PHPShopGUI->loadLib('tab_discount', $data['cumulative_discount'], 'shopusers/')
    );

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true,false,true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "Удалить", "right", 70, "", "but", "actionDelete.shopusers.edit") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.shopusers.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.shopusers.edit");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Функция удаления
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);


    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array('success' => $action);
}

/**
 * Экшен сохранения
 */
function actionSave() {

    // Сохранение данных
    actionUpdate();

    header('Location: ?path=' . $_GET['path']);
}

// Функция обновления
function actionUpdate() {
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

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array('success' => $action);
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>