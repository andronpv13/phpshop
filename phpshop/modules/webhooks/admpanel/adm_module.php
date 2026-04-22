<?php

PHPShopObj::loadClass('order');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['webhooks']['webhooks_system']);

function actionUpdate() {
    global $PHPShopOrm;
    $PHPShopOrm->debug = false;

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    $data = $PHPShopOrm->select();

    // Инструкция
    $info = '
        <h4>Настройка модуля</h4>
        <ol>
<li>В поле <kbd>URL WebHook</kbd> ввести URL для приема данных.</li>
<li>Выбрать действие срабатывания WebHook (новый заказ, новый пользователь и т.д.).</li>
<li>Выбрать метод передачи данных.</li>
<li>Список переданных данных доступен в закладке <kbd>Журнал выполнения</kbd> по сылке в колонке "Действие".</li>
<li>После срабатывания WebHook все данные будут переданы на указанный "URL WebHook" для дальнейшей обработки сервисами автоматизации <a href="https://apix-drive.com/?p=816b11bc8e756b0cd344fb728e2a2727" target="_blank">APIXDrive</a> и <a href="https://zapier.com" target="_blank">Zapier</a>.</li>
</ol>';

    $Tab2 = $PHPShopGUI->setInfo($info);
    $Tab3 = $PHPShopGUI->setPay($serial = false, false, $data['version'], true);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Инструкция", $Tab2), array("О Модуле", $Tab3), array("Обзор WebHooks", null, '?path=modules.dir.webhooks'));

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