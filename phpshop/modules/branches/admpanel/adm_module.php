<?php

include_once dirname(__DIR__) . '/class/include.php';

$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam('base.branches.branches_system'));

function actionUpdate() {
    global $PHPShopModules;

    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam('base.branches.branches_system'));
    $PHPShopOrm->debug = false;

    $_POST['favorite_cities_new'] = serialize($_POST['favorite_cities']);

    $action = $PHPShopOrm->update($_POST);

    header('Location: ?path=modules&id=' . $_GET['id']);

    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    $data = $PHPShopOrm->select();

    $Branches = new Branches();

    $Tab1 = $PHPShopGUI->setField('API Ключ Яндекс.Карт', $PHPShopGUI->setInputText(false, 'yandex_api_key_new', $data['yandex_api_key'], 300). $PHPShopGUI->setHelp('Персональные ключи для домена выдаются через <a href="https://developer.tech.yandex.ru" target="_blank">Кабинет разработчика</a>'));
    $Tab1 .= $PHPShopGUI->setField('Город по умолчанию', $PHPShopGUI->setSelect('default_city_id_new', $Branches->getDefaultCity($data['default_city_id']), 300, null, false, true, false, 1, false));
    $Tab1 .= $PHPShopGUI->setField('Города быстрого доступа', $PHPShopGUI->setSelect('favorite_cities[]', $Branches->getFavoriteCitiesForSelect(unserialize($data['favorite_cities'])), 300, false, false, true, false, 1, true));

    // Редактор 
    $PHPShopGUI->setEditor('ace');
    $oFCKeditor = new Editor('conten1');
    $oFCKeditor->Value = '<div>
Мой город: <a href="#" class="geo-changecity">@geolocation_city@</a>
</div>';
    $oFCKeditor->Height = '50';
    
    $info .= '<h4>Установка меню</h4>
<p>Для вставки элемента в главное меню сайта для просмотра доступных пунктов выдачи на Яндекс.Карте следует создать новую страницу с именем "Пункты выдачи" в разделе:<br> <kbd>Страницы</kbd> &rarr; <kbd>Главное меню сайта</kbd> с ссылкой: <code>../branches/branches</code></p>';
    
    $info .= '<h4>Установка базы городов</h4>
<p>Для установки базы городов подбора следует восстановить файл <code>citylist_install.sql</code> в разделе <kbd>База</kbd> &rarr; <kbd>Резервное копирование</kbd>
</p>';

    $info .= '<h4>Установка кода</h4>
        <p>Для вставки элемента выбора города следует в ручном режиме вставить код в шапку сайта:</p>
' . $oFCKeditor->AddGUI();

    $oFCKeditor = new Editor('content2');
    $oFCKeditor->Value = '@geolocationPopup@
<script src="phpshop/modules/branches/templates/jquery-ui.min.js"></script>
<script src="phpshop/modules/branches/templates/geolocation.js"></script>
<script>
  $(document).ready(function () {
    var GeolocationInstance = new GeolocationModule();
    GeolocationInstance.init();
  });
</script> ';
    $oFCKeditor->Height = '140';
    $info .= '<p>Для вставки элемента выбора города следует в ручном режиме вставить код в подвал сайта:</p>
' . $oFCKeditor->AddGUI();
    
   

    $Tab2 = $PHPShopGUI->setInfo($info);

    $Tab4 = $PHPShopGUI->setPay(false, false, $data['version'], false);

    // Вывод формы закладки
    $PHPShopGUI->setTab(['Основное', $Tab1, true], ["Инструкция", $Tab2], ['О Модуле', $Tab4]);

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