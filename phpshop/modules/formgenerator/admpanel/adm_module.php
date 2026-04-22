<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.formgenerator.formgenerator_system"));

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;
    
    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);
    
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    
    return $action;
}

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI;


    $Info = 'Для интеграции формы  в ручном режиме включите следующий код в содержание страницы или текстового блока:
        <p>
        <code>@php<br>
        $PHPShopFormgeneratorElement = new PHPShopFormgeneratorElement();<br>
        echo $PHPShopFormgeneratorElement->forma("маркер формы");<br>
        php@</code>
         </p>
         <p>
         Для добавления новых полей используйте в обязательном порядке имена полей с префиксом formgenerator_, например:<code><br>
         &lt;input  type="text" <b>name="formgenerator_Тест"</b>&gt; </code>  
         </p>
         <p>
         Для включения поля в список обязательного заполнения вставьте знак звездочки в имя поля, например:<code><br>
         &lt;input  type="text" name="formgenerator_<b>*Тест</b>"&gt;  </code>
         </p>
         <p>
         Для запоминания данных поля и вывода сохраненных результытов при повторном заполнении формы используйете параметр значение
         поля с номером поля в форме по порядку, начиная сверху, например:<code><br>
         &lt;input  type="text" name="formgenerator_Тест" <b>value="@formamemory3@</b>"&gt;</code>
         </p>
         <p>
         Для включения защитной каптчи используйте <kbd>@formgenerator_captcha@</kbd>
         </p>
         <p>
         Для передачи данных по товару в сообщение администратору (наименование, артикул, ИД, ссылка) добавьте переменную <kbd>?product_id=XXX</kbd> на страницу вызова формы, где (ХХХ - ID товара). Пример: <code>http://'.$_SERVER['SERVER_NAME'].'/formgenerator/example/?product_id=101</code>
         </p>';

    $Tab2 = $PHPShopGUI->setInfo($Info, 250, '97%');


    // Содержание закладки 2
    $Tab3 = $PHPShopGUI->setPay();

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Описание", $Tab2), array("О Модуле", $Tab3),array("Формы", null,'?path=modules.dir.formgenerator'));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", 1) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>