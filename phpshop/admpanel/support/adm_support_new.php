<?php

$TitlePage = __('Новая заявка в техподдержку');

function actionStart() {
    global $PHPShopGUI, $TitlePage, $PHPShopSystem;

    $PHPShopGUI->addJSFiles('./js/jquery.waypoints.min.js', './support/gui/support.gui.js');

    $PHPShopGUI->action_button['Отправить сообщение'] = array(
        'name' => 'Отправить сообщение',
        'locale' => true,
        'action' => 'saveID',
        'class' => 'btn  btn-default btn-sm navbar-btn' . $xs_class . $GLOBALS['isFrame'],
        'type' => 'submit',
        'icon' => 'glyphicon glyphicon-send'
    );

    $PHPShopGUI->setActionPanel($TitlePage, false, array('Отправить сообщение'));
    $PHPShopGUI->field_col = 2;

    $value[] = array('Поддержка', 1, 1);
    $value[] = array('Ошибка в скрипте', 2);
    $value[] = array('Настройка 1С и МойСклад', 3);
    $value[] = array('Программирование PHPShop', 5);
    $value[] = array('Общие вопросы', 6);
    $value[] = array('Платное добавление функционала', 8);
    $value[] = array('Обновление системы', 15);
    $value[] = array('Лицензия не проходит проверку', 18);
    $value[] = array('Платные услуги и консультации', 19);
    $value[] = array('Изменение дизайна', 20);

    $Tab1 .= $PHPShopGUI->setField("E-mail", $PHPShopGUI->setInput('email.required.6', "email", $PHPShopSystem->getEmail(), null, 400));
    $Tab1 .= $PHPShopGUI->setField("Имя", $PHPShopGUI->setInput('text.required.4', "name", null, null, 400));
    $Tab1 .= $PHPShopGUI->setField("Приоритет", $PHPShopGUI->setSelect('priority', array(array('Низкий', 3), array('Средний', 2), array('Высокий', 1)), 400, true));
    $Tab1 .= $PHPShopGUI->setField("Категория", $PHPShopGUI->setSelect('category', $value, 400, true));
    $Tab1 .= $PHPShopGUI->setField("Тема", $PHPShopGUI->setInput('text.required.10', "subject", null));
    $Tab1 .= $PHPShopGUI->setField('Сообщение', $PHPShopGUI->setTextarea('message.required.10', null, true, false, 300, false, __('Пожалуйста, опишите Вашу проблему. Для ускорения решения вопроса, сразу предоставьте пароли доступа от Админпанели (логин, пароль) сайта и FTP/SFTP (имя сервера, логин, пароль)')));
    $Tab1 .= $PHPShopGUI->setField('Файл', $PHPShopGUI->setIcon(null, "attachment", false, array('load' => true, 'server' => true, 'url' => false, 'multi' => false, 'view' => false)));


    $PHPShopGUI->_CODE = $PHPShopGUI->setCollapse('Форма заявки', $Tab1, 'in', false);

    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "ОК", "right", 70, "", "but", "actionInsert.system.edit");

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);

    return true;
}

// Функция обновления
function actionInsert() {

    $licFile = PHPShopFile::searchFile('../../license/', 'getLicense', true);
    @$License = parse_ini_file_true("../../license/" . $licFile, 1);

    $path = 'https://help.phpshop.ru/base-xml-manager/search/xml.php?s=' . $License['License']['Serial'] . '&u=' . $License['License']['DomenLocked'] . '&do=create&code='.$GLOBALS['PHPShopBase']->codBase;
    $ch = curl_init();
    
    if (!empty($_POST['attachment'])) {

        $fileAdd = fileAdd();
        if (!empty($fileAdd))
            $_POST['attachment'] = $fileAdd;

        $pathinfo = pathinfo($_POST['attachment']);
        $_POST['message'] .= '

<a href="http://' . $_SERVER['SERVER_NAME'] . $_POST['attachment'] . '" target="_blank"><span class="glyphicon glyphicon-paperclip"></span> ' . $pathinfo['basename'] . '</a>';
    }
    
    $_POST['subject']= str_replace('"','',$_POST['subject']);
    $_POST['name']= str_replace('"','',$_POST['name']);


    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
    curl_setopt($ch, CURLOPT_TIMEOUT, 600);
    curl_setopt($ch, CURLOPT_URL, $path);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_exec($ch);
    curl_close($ch);
    header('Location: ?path=' . $_GET['path']);
}

// Добавление файла
function fileAdd() {
    global $PHPShopSystem;

    // Папка сохранения
    $path = $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $PHPShopSystem->getSerilizeParam('admoption.image_result_path');

    // Копируем от пользователя
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        $_FILES['file']['name'] = PHPShopString::toLatin(str_replace('.' . $_FILES['file']['ext'], '', $_FILES['file']['name'])) . '.' . $_FILES['file']['ext'];
       if (!empty($_FILES['file']['ext'])) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'])) {
                $file = $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'];
            }
        }
        else $file='Error_PHP_ext';
    }

    if (empty($file))
        $file = '';

    return $file;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>