<?php

PHPShopObj::loadClass("base");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("page");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("category");


$TitlePage = __('Редактирование Товара') . ' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);

// Построение дерева категорий
function treegenerator($array, $i, $curent, $dop_cat_array) {
    global $tree_array;
    $del = '&brvbar;&nbsp;&nbsp;&nbsp;&nbsp;';
    $tree_select = $tree_select_dop = $check = false;

    $del = str_repeat($del, $i);
    if (!empty($array['sub']) and is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {

            $check = treegenerator(@$tree_array[$k], $i + 1, $curent, $dop_cat_array);

            if ($k == $curent)
                $selected = 'selected';
            else
                $selected = null;

            // Допкаталоги
            $selected_dop = null;
            if (is_array($dop_cat_array))
                foreach ($dop_cat_array as $vs) {
                    if ($k == $vs)
                        $selected_dop = "selected";
                }

            if (empty($check['select'])) {
                $tree_select .= '<option value="' . $k . '" ' . $selected . '>' . $del . $v . '</option>';
                $tree_select_dop .= '<option value="' . $k . '" ' . $selected_dop . '>' . $del . $v . '</option>';

                $i = 1;
            } else {
                $tree_select .= '<option value="' . $k . '" disabled>' . $del . $v . '</option>';
                $tree_select_dop .= '<option value="' . $k . '" disabled >' . $del . $v . '</option>';
            }

            $tree_select .= $check['select'];
            $tree_select_dop .= $check['select_dop'];
        }
    }
    return array('select' => $tree_select, 'select_dop' => $tree_select_dop);
}

function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $PHPShopOrm, $PHPShopBase, $PHPShopSystem, $CategoryArray, $isFrame, $hideCatalog, $hideCatalogExt;

    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));

    // Редирект на родителя подтипа
    if ($data['parent_enabled'] == 1 and empty($_GET['view'])) {
        $data_parent = $PHPShopOrm->select(array('id'), array('parent' => "='" . $data['id'] . "' or parent LIKE '," . $data['id'] . "' or  parent LIKE '" . $data['id'] . ",'", 'parent_enabled' => "='0'"), false, array('limit' => 1));
        if (!empty($data_parent['id']))
            header('Location: ?path=' . $_GET['path'] . '&id=' . $data_parent['id'] . '&tab=6&return=' . $_GET['return']);
    }

    // Нет данных
    if (!is_array($data)) {
        header('Location: ?path=' . $_GET['return']);
    }

    // Имя товара
    if (strlen($data['name']) > 47)
        $title_name = mb_substr($data['name'], 0, 47) . '...';
    else
        $title_name = $data['name'];

    if (empty($isFrame))
        $title_name .= ' [ID ' . $data['id'] . ']';

    $PHPShopGUI->action_select['Предпросмотр'] = array(
        'name' => 'Предпросмотр',
        'url' => '../../shop/UID_' . $data['id'] . '.html',
        'action' => 'front',
        'target' => '_blank',
        'class' => $GLOBALS['isFrame']
    );

    $PHPShopGUI->setActionPanel('<span class="' . $isFrame . '">' . __("Товар") . ": </span>" . $title_name, array('Сделать копию', 'Предпросмотр', '|', 'Удалить'), array('Сохранить', 'Сохранить и закрыть'), false);

    // Размер названия поля
    $PHPShopGUI->field_col = 4;
    $PHPShopGUI->addJSFiles('./js/jquery.tagsinput.min.js', './catalog/gui/catalog.gui.js', './js/jquery.waypoints.min.js', './product/gui/product.gui.js', './js/bootstrap-colorpicker.min.js');
    $PHPShopGUI->addCSSFiles('./css/jquery.tagsinput.css', './css/bootstrap-colorpicker.min.css');

    // Права менеджеров
    if ($PHPShopSystem->ifSerilizeParam('admoption.rule_enabled', 1) and ! $PHPShopBase->Rule->CheckedRules('catalog', 'remove')) {
        $where = array('secure_groups' => " REGEXP 'i" . $_SESSION['idPHPSHOP'] . "i' or secure_groups = ''");
        $secure_groups = true;
    } else
        $where = $secure_groups = false;

    $PHPShopCategoryArray = new PHPShopCategoryArray($where);
    $CategoryArray = $PHPShopCategoryArray->getArray();

    $CategoryArray[0]['name'] = '- ' . __('Выбрать каталог') . ' -';
    $tree_array = array();

    $getKey = $PHPShopCategoryArray->getKey('parent_to.id', true);

    if (is_array($getKey))
        foreach ($getKey as $k => $v) {
            foreach ($v as $cat) {
                $tree_array[$k]['sub'][$cat] = $CategoryArray[$cat]['name'];
            }
            $tree_array[$k]['name'] = $CategoryArray[$k]['name'];
            $tree_array[$k]['id'] = $k;
        }

    $tree_array[0]['sub'][1000000] = __('Неопределенные товары');
    $tree_array[1000000]['name'] = __('Неопределенные товары');
    $tree_array[1000000]['id'] = 1000000;
    $tree_array[1000000]['sub'][1000001] = __('Загруженные CRM');
    $tree_array[1000000]['sub'][1000002] = __('Загруженные CSV');
    $tree_array[1000002]['id'] = 0;

    $GLOBALS['tree_array'] = &$tree_array;
    $tree_select = $tree_select_dop = null;

    // Допкаталоги
    $dop_cat_array = preg_split('/#/', $data['dop_cat'], -1, PREG_SPLIT_NO_EMPTY);

    if (is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator(@$tree_array[$k], 1, $data['category'], $dop_cat_array);

            if ($k == $data['category'])
                $selected = 'selected';
            else
                $selected = null;

            // Допкаталоги
            $selected_dop = null;
            if (is_array($dop_cat_array))
                foreach ($dop_cat_array as $vs) {
                    if ($k == $vs)
                        $selected_dop = "selected";
                }

            if (empty($tree_array[$k]))
                $disabled = null;
            else
                $disabled = ' disabled';

            $tree_select .= '<option value="' . $k . '" ' . $selected . $disabled . '>' . $v . '</option>';
            $tree_select_dop .= '<option value="' . $k . '" ' . $selected_dop . $disabled . '>' . $v . '</option>';
            $tree_select .= $check['select'];
            $tree_select_dop .= $check['select_dop'];
        }

    $tree_select_dop = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container="body"  data-style="btn btn-default btn-sm" name="dop_cat[]" data-width="100%" multiple><option value="0">' . $CategoryArray[0]['name'] . '</option>' . $tree_select_dop . '</select>';

    $tree_select = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container="body"  data-style="btn btn-default btn-sm" name="category_new"  data-width="100%"><option value="0">' . $CategoryArray[0]['name'] . '</option>' . $tree_select . '</select>';

    // Наименование
    $Tab_info .= $PHPShopGUI->setField("Название", $PHPShopGUI->setTextarea('name_new', $data['name']));

    // Артикул
    $Tab_info .= $PHPShopGUI->setField('Артикул', $PHPShopGUI->setInputText(null, 'uid_new', $data['uid'], '100%'));

    $icon_title = false;

    if ($PHPShopSystem->ifSerilizeParam("admoption.image_off", 1)) {
        $icon_server = false;
        $icon_title = 'Активирован режим для товара с одной картинкой, заранее подготовленной на сервере. Для загрузки дополнительных фото товара, снимите запрет на фотогалерею, снимите галку в Настройка - Изображения - Отключить фотогалерею.';
    } else
        $icon_server = true;

    $Tab_info .= $PHPShopGUI->setField("Изображение", $PHPShopGUI->setIcon($data['pic_big'], "pic_big_new", true, array('load' => false, 'server' => true, 'url' => true, 'view' => $icon_server)), 1, $icon_title);

    if (empty($icon_server))
        $Tab_info .= $PHPShopGUI->setField("Превью", $PHPShopGUI->setFile($data['pic_small'], "pic_small_new", array('load' => false, 'server' => 'image', 'url' => true, 'view' => $icon_server)), 1, $icon_title);
    else
        $Tab_info .= $PHPShopGUI->setFile($data['pic_small'], "pic_small_new", array('load' => false, 'server' => 'image', 'url' => false, 'view' => $icon_server));

    // Единица измерения
    if (empty($data['ed_izm']))
        $ed_izm = __('шт.');
    else
        $ed_izm = $data['ed_izm'];

    if (empty($hideCatalog)) {

        // Дополнительный склад
        $PHPShopOrmWarehouse = new PHPShopOrm($GLOBALS['SysValue']['base']['warehouses']);
        $dataWarehouse = $PHPShopOrmWarehouse->select(array('*'), array('enabled' => "='1'"), array('order' => 'num'), array('limit' => 100));
        if (is_array($dataWarehouse)) {

            $Tab_info .= $PHPShopGUI->setField('Общий склад', $PHPShopGUI->setInputText(false, 'items_new', $data['items'], 100, $ed_izm), 'left');

            foreach ($dataWarehouse as $row) {
                $Tab_info .= $PHPShopGUI->setField($row['name'], $PHPShopGUI->setInputText(false, 'items' . $row['id'] . '_new', $data['items' . $row['id']], 100, $ed_izm), 2, $row['description'], null, 'control-label', false);
            }
        } else
            $Tab_info .= $PHPShopGUI->setField('Склад', $PHPShopGUI->setInputText(false, 'items_new', $data['items'], 100, $ed_izm), 'left');
    }

    // Вес
    $Tab_info_size = $PHPShopGUI->setField('Вес', $PHPShopGUI->setInputText(false, 'weight_new', $data['weight'], 100, __('г&nbsp;&nbsp;&nbsp;&nbsp;')), 'left');

    // Габариты
    $Tab_info_size .= $PHPShopGUI->setField('Длина', $PHPShopGUI->setInputText(false, 'length_new', $data['length'], 100, __('см&nbsp;')), 'left');
    $Tab_info_size .= $PHPShopGUI->setField('Ширина', $PHPShopGUI->setInputText(false, 'width_new', $data['width'], 100, __('см&nbsp;')), 'left');
    $Tab_info_size .= $PHPShopGUI->setField('Высота', $PHPShopGUI->setInputText(false, 'height_new', $data['height'], 100, __('см&nbsp;')), 'left');
    $Tab_info_size .= $PHPShopGUI->setField('Единица измерения', $PHPShopGUI->setInputText(false, 'ed_izm_new', $ed_izm, 100));

    // Выбор каталога
    $Tab_info_dop = $PHPShopGUI->setField($PHPShopGUI->setLink('./admin.php?path=catalog&id=' . $data['category'], 'Каталог'), $tree_select, 1, __('Вывод в каталоге ID', false) . ' ' . $data['category'], false, 'control-label', true);

    // Изменено
    $stat = PHPShopDate::get($data['datas'], true);
    if (!empty($data['import_id'])) {
        $import_link = $PHPShopGUI->setLink('./admin.php?path=catalog&cat=0&import=' . $data['import_id'], 'Изменено');
        $import_help = __('Показать все товары этого импорта');
    } else {
        $import_link = __('Изменено');
        $import_help = null;
    }

    $Tab_info_dop .= $PHPShopGUI->setField($import_link, $PHPShopGUI->setText($stat), 1, $import_help, null, 'control-label', false);

    // Рекомендуемые товары
    $Tab_info_dop .= $PHPShopGUI->setField('Рекомендуемые товары для совместной продажи', $PHPShopGUI->setTextarea('odnotip_new', $data['odnotip'], false, false, false, __('Укажите ID товаров или воспользуйтесь') . ' <a href="#" data-target="#odnotip_new"  class="btn btn-sm btn-default tag-search"><span class="glyphicon glyphicon-search"></span> ' . __('поиском товаров') . '</a>'));

    // Дополнительные каталоги
    $Tab_info_dop .= $PHPShopGUI->setField('Дополнительные каталоги', $tree_select_dop, 1, 'Товары одновременно выводятся в нескольких каталогах.');

    // Опции вывода
    $Tab_info .= $PHPShopGUI->setField('Опции вывода', $PHPShopGUI->setCheckbox('enabled_new', 1, 'В каталоге', $data['enabled']) . '<br>' .
            $PHPShopGUI->setCheckbox('sklad_new', 1, 'Нет в наличии', $data['sklad']) . '<br>' .
            $PHPShopGUI->setCheckbox('spec_new', 1, 'Спецпредложение', $data['spec']) . '<br>' .
            $PHPShopGUI->setCheckbox('newtip_new', 1, 'Новинка', $data['newtip']));
    $Tab_info .= $PHPShopGUI->setField('Сортировка', $PHPShopGUI->setInputText('&#8470;', 'num_new', $data['num'], 100));

    $type_value[] = array('Товар', 1, $data['type']);
    $type_value[] = array('Услуга', 2, $data['type']);
    $Tab_info .= $PHPShopGUI->setField('Тип', $PHPShopGUI->setSelect('type_new', $type_value, 100, true));

    if (!empty($_GET['view']) and $_GET['view'] == 'option')
        $Tab_info .= $PHPShopGUI->setField('Связи', $PHPShopGUI->setRadio('parent_enabled_new', 0, 'Обычный товар', $data['parent_enabled']) . $PHPShopGUI->setRadio('parent_enabled_new', 1, 'Подтип товара', $data['parent_enabled']));

    $Tab_rating = $PHPShopGUI->setField('Значение', $PHPShopGUI->setInputText(null, 'rate_new', $data['rate'], 50), 1, 'Значение от 0 до 5');
    $Tab_rating .= $PHPShopGUI->setField('Голоса', $PHPShopGUI->setInputText(null, 'rate_count_new', $data['rate_count'], 50));

    $Tab1 = $PHPShopGUI->setCollapse('Информация', $Tab_info);
    $Tab1 .= $PHPShopGUI->setCollapse('Размещение', $Tab_info_dop);

    // Валюты
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    $valuta_area = null;
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            if ($data['baseinputvaluta'] == $val['id']) {
                $check = 'checked';
                $valuta_def_name = $val['code'];
            } else
                $check = false;
            $valuta_area .= $PHPShopGUI->setRadio('baseinputvaluta_new', $val['id'], $val['name'], $data['baseinputvaluta'], false);
        }

    // Цены
    if (!empty($data['parent']) and $PHPShopSystem->ifSerilizeParam('admoption.parent_price_enabled') == 0)
        $price_parent_help = 'Если созданы Подтипы, Главная цена товара автоматически проставляется из наименьшей цены Подтипа, для корректного отображения цены в превью товара.';
    else
        $price_parent_help = null;
    $Tab_price = $PHPShopGUI->setField('Цена', $PHPShopGUI->setInputText(null, 'price_new', $data['price'], 150, $valuta_def_name), 2, $price_parent_help);

    if (empty($data['parent']) or $PHPShopSystem->ifSerilizeParam('admoption.parent_price_enabled')) {
        $Tab_price .= $PHPShopGUI->setField('Цена 2', $PHPShopGUI->setInputText(null, 'price2_new', $data['price2'], 150, $valuta_def_name), 2);
        $Tab_price .= $PHPShopGUI->setField('Цена 3', $PHPShopGUI->setInputText(null, 'price3_new', $data['price3'], 150, $valuta_def_name), 2);
        $Tab_price .= $PHPShopGUI->setField('Цена 4', $PHPShopGUI->setInputText(null, 'price4_new', $data['price4'], 150, $valuta_def_name), 2);
        $Tab_price .= $PHPShopGUI->setField('Цена 5', $PHPShopGUI->setInputText(null, 'price5_new', $data['price5'], 150, $valuta_def_name), 2);
    }
    $Tab_price .= $PHPShopGUI->setField('Старая цена', $PHPShopGUI->setInputText(null, 'price_n_new', $data['price_n'], 150, $valuta_def_name));
    $Tab_price .= $PHPShopGUI->setField('Закупочная цена', $PHPShopGUI->setInputText(null, 'price_purch_new', $data['price_purch'], 150, $valuta_def_name));

    // Валюта
    $Tab_price .= $PHPShopGUI->setField('Валюта', $valuta_area);

    $Tab1 .= $PHPShopGUI->setCollapse('Цены', $Tab_price, 'in', true, true, array('type' => 'price'));
    $Tab1 .= $PHPShopGUI->setCollapse('Габариты', $Tab_info_size);


    $Tab_rating = $PHPShopGUI->setCollapse('Рейтинг', $Tab_rating, false);

    // Редактор краткого описания
    $Tab2 = $PHPShopGUI->setCollapse('Краткое описание', $PHPShopGUI->loadLib('tab_description', $data));

    // Редактор подробного описания
    $Tab3 = $PHPShopGUI->setCollapse('Подробное описание', $PHPShopGUI->loadLib('tab_content', $data));

    // Статьи
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page']);
    $data_page = $PHPShopOrm->select(['*'], ['category' => '!=2000', 'enabled' => "='1'"], array('order' => 'name'), array('limit' => 500));

    if (strstr($data['page'], ',')) {
        $data['page'] = explode(",", $data['page']);
    } else
        $data['page'] = array($data['page']);

    $value = array();
    if (is_array($data_page))
        foreach ($data_page as $val) {
            if (is_numeric(array_search($val['link'], $data['page']))) {
                $check = 'selected';
            } else
                $check = false;

            $value[] = array($val['name'], $val['link'], $check);
        }

    // Статьи
    $Tab_docs = $PHPShopGUI->setCollapse('Статьи', $PHPShopGUI->setSelect('page_new[]', $value, '100%', false, false, true, false, false, true));

    // Файлы
    $Tab_docs .= $PHPShopGUI->setCollapse('Файлы', $PHPShopGUI->loadLib('tab_files', $data));

    // Фотогалерея
    $Tab6 = $PHPShopGUI->loadLib('tab_img', $data);

    // Характеристики
    $Tab_sorts = $PHPShopGUI->loadLib('tab_sorts', $data);

    // Заголовки
    $Tab_header = $PHPShopGUI->loadLib('tab_headers', $data);

    // Внешний код
    $Tab_external .= $PHPShopGUI->setCollapse('Интеграция', $PHPShopGUI->setField('Внешний код', $PHPShopGUI->setInputText(null, 'external_code_new', $data['external_code'], '100%')));


    // Отзывы
    $Tab_comments = $PHPShopGUI->loadLib('tab_comments', $data);
    if (empty($Tab_comments))
        $Tab_comments_enabled = true;

    // Подтипы
    $Tab_option = $PHPShopGUI->loadLib('tab_option', $data);
    $option_help = '<p class="text-muted">' . __('В одном каталоге можно заводить только 2 варианта подтипов. Штатно идут - размер и цвет, который вы можете переименовать на свои. К разным каталогам можно привязать разные варианты подтипов. Например, в Велосипедах будет ширина и диагональ, в Одежде – размер, материал.') . '</p>';
    $Tab_option .= $PHPShopGUI->setCollapse('Подсказка', $option_help);

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true, false, true), array("Изображение и видео", $Tab6, true, $PHPShopSystem->ifSerilizeParam("admoption.image_off", 1)), array("Описание", $Tab2 . $Tab3, true, false, true), array("Дополнительно", $Tab_docs . $Tab_rating . $Tab_header . $Tab_external, true, false, true), array("Характеристики", $Tab_sorts, true), array("Подтипы", $Tab_option, true), array("Отзывы", $Tab_comments, true, $Tab_comments_enabled));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "Удалить", "right", 70, "", "but", "actionDelete.catalog.edit") .
            $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.catalog.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.catalog.edit");

    $_GET['path'] = 'catalog';

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

/**
 * Экшен сохранения
 */
function actionSave() {

    // Сохранение данных
    actionUpdate();

    header('Location: ?path=' . $_GET['return'] . '&cat=' . $_POST['category_new']);
}

/**
 * Экшен обновления
 * @return bool
 */
function actionUpdate() {
    global $PHPShopModules, $PHPShopSystem, $PHPShopOrm;

    $PHPShopProduct = new PHPShopProduct($_POST['rowID']);

    $category = $PHPShopProduct->getParam('category');

    // Смена каталога
    if ($category != $_POST['category_new'])
        $category_update = true;

    // Поиск минимальной цены подтипов
    if ($PHPShopSystem->ifSerilizeParam('admoption.parent_price_enabled') != 1) {

        $PHPShopOrm->mysql_error = false;

        $parent_enabled = $PHPShopProduct->getParam('parent_enabled');

        $parentIds = $PHPShopProduct->getParam('parent');
        if (!empty($parentIds)) {
            $parent = @explode(",", $parentIds);
        }

        if (empty($parent_enabled) and ! empty($parent)) {

            // Подтипы из 1С
            if ($PHPShopSystem->ifSerilizeParam('1c_option.update_option')) {
                $ParentData = $PHPShopOrm->select(array('min(price) as price, price_n'), array('uid' => ' IN ("' . @implode('","', $parent) . '")', 'enabled' => "='1'", 'sklad' => "!='1'", 'parent_enabled' => "='1'"), false, array('limit' => 1));

                $ParentDataItems = $PHPShopOrm->select(array('sum(items) as items'), array('uid' => ' IN ("' . @implode('","', $parent) . '")', 'enabled' => "='1'", 'sklad' => "!='1'", 'parent_enabled' => "='1'"), false, false);

                if ($category_update) {
                    $PHPShopOrm->update(array('category_new' => $_POST['category_new']), array('uid' => ' IN ("' . @implode('","', $parent) . '")', 'parent_enabled' => "='1'"));
                }
            } else {
                $ParentData = $PHPShopOrm->select(array('min(price) as price, price_n'), array('id' => ' IN ("' . @implode('","', $parent) . '")', 'enabled' => "='1'", 'sklad' => "!='1'", 'parent_enabled' => "='1'"), false, false);

                $ParentDataItems = $PHPShopOrm->select(array('sum(items) as items'), array('id' => ' IN ("' . @implode('","', $parent) . '")', 'enabled' => "='1'", 'sklad' => "!='1'", 'parent_enabled' => "='1'"), false, false);

                if ($category_update) {
                    $PHPShopOrm->update(array('category_new' => $_POST['category_new']), array('id' => ' IN ("' . @implode('","', $parent) . '")', 'parent_enabled' => "='1'"));
                }
            }

            if (!empty($ParentData['price'])) {

                $_POST['price_new'] = $ParentData['price'];
                $_POST['items_new'] = $ParentDataItems['items'];

                if (!empty($ParentData['price_n']))
                    $_POST['price_n_new'] = $ParentData['price_n'];
            }
        }
    }

    // Дата измененения
    $_POST['datas_new'] = time();

    if (empty($_POST['ajax'])) {

        // Сумма по складам
        if ($PHPShopSystem->ifSerilizeParam('admoption.sklad_sum_enabled')) {
            $PHPShopOrmW = new PHPShopOrm($GLOBALS['SysValue']['base']['warehouses']);
            $data = $PHPShopOrmW->select(array('*'), false, array('order' => 'num DESC'), array('limit' => 100));
            if (is_array($data)) {
                $items = 0;
                foreach ($data as $row) {
                    if (isset($_POST['items' . $row['id'] . '_new'])) {
                        $items += $_POST['items' . $row['id'] . '_new'];
                    }
                }
            }

            if (!empty($items)) {
                $_POST['items_new'] = $items;
            }
        }

        // Добавление характеристик
        if (is_array($_POST['vendor_array_add'])) {
            foreach ($_POST['vendor_array_add'] as $k => $val) {

                $sort_array = $result = null;

                if (!empty($val)) {

                    if (strstr($val, '#')) {
                        $sort_array = explode('#', $val);
                    } else
                        $sort_array[] = $val;

                    if (is_array($sort_array))
                        foreach ($sort_array as $val_sort) {

                            $PHPShopOrmSort = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);

                            // Проверка уникальности
                            $checkName = $PHPShopOrmSort->select(array('id'), array('name' => '="' . trim($val_sort) . '"', 'category' => '=' . intval($k)), false, array('limit' => 1));

                            // Нет характеристики, создаем новую
                            if (empty($checkName['id'])) {
                                $PHPShopOrmSort->clean();

                                $result = $PHPShopOrmSort->insert(array('name_new' => trim($val_sort), 'category_new' => intval($k)));
                                if (!empty($result))
                                    $_POST['vendor_array_new'][$k][] = $result;
                            }
                            // Есть, назначем Из базы
                            else {
                                $_POST['vendor_array_new'][$k][] = $checkName['id'];
                            }
                        }
                } else
                    unset($_POST['vendor_array_add'][$k]);
            }
        }

        // Изменение характеристик
        $_POST['vendor_new'] = null;

        if (is_array($_POST['vendor_array_new'])) {

            foreach ($_POST['vendor_array_new'] as $k => $v) {
                if (is_array($v)) {
                    $v = array_unique($v);
                    $_POST['vendor_array_new'][$k] = $v;

                    foreach ($v as $key => $p) {
                        $_POST['vendor_new'] .= "i" . $k . "-" . $p . "i";
                        if (empty($p))
                            unset($_POST['vendor_array_new'][$k][$key]);
                    }
                } else
                    $_POST['vendor_new'] .= "i" . $k . "-" . $v . "i";
            }
        }

        $_POST['vendor_array_new'] = serialize($_POST['vendor_array_new']);

        // Статьи
        if (is_array($_POST['page_new'])) {

            if (empty($_POST['saveID']))
                array_pop($_POST['page_new']);

            $_POST['page_new'] = implode(",", $_POST['page_new']);
        } else
            $_POST['page_new'] = '';


        // Файлы
        if (isset($_POST['editID'])) {
            if (!empty($_POST['files_new']) and is_array($_POST['files_new'])) {
                foreach ($_POST['files_new'] as $k => $files) {

                    if (empty($files['name']))
                        $files['name'] = pathinfo($files['path'])['basename'];

                    $files_new[] = @array_map("urldecode", $files);
                }

                $_POST['files_new'] = serialize($files_new);
            } else
                $_POST['files_new'] = array();
        } else
            $_POST['files_new'] = serialize($_POST['files_new']);

        // Доп каталоги
        $_POST['dop_cat_new'] = "";
        if (is_array($_POST['dop_cat']) and $_POST['dop_cat'][0] != 'null') {
            $_POST['dop_cat_new'] = "#";
            foreach ($_POST['dop_cat'] as $v)
                if ($v != 'null' and ! strstr($v, ','))
                    $_POST['dop_cat_new'] .= $v . "#";
        }

        // Конвертер цвета
        if (!empty($_POST['parent2_new']) and empty($_POST['color_new']))
            $_POST['color_new'] = PHPShopString::getColor($_POST['parent2_new']);

        // Изображение подтипа
        if (!empty($_POST['editParent']) and ! empty($_POST['pic_big_new'])) {
            $_POST['pic_small_new'] = $_POST['pic_big_new'];
        }

        // Рейтинг
        if ($_POST['rate_new'] > 5)
            $_POST['rate_new'] = 5;

        $postOdnotip = explode(',', $_POST['odnotip_new']);
        $odnotip = [];
        if (is_array($postOdnotip)) {
            foreach ($postOdnotip as $value) {
                if ((int) $value > 0) {
                    $odnotip[] = (int) $value;

                    // Связи однотипов
                    if ($PHPShopSystem->ifSerilizeParam('admoption.odnotip')) {
                        $odnotip_data = $PHPShopOrm->getOne(['odnotip'], ['id' => '=' . (int) $value]);
                        $odnotip_array = explode(',', $odnotip_data['odnotip']);
                        if (is_array($odnotip_array)) {
                            if (!in_array($_POST['rowID'], $odnotip_array))
                                $odnotip_array[] = $_POST['rowID'];
                        } else
                            $odnotip_array[] = $_POST['rowID'];

                        $PHPShopOrm->update(['odnotip_new' => implode(',', $odnotip_array)], ['id' => '=' . (int) $value]);
                    }
                }
            }

            $_POST['odnotip_new'] = implode(',', $odnotip);
        }

        // Корректировка пустых значений
        $PHPShopOrm->updateZeroVars('newtip_new', 'enabled_new', 'spec_new', 'yml_new', 'sklad_new', 'pic_small_new', 'pic_big_new');

        if (empty($_POST['pic_small_new']) || empty($_POST['pic_big_new'])) {
            $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
            $photo = $orm->getOne(['name'], ['parent' => sprintf('="%s"', $_POST['rowID'])], ['order' => 'id asc']);
            if (empty($_POST['pic_big_new'])) {
                $_POST['pic_big_new'] = $photo['name'];
            }
            if (empty($_POST['pic_small_new'])) {
                $_POST['pic_small_new'] = str_replace(".", "s.", $photo['name']);
                if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $_POST['pic_small_new'])) {
                    $_POST['pic_small_new'] = $photo['name'];
                }
            }
        }
    }

    if (isset($_POST['price_new'])) {
        $_POST['price_new'] = str_replace(',', '.', $_POST['price_new']);
    }
    if (isset($_POST['price_n_new'])) {
        $_POST['price_n_new'] = str_replace(',', '.', $_POST['price_n_new']);
    }
    if (isset($_POST['price2_new'])) {
        $_POST['price2_new'] = str_replace(',', '.', $_POST['price2_new']);
    }
    if (isset($_POST['price3_new'])) {
        $_POST['price3_new'] = str_replace(',', '.', $_POST['price3_new']);
    }
    if (isset($_POST['price4_new'])) {
        $_POST['price4_new'] = str_replace(',', '.', $_POST['price4_new']);
    }
    if (isset($_POST['price5_new'])) {
        $_POST['price5_new'] = str_replace(',', '.', $_POST['price5_new']);
    }

    // Сохранение характеристик при смене каталога
    if (!empty($category_update)) {
        $PHPShopCategory = new PHPShopCategory($category);
        $sort_old = $PHPShopCategory->unserializeParam('sort');

        if (is_array($sort_old)) {
            $PHPShopCategory = new PHPShopCategory($_POST['category_new']);
            $sort_new = $PHPShopCategory->unserializeParam('sort');

            if (is_array($sort_new)) {
                foreach ($sort_old as $val) {
                    if (!in_array($val, $sort_new))
                        $sort_new[] = $val;
                }
            } else
                $sort_new = $sort_old;

            $PHPShopCategory->updateParam(array('sort_new' => serialize($sort_new)));
        }
    }

    // Перехват модуля до записи в БД
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    // Добавление изображения в фотогалерею
    $insert = fotoAdd();
    if (empty($_POST['pic_small_new']) and ! empty($insert['pic_small_new']))
        $_POST['pic_small_new'] = $insert['pic_small_new'];
    if (empty($_POST['pic_big_new']) and ! empty($insert['name_new']))
        $_POST['pic_big_new'] = $insert['name_new'];

    // Права пользователя
    $_POST['user_new'] = $_SESSION['idPHPSHOP'];


    if (strstr($_POST['rowID'], ","))
        $where = ['id' => ' IN (' . $_POST['rowID'] . ')'];
    else
        $where = ['id' => '=' . $_POST['rowID']];

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST, $where);
    $PHPShopOrm->clean();

    // Списывание со склада
    if (isset($_POST['items_new'])) {
        $PHPShopProduct->objRow['items'] = $_POST['items_new'];
        $PHPShopProduct->objRow['enabled'] = $_POST['enabled_new'];
        $PHPShopProduct->objRow['sklad'] = $_POST['sklad_new'];
        $PHPShopProduct->applyWarehouseControl();
    }

    // Перехват модуля после записи в БД
    $PHPShopModules->setAdmHandler(__FILE__, 'actionSave', $_POST);

    return array('success' => $action, 'enabled' => $PHPShopProduct->objRow['enabled'], 'sklad' => $PHPShopProduct->objRow['sklad'], 'id' => $_POST['rowID']);
}

// Добавление изображенияи видео в фотогалерею
function fotoAdd() {
    global $PHPShopSystem;
    require_once $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/thumb/phpthumb.php';

    // Параметры ресайзинга
    $img_tw = $PHPShopSystem->getSerilizeParam('admoption.img_tw');
    $img_th = $PHPShopSystem->getSerilizeParam('admoption.img_th');
    $img_w = $PHPShopSystem->getSerilizeParam('admoption.img_w');
    $img_h = $PHPShopSystem->getSerilizeParam('admoption.img_h');
    $img_tw = empty($img_tw) ? 150 : $img_tw;
    $img_th = empty($img_th) ? 150 : $img_th;
    $img_w = empty($img_w) ? 300 : $img_w;
    $img_h = empty($img_h) ? 300 : $img_h;

    $img_adaptive = $PHPShopSystem->getSerilizeParam('admoption.image_adaptive_resize');
    $image_save_source = $PHPShopSystem->getSerilizeParam('admoption.image_save_source');
    $width_kratko = $PHPShopSystem->getSerilizeParam('admoption.width_kratko');
    $width_podrobno = $PHPShopSystem->getSerilizeParam('admoption.width_podrobno');

    // Папка сохранения
    $path = $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $PHPShopSystem->getSerilizeParam('admoption.image_result_path');

    // Сохранять в папки каталогов
    if ($PHPShopSystem->ifSerilizeParam('admoption.image_save_catalog')) {

        $PHPShopCategory = new PHPShopCategory($_POST['category_new']);
        $parent_to = $PHPShopCategory->getParam('parent_to');
        $pathName = ucfirst(PHPShopString::toLatin($PHPShopCategory->getName()));

        if (!empty($parent_to)) {
            $PHPShopCategory = new PHPShopCategory($parent_to);
            $pathName = ucfirst(PHPShopString::toLatin($PHPShopCategory->getName())) . '/' . $pathName;
            $parent_to = $PHPShopCategory->getParam('parent_to');
        }

        if (!empty($parent_to)) {
            $PHPShopCategory = new PHPShopCategory($parent_to);
            $pathName = '/' . ucfirst(PHPShopString::toLatin($PHPShopCategory->getName())) . '/' . $pathName;
        }

        $path .= $pathName . '/';

        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . $path))
            @mkdir($_SERVER['DOCUMENT_ROOT'] . $path, 0777, true);
    }

    // Корекция
    $path = str_replace('//', '/', $path);

    // Соль
    $RName = substr(abs(crc32(time())), 0, 5);

    // Копируем от пользователя
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        $_FILES['file']['name'] = PHPShopString::toLatin(str_replace('.' . $_FILES['file']['ext'], '', PHPShopString::utf8_win1251($_FILES['file']['name']))) . '.' . $_FILES['file']['ext'];
        if (in_array($_FILES['file']['ext'], array('gif', 'png', 'jpg', 'jpeg', 'webp','mp4','mov'))) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $path . $_FILES['file']['name'])) {
                $file = $_SERVER['DOCUMENT_ROOT'] . $path . $_FILES['file']['name'];
                $file_name = $_FILES['file']['name'];
                $path_parts = pathinfo($file);
                $tmp_file = $_SERVER['DOCUMENT_ROOT'] . $path . $_FILES['file']['name'];
            }
        }
    }

    // Читаем файл из URL
    elseif (!empty($_POST['furl'])) {
        $file = $_POST['img_new'];
        $path_parts = pathinfo($file);
        $file_name = $path_parts['basename'];
    }

    // Читаем файл из файлового менеджера
    elseif (!empty($_POST['img_new'])) {
        $file = $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $_POST['img_new'];
        $path_parts = pathinfo($file);
        $file_name = $path_parts['basename'];

        // Сохранение пути файла
        if ($PHPShopSystem->ifSerilizeParam('admoption.image_save_path'))
            $path = $GLOBALS['SysValue']['dir']['dir'] . str_replace($_SERVER['DOCUMENT_ROOT'], '', $path_parts['dirname']) . '/';
    }

    if (!empty($file)) {

        // Видео
        if (in_array($path_parts['extension'], array('mp4','mov'))) {
            $name = 'vid' . $_POST['rowID'] . '_' . $RName . '.' . strtolower($path_parts['extension']);
            $name_s = '';
            @copy($file, $_SERVER['DOCUMENT_ROOT'] . $path . $name);
        } 
        // Изображение
        else {

            // Маленькое изображение (тумбнейл)
            $thumb = new PHPThumb($file);
            $thumb->setOptions(array('jpegQuality' => $width_kratko));

            // Адаптивность
            if (!empty($img_adaptive))
                $thumb->adaptiveResize($img_tw, $img_th);
            else
                $thumb->resize($img_tw, $img_th);

            $watermark = $PHPShopSystem->getSerilizeParam('admoption.watermark_image');
            $watermark_text = $PHPShopSystem->getSerilizeParam('admoption.watermark_text');

            // Исходное название
            if ($PHPShopSystem->ifSerilizeParam('admoption.image_save_name')) {
                $name_s = $path_parts['filename'] . 's.' . strtolower($thumb->getFormat());
                $name = $path_parts['filename'] . '.' . strtolower($thumb->getFormat());
                $name_big = $path_parts['filename'] . '_big.' . strtolower($thumb->getFormat());

                if (!empty($image_save_source)) {
                    $file_big = $_SERVER['DOCUMENT_ROOT'] . $path . $name_big;
                    @copy($file, $file_big);
                }
            }
            // SEO название
            elseif ($PHPShopSystem->ifSerilizeParam('admoption.image_save_seo')) {

                if (!empty($_POST['prod_seo_name'])) {
                    $seo_name = $_POST['prod_seo_name'];
                } else {
                    PHPShopObj::loadClass("string");
                    $seo_name = str_replace(array("_", "+", '&#43;'), array("-", "", ""), PHPShopString::toLatin($_POST['name_new']));
                }
                $name_s = $seo_name . '-' . $_POST['rowID'] . '-' . $RName . 's.' . strtolower($thumb->getFormat());
                $name = $seo_name . '-' . $_POST['rowID'] . '-' . $RName . '.' . strtolower($thumb->getFormat());
                $name_big = $seo_name . '-' . $_POST['rowID'] . '-' . $RName . '_big.' . strtolower($thumb->getFormat());
            } else {
                $name_s = 'img' . $_POST['rowID'] . '_' . $RName . 's.' . strtolower($thumb->getFormat());
                $name = 'img' . $_POST['rowID'] . '_' . $RName . '.' . strtolower($thumb->getFormat());
                $name_big = 'img' . $_POST['rowID'] . '_' . $RName . '_big.' . strtolower($thumb->getFormat());
            }


            // Ватермарк тубнейла
            if ($PHPShopSystem->ifSerilizeParam('admoption.watermark_small_enabled')) {

                // Image
                if (!empty($watermark) and file_exists($_SERVER['DOCUMENT_ROOT'] . $watermark))
                    $thumb->createWatermark($_SERVER['DOCUMENT_ROOT'] . $watermark, $PHPShopSystem->getSerilizeParam('admoption.watermark_right'), $PHPShopSystem->getSerilizeParam('admoption.watermark_bottom'), $PHPShopSystem->getSerilizeParam('admoption.watermark_center_enabled'));
                // Text
                elseif (!empty($watermark_text))
                    $thumb->createWatermarkText($watermark_text, $PHPShopSystem->getSerilizeParam('admoption.watermark_text_size'), $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/font/' . $PHPShopSystem->getSerilizeParam('admoption.watermark_text_font') . '.ttf', $PHPShopSystem->getSerilizeParam('admoption.watermark_right'), $PHPShopSystem->getSerilizeParam('admoption.watermark_bottom'), $PHPShopSystem->getSerilizeParam('admoption.watermark_text_color'), $PHPShopSystem->getSerilizeParam('admoption.watermark_text_alpha'), 0, $PHPShopSystem->getSerilizeParam('admoption.watermark_center_enabled'));
            }

            // Сохранение в webp
            if ($PHPShopSystem->ifSerilizeParam('admoption.image_webp_save')) {
                $thumb->setFormat('WEBP');
                $name_s = str_replace([".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF"], '.webp', $name_s);
            }

            $thumb->save($_SERVER['DOCUMENT_ROOT'] . $path . $name_s);

            // Большое изображение
            $thumb = new PHPThumb($file);
            $thumb->setOptions(array('jpegQuality' => $width_podrobno));

            // Адаптивность
            if (!empty($img_adaptive))
                $thumb->adaptiveResize($img_w, $img_h);
            else
                $thumb->resize($img_w, $img_h);

            // Ватермарк большого изображения
            if ($PHPShopSystem->ifSerilizeParam('admoption.watermark_big_enabled')) {

                // Image
                if (!empty($watermark) and file_exists($_SERVER['DOCUMENT_ROOT'] . $watermark))
                    $thumb->createWatermark($_SERVER['DOCUMENT_ROOT'] . $watermark, $PHPShopSystem->getSerilizeParam('admoption.watermark_right'), $PHPShopSystem->getSerilizeParam('admoption.watermark_bottom'), $PHPShopSystem->getSerilizeParam('admoption.watermark_center_enabled'));
                // Text
                elseif (!empty($watermark_text))
                    $thumb->createWatermarkText($watermark_text, $PHPShopSystem->getSerilizeParam('admoption.watermark_text_size'), $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/font/' . $PHPShopSystem->getSerilizeParam('admoption.watermark_text_font') . '.ttf', $PHPShopSystem->getSerilizeParam('admoption.watermark_right'), $PHPShopSystem->getSerilizeParam('admoption.watermark_bottom'), $PHPShopSystem->getSerilizeParam('admoption.watermark_text_color'), $PHPShopSystem->getSerilizeParam('admoption.watermark_text_alpha'), 0, $PHPShopSystem->getSerilizeParam('admoption.watermark_center_enabled'));
            }

            // Сохранение в webp
            if ($PHPShopSystem->ifSerilizeParam('admoption.image_webp_save')) {
                $thumb->setFormat('WEBP');
                $name = str_replace(['.jpg', '.JPG', '.png', '.PNG', '.gif', '.GIF'], '.webp', $name);
            }

            $thumb->save($_SERVER['DOCUMENT_ROOT'] . $path . $name);

            // Исходное изображение
            if (!empty($image_save_source)) {

                // Сохранение в webp
                if ($PHPShopSystem->ifSerilizeParam('admoption.image_webp_save')) {
                    $thumb->setFormat('WEBP');
                    $name_big = str_replace(['.jpg', '.JPG', '.png', '.PNG', '.gif', '.GIF'], '.webp', $name_big);
                }

                if (!$PHPShopSystem->ifSerilizeParam('admoption.image_save_name')) {
                    $file_big = $_SERVER['DOCUMENT_ROOT'] . $path . $name_big;
                    @copy($file, $file_big);
                }

                // Ватермарк
                if ($PHPShopSystem->ifSerilizeParam('admoption.watermark_source_enabled')) {

                    $thumb = new PHPThumb($file_big);
                    $thumb->setOptions(array('jpegQuality' => $width_podrobno));
                    $thumb->setWorkingImage($thumb->getOldImage());

                    // Image
                    if (!empty($watermark) and file_exists($_SERVER['DOCUMENT_ROOT'] . $watermark))
                        $thumb->createWatermark($_SERVER['DOCUMENT_ROOT'] . $watermark, $PHPShopSystem->getSerilizeParam('admoption.watermark_right'), $PHPShopSystem->getSerilizeParam('admoption.watermark_bottom'), $PHPShopSystem->getSerilizeParam('admoption.watermark_center_enabled'));
                    // Text
                    elseif (!empty($watermark_text))
                        $thumb->createWatermarkText($watermark_text, $PHPShopSystem->getSerilizeParam('admoption.watermark_text_size'), $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/font/' . $PHPShopSystem->getSerilizeParam('admoption.watermark_text_font') . '.ttf', $PHPShopSystem->getSerilizeParam('admoption.watermark_right'), $PHPShopSystem->getSerilizeParam('admoption.watermark_bottom'), $PHPShopSystem->getSerilizeParam('admoption.watermark_text_color'), $PHPShopSystem->getSerilizeParam('admoption.watermark_text_alpha'), 0, $PHPShopSystem->getSerilizeParam('admoption.watermark_center_enabled'));

                    $thumb->save($file_big);
                }
            }
        }

        if (!$PHPShopSystem->ifSerilizeParam('admoption.image_save_name') and ! empty($tmp_file))
            unlink($tmp_file);

        // Добавление в таблицу фотогалереи
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
        $insert['parent_new'] = $_POST['rowID'];
        $insert['name_new'] = $path . $name;
        $insert['pic_small_new'] = $path . $name_s;
        $PHPShopOrm->insert($insert);
        return $insert;
    }
}

// Удаление фотогалереи
function fotoDelete($where = null) {

    if (!is_array($where))
        $where = array('parent' => '=' . intval($_POST['rowID']));

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
    $data = $PHPShopOrm->select(array('*'), $where, false, array('limit' => 100));
    if (is_array($data)) {
        foreach ($data as $row) {
            $name = $row['name'];
            $pathinfo = pathinfo($name);
            $oldWD = getcwd();
            $dirWhereRenameeIs = $_SERVER['DOCUMENT_ROOT'] . $pathinfo['dirname'];
            $oldFilename = $pathinfo['basename'];

            @chdir($dirWhereRenameeIs);
            @unlink($oldFilename);
            $oldFilename_s = str_replace(".", "s.", $oldFilename);
            @unlink($oldFilename_s);
            $oldFilename_big = str_replace(".", "_big.", $oldFilename);
            @unlink($oldFilename_big);
            @chdir($oldWD);
        }
        $PHPShopOrm->clean();
        $result = $PHPShopOrm->delete($where);

        // Проверка главного изображения товара
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
        $data_main = $PHPShopOrm->getOne(array('pic_big'), array('id' => '=' . intval($row['parent'])));

        if (is_array($data_main) and $name == $data_main['pic_big']) {
            $result = $PHPShopOrm->update(array('pic_small_new' => '', 'pic_big_new' => ''), array('id' => '=' . intval($row['parent'])));
        }


        return $result;
    }
}

// Функция удаления
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules, $PHPShopSystem;

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    // Удаление подтипов
    $data = $PHPShopOrm->select(array('parent'), array('id' => '=' . intval($_POST['rowID'])));
    $PHPShopOrm->clean();

    $action = $PHPShopOrm->delete(array('id' => '=' . intval($_POST['rowID'])));

    // Удаление фотогалереи
    if ($action)
        fotoDelete();

    // Удаление подтипов при удалении главного товара
    if ($action and ! empty($data['parent'])) {

        $parent = @explode(",", $data['parent']);
        $PHPShopOrm->mysql_error = false;

        if ($PHPShopSystem->ifSerilizeParam('1c_option.update_option'))
            $PHPShopOrm->delete(array('uid' => ' IN ("' . @implode('","', $parent) . '")'));
        else
            $PHPShopOrm->delete(array('id' => ' IN ("' . @implode('","', $parent) . '")'));
    }

    // Удаление подтипа, изменение основного товара по подтипу
    if (!empty($_POST['parent_enabled'])) {

        $PHPShopProduct = new PHPShopProduct($_POST['parent']);
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
        $parent_array = @explode(",", $PHPShopProduct->getParam('parent'));
        if (is_array($parent_array)) {
            foreach ($parent_array as $v)
                if (!empty($v) and $v != $_POST['rowID'])
                    $parent_array_true[] = $v;
        }

        if (is_array($parent_array_true))
            $PHPShopOrm->update(array('parent_new' => @implode(",", $parent_array_true)), array('id' => '=' . intval($_POST['parent'])));
        else
            $PHPShopOrm->update(array('parent_new' => ''), array('id' => '=' . intval($_POST['parent'])));
    }

    return array("success" => $action);
}

/**
 * Редактировать опцию
 */
function actionOptionEdit() {
    global $PHPShopGUI, $PHPShopModules, $PHPShopOrm;

    PHPShopObj::loadClass('sort');

    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_REQUEST['id'])));

    if (empty($data['name'])) {
        $data['name'] = $_REQUEST['parent_name'] . ' ' . $data['parent'] . ' ' . $data['parent2'];
    }

    $PHPShopGUI->field_col = 2;
    $PHPShopGUI->tab_key = 1000;

    // Конвертер цвета
    if (!empty($data['parent2']) and empty($data['color']))
        $data['color'] = PHPShopString::getColor($data['parent2']);

    $PHPShopCategoryArray = new PHPShopCategoryArray(array('id' => '=' . $data['category']));
    $CategoryArray = $PHPShopCategoryArray->getArray();

    $PHPShopParentNameArray = new PHPShopParentNameArray(array('id' => '=' . $CategoryArray[$data['category']]['parent_title']));
    $parent_title = $PHPShopParentNameArray->getParam($CategoryArray[$data['category']]['parent_title'] . ".name");
    $parent_color = $PHPShopParentNameArray->getParam($CategoryArray[$data['category']]['parent_title'] . ".color");
    if (empty($parent_title))
        $parent_title = 'Размер';

    if (empty($parent_color))
        $parent_color = 'Цвет';

    $Tab1 = $PHPShopGUI->setField(array($parent_title, '&#8470;'), array($PHPShopGUI->setInputArg(array('name' => 'parent_new', 'type' => 'text', 'value' => $data['parent'])), $PHPShopGUI->setInputArg(array('name' => 'num_new', 'type' => 'text', 'value' => $data['num'], 'size' => 110))), array(array(2, 6), array(1, 2)), null, null, 'control-label', false, false);
    $Tab1 .= $PHPShopGUI->setField(array($parent_color, 'Код'), array($PHPShopGUI->setInputArg(array('name' => 'parent2_new', 'type' => 'text', 'value' => $data['parent2'])), $PHPShopGUI->setInputColor('color_new', $data['color'], 110)), array(array(2, 6), array(1, 2)));
    $Tab1 .= $PHPShopGUI->setField('Название', $PHPShopGUI->setInputArg(array('name' => 'name_new', 'type' => 'text.required', 'value' => $data['name'])) . $PHPShopGUI->setHelp(__('Полное') . ' <a href="?path=product&return=catalog.' . $data['category'] . '&id=' . $_REQUEST['id'] . '&view=option" target="_blank">' . __('название товара') . '</a>, ' . __('попадающего в корзину'), false, false));
    $Tab1 .= $PHPShopGUI->setField('Артикул', $PHPShopGUI->setInputArg(array('name' => 'uid_new', 'type' => 'text', 'value' => $data['uid'], 'size' => '100%')));

    // Склад
    if (empty($data['ed_izm']))
        $ed_izm = __('шт.');
    else
        $ed_izm = $data['ed_izm'];

    // Дополнительный склад
    $PHPShopOrmWarehouse = new PHPShopOrm($GLOBALS['SysValue']['base']['warehouses']);
    $dataWarehouse = $PHPShopOrmWarehouse->select(array('*'), array('enabled' => "='1'"), array('order' => 'num DESC'), array('limit' => 100));
    if (is_array($dataWarehouse)) {

        $warehouse_main = 'Общий склад';
        foreach ($dataWarehouse as $row) {
            $warehouse_name[] = $row['name'];
            $warehouse[] = $PHPShopGUI->setInputText(false, 'items' . $row['id'] . '_new', $data['items' . $row['id']], 80, $ed_izm);
            $warehouse_size[] = array(2, 1);
        }

        $Tab1 .= $PHPShopGUI->setField($warehouse_name, $warehouse, $warehouse_size);
    } else
        $warehouse_main = 'Склад';

    // Склад и вес
    $Tab1 .= $PHPShopGUI->setField(array($warehouse_main, 'Вес'), array($PHPShopGUI->setInputText(false, 'items_new', $data['items'], 150, $ed_izm), $PHPShopGUI->setInputText(false, 'weight_new', $data['weight'], 150, __('г&nbsp;&nbsp;&nbsp;&nbsp;'))), array(array(2, 4), array(2, 4)));

    // Валюты
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    $valuta_area = null;
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            if ($data['baseinputvaluta'] == $val['id']) {
                $check = 'checked';
                $valuta_def_name = $val['code'];
            } else
                $check = false;
            $valuta_area .= $PHPShopGUI->setRadio('baseinputvaluta_new', $val['id'], $val['name'], $data['baseinputvaluta'], false);
        }

    // Цена
    $Tab1 .= $PHPShopGUI->setField(array('Цена 1', 'Цена 2'), array($PHPShopGUI->setInputText(null, 'price_new', $data['price'], 150, $valuta_def_name), $PHPShopGUI->setInputText(null, 'price2_new', $data['price2'], 150, $valuta_def_name)), array(array(2, 4), array(2, 4)));

    // Вывод
    $Tab1 .= $PHPShopGUI->setField(array('Вывод', 'Валюта'), array($PHPShopGUI->setCheckbox('enabled_new', 1, 'Вывод в товаре', $data['enabled']), $valuta_area), array(array(2, 4), array(2, 4)));


    $Tab1 .= $PHPShopGUI->setInputArg(array('name' => 'rowID', 'type' => 'hidden', 'value' => $_REQUEST['id']));
    $Tab1 .= $PHPShopGUI->setInputArg(array('name' => 'parentID', 'type' => 'hidden', 'value' => $_REQUEST['parentID']));

    $Tab1 .= $PHPShopGUI->setField("Изображение", $PHPShopGUI->setIcon($data['pic_big'], "pic_big_new", false, array('load' => false, 'server' => false, 'url' => false, 'view' => true)) . $PHPShopGUI->setHelp('Вы можете выбрать фото в закладке <a href="#" class="set-image-tab">Изображение</a>.'));

    $Tab1 = $PHPShopGUI->setCollapse('Информация', $Tab1);

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Габариты
    $Tab_info_size = $PHPShopGUI->setField('Длина', $PHPShopGUI->setInputText(false, 'length_new', $data['length'], 150, __('см&nbsp;')), 'left');
    $Tab_info_size .= $PHPShopGUI->setField('Ширина', $PHPShopGUI->setInputText(false, 'width_new', $data['width'], 150, __('см&nbsp;')), 'left');
    $Tab_info_size .= $PHPShopGUI->setField('Высота', $PHPShopGUI->setInputText(false, 'height_new', $data['height'], 150, __('см&nbsp;')), 'left');
    $Tab_info_size .= $PHPShopGUI->setField('Единица измерения', $PHPShopGUI->setInputText(false, 'ed_izm_new', $ed_izm, 150));
    $Tab2 = $PHPShopGUI->setCollapse('Габариты', $Tab_info_size);

    $Tab_price .= $PHPShopGUI->setField('Цена 3', $PHPShopGUI->setInputText(null, 'price3_new', $data['price3'], 150, $valuta_def_name), 2);
    $Tab_price .= $PHPShopGUI->setField('Цена 4', $PHPShopGUI->setInputText(null, 'price4_new', $data['price4'], 150, $valuta_def_name), 2);
    $Tab_price .= $PHPShopGUI->setField('Цена 5', $PHPShopGUI->setInputText(null, 'price5_new', $data['price5'], 150, $valuta_def_name), 2);
    $Tab_price .= $PHPShopGUI->setField('Старая цена', $PHPShopGUI->setInputText(null, 'price_n_new', $data['price_n'], 150, $valuta_def_name));
    $Tab_price .= $PHPShopGUI->setField('Закупочная цена', $PHPShopGUI->setInputText(null, 'price_purch_new', $data['price_purch'], 150, $valuta_def_name));
    $Tab2 .= $PHPShopGUI->setCollapse('Цены', $Tab_price);

    // Внешний код
    $Tab2 .= $PHPShopGUI->setCollapse('Интеграция', $PHPShopGUI->setField('Внешний код', $PHPShopGUI->setInputText(null, 'external_code_new', $data['external_code'], '100%')));

    $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("Дополнительно", $Tab2));
    exit($PHPShopGUI->_CODE . '<p class="clearfix"> </p>');
}

/**
 * Редактировать файл
 */
function actionFileEdit() {
    global $PHPShopGUI, $PHPShopModules;


    $PHPShopGUI->field_col = 2;
    $PHPShopGUI->_CODE .= $PHPShopGUI->setField('Название', $PHPShopGUI->setInputArg(array('name' => 'modal_file_name', 'type' => 'text.required', 'value' => urldecode($_GET['name']))));
    $PHPShopGUI->_CODE .= $PHPShopGUI->setField('Файл', $PHPShopGUI->setFile($_GET['file'], 'lfile', array('server' => true)));
    $PHPShopGUI->_CODE .= $PHPShopGUI->setInput('hidden', 'selectID', $_POST['selectID']);
    $PHPShopGUI->_CODE .= $PHPShopGUI->setInput('hidden', 'fileID', $_POST['fileID']);

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    exit($PHPShopGUI->_CODE . '<p class="clearfix"> </p>');
}

// Функция удаления изображения
function actionImgDelete() {
    global $PHPShopModules;

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = fotoDelete(array('id' => '=' . $_POST['rowID']));

    return array("success" => $action);
}

// Функция редактирования изображения
function actionImgEdit() {
    global $PHPShopModules;

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . intval($_POST['rowID'])));

    return array("success" => $action);
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>