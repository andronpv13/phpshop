<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.productservices.productservices_system"));

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));
    header('Location: ?path=modules&id='.$_GET['id']);
    return $action;
}

// Функция обновления
function actionUpdate() {
    global $PHPShopModules, $PHPShopOrm;

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm->update($_POST);

    header('Location: ?path=modules&id='.$_GET['id']);
}


function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;
    
        // Выборка
    $data = $PHPShopOrm->select();

    $Info = '<p>Модуль позволяет выводить дополнительные услуги в карточке товара.</p>
        <h4>Настройка товара</h4>
        <p>При редактирование товара во вкладке <kbd>Модули</kbd> - <kbd>Услуги</kbd> есть возможность настроить список услуг и скидку на услугу. Услуга - отдельно созданный товар (может быть отключен на сайте).</p>
<h4>Настройка шаблона</h4>
    <p><kbd>@productservices_list@</kbd> - переменная отвечает за вывод блока в шаблоне подробного описания товара <code>/phpshop/templates/имя_шаблона/product/main_product_forma_full.tpl</code></p>
    <p>Для изменения динамически цены при выборе кол-ва товара в карточке товара, нужно внести изменения в шаблоне страницы товара <code>/phpshop/templates/имя_шаблона/product/main_product_forma_full.tpl</code>. Добавить класс "<b>priceService</b>" в тэг, содержащий цену. Пример: <pre>
&lt;div class="tovarDivPrice12"&gt;Цена: &lt;span class="priceService"&gt;@productPrice@&lt;/span&gt; &lt;span&gt;@productValutaName@&lt;/span>&lt;/div&gt;</pre>';

    // Содержание закладки 1
    $Tab2 = $PHPShopGUI->setInfo($Info);

    // Форма регистрации
    $Tab3 = $PHPShopGUI->setPay($serial = false, false, $data['version'], true);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Инструкция", $Tab2), array("О Модуле", $Tab3));

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