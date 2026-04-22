<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.seourlpro.seourlpro_system"));

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

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);

    // Очистит память настроек
    unset($_SESSION['Memory']['PHPShopSeourlOption']);

    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $hideSite;
    
    $PHPShopGUI->field_col = 4;

    // Выборка
    $data = $PHPShopOrm->select();


    // Содержание закладки
    $Info = '<p>Выключение модуля приведет к потере адресов страниц вида <code>/knigi.html</code> на <code>/shop/CID_1.html</code>. Повторное включение заново создаст seo-url, на основе названия каталогов, товаров, при этом, если вы вручную вводили url в поле, он не сохранится.</p>';

    if (empty($hideSite)) {
        $Tab1 = $PHPShopGUI->setField('SEO пагинация', $PHPShopGUI->setRadio('paginator_new', 2, 'Включить', $data['paginator']) . $PHPShopGUI->setRadio('paginator_new', 1, 'Выключить', $data['paginator']), false, 'Добавляет в теги Title и Description нумерацию страниц для уникальности индексации');
        $Tab1 .= $PHPShopGUI->setField('Описание каталога на внутренних страницах', $PHPShopGUI->setRadio('cat_content_enabled_new', 1, 'Включить', $data['cat_content_enabled']) . $PHPShopGUI->setRadio('cat_content_enabled_new', 2, 'Выключить', $data['cat_content_enabled']), false, 'Убирает описание каталога для внутренних страниц для сохранения уникальности первой.');
        $Tab1 .= $PHPShopGUI->setField('Совет', $PHPShopGUI->setInfo($Info));
        $Tab1 .= $PHPShopGUI->setField('SEO ссылки брендов', $PHPShopGUI->setRadio('seo_brands_enabled_new', 2, 'Включить', $data['seo_brands_enabled']) . $PHPShopGUI->setRadio('seo_brands_enabled_new', 1, 'Выключить', $data['seo_brands_enabled']), false, false);
        $Tab1 .= $PHPShopGUI->setField('SEO редиректы', $PHPShopGUI->setRadio('redirect_enabled_new', 2, 'Включить', $data['redirect_enabled']) . $PHPShopGUI->setRadio('redirect_enabled_new', 1, 'Выключить', $data['redirect_enabled']), false, '301 редиректы при миграции с другой CMS');
    }
    $Tab1 .= $PHPShopGUI->setField('SEO ссылки новостей', $PHPShopGUI->setRadio('seo_news_enabled_new', 2, 'Включить', $data['seo_news_enabled']) . $PHPShopGUI->setRadio('seo_news_enabled_new', 1, 'Выключить', $data['seo_news_enabled']), false, false);
    $Tab1 .= $PHPShopGUI->setField('SEO ссылки страниц', $PHPShopGUI->setRadio('seo_page_enabled_new', 2, 'Включить', $data['seo_page_enabled']) . $PHPShopGUI->setRadio('seo_page_enabled_new', 1, 'Выключить', $data['seo_page_enabled']), false, false);
    $Tab1 .= $PHPShopGUI->setField('Окончание .html в SEO ссылках', $PHPShopGUI->setRadio('html_enabled_new', 1, 'Включить', $data['html_enabled']) . $PHPShopGUI->setRadio('html_enabled_new', 2, 'Выключить', $data['html_enabled']), false, false);

    $Tab2 = $PHPShopGUI->setPay($serial = false, false, $data['version'], true);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("О Модуле", $Tab2));

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