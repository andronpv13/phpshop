<?php

$TitlePage = __('Редактирование уведомления').' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['notice']);

function actionSave() {
    global $PHPShopGUI;

    // Сохранение данных
    actionUpdate();

    //header('Location: ?path=' . $_GET['path']);
}

// Сообщение пользователю
function sendMailNotice($productID, $saveID, $email) {
    global $PHPShopSystem;

    if (PHPShopSecurity::true_email($email)) {

        $title = $PHPShopSystem->getName() . " - ".__('уведомление о товаре, заявка')." №" . $saveID;
        PHPShopParser::set('title', $title);

        PHPShopObj::loadClass(array('array', 'valuta', 'product'));
        $PHPShopProduct = new PHPShopProduct($productID);

        $PHPShopMail = new PHPShopMail($email, $PHPShopSystem->getEmail(), $title, '', true, true);

        $text = "<p>".__('Поступило уведомление')." №" . $saveID . " ".__('с Интернет-магазина')." '" . $PHPShopSystem->getName() . "' ".__('для пользователя')." " . $email . "</p>
<p>
".__('Артикул').": " . $PHPShopProduct->getParam('uid') . "<br>
".__('Cтоимость').": " . $PHPShopProduct->getPrice() . " " . $PHPShopSystem->getDefaultValutaCode() . "<br>
".__('Дата изменения информации по товару').": " . PHPShopDate::get($PHPShopProduct->getParam('datas')) . "<br>
".__('Ссылка').": http://" . $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir'] . "/shop/UID_" . $productID . ".html</p>";
        PHPShopParser::set('content', $text);
        PHPShopParser::set('logo', $_SERVER['SERVER_NAME'] . "/" . $GLOBALS['SysValue']['dir']['dir'] . $PHPShopSystem->getParam('logo'));
        $content = PHPShopParser::file('tpl/sendmail.mail.tpl', true, false);

        if ($PHPShopMail->sendMailNow($content))
            return true;
    }
}

// Функция обновления
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    if (!empty($_POST['saveID'])) {
        if (sendMailNotice($_POST['productID'], $_POST['rowID'], $_POST['email'])) {
            $_POST['enabled_new'] = 1;
        }
    }
    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));

    return array("success" => $action);
}

// Функция автоматического уведомления
function actionUpdateAuto() {
    global $PHPShopOrm, $PHPShopModules;

    $updateIds = array();

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    // Таблица с данными
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['notice']);
    $PHPShopOrm->debug = false;
    $PHPShopOrm->sql = 'SELECT a.*, b.name, b.pic_small, c.login FROM ' . $GLOBALS['SysValue']['base']['notice'] . ' AS a 
        JOIN ' . $GLOBALS['SysValue']['base']['products'] . ' AS b ON a.product_id = b.id 
        JOIN ' . $GLOBALS['SysValue']['base']['shopusers'] . ' AS c ON a.user_id = c.id     
            limit 1000';

    $data = $PHPShopOrm->select();
    if (is_array($data))
        foreach ($data as $row) {

            if (empty($row['enabled']))
                if (sendMailNotice($row['product_id'], $row['id'], $row['login'])) {

                    $updateIds[] = $row['id'];
                }
        }

    $_POST['enabled_new'] = 1;

    if (is_array($updateIds)){
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['notice']);
        $action = $PHPShopOrm->update($_POST, array('id' => ' IN (' . implode(',', $updateIds).')'));
    }

    return array("success" => $action);
}

// Функция удаления
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);


    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return $action;
}

// Обработка событий
$PHPShopGUI->getAction();
?>