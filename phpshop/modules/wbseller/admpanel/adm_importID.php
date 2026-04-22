<?php

include_once dirname(__FILE__) . '/../class/WbSeller.php';
$TitlePage = __('Товар из Wildberries');
PHPShopObj::loadClass("product");
PHPShopObj::loadClass('category');

// Построение дерева категорий
function treegenerator($array, $i, $curent) {
    global $tree_array, $CategoryArray;
    $del = '&brvbar;&nbsp;&nbsp;&nbsp;&nbsp;';
    $tree_select = $tree_select_dop = $check = false;

    $del = str_repeat($del, $i);
    if (is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {

            $check = treegenerator($tree_array[$k], $i + 1, $k);
            $disabled = null;

            if ($CategoryArray[$k]['category_wbseller'] == PHPShopString::utf8_win1251($curent))
                $selected = 'selected';
            else
                $selected = null;

            if (empty($check['select'])) {
                $tree_select .= '<option value="' . $k . '" ' . $selected . $disabled . '>' . $del . $v . '</option>';

                $i = 1;
            } else {
                $tree_select .= '<option value="' . $k . '" ' . $selected . ' disabled>' . $del . $v . '</option>';
            }

            $tree_select .= $check['select'];
        }
    }
    return array('select' => $tree_select);
}

// Озон
$WbSeller = new WbSeller();

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $WbSeller;

    $PHPShopGUI->field_col = 4;

    $product_info = $WbSeller->getProduct($_GET['id'])['cards'][0];

    if (!empty($product_info['nmID']))
        $PHPShopGUI->action_button['Загрузить товар'] = array(
            'name' => 'Загрузить товар',
            'locale' => true,
            'action' => 'saveID',
            'class' => 'btn  btn-default btn-sm navbar-btn' . $GLOBALS['isFrame'],
            'type' => 'submit',
            'icon' => 'glyphicon glyphicon-save'
        );


    $PHPShopGUI->setActionPanel(__('Товар') . ': ' . PHPShopString::utf8_win1251($product_info['title'], true) . '', false, array('Загрузить товар'));

    // Знак рубля
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();

    if (is_array($product_info['characteristics']))
        foreach ($product_info['characteristics'] as $characteristics) {

            // Вес
            if ($characteristics['name'] == PHPShopString::win_utf8('Вес товара с упаковкой (г)'))
                $product_info['weight'] = $characteristics['value'];
        }


    $PHPShopCategoryArray = new PHPShopCategoryArray(false, ["id", "name", "parent_to", "category_wbseller"]);
    $GLOBALS['CategoryArray'] = $CategoryArray = $PHPShopCategoryArray->getArray();


    $GLOBALS['count'] = count($CategoryArray);

    $CategoryArray[0]['name'] = '- ' . __('Выбрать каталог') . ' -';
    $tree_array = array();

    if (is_array($CategoryArray)) {
        foreach ($PHPShopCategoryArray->getKey('parent_to.id', true) as $k => $v) {
            foreach ($v as $cat) {
                $tree_array[$k]['sub'][$cat] = $CategoryArray[$cat]['name'];
            }
            $tree_array[$k]['name'] = $CategoryArray[$k]['name'];
            $tree_array[$k]['id'] = $k;
        }
    }

    $GLOBALS['tree_array'] = &$tree_array;


    if (is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator($tree_array[$k], 1, $product_info['category']);

            if (empty($tree_array[$k]))
                $disabled = null;
            else
                $disabled = ' disabled';

            if ($CategoryArray[$k]['category_wbseller'] == PHPShopString::utf8_win1251($product_info['category']))
                $selected = 'selected';
            else
                $selected = null;

            $tree_select .= '<option value="' . $k . '"  ' . $disabled . ' ' . $selected . '>' . $v . '</option>';

            $tree_select .= $check['select'];
        }


    $tree_select = '<select class="selectpicker show-menu-arrow" data-live-search="true" data-container="body"  data-style="btn btn-default btn-sm" name="category_new"  data-width="100%"><option value="0">' . $CategoryArray[0]['name'] . '</option>' . $tree_select . '</select>';


    // Редактор
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('content_new');
    $oFCKeditor->Height = '400';
    $oFCKeditor->Value = PHPShopString::utf8_win1251(nl2br($product_info['description']));

    if (is_array($product_info['photos']) and count($product_info['photos']) > 0) {
        $icon = null;
        foreach ($product_info['photos'] as $img)
            if (!empty($img) and ! stristr($img['big'], '.mp4'))
                $icon .= '<div class="pull-left" style="padding:3px">' . $PHPShopGUI->setIcon($img['big'], "images[]", true, array('load' => false, 'server' => true, 'url' => true, 'view' => true)) . '</div>';
    } else
        $icon = $PHPShopGUI->setIcon('./images/no_photo.gif', "pic", true, array('load' => false, 'server' => true, 'url' => true, 'view' => true));


    $Tab1 = $PHPShopGUI->setField("Название", $PHPShopGUI->setTextarea('name_new', PHPShopString::utf8_win1251($product_info['title'], true)));
    $Tab1 .= $PHPShopGUI->setField("Артикул", $PHPShopGUI->setInputText(null, 'uid_new', PHPShopString::utf8_win1251($product_info['vendorCode'])));
    $Tab1 .= $PHPShopGUI->setField("Изображения", $icon);
    $Tab1 .= $PHPShopGUI->setField("WB ID", $PHPShopGUI->setText($PHPShopGUI->setLink('https://www.wildberries.ru/catalog/' . $product_info['nmID'] . '/detail.aspx', $product_info['nmID'])) . $PHPShopGUI->setInput("hidden", "export_wb_id_new", $product_info['nmID']));
    $Tab1 .= $PHPShopGUI->setField("Шрихкод", $PHPShopGUI->setInputText(null, 'barcode_wb_new', $product_info['sizes'][0]['skus'][0]));
    $Tab1 .= $PHPShopGUI->setField("Категория в WB", $PHPShopGUI->setText(PHPShopString::utf8_win1251($product_info['subjectName'], true)) . $PHPShopGUI->setInput("hidden", "category_wbseller", PHPShopString::utf8_win1251($product_info['category'])));

    // Выбор каталога
    $Tab1 .= $PHPShopGUI->setField("Размещение", $tree_select);

    $Tab1 .= $PHPShopGUI->setField('Склад', $PHPShopGUI->setInputText(false, 'items_new', 1, 150, __('шт.')));

    // Опции вывода
    $Tab1 .= $PHPShopGUI->setField('Опции вывода', $PHPShopGUI->setCheckbox('enabled_new', 1, 'Вывод в каталоге', 1) . '<br>' .
            $PHPShopGUI->setCheckbox('spec_new', 1, 'Спецпредложение', 0) . '<br>' .
            $PHPShopGUI->setCheckbox('newtip_new', 1, 'Новинка', 0));
    $Tab1 .= $PHPShopGUI->setField('Сортировка', $PHPShopGUI->setInputText('&#8470;', 'num_new', 0, 150));

    // Цена
    $product_price = $WbSeller->getProductPrice($product_info['nmID'])['data']['listGoods'][0]['sizes'][0]['price'];

    $Tab1 .= $PHPShopGUI->setField("Цена", $PHPShopGUI->setInputText(null, 'price_new', (float) $product_price, 150));
    $Tab1 .= $PHPShopGUI->setField("Старая цена", $PHPShopGUI->setInputText(null, 'price_n_new', (float) 0, 150));
    $Tab1 .= $PHPShopGUI->setField("Закупочная цена", $PHPShopGUI->setInputText(null, 'price_purch_new', (float) 0, 150));

    $baseinputvaluta = $PHPShopSystem->getDefaultOrderValutaId();

    // Валюты
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();

    $valuta_area = null;
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            $valuta_area .= $PHPShopGUI->setRadio('baseinputvaluta_new', $val['id'], $val['name'], $baseinputvaluta);
        }

    // Валюта
    $Tab1 .= $PHPShopGUI->setField('Валюта', $valuta_area);

    $Tab_size = $PHPShopGUI->setField("Вес", $PHPShopGUI->setInputText(null, 'weight_new', $product_info['weightBrutto']*100, 100, __('г&nbsp;&nbsp;&nbsp;&nbsp;')));
    $Tab_size .= $PHPShopGUI->setField("Высота", $PHPShopGUI->setInputText(null, 'height_new', $product_info['dimensions']['height'], 100, __('cм')));
    $Tab_size .= $PHPShopGUI->setField("Ширина", $PHPShopGUI->setInputText(null, 'width_new', $product_info['dimensions']['width'], 100, __('cм')));
    $Tab_size .= $PHPShopGUI->setField("Длина", $PHPShopGUI->setInputText(null, 'length_new', $product_info['dimensions']['length'], 100, __('cм')));

    $Tab1 = $PHPShopGUI->setCollapse('Данные', $Tab1);
    $Tab1 .= $PHPShopGUI->setCollapse('Габариты', $Tab_size);
    $Tab2 = $PHPShopGUI->setCollapse("Описание", $oFCKeditor->AddGUI());


    $Tab_sort = null;

    if (is_array($product_info['characteristics']))
        foreach ($product_info['characteristics'] as $val) {


            unset($value_new);

            if ($val['name'] == PHPShopString::win_utf8('Вес товара с упаковкой (г)') or $val['name'] == PHPShopString::win_utf8('Раздел меню')) {
                continue;
            }

            if (is_array($val['value']) and !empty($val['value'][0]))
                $sort_name = PHPShopString::utf8_win1251((string)$val['value'][0], true);
            elseif(!is_array($val['value']) and !empty($val['value']))
                $sort_name = PHPShopString::utf8_win1251((string)$val['value'], true);
            else $sort_name=null;


            $value_new[] = [__('Ничего не выбрано'), 0];
            $value_new[] = array($sort_name, $sort_name, $sort_name);

            $Tab_sort .= $PHPShopGUI->setField(PHPShopString::utf8_win1251($val['name'], true), $PHPShopGUI->setSelect('vendor_array[' . PHPShopString::utf8_win1251($val['name'], true) . ']', $value_new, '100%', false, false, false, false, 1, false, false, 'selectpicker'));
        }

    $Tab2 .= $PHPShopGUI->setCollapse("Характеристики", $Tab_sort);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Информация", $Tab1 . $Tab2, true, false, true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", PHPShopString::toLatin($_GET['id']), "right", 70, "", "but") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.catalog.edit");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);

    return true;
}

/**
 *  Проверка характеристик
 */
function wb_sort($sort_name, $sort_value, $category) {
    global $PHPShopBase;

    $return = null;
    $debug = false;

    // Получить ИД набора характеристик в каталоге
    $PHPShopOrm = new PHPShopOrm();
    $PHPShopOrm->debug = $debug;
    $result_1 = $PHPShopOrm->query('select sort,name from ' . $GLOBALS['SysValue']['base']['categories'] . ' where id="' . $category . '"  limit 1', __FUNCTION__, __LINE__);
    $row_1 = mysqli_fetch_array($result_1);
    $cat_sort = unserialize($row_1['sort']);
    $cat_name = $row_1['name'];

    // Отсутствует в базе
    if (is_array($cat_sort))
        $where_in = ' and a.id IN (' . @implode(",", $cat_sort) . ') ';
    else
        $where_in = null;

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
    $PHPShopOrm->debug = $debug;

    $result_2 = $PHPShopOrm->query('select a.id as parent, b.id from ' . $GLOBALS['SysValue']['base']['sort_categories'] . ' AS a 
        JOIN ' . $GLOBALS['SysValue']['base']['sort'] . ' AS b ON a.id = b.category where a.name="' . $sort_name . '" and b.name="' . $sort_value . '" ' . $where_in . ' limit 1', __FUNCTION__, __LINE__);
    $row_2 = mysqli_fetch_array($result_2);

    // Присутствует в  базе
    if (!empty($where_in) and isset($row_2['id'])) {
        $return[$row_2['parent']][] = $row_2['id'];
    }
    // Отсутствует в базе
    else {

        // Проверка характеристики
        if (!empty($where_in))
            $sort_name_present = $PHPShopBase->getNumRows('sort_categories', 'as a where a.name="' . $sort_name . '" ' . $where_in . ' limit 1');

        // Создаем новую характеристику
        if (empty($sort_name_present) and ! empty($category)) {

            // Есть
            if (!empty($cat_sort[0])) {
                $PHPShopOrm = new PHPShopOrm();
                $PHPShopOrm->debug = $debug;

                $result_3 = $PHPShopOrm->query('select category from ' . $GLOBALS['SysValue']['base']['sort_categories'] . ' where id="' . intval($cat_sort[0]) . '"  limit 1', __FUNCTION__, __LINE__);
                $row_3 = mysqli_fetch_array($result_3);
                $cat_set = $row_3['category'];
            }
            // Нет, создать новый набор
            elseif (!empty($cat_name)) {

                // Создание набора характеристик
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
                $PHPShopOrm->debug = $debug;
                $cat_set = $PHPShopOrm->insert(array('name_new' => __('Для каталога') . ' ' . $cat_name, 'category_new' => 0), '_new', __FUNCTION__, __LINE__);
            }

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
            $PHPShopOrm->debug = $debug;

            if (!empty($sort_name) and ! empty($cat_set))
                if ($parent = $PHPShopOrm->insert(array('name_new' => $sort_name, 'category_new' => $cat_set, 'attribute_ozonseller_new' => $sort_id), '_new')) {

                    // Создаем новое значение характеристики
                    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);
                    $PHPShopOrm->debug = $debug;
                    $slave = $PHPShopOrm->insert(array('name_new' => $sort_value, 'category_new' => $parent, 'sort_seo_name_new' => PHPShopString::toLatin($sort_value)), '_new', __FUNCTION__, __LINE__);

                    $return[$parent][] = $slave;
                    $cat_sort[] = $parent;

                    // Обновляем набор каталога товаров
                    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
                    $PHPShopOrm->debug = $debug;
                    $PHPShopOrm->update(array('sort_new' => serialize($cat_sort)), array('id' => '=' . $category), '_new', __FUNCTION__, __LINE__);
                }
        }
        // Дописываем значение 
        elseif (!empty($sort_value)) {

            // Получаем ИД существующей характеристики
            $PHPShopOrm = new PHPShopOrm();
            $PHPShopOrm->debug = $debug;
            $result = $PHPShopOrm->query('select a.id  from ' . $GLOBALS['SysValue']['base']['sort_categories'] . ' AS a where a.name="' . $sort_name . '" ' . $where_in . ' limit 1', __FUNCTION__, __LINE__);
            if ($row = mysqli_fetch_array($result)) {
                $parent = $row['id'];
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);
                $PHPShopOrm->debug = $debug;
                $slave = $PHPShopOrm->insert(array('name_new' => $sort_value, 'category_new' => $parent), '_new', __FUNCTION__, __LINE__);

                $return[$parent][] = $slave;
            }
        }
    }

    return $return;
}

/**
 * Экшен загрузки товара
 */
function actionSave() {
    global $PHPShopSystem, $WbSeller;

    $_POST['export_wb_task_status_new'] = time();
    $_POST['export_wb_new'] = 1;

    // Поиск привязки категории Озон
    $PHPShopCategoryOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
    $category_wbseller = $_POST['category_wbseller'];
    $category = $_POST['category_new'];
    $data_categories = $PHPShopCategoryOrm->getOne(['id'], ['category_wbseller' => '=' . $category_wbseller]);

    // Нет привязки категории Wb
    if (empty($data_categories['id'])) {
        $PHPShopCategoryOrm->update(['category_wbseller_new' => $category_wbseller], ['id' => '=' . $category]);
    }

    $vendor_array = [];


    if (is_array($_POST['vendor_array']))
        foreach ($_POST['vendor_array'] as $sort_id => $sort_value) {
            $wb_sort = wb_sort($sort_id, $sort_value, $category);

            if (is_array($wb_sort))
                $vendor_array += $wb_sort;
        }

    if (is_array($vendor_array)) {
        $_POST['vendor_array_new'] = serialize($vendor_array);
        foreach ($vendor_array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $p) {
                    $_POST['vendor_new'] .= "i" . $k . "-" . $p . "i";
                }
            } else
                $_POST['vendor_new'] .= "i" . $k . "-" . $v . "i";
        }
    }

    // Права пользователя
    $_POST['user_new'] = $_SESSION['idPHPSHOP'];

    // Дата измененения
    $_POST['datas_new'] = time();

    // Корректировка пустых значений
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $PHPShopOrm->updateZeroVars('newtip_new', 'enabled_new', 'spec_new', 'yml_new');
    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->insert($_POST);

    require_once $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/thumb/phpthumb.php';
    $width_kratko = $PHPShopSystem->getSerilizeParam('admoption.width_kratko');
    $img_tw = $PHPShopSystem->getSerilizeParam('admoption.img_tw');
    $img_th = $PHPShopSystem->getSerilizeParam('admoption.img_th');

    // Папка картинок
    $path = $PHPShopSystem->getSerilizeParam('admoption.image_result_path');
    if (!empty($path))
        $path = $path . '/';

    if (is_array($_POST['images'])) {
        foreach ($_POST['images'] as $k => $img) {
            if (!empty($img)) {

                $path_parts = pathinfo($img);

                $path_parts['basename'] = $_POST['rowID'] . '_' . $path_parts['basename'];

                // Файл загружен
                if ($WbSeller->downloadFile($img, $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . '/UserFiles/Image/' . $path . $path_parts['basename']))
                    $img_load++;
                else
                    continue;

                // Новое имя
                $img = $GLOBALS['dir']['dir'] . '/UserFiles/Image/' . $path . $path_parts['basename'];

                // Запись в фотогалерее
                $PHPShopOrmImg = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
                $PHPShopOrmImg->insert(array('parent_new' => intval($action), 'name_new' => $img, 'num_new' => $k));

                $file = $_SERVER['DOCUMENT_ROOT'] . $img;
                $name = str_replace(array(".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF"), array("s.png", "s.jpg", "s.jpeg", "s.gif", "s.png", "s.jpg", "s.jpeg", "s.gif"), $file);

                if (!file_exists($name) and file_exists($file)) {

                    // Генерация тубнейла 
                    if (!empty($_POST['export_imgproc'])) {
                        $thumb = new PHPThumb($file);
                        $thumb->setOptions(array('jpegQuality' => $width_kratko));
                        $thumb->resize($img_tw, $img_th);
                        $thumb->save($name);
                    } else
                        copy($file, $name);
                }

                // Главное изображение
                if ($k == 0 and ! empty($file)) {

                    $update['pic_big_new'] = $img;

                    // Главное превью
                    $update['pic_small_new'] = str_replace(array(".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF"), array("s.png", "s.jpg", "s.jpeg", "s.gif", "s.png", "s.jpg", "s.jpeg", "s.gif"), $img);
                }
            }
        }

        $PHPShopOrm->update($update, ['id' => '=' . intval($action)]);
    }

    header('Location: ?path=product&return=catalog&id=' . $action);
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>