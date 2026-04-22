<?php
include($_classPath."modules/pozvonim/class/pcurl.php");
include($_classPath."modules/pozvonim/class/pozvonim.php");
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.pozvonim.pozvonim_system"));

// Функция регистрации
function actionRegister()
{
    global $PHPShopOrm;
    $p = new Pozvonim();
    $data = array();
    foreach (array('email', 'phone', 'host', 'code', 'reset', 'token', 'restore') as $field) {
        if (isset($_POST[$field])) {
            $data[$field] = $_POST[$field];
        }
    }
    $oldData = $PHPShopOrm->select();
    if ($oldData['appId'] > 1) {
        echo 'Плагин уже зарегистрирован';
        return false;
    }
    if (empty($data['token']) && $oldData['token']) {
        $data['token'] = $oldData['token'];
    }
    if ($result = $p->update($data)) {
        if ($PHPShopOrm->update($result, false, '')) {
            echo 'ok';
        } else {
            echo 'Ошибка';
        }
    } else {
        echo $p->errorMessage ? $p->errorMessage : 'Ошибка сохранения';
    }
    exit();
    return false;
}

// Функция установки кода
function actionCode()
{
    global $PHPShopOrm;
    $p = new Pozvonim();
    if (!isset($_POST['code'])) {
        echo 'Необходимо указать код виджета';
        return false;
    }
    $code = $_POST['code'];

    $data = $PHPShopOrm->select();
    $data['code'] = $code;

    if ($data = $p->update($data)) {
        if ($PHPShopOrm->update($data, false, '')) {
            echo 'ok';
        } else {
            echo 'Ошибка установки кода';
        }
    } else {
        echo $p->errorMessage;
    }
    exit();
    return false;
}

// Функция установки кода
function actionRestore()
{
    $p = new Pozvonim();
    if (!isset($_POST['email'])) {
        echo 'Необходимо указать email';
        return false;
    }
    if ($p->restoreTokenToEmail($_POST['email'])) {
        echo 'Секретный код отправлен на ' . htmlspecialchars($_POST['email']);
    } else {
        echo $p->errorMessage;
    }
    exit();
    return false;
}

function actionStart()
{
    global $PHPShopGUI, $PHPShopOrm,$PHPShopSystem;
    
    $PHPShopGUI->addJSFiles('../modules/pozvonim/gui/pozvonim.gui.js');

    //Выборка
    $data = $PHPShopOrm->select();
    $isRegistered = $data['appId'] > 0;

    $Tab1 = '<fieldset ' . ($isRegistered ? 'disabled="disabled"' : '') . ' >';
    $Tab1 .= $PHPShopGUI->setField('Email', $PHPShopGUI->setInputText(false, 'email', $data['email'] ? $data['email'] : $PHPShopSystem->getEmail()));
    $Tab1 .= $PHPShopGUI->setField('Телефон', $PHPShopGUI->setInputText(false, 'phone', $data['phone'] ? $data['phone'] : $PHPShopSystem->getParam('tel')));
    $Tab1 .= $PHPShopGUI->setField('Домен', $PHPShopGUI->setInputText(false, 'host', $data['host'] ? $data['host'] : $_SERVER['HTTP_HOST']));
    $Tab1 .= $PHPShopGUI->setField('Секретный код',
        $PHPShopGUI->setInputText(false, 'token', $data['token'] ? $data['token'] : md5(uniqid('', true)))
    );
    if (!$isRegistered) {
        $Tab1 .= $PHPShopGUI->setField(false,$PHPShopGUI->setButton('Зарегистрировать плагин', 'download-alt', 'btn-success pozvonim-register').$PHPShopGUI->setButton('Восстановить секретный код', 'refresh', 'pozvonim-restore'));
    } else {
        $Tab1 .= '<br/>';
        $link = $PHPShopGUI->setLink(
            'http://appspozvonim.com/phpshop/login?id=' . $data['appId'] . '&token=' . md5($data['appId'] . $data['token']),
            'Открыть личный кабинет my.pozvonim.com'
        );
        $Tab1 .= $PHPShopGUI->setField(false,$PHPShopGUI->setInfo('Плагин зарегистрирован и привязан к аккаунту <b>' . $data['email'] . '</b><br/>' .$link, false, '400px'));
    }
    $Tab1 .= '</fieldset>';

    $Tab12 = '<fieldset>' . $PHPShopGUI->setField('Код виджета', $PHPShopGUI->setTextarea('code', $data['code']));;
    if ($data['code']) {
        $Tab12 .= $PHPShopGUI->setField(false,$PHPShopGUI->setInfo('<span class="text-success">Плагин использует указанный код виджета</span>', false, '300px'));
    }
    $Tab12 .= $PHPShopGUI->setField(false,$PHPShopGUI->setButton($data['code'] ? 'Сохранить' : 'Установить', false, 'pozvonim-save'));
    $Tab12 .= '</fieldset>';

    $Tab2 = $PHPShopGUI->setInfo('
           <b>Для работы плагина достаточно зарегистрировать его</b> через форму регистрации во вкладке "регистрация".<br/>
           <br/>
           В случае <b>если вы регистрировали плагин, но потеряли свой секретный код</b> (создается автоматически перед регистрацией),
           вы можете восстановить секретный код заполнив поле email и нажав кнопку "Восстановить секретный код".<br/>
           <br/>
           Если вы уже зарегистрированы в сервисе <a href="http://pozvonim.com/?i=513468710" target="_blank">pozvonim.com</a> и <b>хотите использовать существующий код виджета</b>.
           Вы можете указать код виджета в соотвествующей вкладке.
             <br/> <br/>
           По умолчанию виджет выводится в переменную <kbd>@leftMenu@</kbd>.
           Если в шаблоне нет переменной <kbd>@leftMenu@</kbd> то необходимо добавить переменную <kbd>@pozvonim@</kbd> в активный шаблон.<br/>
           Положение и внешний вид виджета редактируются в личном кабинете pozvonim.com.<br/>
           В личный кабинет можно попасть по ссылке отображаемой после регистрации плагина.<br/>
           <p>Для отключения вывода в переменную <kbd>@leftMenu@</kbd> закомментируйте 46 строку в <mark>phpshop/modules/pozvonim/inc/pozvonim.inc.php</mark> </p>
   ');
    
        // Форма регистрации
    $Tab3=$PHPShopGUI->setPay();

    // Вывод формы закладки
    if (isset($data['code']) && $data['code'] != '') {
        $PHPShopGUI->setTab(array("Код виджета", $Tab12, true), array("Регистрация", $Tab1, true), array("Инструкция", $Tab2),array("О Модуле",$Tab3));
    } else {
        $PHPShopGUI->setTab(array("Регистрация", $Tab1, true), array("Код виджета", $Tab12, true), array("Инструкция", $Tab2),array("О Модуле",$Tab3));
    }

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>