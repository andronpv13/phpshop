<?php

$TitlePage = __("Обновить заказы");
PHPShopObj::loadClass('valuta');
PHPShopObj::loadClass('category');
PHPShopObj::loadClass('order');

$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);

/**
 * Настройка полей - 2 шаг
 */
function actionOptionSave() {

    // Память выбранных полей
    if (is_array($_POST['option'])) {

        $memory = json_decode($_COOKIE['check_memory'], true);
        unset($memory['order.option']);
        foreach ($_POST['option'] as $k => $v) {
            $memory['order.option'][$k] = $v;
        }
        if (is_array($memory))
            setcookie("check_memory", json_encode($memory), time() + 3600000 * 6, $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/admpanel/');
    }

    return array('success' => true);
}

/**
 * Настройка полей - 1 шаг
 */
function actionOption() {
    global $PHPShopInterface;

    // Память выбранных полей
    if (!empty($_COOKIE['check_memory'])) {
        $memory = json_decode($_COOKIE['check_memory'], true);
    }
    
    if (!is_array($memory['order.option'])) {

        // Мобильная версия
        if (PHPShopString::is_mobile()) {
            $memory['order.option']['uid'] = 1;
            $memory['order.option']['statusi'] = 1;
            $memory['order.option']['datas'] = 1;
            $memory['order.option']['fio'] = 0;
            $memory['order.option']['menu'] = 0;
            $memory['order.option']['tel'] = 0;
            $memory['order.option']['sum'] = 1;
            $memory['order.option']['city'] = 0;
            $memory['order.option']['adres'] = 0;
            $memory['order.option']['org'] = 0;
            $memory['order.option']['comment'] = 0;
            $memory['order.option']['cart'] = 0;
            $memory['order.option']['tracking'] = 0;
            $memory['order.option']['admin'] = 0;
            $PHPShopInterface->mobile = true;
        } else {
            $memory['order.option']['uid'] = 1;
            $memory['order.option']['statusi'] = 1;
            $memory['order.option']['datas'] = 1;
            $memory['order.option']['fio'] = 1;
            $memory['order.option']['menu'] = 1;
            $memory['order.option']['tel'] = 1;
            $memory['order.option']['sum'] = 1;
            $memory['order.option']['city'] = 0;
            $memory['order.option']['adres'] = 0;
            $memory['order.option']['org'] = 0;
            $memory['order.option']['comment'] = 0;
            $memory['order.option']['cart'] = 0;
            $memory['order.option']['tracking'] = 0;
            $memory['order.option']['admin'] = 0;
        }
    }

    $message = '<p class="text-muted">' . __('Вы можете изменить перечень полей в таблице отображения заказов') . '</p>';

    $searchforma = $message .
            $PHPShopInterface->setCheckbox('uid', 1, '№ Заказа', $memory['order.option']['uid']) .
            $PHPShopInterface->setCheckbox('statusi', 1, 'Статус', $memory['order.option']['statusi']) .
            $PHPShopInterface->setCheckbox('datas', 1, 'Дата', $memory['order.option']['datas']) .
            $PHPShopInterface->setCheckbox('id', 1, 'ID', $memory['order.option']['id']) .
            $PHPShopInterface->setCheckbox('fio', 1, 'Покупатель', $memory['order.option']['fio']) .
            $PHPShopInterface->setCheckbox('sum', 1, 'Сумма', $memory['order.option']['sum']) .
            $PHPShopInterface->setCheckbox('tel', 1, 'Телефон', $memory['order.option']['tel']) . '<br>' .
            $PHPShopInterface->setCheckbox('menu', 1, 'Экшен меню', $memory['order.option']['menu']) .
            $PHPShopInterface->setCheckbox('discount', 1, 'Скидка', $memory['order.option']['discount']) .
            $PHPShopInterface->setCheckbox('city', 1, 'Город', $memory['order.option']['city']) .
            $PHPShopInterface->setCheckbox('adres', 1, 'Адрес', $memory['order.option']['adres']) .
            $PHPShopInterface->setCheckbox('org', 1, 'Компания', $memory['order.option']['org']) .
            $PHPShopInterface->setCheckbox('comment', 1, 'Комментарий', $memory['order.option']['comment']) . '<br>' .
            $PHPShopInterface->setCheckbox('cart', 1, 'Корзина', $memory['order.option']['cart']) .
            $PHPShopInterface->setCheckbox('tracking', 1, 'Tracking', $memory['order.option']['tracking']) .
            $PHPShopInterface->setCheckbox('admin', 1, 'Менеджер', $memory['order.option']['admin']) .
            $PHPShopInterface->setCheckbox('company', 1, 'Юр. лицо', $memory['order.option']['company'])
    ;

    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'hidden', 'name' => 'path', 'value' => 'order'));
    $searchforma .= '<p class="clearfix"> </p>';


    $PHPShopInterface->_CODE .= $searchforma;

    exit($PHPShopInterface->getContent() . '<p class="clearfix"> </p>');
}

// Определение визульного вывода поля
function getKeyView($val) {

    if (strpos($val['Type'], "(")) {
        $a = explode("(", $val['Type']);
        $b = $a[0];
    } else
        $b = $val['Type'];
    $key_view = array(
        'varchar' => array('type' => 'text', 'name' => $val['Field'] . '_new'),
        'text' => array('type' => 'textarea', 'height' => 150, 'name' => $val['Field'] . '_new'),
        'int' => array('type' => 'text', 'size' => 100, 'name' => $val['Field'] . '_new'),
        'float' => array('type' => 'text', 'size' => 200, 'name' => $val['Field'] . '_new'),
        'enum' => array('type' => 'checkbox', 'name' => $val['Field'] . '_new', 'value' => 1, 'caption' => 'Вкл.'),
    );

    if (!empty($key_view[$b]))
        return $key_view[$b];
    else
        return array('type' => 'text', 'name' => $val['Field'] . '_new');
}

// Описания полей
$key_name = array(
    'id' => 'Id',
    'status' => 'Примечание менеджера',
    'seller' => 'Загружено в CRM',
    'country' => 'Страна',
    'statusi' => '<b>Статус</b>',
    'state' => 'Регион/штат',
    'city' => 'Город',
    'index' => 'Индекс',
    'fio' => 'ФИО',
    'tel' => 'Телефон',
    'street' => 'Улица',
    'house' => 'Дом',
    'porch' => 'Подъезд',
    'door_phone' => 'Домофон',
    'flat' => 'Квартира',
    'delivtime' => 'Время доставки',
    'org_name' => 'Компания',
    'org_inn' => 'ИНН',
    'org_kpp' => 'КПП',
    'org_yur_adres' => 'Юр. адрес',
    'org_fakt_adres' => 'Факт. адрес',
    'org_ras' => 'Р/С',
    'org_bank' => 'Банк',
    'org_kor' => 'К/С',
    'org_bik' => 'БИК',
    'org_city' => 'Город',
    'dop_info' => 'Примечание покупателя',
    'company' => 'Юридическое лицо',
    'admin' => 'Менеджер',
    'user' => 'Пользователь'
);

// Стоп лист
$key_stop = array('id', 'datas', 'uid', 'orders', 'sum', 'servers', 'date', 'paid', 'bonus_minus', 'bonus_plus', 'files');

/**
 * Редактировать с выбранными Шаг 1
 */
function actionSelect() {
    global $PHPShopGUI, $key_name, $key_stop;

    // Выбранные товары
    if (!empty($_POST['select'])) {
        unset($_SESSION['select']['order']);
        $_SESSION['select']['order'] = $_POST['select'];
    }

    // Наборы
    $command[] = array('Прайс-лист', 1, false);
    $command[] = array('База Excel', 2, false);

    $PHPShopGUI->_CODE .= '<p class="text-muted">' . __('Вы можете редактировать одновременно несколько записей. Выберите записи из списка выше, отметьте галочкой поля, которые нужно отредактировать, и нажмите на кнопку "Редактировать выбранные".</p><p class="text-muted"><a href="#" id="select-all">Выбрать все</a> | <a href="#" id="select-none">Снять выделение со всех</a>') . '</p>';

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
    $data = $PHPShopOrm->select(array('*'), false, false, array('limit' => 1));

    if (is_array($data))
        foreach ($data as $key => $val) {

            if ((!in_array($key, $key_stop))) {
                if (!empty($key_name[$key])) {
                    $name = $key_name[$key];
                    $select = 0;
                } else {
                    $name = $key;
                    $select = 0;
                }

                // Память выбранных полей
                if (!empty($_COOKIE['check_memory'])) {
                    $memory = json_decode($_COOKIE['check_memory'], true);
                    if (is_array($memory[$_GET['path']])) {
                        if ($memory[$_GET['path']][$key] == 1)
                            $select = 1;
                        else
                            $select = 0;
                    }
                }


                $PHPShopGUI->_CODE .= '<div class="pull-left" style="width:200px;>' . $PHPShopGUI->setCheckBox($key, 1, ucfirst($name), $select) . '</div>';
            }
        }

    exit($PHPShopGUI->_CODE . '<p class="clearfix"> </p>');
}

// Добавление полей товара в выбор
function actionSelectEdit() {

    unset($_SESSION['select_col']);
    if (!empty($_POST['select_col'])) {
        $_SESSION['select_col'] = $_POST['select_col'];
    }
    return array("success" => true);
}

/**
 * Экшен сохранения
 */
function actionSave() {
    global $PHPShopOrm;

    if (is_array($_SESSION['select']['order'])) {
        $val = array_values($_SESSION['select']['order']);
        $where = array('id' => ' IN (' . implode(',', $val) . ')');
    } else
        $where = null;

    $PHPShopOrm->debug = false;

    // Комментарий и время обработки
    if (!empty($_POST['status_new'])) {
        $status['maneger'] = $_POST['status_new'];
        $status['time'] = PHPShopDate::dataV();
        $_POST['status_new'] = serialize($status);
    }

    // Память выбранных полей
    if (is_array($_POST)) {
        $memory = json_decode($_COOKIE['check_memory'], true);
        unset($memory[$_GET['path']]);
        foreach ($_POST as $k => $v) {
            if (strstr($k, '_new'))
                $memory[$_GET['path']][str_replace('_new', '', $k)] = 1;
        }
        if (is_array($memory))
            setcookie("check_memory", json_encode($memory), time() + 3600000, $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/admpanel/');
    }

    $oldStatuses = $PHPShopOrm->getList(array('statusi', 'id'), $where);

    if ($PHPShopOrm->update($_POST, $where)) {
        
        // Оповещение пользователя о новом статусе и списание со склада
        if (isset($_POST['statusi_new']))
            foreach ($oldStatuses as $status) {
            
            if ((int) $status['statusi'] !== (int) $_POST['statusi_new']) {
                $PHPShopOrderFunction = new PHPShopOrderFunction((int) $status['id']);
                $PHPShopOrderFunction->changeStatus((int) $_POST['statusi_new'], (int) $status['statusi']);
            }
        }

        header('Location: ?path=order');
    } else
        return true;
}

/**
 * Редактировать с выбранными Шаг 2
 */
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopModules, $key_name, $key_stop, $PHPShopSystem,$PHPShopBase;

    $PHPShopGUI->setActionPanel(__("Обновить Заказы"), false, array('Сохранить и закрыть'));
    $PHPShopGUI->addJSFiles('./order/gui/order.gui.js');
    $PHPShopGUI->field_col = 2;
    $select_error = null;

    $PHPShopGUI->_CODE .= $PHPShopGUI->setHelp('Вы можете редактировать одновременно несколько записей. Выберите записи из списка товаров, отметьте галочкой товары, которые нужно отредактировать, и нажмите на кнопку "Редактировать выбранные".<hr>', false);

    $PHPShopOrm->sql = 'show fields  from ' . $GLOBALS['SysValue']['base']['orders'];
    $select = array_values($_SESSION['select_col']);
    $data = $PHPShopOrm->select();
    if (is_array($data))
        foreach ($data as $val) {

            if (in_array($val['Field'], $select) and ! in_array($val['Field'], $key_stop)) {

                // Статусы
                if ($val['Field'] == 'statusi') {
                    // Статусы заказов
                    PHPShopObj::loadClass('order');
                    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
                    $status_array = $PHPShopOrderStatusArray->getArray();
                    $status[] = __('Новый заказ');
                    $order_status_value[] = array(__('Новый заказ'), 0, '');
                    if (is_array($status_array))
                        foreach ($status_array as $status_val) {

                            $status[$status_val['id']] = $status_val['name'];
                            $order_status_value[] = array($status_val['name'], $status_val['id'], $_GET['where']['statusi']);
                        }
                    $PHPShopGUI->_CODE .= $PHPShopGUI->setField('Статус', $PHPShopGUI->setSelect('statusi_new', $order_status_value));
                }
                // Пользователь
                elseif ($val['Field'] == 'user') {
                    $PHPShopGUI->_CODE .= '
                     <div class="form-group form-group-sm ">
        <label class="col-sm-2 control-label">Пользователь:</label><div class="col-sm-10">
        <input data-set="3" name="user_search" maxlength="50" class="search_user form-control input-sm" required="" type="search" data-trigger="manual" data-container="body" data-toggle="popover" data-placement="bottom" data-html="true"  data-content="" placeholder="Найти...">
        <input name="user_new" type="hidden">
     </div></div> ';
                }
                // Менеджер
                elseif ($val['Field'] == 'admin') {
                    if ($PHPShopBase->Rule->CheckedRules('order', 'rule')) {
                        $PHPShopOrmAdmin = new PHPShopOrm($GLOBALS['SysValue']['base']['users']);
                        $data_admin = $PHPShopOrmAdmin->select(array('*'), array('enabled' => "='1'", 'id' => '!=' . $_SESSION['idPHPSHOP']), array('order' => 'name'), array('limit' => 300));

                        $admin_value[] = array(__('Не выбрано'), 0, $data['admin']);
                        if (is_array($data_admin))
                            foreach ($data_admin as $row) {
                                if (empty($row['name']))
                                    $row['name'] = $row['login'];
                                $admin_value[] = array($row['name'], $row['id'], $data['admin']);
                            }

                        $PHPShopGUI->_CODE .= $PHPShopGUI->setField(ucfirst($key_name[$val['Field']]), $PHPShopGUI->setSelect('admin_new', $admin_value, '100%'));
                    }
                }
                // Юридические лица
                elseif ($val['Field'] == 'company') {

                    $PHPShopCompany = new PHPShopCompanyArray();
                    $PHPShopCompanyArray = $PHPShopCompany->getArray();
                    $company_value[] = array($PHPShopSystem->getSerilizeParam("bank.org_name"), 0, $data['company']);
                    if (is_array($PHPShopCompanyArray))
                        foreach ($PHPShopCompanyArray as $company)
                            $company_value[] = array($company['name'], $company['id'], $data['company']);

                    if (is_array($PHPShopCompanyArray))
                        $PHPShopGUI->_CODE .= $PHPShopGUI->setField(ucfirst($key_name[$val['Field']]), $PHPShopGUI->setSelect('company_new', $company_value, '100%'));
                }
                elseif (!empty($key_name[$val['Field']])) {
                    $name = $key_name[$val['Field']];
                    $PHPShopGUI->_CODE .= $PHPShopGUI->setField(ucfirst($name), $PHPShopGUI->setInputArg(getKeyView($val)));
                } else {
                    $name = $val['Field'];
                    $PHPShopGUI->_CODE .= $PHPShopGUI->setField(ucfirst($name), $PHPShopGUI->setInputArg(getKeyView($val)));
                }
            }
        }


    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "editID", "Сохранить", "right", 70, "", "but", "actionUpdate.order.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionSave.order.edit");


    // Выбранные данные
    $select_action_path = 'order';
    if (is_array($_SESSION['select'][$select_action_path])) {
        foreach ($_SESSION['select'][$select_action_path] as $val)
            $select_message = '<span class="label label-default">' . count($_SESSION['select']['order']) . '</span> ' . __('заказов выбрано') . '<hr><a href="#" class="back"><span class="glyphicon glyphicon-ok"></span> ' . __('Изменить интервал') . '</a>';
    } else
        $select_message = '<p class="text-muted">' . __('Вы можете выбрать конкретные объекты для экспорта. По умолчанию будут экспортированы все позиции') . '.: <a href="?path=catalog"><span class="glyphicon glyphicon-share-alt"></span> ' . __('Выбрать') . '</a></p>';

    $sidebarleft[] = array('title' => 'Подсказка', 'content' => $select_message);

    // Ошибки
    if (!empty($select_error))
        $sidebarleft[] = array('title' => 'Ошибка', 'content' => $select_error, 'class' => 'text-danger');


    $PHPShopGUI->setSidebarLeft($sidebarleft, 2);

    // Футер
    $PHPShopGUI->setFooter($ContentFooter);

    $PHPShopGUI->Compile(2);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();
?>