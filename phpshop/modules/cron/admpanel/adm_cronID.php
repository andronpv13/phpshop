<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.cron.cron_job"));

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm;

    if (empty($_POST['enabled_new']))
        $_POST['enabled_new'] = 0;

    $_POST['used_new'] = 0;

    // Мультибаза
    if (is_array($_POST['servers'])) {
        $_POST['servers_new'] = "";
        foreach ($_POST['servers'] as $v)
            if ($v != 'null' and ! strstr($v, ','))
                $_POST['servers_new'] .= "i" . $v . "i";
    }


    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array('success' => $action);
}

/**
 * Экшен сохранения
 */
function actionSave() {

    // Сохранение данных
    actionUpdate();

    header('Location: ?path=' . $_GET['path']);
}

// Функция удаления
function actionDelete() {
    global $PHPShopOrm;
    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array("success" => $action);
}

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));
    $PHPShopGUI->setActionPanel(__("Задача") . ": " . $data['name'] . ' [ID ' . $data['id'] . ']', array('Удалить'), array('Сохранить и закрыть'), false);

    $work[] = array('Выбрать', '');
    $work[] = array('Бекап БД', 'phpshop/modules/cron/sample/dump.php');
    $work[] = array('Курсы валют для России', 'phpshop/modules/cron/sample/currency.php');
    $work[] = array('Курсы валют для Казахстана', 'phpshop/modules/cron/sample/currencykz.php');
    $work[] = array('Снятие с продаж товаров', 'phpshop/modules/cron/sample/product.php');
    $work[] = array('Разновалютый поиск', 'phpshop/modules/cron/sample/pricesearch.php');
    $work[] = array('Кеширование фильтра быстрое', 'phpshop/modules/cron/sample/filter.php');
    $work[] = array('Кеширование фильтра полное', 'phpshop/modules/cron/sample/filterpro.php');
    $work[] = array('Генерация видео из фото', 'phpshop/modules/cron/sample/video.php');

    // Учет модуля SiteMap
    if (!empty($GLOBALS['SysValue']['base']['sitemap']['sitemap_system'])) {
        $work[] = array("|");
        $work[] = array('Карта сайта', 'phpshop/modules/sitemap/cron/sitemap_generator.php');
        $work[] = array('Карта сайта SSL', 'phpshop/modules/sitemap/cron/sitemap_generator.php?ssl');
    }

    // Учет модуля SiteMap Pro
    if (!empty($GLOBALS['SysValue']['base']['sitemappro']['sitemappro_system'])) {
        $work[] = array("|");
        $work[] = array('Большая карта сайта', 'phpshop/modules/sitemappro/cron/sitemap_generator.php');
        $work[] = array('Большая карта сайта SSL', 'phpshop/modules/sitemappro/cron/sitemap_generator.php?ssl');
    }

    // Учет модуля VisualCart
    if (!empty($GLOBALS['SysValue']['base']['visualcart']['visualcart_system'])) {
        $work[] = array("|");
        $work[] = array('Очистка брошенных корзин', 'phpshop/modules/visualcart/cron/clean.php');
    }

    // Загрузка CSV
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges']);
    $exchanges_data = $PHPShopOrm->select(array('*'), false, array('order' => 'id DESC'), array("limit" => "1000"));
    if (is_array($exchanges_data)) {

        foreach ($exchanges_data as $row) {

            if ($row['type'] == 'import')
                $import[] = array($row['name'], 'phpshop/modules/cron/sample/import.php?id=' . $row['id']);
            elseif ($row['type'] == 'export')
                $export[] = array($row['name'], 'phpshop/modules/cron/sample/export.php?id=' . $row['id'] . '&file=export_' . md5($row['name']));
        }

        if (is_array($import))
            $work[] = array('Импорт данных', $import);

        if (is_array($export))
            $work[] = array('Экспорт данных', $export);
    }

    $Tab1 = $PHPShopGUI->setField("Название задачи:", $PHPShopGUI->setInput("text.requared", "name_new", $data['name']));
    $Tab1 .= $PHPShopGUI->setField("Запускаемый файл:", $PHPShopGUI->setInputArg(array('type' => "text.requared", 'name' => "path_new", 'size' => '70%', 'float' => 'left', 'placeholder' => 'phpshop/modules/cron/sample/testcron.php', 'value' => $data['path'])) . '&nbsp;' . $PHPShopGUI->setSelect('work', $work, '29%', true, false, false, false, false, false, false, 'selectpicker', '$(\'input[name=path_new]\').val(this.value);'));

    parse_str($data['path'], $path);

    if (!empty($path['file'])) {

        $file = './csv/' . $path['file'];

        if (file_exists($file . '.xml'))
            $file_ext = '.xml';
        else if (file_exists($file . '.csv'))
            $file_ext = '.csv';

        $Tab1 .= $PHPShopGUI->setField("Файл выгрузки", $PHPShopGUI->setLink($file . $file_ext, $path['file'] . $file_ext), false, false, false, 'text-right');
    }

    $Tab1 .= $PHPShopGUI->setField("Статус", $PHPShopGUI->setCheckbox("enabled_new", 1, "Включить", $data['enabled']));
    $Tab1 .= $PHPShopGUI->setField("Кол-во запусков в день", $PHPShopGUI->setInputText(null, 'execute_day_num_new', (int) $data['execute_day_num'], 70));
    $Tab1 .= $PHPShopGUI->setField("Витрины", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/'));


    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "Удалить", "right", 70, "", "but", "actionDelete.modules.edit") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.modules.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart');
?>