<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.productsgroup.productsgroup_system"));

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

// Функция обновления
function actionUpdate() {
    global $PHPShopModules,$PHPShopOrm;

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);
    $PHPShopOrm->update($_POST);

    header('Location: ?path=modules&id='.$_GET['id']);
}

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    $data = $PHPShopOrm->select();

    $Tab1 = '<p>Модуль позволяет выводить составные товары в виде единой карточки и управлять составом корзины при их добавлении. Подходит для продажи мебели, компьютеров в сборе и т.д.</p>
        <h4>Настройка товара</h4>
        <p>При редактирование товара во вкладке <kbd>Модули</kbd> - "<b>Группы</b>" есть возможность настроить список составных товаров в группе.</p>
<h4>Настройка шаблона</h4>
    <p><kbd>@productsgroup_list@</kbd> - переменная отвечает за вывод блока в шаблоне подробного описания товара <code>/phpshop/templates/имя_шаблона/product/main_product_forma_full.tpl</code></p>
    <p><kbd>@productsgroup_button_buy@</kbd> - кнопка покупки для списков товаров, например файл шаблона: <code>/phpshop/templates/имя_шаблона/product/main_product_forma_2.tpl</code></p>
    <p>Для изменения динамически цены при выборе кол-ва товара в карточке товара, нужно внести изменения в шаблоне страницы товара <code>/phpshop/templates/имя_шаблона/product/main_product_forma_full.tpl</code>. Добавить класс "<b>priceGroupeR</b>" в тэг, содержащий цену. Пример: <pre>
&lt;div class="tovarDivPrice12"&gt;Цена: &lt;span class="priceGroupeR"&gt;@productPrice@&lt;/span&gt; &lt;span&gt;@productValutaName@&lt;/span>&lt;/div&gt;</pre>
    <p>Для автоматического обновления цен у групп товаров по расписанию следует добавить новую задачу в модуль <a href="https://docs.phpshop.ru/moduli/razrabotchikam/cron" target="_blank">Задачи</a> с адресом запускаемого файла <code>phpshop/modules/productsgroup/cron/products.php</code>. Цены обновляются так же при редактировании карточки товара в магазине.</p>

    ';

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Инструкция", $Tab1), array("О Модуле", $PHPShopGUI->setPay(false, false, $data['version'], true)));

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
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>