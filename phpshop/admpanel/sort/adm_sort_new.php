<?php

PHPShopObj::loadClass('sort');

if (!empty($_GET['type']))
    $TitlePage = __('Создание группы характеристики');
else
    $TitlePage = __('Создание характеристики');

$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopModules, $TitlePage, $PHPShopBase;

    // Выборка
    $newId = getLastID();

    if (empty($_GET['id'])) {
        $data['id'] = $newId;
    } else {
        // Создание копии 
        $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));
        $data['id'] = $newId;

        // Копирование характеристик
        if ($PHPShopBase->Rule->CheckedRules('sort', 'create'))
            valueCopy($_GET['id'], $newId);
    }

    $data = $PHPShopGUI->valid($data, 'page', 'brand', 'product', 'filtr', 'goodoption', 'optionname', 'virtual', 'show_preview', 'name', 'num', 'description');

    // Размер названия поля
    $PHPShopGUI->field_col = 4;
    $PHPShopGUI->addJSFiles('./sort/gui/sort.gui.js');
    $PHPShopGUI->setActionPanel($TitlePage, false, array('Создать и редактировать', 'Сохранить и закрыть'));

    // Страницы
    $page_value[] = array('- ' . __('Нет описания') . ' - ', null, $data['page']);
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page']);
    $data_page = $PHPShopOrm->select(array('*'), false, false, array('limit' => 1000));
    if (is_array($data_page))
        foreach ($data_page as $v)
            $page_value[] = array($v['name'], $v['link'], $data['page']);

    // Категории
    $PHPShopSort = new PHPShopSortCategoryArray(array('category' => '=0'));
    $PHPShopSortArray = $PHPShopSort->getArray();
    if (is_array($PHPShopSortArray))
        foreach ($PHPShopSortArray as $v)
            $category_value[] = array($v['name'], $v['id'], @$_GET['cat']);

    // Группа категорий
    if (empty($_GET['type'])) {
        $Tab3 = $PHPShopGUI->setField("Группа", $PHPShopGUI->setSelect('category_new', $category_value, '100%', false, false, true).
                $PHPShopGUI->setHelp('Можно скрыть пустые значения фильтра с одной Группой хар-к. В основных настройках отметьте <a href="?path=system#2" target="_blank">Кешировать значения фильтра</a>.')).
                $PHPShopGUI->setField("Бренд:", $PHPShopGUI->setCheckbox('brand_new', 1, null, $data['brand']), 1, 'Характеристика становится брендом и отображается в списке брендов') .
                $PHPShopGUI->setField("Переключение", $PHPShopGUI->setCheckbox('product_new', 1, null, $data['product']), 1, 'Вместо значений хар-ки выводить Рекомендуемые товары для совместной продажи, указанные в карточке товара') .
                $PHPShopGUI->setField('Фильтр',$PHPShopGUI->setCheckbox('filtr_new', 1, null, $data['filtr'])).
                $PHPShopGUI->setField('Товарная опция',$PHPShopGUI->setCheckbox('goodoption_new', 1, null, $data['goodoption']).'<br>'.
                 $PHPShopGUI->setCheckbox('optionname_new', 1, 'Не обязательна для добавления в корзину', $data['optionname'])
                        ).
                $PHPShopGUI->setField('Виртуальный каталог',$PHPShopGUI->setCheckbox('virtual_new', 1, null, $data['virtual'])).
                $PHPShopGUI->setField('Отображать в превью товара',$PHPShopGUI->setCheckbox('show_preview_new', 1, null, $data['show_preview']));
    }
    
    $help = '<p class="text-muted">'.__('Для отображения характеристик у товаров, необходимо объединить их в группы и выбрать эти группы у <a href="?path=catalog&action=new" class=""><span class="glyphicon glyphicon-share-alt"></span> Каталогов товаров</a>. Характеристики из выбранных групп появятся в товарах указанных каталогов').'.</p>';

    // Содержание закладки 1
    $Tab1 = $PHPShopGUI->setCollapse('Информация', $PHPShopGUI->setField("Наименование", $PHPShopGUI->setInputArg(array('type' => 'text.requared', 'name' => 'name_new', 'value' => $data['name']))) .
            $PHPShopGUI->setField("Приоритет", $PHPShopGUI->setInputArg(array('type' => 'text', 'name' => 'num_new', 'value' => $data['num'], 'size' => 100))) .
            $Tab3 
    );

    // Варианты
    if (empty($_GET['type'])) {
        $Tab1 .= $PHPShopGUI->setCollapse('Подсказка',$help);
        $Tab1 .= $PHPShopGUI->setCollapse('Значения', $PHPShopGUI->loadLib('tab_value', $data));

        // Дополнительно
        $Tab1 .= $PHPShopGUI->setCollapse('Дополнительно', $PHPShopGUI->setField("Витрины", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/','100%')) .
                $PHPShopGUI->setField("Подсказка", $PHPShopGUI->setTextarea('description_new', $data['description'])));
        
        $masonry_grid = true;
    }
    else $masonry_grid = 'block-grid';

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true, false, $masonry_grid));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "ОК", "right", 70, "", "but", "actionInsert.sort.create") . $PHPShopGUI->setInput("hidden", "rowID", $data['id']);

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

/**
 * ID новой записи в таблице
 * @return integer 
 */
function getLastID() {
    $PHPShopOrm = new PHPShopOrm();
    $PHPShopOrm->sql = 'SHOW TABLE STATUS LIKE "' . $GLOBALS['SysValue']['base']['sort_categories'] . '"';
    $data = $PHPShopOrm->select();
    if (is_array($data)) {
        return $data[0]['Auto_increment'];
    }
}

/**
 * Копирование галереи товара
 */
function valueCopy($j, $n) {

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);
    $data = $PHPShopOrm->select(array('*'), array('category' => "=" . intval($j)), array('order' => 'num,name DESC'), array('limit' => 1000));
    if (is_array($data))
        foreach ($data as $row) {

            $insert['category_new'] = $n;
            $insert['name_new'] = $row['name'];
            $insert['num_new'] = $row['num'];
            $insert['icon_new'] = $row['icon'];
            $insert['page_new'] = $row['page'];
            $insert['sort_seo_name_new'] = $row['sort_seo_name'];

            $PHPShopOrm->clean();
            $PHPShopOrm->insert($insert);
        }
}

/**
 * Экшен записи
 */
function actionInsert() {
    global $PHPShopModules, $PHPShopOrm;

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $_POST['category_new'] = intval($_POST['category_new']);

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->insert($_POST, '_new');

    if ($_POST['saveID'] == 'Создать и редактировать') {
        if (empty($_POST['category_new']))
            header('Location: ?path=' . $_GET['path'] . '&id=' . $_POST['rowID'] . '&type=sub');
        else
            header('Location: ?path=' . $_GET['path'] . '&id=' . $_POST['rowID']);
    }
    else if (!empty($_GET['type']))
        header('Location: ?path=' . $_GET['path'] . '&cat=' . $_POST['rowID']);
    else
        header('Location: ?path=' . $_GET['path'] . '&cat=' . $_POST['category_new']);
    return $action;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>