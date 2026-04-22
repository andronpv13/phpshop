<?php
PHPShopObj::loadClass('order');
// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.oneclick.oneclick_system"));

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    if (empty($_POST["only_available_new"]))
        $_POST["only_available_new"] = 0;

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm,$hideCatalog;

    // Выборка
    $data = $PHPShopOrm->select();


    // Вывод
    $e_value[] = array('кнопка купить', 0, $data['enabled']);
    $e_value[] = array('слева', 1, $data['enabled']);
    $e_value[] = array('справа', 2, $data['enabled']);

    // Тип вывода
    $w_value[] = array('форма', 0, $data['windows']);
    $w_value[] = array('всплывающее окно', 1, $data['windows']);

    // Место вывода
    $d_value[] = array('подробное описание', 0, $data['display']);
    $d_value[] = array('подробное и краткое описание', 1, $data['display']);

    // Храненение заказов
    $o_value[] = array('отдельная база заказов', 0, $data['write_order']);
    
    if(empty($hideCatalog))
    $o_value[] = array('общая база заказов', 1, $data['write_order']);

    // Captcha
    $c_value[] = array('нет', 0, $data['captcha']);
    $c_value[] = array('есть', 1, $data['captcha']);

    // Доступые статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();

    $status_array = $PHPShopOrderStatusArray->getArray();
    $status_array[0] = ['name' => __('Новый заказ'), 'sklad_action' => false, 'id' => 0,'description'=>__('Системный')];

    if (is_array($status_array))
        foreach ($status_array as $status_val) {
            if ($status_val['id'] != 1)
                $order_status_value[] = array($status_val['name'], $status_val['id'], $data['status'],'data-subtext="'.$status_val['description'].'"');
        }

    $Tab1 = $PHPShopGUI->setField('Заголовок', $PHPShopGUI->setInputText(false, 'title_new', $data['title']));
    $Tab1 .= $PHPShopGUI->setField('Сообщение', $PHPShopGUI->setTextarea('title_end_new', $data['title_end']));
    $Tab1 .= $PHPShopGUI->setField('Место вывода', $PHPShopGUI->setSelect('enabled_new', $e_value, 250,true));
    $Tab1 .= $PHPShopGUI->setField('Тип вывода', $PHPShopGUI->setSelect('windows_new', $w_value, 250,true));
    $Tab1 .= $PHPShopGUI->setField('Вывод', $PHPShopGUI->setSelect('display_new', $d_value, 250,true));
    $Tab1 .= $PHPShopGUI->setField('Запись заказа', $PHPShopGUI->setSelect('write_order_new', $o_value, 250,true));
    
    if(empty($hideCatalog))
    $Tab1 .= $PHPShopGUI->setField('Статус заказа', $PHPShopGUI->setSelect('status_new', $order_status_value, 250));
    
    $Tab1 .= $PHPShopGUI->setField('Защитная картинка', $PHPShopGUI->setSelect('captcha_new', $c_value, 250,true));
    
    
    $a_value[]=array('все', 0, $data['only_available']);
    $a_value[]=array('только в наличии', 1, $data['only_available']);
    $a_value[]=array('только под заказ', 2, $data['only_available']);
    
    $Tab1 .= $PHPShopGUI->setField('Показывать у товаров', $PHPShopGUI->setSelect('only_available_new', $a_value, 250,true));


    $Tab2 = $PHPShopGUI->setInfo($info);

    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], true);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("О Модуле", $Tab3), array("Обзор заявок", 0, '?path=modules.dir.oneclick'));

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