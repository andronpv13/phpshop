<?php

PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("date");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("payment");
PHPShopObj::loadClass("delivery");
PHPShopObj::loadClass("user");
PHPShopObj::loadClass("text");

$TitlePage = __('Новый заказ');

/**
 * Генерация номера заказа
 */
function setNum() {
    global $PHPShopBase;

    // Кол-во знаков в постфиксе заказа №_XX, по умолчанию 2
    $format = $PHPShopBase->getParam('my.order_prefix_format');
    if (empty($format))
        $format = 2;

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
    $row = $PHPShopOrm->select(array('uid'), false, array('order' => 'id desc'), array('limit' => 1));
    $last = $row['uid'];
    $all_num = explode("-", $last);
    $ferst_num = $all_num[0];
    $order_num = $ferst_num + 1;
    $order_num = $order_num . "-" . substr(abs(crc32(uniqid(session_id()))), 0, $format);
    return $order_num;
}

/**
 * Экшен загрузки форм редактирования
 */
function actionStart() {
    global $PHPShopOrm, $PHPShopBase, $subpath, $PHPShopModules;

    if (!$PHPShopBase->Rule->CheckedRules($subpath[0], 'create')) {
        return $PHPShopBase->Rule->BadUserFormaWindow();
    }

    // Копия заказа из карточки заказа
    if (!empty($_GET['id'])) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $PHPShopOrm->debug = false;
        $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));
        $data['id'] = null;

        // Очистка корзины
        if (!empty($_GET['cart'])) {
            $order = unserialize($data['orders']);
            unset($order['Person']['discount']);
            unset($order['Cart']);
            $data['orders'] = serialize($order);
            $data['sum'] = 0;
        }
    }

    // Копия заказа из карточки пользователя
    elseif (!empty($_GET['user'])) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
        $PHPShopOrm->debug = false;
        $user_row = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['user'])));
        $data['user'] = $user_row['id'];
        $order['Person']['mail'] = $user_row['mail'];
        $order['Person']['org_name'] = $user_row['company'];
        $order['Person']['adr_name'] = $user_row['adres'];
        $order['Person']['name_person'] = $user_row['name'];
        $order['Person']['tel_code'] = $user_row['tel_code'];
        $order['Person']['tel_name'] = $user_row['tel'];
        $order['Person']['org_inn'] = $user_row['inn'];
        $order['Person']['org_kpp'] = $user_row['kpp'];

        // данные под новую структуру таблицу заказов. Учитывает структуру старых записей.
        $data_adres = unserialize($user_row['data_adres']);
        if (is_array($data_adres) AND is_array($data_adres['list'][$data_adres['main']]))
            foreach ($data_adres['list'][$data_adres['main']] as $key => $value) {
                $key = str_replace("_new", "", $key);
                switch ($key) {
                    case "fio":
                        if (empty($value))
                            $value .= $user_row['name'];
                        else
                            $order['Person']['name_person'] = "";
                        break;
                    case "street":
                        $value .= $user_row['adres'];
                        break;
                    case "org_name":
                        $value .= $user_row['company'];
                        break;
                    case "tel":
                        $value .= $user_row['tel_code'] . $user_row['tel'];
                        break;
                    case "org_inn":
                        $value .= $user_row['inn'];
                        break;
                    case "org_kpp":
                        $value .= $user_row['kpp'];
                        break;

                    default:
                        break;
                }
                $data[$key] = $value;
            }
            
        // Библиотека пользователей для расчета скидки
        $PHPShopUser = new PHPShopUser($user_row['id'], $user_row);
        
        $discount = $PHPShopUser->getDiscount();
        if ($order['Person']['discount'] < $discount)
            $order['Person']['discount'] = $discount;
        
        $data['orders'] = serialize($order);
    }

    // Данные нового заказа
    $data['datas'] = time();
    $data['uid'] = setNum();
    $data['statusi'] = 0;
    $data['seller'] = 0;
    $data['sum_new'] = $order['Cart']['sum'];
    $data['ofd'] = '';
    $data['ofd_status'] = 0;

    // Запись пустого заказа для получения идентификатора заказа
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
    $id = $PHPShopOrm->insert($data, '');

    // Перехват модуля
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $id);

    header('Location: ?path=order&id=' . $id);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>