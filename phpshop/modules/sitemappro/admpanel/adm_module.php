<?php

include_once dirname(__DIR__) . '/class/SitemapPro.php';

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.sitemappro.sitemappro_system"));

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm;

    if (empty($_POST["use_filter_combinations_new"]))
        $_POST["use_filter_combinations_new"] = 0;

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);

    return $action;
}

// Генерация карты без SSL
function setGeneration() {
    (new SitemapPro())->generateSitemap();
}

// Функция обновления
function actionUpdateSSl() {
    (new SitemapPro())->generateSitemap(true);
}

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $TitlePage, $select_name;

    $PHPShopGUI->action_button['Создать'] = array(
        'name' => __('Создать Sitemap'),
        'action' => 'saveIDsitemap',
        'class' => 'btn  btn-default btn-sm navbar-btn hidden-xs',
        'type' => 'submit',
        'icon' => 'glyphicon glyphicon-import'
    );

    $PHPShopGUI->action_button['Создать SSL'] = array(
'name' => __('Создать Sitemap SSL'),
        'action' => 'saveIDssl',
        'class' => 'btn  btn-default btn-sm navbar-btn hidden-xs',
        'type' => 'submit',
        'icon' => 'glyphicon glyphicon-import'
    );
    
    $PHPShopGUI->action_button['Открыть'] = array(
        'name' => __('Открыть Sitemap'),
        'action' => '../../sitemap.xml',
        'class' => 'btn  btn-default btn-sm navbar-btn btn-action-panel-blank hidden-xs',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-export'
    );

    $PHPShopGUI->setActionPanel($TitlePage, $select_name, array('Создать','Создать SSL','Открыть','Сохранить и закрыть'));

    $data = $PHPShopOrm->select();

    switch ($data['step']) {
        case SitemapPro::FILTER_COMBINATIONS_STEP:
            $status = sprintf('Генерация комбинаций фильтра товаров с %s до %s', (int) $data['processed'], (int) $data['processed'] + SitemapPro::FILTER_COMBINATIONS_STEP_LIMIT);
            break;
        case SitemapPro::PRODUCTS_STEP:
            $status = sprintf('Генерация товаров с %s до %s', (int) $data['processed'], (int) $data['processed'] + (int) $data['limit_products']);
            break;
        default:
            $status = 'Генерация категорий, страниц, новостей';
    }

    $Tab1 = $PHPShopGUI->setField('Товаров в одном файле', $PHPShopGUI->setInputText(false, 'limit_products_new', $data['limit_products'], 150));
    $Tab1 .= $PHPShopGUI->setField('Виртуалные каталоги', $PHPShopGUI->setCheckbox("use_filter_combinations_new", 1, "Добавить в карту сайта страницы виртуальных каталогов", $data["use_filter_combinations"]));
    $Tab1 .= $PHPShopGUI->setField('Следующий этап',sprintf('<div class="well well-sm" style="max-width:300px" role="alert">%s.</div>', __($status)));

    $Info = '
        <ol>
        <li>Для автоматического создания sitemap.xml установите модуль <kbd>Cron</kbd> и добавьте в него новую задачу с адресом
        исполняемого файла:<br>  <code>phpshop/modules/sitemappro/cron/sitemap_generator.php</code> или <code>phpshop/modules/sitemappro/cron/sitemap_generator.php?ssl</code> для поддержки HTTPS.
        <li>Для использования опции <kbd>Комбинации значений фильтра</kbd> у значений характеристик фильтра должны быть заполнены поля <kbd>Meta заголовок</kbd> и <kbd>Meta описание</kbd> или должны быть настроены <kbd>Шаблоны фильтра в каталоге</kbd> в <kbd>SEO заголовки</kbd>.</li>
        <li>В поисковиках (Яндекс.Вебмастер и т.д.) укажите адрес <code>http://' . $_SERVER['SERVER_NAME'] . '/sitemap.xml</code> для автоматической обработки поисковыми ботами.         
        <li>Для генерации карты сайта у дополнительных витрин следует добавить отдельную задачу через модуль <kbd>Cron</kbd> и в настройках задачи модуля указать требуемую витрину. Адрес карты сайта витрины примет вид <code>http://адрес_витрины/sitemap_ХХ.xml</code>, где ХХ - ID витрины. ID витрины можно увидеть в интерфейсе настройки витрины (1 - 10).
        <li>Установите опцию CHMOD 775 на папки <code>/</code> и <code>/UserFiles/Files/</code> для записи в нее файлов sitemap.xml
        </ol>';
    $Tab2 = $PHPShopGUI->setInfo($Info);

    $Tab3 = $PHPShopGUI->setPay(false,true);

// Вывод формы закладки
    $PHPShopGUI->setTab(['Основное', $Tab1, true], ['Инструкция', $Tab2], ['О Модуле', $Tab3]);

// Вывод кнопок сохранить и выход в футер
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit").
            $PHPShopGUI->setInput("submit", "saveIDsitemap", "Применить", "right", 80, "", "but", "setGeneration.modules.edit");
            $PHPShopGUI->setInput("submit", "saveIDssl", "Применить", "right", 80, "", "but", "actionUpdateSSL.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>