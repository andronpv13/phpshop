<?php

PHPShopObj::loadClass('order');

// SQL
$PHPShopOrm = new PHPShopOrm("phpshop_modules_twocan_system");

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    if (empty($_POST["dev_mode_new"]))
        $_POST["dev_mode_new"] = 0;

    if (empty($_POST["autocharge_new"]))
        $_POST["autocharge_new"] = 0;

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();
    @extract($data);

    // Настройки для шлюза
    $Tab2 = $PHPShopGUI->setField('Логин API', $PHPShopGUI->setInputText(false, 'login_new', $data['login'], 300));
    $Tab2 .= $PHPShopGUI->setField('Пароль API', $PHPShopGUI->setInput("password", 'password_new', $data['password'], false, 300));
    $Tab2 .= $PHPShopGUI->setField('Terminal ID', $PHPShopGUI->setInputText(false, 'terminal_new', $data['terminal'], 300));
    $Tab2 .= $PHPShopGUI->setField('URL Шлюза', $PHPShopGUI->setInputText(false, 'url_new', $data['url'], 300));
    $Tab2 .= $PHPShopGUI->setField('URL Тестового Шлюза', $PHPShopGUI->setInputText(false, 'test_url_new', $data['test_url'], 300));
    
    $Tab2 .= $PHPShopGUI->setField('Одностадийная оплата', $PHPShopGUI->setCheckbox("autocharge_new", 1, "", $data["autocharge"]));
    $Tab2 .= $PHPShopGUI->setField('Режим разработки', $PHPShopGUI->setCheckbox("dev_mode_new", 1, "Отправка данных на тестовую среду", $data["dev_mode"]));

    $Tab2 .= $PHPShopGUI->setField('Шаблон', $PHPShopGUI->setInputText(false, 'template_new', $data['template'], 300));
    $Tab2 .= $PHPShopGUI->setField('Таймаут сессии', $PHPShopGUI->setInputText(false, 'exptimeout_new', $data['exptimeout'], 300));
    
 
    // Доступные статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = $order_status_auth_value[] = array(__('Новый заказ'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status){
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);
            $order_status_auth_value[] = array($order_status['name'], $order_status['id'], $data['status_auth']);
        }

    // Статус заказа
    $Tab2 .= $PHPShopGUI->setField('Оплата при статусе', $PHPShopGUI->setSelect('status_new', $order_status_value, 300));
    $Tab2 .= $PHPShopGUI->setField('Статус при двустадийном платеже', $PHPShopGUI->setSelect('status_auth_new', $order_status_auth_value, 300));
    $Tab2 .= $PHPShopGUI->setField('Сообщение предварительной проверки:', $PHPShopGUI->setTextarea('title_sub_new', $data['title_sub'],true,300));
    
    
   
    

    // Инструкция
    $info = file_get_contents('../../phpshop/modules/twocan/inc/instructions.html');
        

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Настройки", $Tab2, true), array("Инструкция", $PHPShopGUI->setInfo($info)), array("О Модуле", $PHPShopGUI->setPay(false, false, $data['version'], false)));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>