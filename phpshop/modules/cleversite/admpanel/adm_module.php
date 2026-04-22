<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.cleversite.cleversite_system"));

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    $PHPShopGUI->title = "Настройка модуля Cleversite";

    // Выборка
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('ID',$PHPShopGUI->setInputText(false, 'client_new', $data['client'], '300'));
    $Tab1.= $PHPShopGUI->setField('ID сайта',$PHPShopGUI->setInputText(false, 'site_new', $data['site'], '300'));

    $Info = '<h4>Для вставки данного модуля следуйте инструкции:</h4>
        <ol>
        <li> Зарегистрируйтесь на сайте <a href="https://cleversite.ru/?ref=qD3jt" target="_blank"> cleversite.ru</a>
		<li> Получите на почту письмо с регистрационными данными.
		<li> Выберете в личном кабинете какие виджеты Вы хотите отобразить на своем сайте.
        <li> Скопируйте Ваш ID и вставьте его в поле "ID" на вкладке "Основное" текущего окна настройки модуля.
		<li> Скопируйте Ваш ID сайта для вставки кода и вставьте его в поле "ID сайта" на вкладке "Основное" текущего окна настройки модуля.
		<li> Сохраните введенные Вами данные.
		</ol>';
    $Tab2 = $PHPShopGUI->setInfo($Info, '200px', '100%');

    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay();

    $About = 'Если у Вас возникли вопросы, то можете писать оператору на <a href="http://cleversite.ru/" target="_blank">нашем сайту</a> в онлайн-консультант или отправить сообщение на <a href="mailto:help@cleversite.ru">help@cleversite.ru</a>, принимаем Ваши обращения 24 часа в сутки. Мы поможем установить код на Ваш сайт и начать работу в системе.';
    $Tab3.=$PHPShopGUI->setInfo($About);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $PHPShopGUI->setCollapse('Авторизация',$Tab1)), array("Инструкция", $Tab2), array("О Модуле", $Tab3));

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
