<?php

include_once dirname(__DIR__) . '/class/Avito.php';

PHPShopObj::loadClass("delivery");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("order");


// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.avito.avito_system"));
$Avito = new Avito();

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate(number_format($option['version'], 1, '.', false));
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

// Обновление цен
function actionUpdatePrice() {

    // Безопасность
    $cron_secure = md5($GLOBALS['SysValue']['connect']['host'] . $GLOBALS['SysValue']['connect']['dbase'] . $GLOBALS['SysValue']['connect']['user_db'] . $GLOBALS['SysValue']['connect']['pass_db']);

    $protocol = 'http://';
    if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])) {
        $protocol = 'https://';
    }

    $true_path = $protocol . $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir'] . "/phpshop/modules/avito/cron/products.php?s=" . $cron_secure;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $true_path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_exec($ch);
    curl_close($ch);
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopModules, $Avito, $TitlePage, $select_name;

    $PHPShopGUI->field_col = 4;

    $data = $PHPShopOrm->select();
    if ($data['token'] !== '' and $data['client_id'] !== '') {


        switch ($data['export']) {
            case 0:
                $export_name = __('Выгрузить цены и склад');
                break;
            case 1:
                $export_name = __('Выгрузить цены');
                break;
            case 2:
                $export_name = __('Выгрузить склад');
                break;
        }


        $PHPShopGUI->action_button['Выгрузить цены'] = [
            'name' => $export_name,
            'class' => 'btn btn-default btn-sm navbar-btn ',
            'type' => 'submit',
            'action' => 'exportID',
            'icon' => 'glyphicon glyphicon-export'
        ];

        $PHPShopGUI->setActionPanel($TitlePage, $select_name, ['Выгрузить цены', 'Сохранить и закрыть']);
    }

    $Tab1 .= $PHPShopGUI->setField($PHPShopGUI->setLink($Avito->ssl . $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/modules/avito/xml/products.php', 'Пароль защиты XML','_blank', false, false, false, false, false), $PHPShopGUI->setInputText('', 'password_new', $data['password']));
    $Tab1 .= $PHPShopGUI->setField('Сервер изображений', $PHPShopGUI->setInputText('https://', 'image_url_new', $data['image_url']));
    $Tab1 .= $PHPShopGUI->setField('Карта проезда', $PHPShopGUI->setTextarea('map_url_new', $data['map_url']), 1, 'URL изображний через запятую');

    $Tab1 .= $PHPShopGUI->setField('Ключ обновления', $PHPShopGUI->setRadio("type_new", 1, "ID товара", $data['type']) . $PHPShopGUI->setRadio("type_new", 2, "Артикул товара", $data['type']));
    $Tab1 .= $PHPShopGUI->setField('ФИО менеджера', $PHPShopGUI->setInputText(false, 'manager_new', $data['manager']));
    $Tab1 .= $PHPShopGUI->setField('Телефон менеджера', $PHPShopGUI->setInputText(false, 'phone_new', $data['phone']));
    $Tab1 .= $PHPShopGUI->setField('Адрес', $PHPShopGUI->setInputText(false, 'address_new', $data['address']));
    $Tab1 .= $PHPShopGUI->setField('Широта местоположения', $PHPShopGUI->setInputText(false, 'latitude_new', $data['latitude']));
    $Tab1 .= $PHPShopGUI->setField('Долгота местоположения', $PHPShopGUI->setInputText(false, 'longitude_new', $data['longitude']));
    $Tab1 .= $PHPShopGUI->setField('Удаление слов из описания', $PHPShopGUI->setTextarea('stop_words_new', $data['stop_words']), 1, 'Слова для удаления через запятую');
    $Tab1 .= $PHPShopGUI->setField('Шаблон генерации описания', '<div id="avitotitleShablon">
<textarea class="form-control avito-shablon" name="preview_description_template_new" rows="3" style="max-width: 600px;height: 70px;">' . $data['preview_description_template'] . '</textarea>
    <div class="btn-group" role="group" aria-label="...">
    <input  type="button" value="' . __('Описание') . '" onclick="AvitoShablonAdd(\'@Content@\')" class="btn btn-default btn-sm">
    <input  type="button" value="' . __('Краткое описание') . '" onclick="AvitoShablonAdd(\'@Description@\')" class="btn btn-default btn-sm">
    <input  type="button" value="' . __('Характеристики') . '" onclick="AvitoShablonAdd(\'@Attributes@\')" class="btn btn-default btn-sm">
<input  type="button" value="' . __('Каталог') . '" onclick="AvitoShablonAdd(\'@Catalog@\')" class="btn btn-default btn-sm">
<input  type="button" value="' . __('Подкаталог') . '" onclick="AvitoShablonAdd(\'@Subcatalog@\')" class="btn btn-default btn-sm">
<input  type="button" value="' . __('Товар') . '" onclick="AvitoShablonAdd(\'@Product@\',)" class="btn btn-default btn-sm">
<input  type="button" value="' . __('Артикул') . '" onclick="AvitoShablonAdd(\'@Article@\',)" class="btn btn-default btn-sm">
    </div>
</div>
<script>function AvitoShablonAdd(variable) {
    var shablon = $(".avito-shablon").val() + " " + variable;
    $(".avito-shablon").val(shablon);
}</script>');
    
     $Tab1 .= $PHPShopGUI->setField('Наличие', $PHPShopGUI->setCheckbox('export_items_enabled_new', 1, 'Показывать в фиде только товары в наличии', $data['export_items_enabled']));
     
     $Tab1 .= $PHPShopGUI->setField('Лимит остатков', $PHPShopGUI->setInputText(false, 'export_items_limit_new', $data['export_items_limit'],50), 1, 'Не выгуржать более лимита товаров на складе');

    $export_value[] = ['Цены и склад', 0, $data['export']];
    $export_value[] = ['Цены', 1, $data['export']];
    $export_value[] = ['Склад', 2, $data['export']];

    // Доставка
    $PHPShopDeliveryArray = new PHPShopDeliveryArray(array('is_folder' => "!='1'", 'enabled' => "='1'"));
    $DeliveryArray = $PHPShopDeliveryArray->getArray();
    if (is_array($DeliveryArray)) {
        foreach ($DeliveryArray as $delivery) {
            if (strpos($delivery['city'], '.')) {
                $name = explode(".", $delivery['city']);
                $delivery['city'] = $name[0];
            }
            $delivery_value[] = array($delivery['city'], $delivery['id'], $data['delivery_id']);
        }
    }

    $Tab1 = $PHPShopGUI->setCollapse('Информация', $Tab1);

    $Tab_api .= $PHPShopGUI->setField('Client ID', $PHPShopGUI->setInputText(false, 'client_id_new', $data['client_id']));
    $Tab_api .= $PHPShopGUI->setField('Client Secret', $PHPShopGUI->setInputText(false, 'сlient_secret_new', $data['сlient_secret']));

    $Tab_api .= $PHPShopGUI->setField('Обновление данных', $PHPShopGUI->setSelect('export_new', $export_value, '100%', true));
    $Tab_api .= $PHPShopGUI->setField('Журнал операций', $PHPShopGUI->setCheckbox('log_new', 1, null, $data['log']));

    $PHPShopOrmCat = new PHPShopOrm($PHPShopModules->getParam("base.avito.avitoapi_categories"));
    $category = $PHPShopOrmCat->select(['COUNT(`id`) as num']);

    $Tab_api .= $PHPShopGUI->setField('База категорий', $PHPShopGUI->setText(($category['num']) . ' ' . __('записей в локальной базе'), null, false, false) . '<br>' . $PHPShopGUI->setCheckbox('load', 1, 'Обновить базу категорий', 0));


    $Tab_api .= $PHPShopGUI->setField('Ссылка на товар', $PHPShopGUI->setCheckbox('link_new', 1, 'Показать ссылку на товар в Avito', $data['link']));
    $Tab_api .= $PHPShopGUI->setField('Создавать товар', $PHPShopGUI->setCheckbox('create_products_new', 1, 'Создавать автоматически товар из заказа', $data['create_products']));

    // Доступые статусы заказов
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('Новый заказ'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);


    $Tab_api .= $PHPShopGUI->setField('Статус нового заказа', $PHPShopGUI->setSelect('status_new', $order_status_value, '100%'));
    $Tab_api .= $PHPShopGUI->setField('Подтверждение заказа', $PHPShopGUI->setCheckbox('transition_new', 1, 'Подтверждение заказа при загрузке', $data['transition']));
    $Tab_api .= $PHPShopGUI->setField('Доставка для заказов', $PHPShopGUI->setSelect('delivery_id_new', $delivery_value, '100%'));

    // Статусы автоматической загрузки
    $order_status_import_value[] = array(__('Ничего не выбрано'), 0, $data['status_import']);
    foreach ($Avito->status_list as $k => $status_val) {
        $order_status_import_value[] = array(__($status_val), $k, $data['status_import']);
    }
    $Tab_api .= $PHPShopGUI->setField('Статус заказа в Avito для автоматической загрузки', $PHPShopGUI->setSelect('status_import_new', $order_status_import_value, '100%'));


    if ($data['fee_type'] == 1) {
        $status_pre = '-';
    } else {
        $status_pre = '+';
    }

    $Tab1 .= $PHPShopGUI->setCollapse('Цены', $PHPShopGUI->setField('Колонка цен Avito', $PHPShopGUI->setSelect('price_new', $PHPShopGUI->setSelectValue($data['price'], 5), 100)) .
            $PHPShopGUI->setField('Наценка', $PHPShopGUI->setInputText($status_pre, 'fee_new', $data['fee'], 100, '%')) .
            $PHPShopGUI->setField('Действие', $PHPShopGUI->setRadio("fee_type_new", 1, "Понижение", $data['fee_type']) . $PHPShopGUI->setRadio("fee_type_new", 2, "Повышение", $data['fee_type']))
    );

    $Tab1 .= $PHPShopGUI->setCollapse('Настройка API', $Tab_api);

    // Инструкция
    $Tab2 = $PHPShopGUI->loadLib('tab_info', $data, '../modules/' . $_GET['id'] . '/admpanel/');

    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], true);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1, true, false, true), array("Инструкция", $Tab2), array("О Модуле", $Tab3));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "exportID", "Применить", "right", 80, "", "but", "actionUpdatePrice.modules.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Синхронизация категорий
function actionUpdateCategory() {
    global $PHPShopModules, $Avito;

    $getTree = $Avito->getTree();
    $tree_array = $getTree['categories'];

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.avito.avitoapi_categories"));
    $PHPShopOrm->debug = false;

    if (is_array($tree_array)) {

        // Очистка
        $PHPShopOrm->query('TRUNCATE TABLE `' . $PHPShopModules->getParam("base.avito.avitoapi_categories") . '`');

        foreach ($tree_array as $category) {
            $PHPShopOrm->insert(['name_new' => PHPShopString::utf8_win1251($category['name']), 'id_new' => $category['slug'], 'parent_to_new' => 0]);

            if (is_array($category['nested'])) {
                foreach ($category['nested'] as $children) {

                    $PHPShopOrm->insert(['name_new' => PHPShopString::utf8_win1251($children['name']), 'id_new' => $children['slug'], 'parent_to_new' => $category['slug']]);
                    if (is_array($children['nested']))
                        setChildrenCategory($children['nested'], $children['slug']);
                }
            }
        }
    }
}

function setChildrenCategory($tree_array, $parent_to) {
    global $PHPShopModules;

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.avito.avitoapi_categories"));

    if (is_array($tree_array)) {
        foreach ($tree_array as $category) {

            $PHPShopOrm->insert(['name_new' => PHPShopString::utf8_win1251($category['name']), 'id_new' => $category['slug'], 'parent_to_new' => $parent_to]);

            if (is_array($category['nested'])) {
                foreach ($category['nested'] as $children) {

                    if (!empty($children['name']))
                        $PHPShopOrm->insert(['name_new' => PHPShopString::utf8_win1251($children['name']), 'id_new' => $children['slug'], 'parent_to_new' => $category['slug']]);

                    if (is_array($children['nested']))
                        setChildrenCategory($children['nested'], $children['slug']);
                }
            }
        }
    }
}

/**
 * Подбор категорий
 */
function actionCategorySearch() {
    global $PHPShopModules;
    $PHPShopOrmCat = new PHPShopOrm($PHPShopModules->getParam("base.avito.avitoapi_categories"));
    $data = $PHPShopOrmCat->getList(['*'], ['name' => " LIKE '%" . $_POST['words'] . "%'", 'parent_to' => '!="0"']);
    if (is_array($data)) {
        foreach ($data as $row) {

            $parent = $PHPShopOrmCat->getOne(['name'], ['id' => '="' . $row['parent_to'] . '"'])['name'];

            $child = $PHPShopOrmCat->getOne(['name'], ['parent_to' => '="' . $row['id'] . '"'])['name'];
            if ($child)
                continue;

            $result .= '<a href=\'#\' class=\'select-search-avito\'  data-id=\'' . $row['id'] . '\'  data-name=\'' . $parent . ' - ' . $row['name'] . '\'    >' . $parent . ' &rarr; ' . $row['name'] . '</a><br>';
        }
        if (!empty($result))
            $result .= '<button type="button" class="close pull-right" aria-label="Close"><span aria-hidden="true">&times;</span></button>';

        exit($result);
    } else
        exit();
}

// Функция обновления
function actionUpdate() {
    global $PHPShopModules, $PHPShopOrm;

    // Синхронизация категорий
    if (!empty($_POST['load']))
        actionUpdateCategory();

    // Настройки витрины
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    // Корректировка пустых значений
    $PHPShopOrm->updateZeroVars('use_params_new', 'create_products_new', 'log_new', 'create_products_new', 'link_new', 'transition_new', 'log_new','export_items_enabled_new');

    $PHPShopOrm->debug = false;
    $_POST['region_data_new'] = 1;

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>