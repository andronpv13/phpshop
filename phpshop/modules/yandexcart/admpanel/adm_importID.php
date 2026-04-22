<?php

include_once dirname(__FILE__) . '/../class/YandexMarket.php';
$TitlePage = __('Товар из Яндекс.Маркета');
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

$YandexMarket = new YandexMarket();

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $YandexMarket, $PHPShopModules;

    $PHPShopGUI->field_col = 4;


    $product_info = $YandexMarket->getProductList('ALL', $_GET['id'])['result']['offerMappings'][0]['offer'];

    if (!empty($product_info['name']))
        $PHPShopGUI->action_button['Загрузить товар'] = array(
            'name' => 'Загрузить товар',
            'locale' => true,
            'action' => 'saveID',
            'class' => 'btn  btn-default btn-sm navbar-btn' . $GLOBALS['isFrame'],
            'type' => 'submit',
            'icon' => 'glyphicon glyphicon-save'
        );


    $PHPShopGUI->setActionPanel(__('Товар') . ': ' . PHPShopString::utf8_win1251($product_info['name']), false, array('Загрузить товар'));

    // Знак рубля
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();


    $PHPShopCategoryArray = new PHPShopCategoryArray(false, ["id", "name", "parent_to"]);
    $GLOBALS['CategoryArray'] = $CategoryArray = $PHPShopCategoryArray->getArray();
    $GLOBALS['count'] = count($CategoryArray);

    $CategoryArray[0]['name'] = '- ' . __('Выбрать каталог') . ' -';
    $tree_array = array();

    foreach ($PHPShopCategoryArray->getKey('parent_to.id', true) as $k => $v) {
        foreach ($v as $cat) {
            $tree_array[$k]['sub'][$cat] = $CategoryArray[$cat]['name'];
        }
        $tree_array[$k]['name'] = $CategoryArray[$k]['name'];
        $tree_array[$k]['id'] = $k;
    }

    $GLOBALS['tree_array'] = &$tree_array;


    if (is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator($tree_array[$k], 1, $_GET['type_id']);

            if (empty($tree_array[$k]))
                $disabled = null;
            else
                $disabled = ' disabled';

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

    if (is_array($product_info['pictures']) and count($product_info['pictures']) > 0) {
        $icon = null;
        foreach ($product_info['pictures'] as $img)
            $icon .= $PHPShopGUI->setIcon($img, "images[]", true, array('load' => false, 'server' => true, 'url' => true, 'view' => true));
    } else
        $icon = $PHPShopGUI->setIcon('./images/no_photo.gif', "pic", true, array('load' => false, 'server' => true, 'url' => true, 'view' => true));


    $Tab1 = $PHPShopGUI->setField("Название", $PHPShopGUI->setTextarea('name_new', PHPShopString::utf8_win1251($product_info['name'])));
    $Tab1 .= $PHPShopGUI->setField("SKU", $PHPShopGUI->setInputText(null, 'uid_new', PHPShopString::utf8_win1251($product_info['offerId'])));
    $Tab1 .= $PHPShopGUI->setField("Изображение", $icon);
    $Tab1 .= $PHPShopGUI->setField("Категория в Яндекс", $PHPShopGUI->setText(PHPShopString::utf8_win1251($product_info['category']), "left", false, false));
    $Tab1 .= $PHPShopGUI->setField("Производитель", $PHPShopGUI->setInputText(null, 'vendor_name_new', PHPShopString::utf8_win1251($product_info['vendor'])));
    $Tab1 .= $PHPShopGUI->setField("Штрихкод", $PHPShopGUI->setInputText(null, 'barcode_new', PHPShopString::utf8_win1251($product_info['barcodes'][0])));
    $Tab1 .= $PHPShopGUI->setField("Страна производства", $PHPShopGUI->setInputText(null, 'country_of_origin_new', PHPShopString::utf8_win1251($product_info['manufacturerCountries'][0])));

    // Выбор каталога
    $Tab1 .= $PHPShopGUI->setField("Размещение", $tree_select);

    // Опции вывода
    $Tab1 .= $PHPShopGUI->setField('Опции вывода', $PHPShopGUI->setCheckbox('enabled_new', 1, 'Вывод в каталоге', 1) . '<br>' .
            $PHPShopGUI->setCheckbox('spec_new', 1, 'Спецпредложение', 0) . '<br>' .
            $PHPShopGUI->setCheckbox('newtip_new', 1, 'Новинка', 0));
    $Tab1 .= $PHPShopGUI->setField('Сортировка', $PHPShopGUI->setInputText('&#8470;', 'num_new', 0, 150));

    $Tab1 .= $PHPShopGUI->setField("Цена", $PHPShopGUI->setInputText(null, 'price_new', (float) $product_info['basicPrice']['value'], 150));
    $Tab1 .= $PHPShopGUI->setField("Старая цена", $PHPShopGUI->setInputText(null, 'price_n_new', (float) $product_info['basicPrice']['discountBase'], 150));

    $baseinputvaluta = $PHPShopSystem->getDefaultOrderValutaId();

    // Валюты
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    $valuta_area = null;

    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            $valuta_area .= $PHPShopGUI->setRadio('baseinputvaluta_new', $val['id'], $val['name'], $baseinputvaluta, false);
        }

    // Валюта
    $Tab1 .= $PHPShopGUI->setField('Валюта', $valuta_area);

    $Tab_size = $PHPShopGUI->setField("Вес", $PHPShopGUI->setInputText(null, 'weight_new', $product_info['weightDimensions']['weight'], 100, __('г&nbsp;&nbsp;&nbsp;&nbsp;')));
    $Tab_size .= $PHPShopGUI->setField("Высота", $PHPShopGUI->setInputText(null, 'height_new', $product_info['weightDimensions']['height'] / 10, 100, __('cм')));
    $Tab_size .= $PHPShopGUI->setField("Ширина", $PHPShopGUI->setInputText(null, 'width_new', $product_info['weightDimensions']['width'] / 10, 100, __('cм')));
    $Tab_size .= $PHPShopGUI->setField("Длина", $PHPShopGUI->setInputText(null, 'length_new', $product_info['weightDimensions']['depth'] / 10, 100, __('cм')));

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


/**
 * Экшен загрузки товара
 */
function actionSave() {
    global $PHPShopSystem, $YandexMarket;

    // Права пользователя
    $_POST['user_new'] = $_SESSION['idPHPSHOP'];

    // Дата измененения
    $_POST['datas_new'] = time();
    $_POST['yml_new'] = 1;

    // Корректировка пустых значений
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $PHPShopOrm->updateZeroVars('newtip_new', 'enabled_new', 'spec_new', 'yml_new');
    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->insert($_POST);
    
    $YandexMarket->addProductImage($_POST['images'],$action);

    header('Location: ?path=product&return=catalog&id=' . $action);
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>