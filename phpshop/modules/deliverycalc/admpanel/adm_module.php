<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.deliverycalc.deliverycalc_system"));

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm;

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select();
    
    if(empty($data['code']))
        $data['code']='<script src="https://alliance-catalog.ru/site/delivery-iframe/script.js"></script>
<div>'.__('<a href="https://alliance-catalog.ru/deliverycalc/" id="link" >* Рассчитывается</a> ориентировочная стоимость доставки, конечная стоимость определяется после приема груза на терминале компании').'</div>';

    // Таргетинг
    $Tab1.=$PHPShopGUI->setField("Таргетинг:", $PHPShopGUI->setInput("text", "target_new", $data['target']) .
            $PHPShopGUI->setHelp('* Пример: /,/page/,/page/addres.html. Можно указать несколько адресов через запятую.'));

    $Tab1.=$PHPShopGUI->setField('Код виджета', $PHPShopGUI->setTextarea('code_new', $data['code'], false, false, 150).
            $PHPShopGUI->setHelp('* Пример кода виджета представлен на <a href="https://alliance-catalog.ru/ourdelcalc/" target="_blank">странице разработчика</a>.'));


    $Tab3 = $PHPShopGUI->setPay();
    $Info = '<h4>Настройка модуля</h4>
        <ol>
        <li> В поле "Таргетинг" можно указать адреса страниц для вывода на них виджета калькулятора стоимости доставок. Виджет выводится в конце содержания страницы.
        <li> При отсутствии таргетинга виджет выводится на любой странице через вставку переменной <kbd>@deliverycalc @</kbd> в любое место содержания страницы.
        <li> Для настройки кода виджета требуется внести изменения в одноименное поле "Код виджета".
        <li> Техническая поддержка осуществляется компаний разработчиком виджета <a href="https://alliance-catalog.ru/ourdelcalc/" target="_blank">Alliance-catalog.ru</a>

</ol>';
    $Tab2 = $PHPShopGUI->setInfo($Info);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Код виджета", $Tab1, true), array("Описание", $Tab2), array("О Модуле", $Tab3));

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