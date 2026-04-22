<?php

$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.visualcart.visualcart_memory"));

// Функция удаления
function actionDelete() {
    global $PHPShopOrm;

    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array('success' => $action);
}

function generatePassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $numChars = strlen($chars);
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= substr($chars, rand(1, $numChars) - 1, 1);
    }
    return base64_encode($string);
}

/**
 * Запись нового пользователя в БД
 * @return Int ИД нового пользователя в БД
 */
function user_add($mail,$name,$tel) {
    global $PHPShopSystem;

    $user_mail_activate = 1;
    $subscribe = 1;
    $user_status = $PHPShopSystem->getSerilizeParam('admoption.user_status');

    // Массив данных нового пользователя
    $insert = array(
        'login_new' => PHPShopSecurity::TotalClean($mail, 3),
        'password_new' => generatePassword(),
        'datas_new' => time(),
        'mail_new' => PHPShopSecurity::TotalClean($mail, 3),
        'name_new' => PHPShopSecurity::TotalClean($name),
        'tel_new' => PHPShopSecurity::TotalClean($tel),
        'enabled_new' => $user_mail_activate,
        'status_new' => $user_status,
        'subscribe_new' => $subscribe
    );

    // Запись в БД
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
    $result = $PHPShopOrm->insert($insert);

    // Возвращаем ИД нового пользователя
    return $result;
}

/**
 * Экшен проверки существования пользователя по email. Если существует, возвращает ИД
 */
function user_check_by_email($login,$name,$tel) {
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
    $PHPShopOrm->Option['where'] = " or ";
    if (PHPShopSecurity::true_email($login)) {
        $data = $PHPShopOrm->select(array('id'), array('mail' => '="' . trim($login) . '"', 'login' => '="' . trim($login) . '"'), array('order' => 'id desc'), array('limit' => 1));
        if (is_array($data) AND PHPShopSecurity::true_num($data['id'])) {
            return $data['id'];
        } else {
            return user_add($login,$name,$tel);
        }
    }
    return false;
}

/**
 * Экшен сохранения
 */
function actionUpdate() {
    global $PHPShopOrm;

    // Выборка
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_POST['rowID'])));

    if (empty($data['name']))
        $name = 'Имя не указано';
    else
        $name = PHPShopSecurity::TotalClean($data['name'], 2);

    if (empty($data['tel']))
        $phone = '';
    else
        $phone = PHPShopSecurity::TotalClean($data['tel'], 2);

    $mail = PHPShopSecurity::TotalClean($data['mail'], 2);

    $cart = unserialize($data['cart']);
    $PHPShopCart = new PHPShopCart($cart);

    $order['Cart']['cart'] = $PHPShopCart->getArray();
    $order['Cart']['num'] = $PHPShopCart->getNum();
    $order['Cart']['sum'] = $PHPShopCart->getSum(true);
    $order['Cart']['weight'] = $PHPShopCart->getWeight();
    $order['Cart']['dostavka'] = '';

    $order['Person']['ouid'] = order_num();
    $order['Person']['data'] = time();
    $order['Person']['time'] = '';
    $order['Person']['mail'] = $mail;
    $order['Person']['name_person'] = $name;
    $order['Person']['org_name'] = '';
    $order['Person']['org_inn'] = '';
    $order['Person']['org_kpp'] = '';
    $order['Person']['tel_code'] = '';
    $order['Person']['tel_name'] = '';
    $order['Person']['adr_name'] = '';
    $order['Person']['dostavka_metod'] = '';
    $order['Person']['discount'] = 0;
    $order['Person']['user_id'] = '';
    $order['Person']['dos_ot'] = '';
    $order['Person']['dos_do'] = '';
    $order['Person']['order_metod'] = '';
    $insert['dop_info_new'] = '';

    // данные для записи в БД
    $insert['datas_new'] = time();
    $insert['uid_new'] = order_num();
    $insert['orders_new'] = serialize($order);
    $insert['fio_new'] = $name;
    $insert['tel_new'] = $phone;

    // пользователь
    if (!empty($data['user']))
        $insert['user_new'] = $data['user'];
    elseif (!empty($mail)) {
        $insert['user_new'] = user_check_by_email($mail,$name,$phone);
    }

    $insert['statusi_new'] = 0;
    $insert['status_new'] = serialize(array("maneger" => 'Брошенная корзина'));

    // Запись в базу
    $PHPShopOrmOrder = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
    $action = $PHPShopOrmOrder->insert($insert);
    if (!empty($action))
        $action = true;

    $PHPShopOrm->delete(array('id' => '=' . intval($_POST['rowID'])));
    return array('success' => $action);
}

// номер заказа
function order_num() {
    // Рассчитываем номер заказа
    $PHPShopOrm = new PHPShopOrm();
    $res = $PHPShopOrm->query("select uid from " . $GLOBALS['SysValue']['base']['orders'] . " order by id desc LIMIT 0, 1");
    $row = mysqli_fetch_array($res);
    $last = $row['uid'];
    $all_num = explode("-", $last);
    $ferst_num = $all_num[0];

    if ($ferst_num < 100)
        $ferst_num = 100;
    $order_num = $ferst_num + 1;

    // Номер заказа
    $ouid = $order_num . "-" . substr(abs(crc32(uniqid(session_id()))), 0, 3);
    return $ouid;
}

// Обработка событий
$PHPShopGUI->getAction();
?>
