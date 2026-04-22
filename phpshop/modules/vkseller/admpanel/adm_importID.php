<?php

include_once dirname(__FILE__) . '/../class/VkSeller.php';
$TitlePage = __('Товар из ВКонтакте');
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
$VkSeller = new VkSeller();

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $VkSeller, $PHPShopModules;

    $PHPShopGUI->field_col = 4;

    $product_info = $VkSeller->getProduct($_GET['id'])['response']['items'][0];


    if (!empty($product_info['id']))
        $PHPShopGUI->action_button['Загрузить товар'] = array(
            'name' => 'Загрузить товар',
            'locale' => true,
            'action' => 'saveID',
            'class' => 'btn  btn-default btn-sm navbar-btn' . $GLOBALS['isFrame'],
            'type' => 'submit',
            'icon' => 'glyphicon glyphicon-save'
        );


    $PHPShopGUI->setActionPanel(__('Товар') . ': ' . $_GET['id'] . '', false, array('Загрузить товар'));

    // Знак рубля
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();


    $PHPShopCategoryArray = new PHPShopCategoryArray(false, ["id", "name", "parent_to", "category_vkseller"]);
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
            $check = treegenerator($tree_array[$k], 1, $product_info['category']['id']);

            if (empty($tree_array[$k]))
                $disabled = null;
            else
                $disabled = ' disabled';

            if ($CategoryArray[$k]['category_vkseller'] == $product_info['category']['id'])
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
    $oFCKeditor->Value = PHPShopString::utf8_win1251($product_info['description']);

    if (is_array($product_info['photos']) and count($product_info['photos']) > 0) {
        $icon = null;
        foreach ($product_info['photos'] as $img)
            if (!empty($img['sizes'][9]['url']))
                $icon .= '<div class="pull-left" style="padding:3px">' . $PHPShopGUI->setIcon($img['sizes'][9]['url'], "images[]", true, array('load' => false, 'server' => true, 'url' => true, 'view' => true)) . '</div>';
            elseif (!empty($img['sizes'][7]['url']))
                $icon .= '<div class="pull-left" style="padding:3px">' . $PHPShopGUI->setIcon($img['sizes'][7]['url'], "images[]", true, array('load' => false, 'server' => true, 'url' => true, 'view' => true)) . '</div>';
    } else
        $icon = $PHPShopGUI->setIcon('./images/no_photo.gif', "pic", true, array('load' => false, 'server' => true, 'url' => true, 'view' => true));


    $Tab1 = $PHPShopGUI->setField("Название", $PHPShopGUI->setTextarea('name_new', PHPShopString::utf8_win1251($product_info['title'])));
    $Tab1 .= $PHPShopGUI->setField("Артикул", $PHPShopGUI->setInputText(null, 'uid_new', PHPShopString::utf8_win1251($product_info['sku'])));
    $Tab1 .= $PHPShopGUI->setField("Изображения", $icon);
    $Tab1 .= $PHPShopGUI->setField("VK ID", $PHPShopGUI->setText($PHPShopGUI->setLink('https://vk.ru/market-' . $VkSeller->owner_id . '?screen=cart&w=product-' . $VkSeller->owner_id . '_' . $product_info['id'] . '%2Fquery', $product_info['id'])) . $PHPShopGUI->setInput("hidden", "export_wb_id_new", $product_info['nmID']));
    $Tab1 .= $PHPShopGUI->setField("Категория в ВКонтакте", $PHPShopGUI->setText(PHPShopString::utf8_win1251($product_info['category']['section']['name']) . ' &rarr; ' . PHPShopString::utf8_win1251($product_info['category']['name']), "left", false, false) . $PHPShopGUI->setInput("hidden", "category_vkseller", $product_info['category']['id']));

    // Выбор каталога
    $Tab1 .= $PHPShopGUI->setField("Размещение", $tree_select);

    $product_info['stock_amount'] = 1;
    $Tab1 .= $PHPShopGUI->setField('Склад', $PHPShopGUI->setInputText(false, 'items_new', $product_info['stock_amount'], 150, __('шт.')));

    // Опции вывода
    $Tab1 .= $PHPShopGUI->setField('Опции вывода', $PHPShopGUI->setCheckbox('enabled_new', 1, 'Вывод в каталоге', 1) . '<br>' .
            $PHPShopGUI->setCheckbox('spec_new', 1, 'Спецпредложение', 0) . '<br>' .
            $PHPShopGUI->setCheckbox('newtip_new', 1, 'Новинка', 0));
    $Tab1 .= $PHPShopGUI->setField('Сортировка', $PHPShopGUI->setInputText('&#8470;', 'num_new', 0, 150));

    // Цена
    $product_price = round($product_info['price']['amount'] / 100);

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
            $valuta_area .= $PHPShopGUI->setRadio('baseinputvaluta_new', $val['id'], $val['name'], $baseinputvaluta,false);
        }

    // Валюта
    $Tab1 .= $PHPShopGUI->setField('Валюта', $valuta_area);

    $Tab_size = $PHPShopGUI->setField("Вес", $PHPShopGUI->setInputText(null, 'weight_new', $product_info['weight'], 100, __('г&nbsp;&nbsp;&nbsp;&nbsp;')));
    $Tab_size .= $PHPShopGUI->setField("Высота", $PHPShopGUI->setInputText(null, 'height_new', $product_info['dimensions']['height'] / 100, 100, __('cм')));
    $Tab_size .= $PHPShopGUI->setField("Ширина", $PHPShopGUI->setInputText(null, 'width_new', $product_info['dimensions']['width'] / 100, 100, __('cм')));
    $Tab_size .= $PHPShopGUI->setField("Длина", $PHPShopGUI->setInputText(null, 'length_new', $product_info['dimensions']['length'] / 100, 100, __('cм')));

    $Tab1 = $PHPShopGUI->setCollapse('Данные', $Tab1);
    $Tab1 .= $PHPShopGUI->setCollapse('Габариты', $Tab_size);
    $Tab2 = $PHPShopGUI->setCollapse("Описание", $oFCKeditor->AddGUI());

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Информация", $Tab1 . $Tab2, true, false, true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $_GET['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.catalog.edit");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);

    return true;
}

// Загрузка изображения по ссылке 
function downloadFile($url, $path) {
    $newfname = $path;

    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );

    $file = fopen($url, 'rb', false, stream_context_create($arrContextOptions));
    if ($file) {
        $newf = fopen($newfname, 'wb');
        if ($newf) {
            while (!feof($file)) {
                fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
            }
        }
    }
    if ($file) {
        fclose($file);
    }
    if ($newf) {
        fclose($newf);
        return true;
    }
}

/**
 * Экшен загрузки товара
 */
function actionSave() {
    global $PHPShopSystem;

    $_POST['export_vk_task_status_new'] = time();
    $_POST['export_vk_new'] = 1;

    // Поиск привязки категории
    $PHPShopCategoryOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
    $category_vkseller = $_POST['category_vkseller'];
    $category = $_POST['category_new'];
    $data_categories = $PHPShopCategoryOrm->getOne(['id'], ['category_vkseller' => '=' . $category_vkseller]);

    // Нет привязки категории
    if (empty($data_categories['id'])) {
        $PHPShopCategoryOrm->update(['category_vkseller_new' => $category_vkseller], ['id' => '=' . $category]);
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

                $img_name = $action. '_' .$_POST['rowID'] . '.' . pathinfo(parse_url($img)['path'])['extension'];

                // Файл загружен
                if (downloadFile($img, $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . '/UserFiles/Image/' . $path . $img_name))
                    $img_load++;
                else
                    continue;

                // Новое имя
                $img = $GLOBALS['dir']['dir'] . '/UserFiles/Image/' . $path . $img_name;

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