<?php

$TitlePage = __("Настройки импорта");

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI;

    // SQL
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges_log']);

    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));
    $PHPShopGUI->setActionPanel(__('Настройки импорта от ') . PHPShopDate::get($data['date']), false, array('Закрыть'));
    $PHPShopGUI->field_col = 5;

    $option = unserialize($data['option']);

    // Настройки
    $PHPShopOrmExchanges = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges']);
    $data_exchanges = $PHPShopOrmExchanges->select(array('*'), array('id' => '=' . intval($option['exchanges']) . ' or id=' . intval($option['exchanges_new'])), false, array("limit" => 1));
    if (!is_array($data_exchanges)) {
        $data_exchanges['name'] = '-';
    }

    if (empty($option['export_imgpath']))
        $option['export_imgpath'] = __('Выкл');
    else
        $option['export_imgpath'] = __('Вкл');

    if (empty($option['export_imgproc']))
        $option['export_imgproc'] = __('Выкл');
    else
        $option['export_imgproc'] = __('Вкл');

    if (empty($option['export_imgload']))
        $option['export_imgload'] = __('Выкл');
    else
        $option['export_imgload'] = __('Вкл');

    if (empty($option['export_uniq']))
        $option['export_uniq'] = __('Выкл');
    else
        $option['export_uniq'] = __('Вкл');

    if (empty($option['export_imgsearch']))
        $option['export_imgsearch'] = __('Выкл');
    else
        $option['export_imgsearch'] = __('Вкл');
    
    if (empty($option['export_ai']))
        $option['export_ai'] = __('Выкл');
    else
        $option['export_ai'] = __('Вкл');


    $delim_value = array(';' => __('Точка с запятой'), ',' => __('Запятая'));
    $action_value = array('update' => __('Обновление'), 'insert' => __('Создание'));
    $delim_sortvalue = array('#' => '#', '@' => '@', '$' => '$', '-' => __('Колонка'));
    $delim_sort = array('/' => '/', '\\' => '\\', '-' => '-', '&' => '&', ';' => ';', ',' => ',');
    $delim_imgvalue = array(',' => __('Запятая'), 0 => __('Выкл'), ';' => __('Точка с запятой'), '#' => '#', ' ' => __('Пробел'));
    $code_value = array('ansi' => 'ANSI', 'utf' => 'UTF-8');
    $extension_value = array('csv' => 'CSV', 'xls' => 'XLS', 'xlsx' => 'XLSX', 'yml' => 'YML');

    if (!empty($option['export_key']))
        $key_value = $option['export_key'];
    else
        $key_value = 'Id или Артикул';

    if (empty($data['status'])) {
        $status = "<span class='text-warning'>" . __('Ошибка') . "</span>";
        $text = null;
        $class = 'hide';
    } else {
        $status = __("Выпонен");
        $info = unserialize($data['info']);



        $text = __('Обработано ') . $info[0] . (' строк') . '.<br><a href="' . $info[3] . '" target="_blank">' . $info[1] . ' ' . $info[2] . __(' записей') . '</a>';
    }

    $path_name = [
        'exchange.import.catalog' => __('Каталоги'),
        'exchange.import' => __('Товары'),
        'exchange.import.user' => __('Пользователи'),
        'exchange.import.order' => __('Заказы'),
    ];

    if (!empty($info[3]))
        $path_parts = pathinfo($info[3]);
    $result_file = './csv/' . $path_parts['basename'];
    
    if(stristr($data['file'],'http'))
            $file = $data['file'];
    else $file=pathinfo($data['file'])['basename'];
    

    // Закладка 1
    $Tab1 = $PHPShopGUI->setField("Файл", $PHPShopGUI->setText($PHPShopGUI->setLink('./csv/'.$file, $file))) .
            $PHPShopGUI->setField("Тип данных", $PHPShopGUI->setText($path_name[$option['path']], false, false, false)) .
            $PHPShopGUI->setField("Настройка", $PHPShopGUI->setText('<a href="?path=' . $option['path'] . '&exchanges=' . $data_exchanges['id'] . '">' . $data_exchanges['name'] . '</a>', false, false, false), false, false, $class) .
            $PHPShopGUI->setField("Обработано строк", $PHPShopGUI->setText($info[0]), false, false, $class) .
            $PHPShopGUI->setField($info[1] . ' ' . __('записей'), $PHPShopGUI->setText($PHPShopGUI->setLink('./admin.php?path=catalog&import='.$data['import_id'], $info[2])), false, false, $class, 'control-label', false) .
            $Tab1 .= $PHPShopGUI->setField('Загружено изображений', $PHPShopGUI->setText((int) $info[4]), false, false, $class)
            ;
    
    if(!empty($info[3])){
        $PHPShopGUI->setField('Отчет', $PHPShopGUI->setText('<a href="' . $result_file . '" target="_blank">CSV</a>'), false, false, $class) ;
    }
    
    $Tab1 .=
            $PHPShopGUI->setField('Действие', $PHPShopGUI->setText($action_value[$option['export_action']], false, false, false)) .
            $PHPShopGUI->setField('CSV-разделитель', $PHPShopGUI->setText($delim_value[$option['export_delim']], false, false, false)) .
            $PHPShopGUI->setField('Разделитель для характеристик', $PHPShopGUI->setText($delim_sortvalue[$option['export_sortdelim']], false, false, false)) .
            $PHPShopGUI->setField('Разделитель значений характеристик', $PHPShopGUI->setText($delim_sort[$option['export_sortsdelim']], false, false, false)) .
            $PHPShopGUI->setField('Создание описаний с AI', $PHPShopGUI->setText($option['export_ai'], false, false, false),1,'Создание и обработка описаний с помощью AI. Требуется подписка Yandex Cloud.') .
            $PHPShopGUI->setField('Поиск изображений в Яндекс', $PHPShopGUI->setText($option['export_imgsearch'], false, false, false),1,'Поиск изображений в Яндекс по имени товара. Требуется подписка Yandex Cloud.') .
            $PHPShopGUI->setField('Полный путь для изображений', $PHPShopGUI->setText($option['export_imgpath'], false, false, false), 1, 'Добавляет к изображениям папку /UserFiles/Image/') .
            $PHPShopGUI->setField('Обработка изображений', $PHPShopGUI->setText($option['export_imgproc'], false, false, false), 1, 'Создание тумбнейла и ватермарка') .
            $PHPShopGUI->setField('Загрузка изображений', $PHPShopGUI->setText($option['export_imgload'], false, false, false), 1, 'Загрузка изображений на сервер по ссылке') .
            $PHPShopGUI->setField('Разделитель для изображений', $PHPShopGUI->setText($delim_imgvalue[$option['export_imgdelim']], false, false, false)) .
            
            $PHPShopGUI->setField('Кодировка текста', $PHPShopGUI->setText($code_value[$option['export_code']])) .
            $PHPShopGUI->setField('Тип файла', $PHPShopGUI->setText($extension_value[$option['export_extension']])) .
            $PHPShopGUI->setField('Ключ обновления', $PHPShopGUI->setText($key_value)) .
            $PHPShopGUI->setField('Проверка уникальности', $PHPShopGUI->setText($option['export_uniq'], false, false, false), 1, 'Исключает дублирование данных при создании');

    $Tab1 = $PHPShopGUI->setCollapse('Настройки', $Tab1);

    $name_col = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T'];
    
    foreach ($option['select_action'] as $k => $p) {
        if (empty($p))
            $p = '-';
        $Tab2 .= $PHPShopGUI->setField('Колонка ' . $name_col[$k], $PHPShopGUI->setText($p,false,false,false));
    }

    $Tab2 = $PHPShopGUI->setCollapse('Сопоставление полей', $Tab2);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Информация", $Tab1 . $Tab2, true, false, true));

    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>