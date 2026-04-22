<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.visualcart.visualcart_system"));

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));

}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    if (empty($_POST['memory_new']))
        $_POST['memory_new'] = 0;
    
    if (empty($_POST['nowbuy_new']))
        $_POST['nowbuy_new'] = 0;

    if (empty($_POST['referal_new']))
        $_POST['referal_new'] = 0;

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();

    // Место вывода
    $e_value[] = array('корзина', 0, $data['enabled']);
    $e_value[] = array('слева', 1, $data['enabled']);
    $e_value[] = array('справа', 2, $data['enabled']);
    

    $Tab1 = $PHPShopGUI->setField('Заголовок блока', $PHPShopGUI->setInputText(false, 'title_new', $data['title'],300),1,'Для вывода слева или справа');
    $Tab1.= $PHPShopGUI->setField('Память корзины', $PHPShopGUI->setInputText(false, 'day_new', $data['day'],100,__('дней')).$PHPShopGUI->setCheckbox('memory_new', 1, 'Хранить незаконченные корзины в базе', $data['memory']));
    
    $Tab1.=$PHPShopGUI->setField('Сейчас покупают', $PHPShopGUI->setCheckbox('nowbuy_new', 1, 'Вывод случайного товара из последних заказов', $data['nowbuy']));
    $Tab1.=$PHPShopGUI->setField('Источник', $PHPShopGUI->setCheckbox('referal_new', 1, 'Добавлять источник перехода в комментарий менеджеру', $data['referal']));
    $Tab1.=$PHPShopGUI->setField('Место вывода', $PHPShopGUI->setSelect('enabled_new', $e_value, 150,true));
    $Tab1.= $PHPShopGUI->setField('Рассылка уведомлений', $PHPShopGUI->setInputText(false, 'sendmail_new', $data['sendmail'],100,__('писем')),1,'Количество писем при рассылке за раз');
   

    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay($serial = false, false, $data['version'], true);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("Брошенные корзины", null, '?path=modules.dir.visualcart'), array("Журнал добавления в корзину", null, '?path=modules.dir.visualcart.log'), array("О Модуле", $Tab3));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>