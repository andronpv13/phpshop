<?php
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("category");

include_once dirname(__DIR__) . '/class/ThumbnailImages.php';

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.thumbnailimages.thumbnailimages_system"));
$PHPShopOrm->debug = false;

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm;
    
    if(empty($_POST['stop_new']))
        $_POST['stop_new']=0;

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionGenerateOriginal() {
    global $PHPShopOrm;

    $data = $PHPShopOrm->select();
    $thumbnailImages = new ThumbnailImages();
    $result = $thumbnailImages->generateOriginal();

    if ((int) $result['count'] < (int) $data['limit']) {
        $message = '<div class="alert alert-success" id="rules-message"  role="alert">' .
                __(sprintf('Обработано изображений: с %s до %s. Все доступные изображения обработаны. Следующее нажатие кнопки запустит операцию с 0.', (int) $data['processed'], (int) $data['processed'] + (int) $result['count']))
                . '</div>';
    }

    if ('original' !== $data['last_operation']) {
        $data['processed'] = 0;
    }

    if (!isset($message)) {
        $message = '<div class="alert alert-success" id="rules-message"  role="alert">' .
                __(sprintf('Выполнено. Обработано изображений: с %s до %s', (int) $data['processed'], (int) $data['processed'] + (int) $result['count']))
                . '</div>';
    }

    echo $message;

    if (count($result['skipped']) > 0) {
        $skipped = '';
        foreach ($result['skipped'] as $file) {
            $skipped .= 'Не найден файл: ' . $file . '<br>';
        }
        echo '<div class="alert alert-warning" id="rules-message"  role="alert">' .
        $skipped
        . '</div>';
    }
}

function actionGenerateThumbnail() {
    global $PHPShopOrm;

    $data = $PHPShopOrm->select();
    $thumbnailImages = new ThumbnailImages();
    $result = $thumbnailImages->generateThumbnail();

    if ((int) $result['count'] < (int) $data['limit']) {
        $message = '<div class="alert alert-success" id="rules-message"  role="alert">' .
                __(sprintf('Обработано изображений: с %s до %s. Все доступные изображения обработаны. Следующее нажатие кнопки запустит операцию с 0.', (int) $data['processed'], (int) $data['processed'] + (int) $result['count']))
                . '</div>';
    }

    if ('thumb' !== $data['last_operation']) {
        $data['processed'] = 0;
    }

    if (!isset($message)) {
        $message = '<div class="alert alert-success" id="rules-message"  role="alert">' .
                __(sprintf('Выполнено. Обработано изображений: с %s до %s', (int) $data['processed'], (int) $data['processed'] + (int) $result['count']))
                . '</div>';
    }

    echo $message;

    if (count($result['skipped']) > 0) {
        $skipped = '';
        foreach ($result['skipped'] as $file) {
            $skipped .= 'Не найден файл: ' . $file . '<br>';
        }
        echo '<div class="alert alert-warning" id="rules-message"  role="alert">' .
        $skipped
        . '</div>';
    }
}

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;

    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(['version_new' => $new_version]);
}

// Начальная функция загрузки
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $TitlePage, $select_name;

    // Выборка
    $data = $PHPShopOrm->select();
    $PHPShopGUI->field_col = 4;
    
    $PHPShopGUI->action_button['Сгенерировать превью'] = [
        'name' => __('Сгенерировать превью'),
        'action' => 'saveIDthumb',
        'class' => 'btn  btn-default btn-sm navbar-btn',
        'type' => 'submit',
        'icon' => 'glyphicon glyphicon-import'
    ];

    $PHPShopGUI->action_button['Сгенерировать большие'] = [
        'name' => __('Сгенерировать большие'),
        'action' => 'saveIDorig',
        'class' => 'btn  btn-default btn-sm navbar-btn',
        'type' => 'submit',
        'icon' => 'glyphicon glyphicon-import'
    ];

    $PHPShopGUI->setActionPanel($TitlePage, $select_name, ['Сгенерировать превью', 'Сгенерировать большие', 'Сохранить и закрыть']);

    $Tab1 = '<div class="alert alert-info" role="alert">' .
            __('Пожалуйста, ознакомьтесь с информацией на вкладке <kbd>Описание</kbd> перед использованием модуля.')
            . '</div>';

    $Tab1 .= $PHPShopGUI->setField('Генерировать изображений за шаг', $PHPShopGUI->setInputText(false, 'limit_new', $data['limit'], 150));

    $e_value[] = array('Оригинальный', 1, $data['type']);
    $e_value[] = array('JPG', 2, $data['type']);
    $e_value[] = array('WEBP', 3, $data['type']);
    

    $Tab1 .= $PHPShopGUI->setField('Формат изображений для сохранения', $PHPShopGUI->setSelect('type_new', $e_value, 150, true));
    
    $d_value[] = array('Нет', 1, $data['delete']);
    $d_value[] = array('Да', 2, $data['delete']);
    

    $Tab1 .= $PHPShopGUI->setField('Удалить старые изображения при смене формата', $PHPShopGUI->setSelect('delete_new', $d_value, 150, true));
    
    $Tab1 .= $PHPShopGUI->setField('Блокировать случайный запуск', $PHPShopGUI->setCheckbox('stop_new', 1, '', $data['stop']));

    $Info = '<p>
        Модуль позволяет сгенерировать новые картинки по указанным в <kbd>Настройки</kbd> &rarr; <kbd>Изображения</kbd> параметрам.<br>
        Превью для товаров в каталоге генерируются по такому сценарию:
        <ul>
            <li>Проверяется настройка <kbd>Сохранять исходное изображение при ресайзинге</kbd></li>
            <li>Если настройка включена - проверяется наличие файла картинки с суффиксом <code>_big</code>, это сохраненная картинка в оригинальном размере, для создания превью используется она.</li>
            <li>Если настройка отключена или изображения с суффиксом <code>_big</code> нет - для генерации превью изображения используется большая картинка товара, обрезанная согласно настройкам 
                <kbd>Макс. ширина оригинала</kbd> и <kbd>Макс. высота оригинала</kbd>.
            </li>
            <li>Все изображения товаров с суффиксом <code>_s</code> будут заменены новыми сгенерированными изображениями.</li>
            <li>Для перехода на webp всех изображений следует запустить обе генерации маленьких и больших картинок.</li>
            <li>Для автоматизации процесса следует добавить новую задачу в <kbd>Cron</kbd> с адресом запускаемого файла <code>phpshop/modules/thumbnailimages/cron/images.php thumb</code> для генерации превью и <code>phpshop/modules/thumbnailimages/cron/images.php orig</code> для генерации больших картинок.</li>
        </ul>
       </p>
       <p>
       Генерация больших изображений возможна только, если включена настройка <kbd>Сохранять исходное изображение при ресайзинге</kbd> или уменьшены размеры 
       <kbd>Макс. ширина оригинала</kbd> и <kbd>Макс. высота оригинала</kbd> и необходимо сгенерировать меньшие изображения.
        </p>
';

    $Tab2 = $PHPShopGUI->setInfo($Info);


    // Содержание закладки 2
    $Tab3 = $PHPShopGUI->setPay(false, true, $data['version'], true);

    // Вывод формы закладки
    $PHPShopGUI->setTab(["Основное", $Tab1, true], ["Описание", $Tab2], ["О Модуле", $Tab3]);

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit") .
            $PHPShopGUI->setInput("submit", "saveIDthumb", "Применить", "right", 80, "", "but", "actionGenerateThumbnail.modules.edit") .
            $PHPShopGUI->setInput("submit", "saveIDorig", "Применить", "right", 80, "", "but", "actionGenerateOriginal.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>