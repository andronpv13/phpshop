<?php
PHPShopObj::loadClass('order');
include($_classPath . "modules/retailcrm/class/Tools.php");
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.retailcrm.retailcrm_system"));

function actionUpdate() {
    global $PHPShopOrm;
    $post = Tools::clearArray($_POST);
    if ('/' != substr($post["siteurl"], strlen($post["siteurl"]) - 1, 1)) {
        $post["siteurl"] .= '/';
    }
    if ('/' != substr($post["url"], strlen($post["url"]) - 1, 1)) {
        $post["url"] .= '/';
    }
    $sql = "update phpshop_modules_retailcrm_system set value='" . serialize($post) . "' where code='options'";
    $action = $PHPShopOrm->query($sql);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

// Обновление версии модуля
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    $data = $PHPShopOrm->select();

    $value = unserialize($data['value']);

    $help = '
    <h4>Как подключиться к RetailCRM?</h4>
        <ol>
        <li>Зарегистрироваться на сайте <a href="http://www.retailcrm.ru/?partner=RCM-6931" target="_blank">RetailCRM</a></li>
        <li>Выбрать CMS PHPShop в настройках RetailCRM</li>
        <li>В поле <kbd>Компания</kbd> введите название своей компании, указанное при регистрации аккаунта в RetailCRM</li>
        <li>В поле <kbd>URL сайта магазина</kbd> указать <code>http://' . $_SERVER['SERVER_NAME'] . '/</code></li>
        <li>В поле <kbd>API URL</kbd> указать URL адрес вашего кабинета в RetailCRM <code>https://name.retailcrm.ru/</code></li>
        <li>В поле <kbd>API KEY</kbd> указать ключ из личного кабинета RetailCRM, доступный в  "Администрирование / Интеграция / Ключи доступа к API"</li>
        <li>В поле <kbd>Название магазина</kbd> указать название магазина из личного кабинета RetailCRM</li>
        <li>Перейти на вкладку Справочники в кабинете RetailCRM и настроить соответствие способов доставки, способов оплаты, статусов заказа интернет-магазина и CRM.</li>
</li>
		</ol>';

    $tab1 = $PHPShopGUI->setField('Название магазина', $PHPShopGUI->setInputText(false, 'shopname', $value["shopname"], 400));
    $tab1 .= $PHPShopGUI->setField('Компания', $PHPShopGUI->setInputText(false, 'companyname', $value["companyname"], 400));
    $tab1 .= $PHPShopGUI->setField('URL сайта магазина', $PHPShopGUI->setInputArg(array('type' => 'text', 'name' => 'siteurl', 'value' => $value["siteurl"], 'size' => 400, 'placeholder' => 'http://' . $_SERVER['SERVER_NAME'])));

    $tab1 .= $PHPShopGUI->setField('API URL', $PHPShopGUI->setInputArg(array('type' => 'text', 'name' => 'url', 'value' => $value["url"], 'size' => 400, 'placeholder' => 'https://name.retailcrm.ru/')));
    $tab1 .= $PHPShopGUI->setField('API KEY', $PHPShopGUI->setInputText(false, 'key', $value["key"], 400));

    $tab1 = $PHPShopGUI->setCollapse('Настройки', $tab1);

    if (isset($value["url"]) && isset($value["key"]) && $helper = new ApiHelper($value["url"], $value["key"])) {
        $field1 = "";
        $tab2 = "";
        // Способы доставки
        try {
            $response = $helper->api->deliveryTypesList();
        } catch (CurlException $e) {
            Tools::logger(array('error' => 'Ошибка соединения с RetailCRM'), "connect", 'Ошибка соединения с RetailCRM');
        }

        $deliveryTypes[] = array(__("Не выбрано"), "", false);
        if ($response->isSuccessful()) {
            foreach ($response->deliveryTypes as $code => $params) {
                $deliveryTypes[] = array(Tools::iconvArray($params["name"], "UTF-8", "WINDOWS-1251"), $params["code"], false);
            }
        } else {
            Tools::logger(array('error' => 'Ошибка соединения с RetailCRM'), "connect", 'Ошибка соединения с RetailCRM');
        }

        $deliveryOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['delivery']);
        $delivery = $deliveryOrm->select(array('*'), array('is_folder' => "=''"));
        

        foreach ($delivery as $del) {
            $tmpDeliveryTypes = $deliveryTypes;
            if (isset($value["delivery"][$del["id"]])) {
                foreach ($tmpDeliveryTypes as $key => $val) {
                    if ($val[1] == $value["delivery"][$del["id"]]) {
                        $tmpDeliveryTypes[$key][2] = "selected";
                        break;
                    }
                }
            }

            $field1 .= $PHPShopGUI->setField($del["city"], $PHPShopGUI->setSelect('delivery[' . $del["id"] . ']', $tmpDeliveryTypes),1, null, null, 'control-label', false);
        }

        $tab2 .= $PHPShopGUI->setCollapse('Способы доставки', $field1);

        // Способы оплаты
        try {
            $response = $helper->api->paymentTypesList();
        } catch (CurlException $e) {
            Tools::logger(array('error' => 'Ошибка соединения с RetailCRM'), "connect", 'Ошибка соединения с RetailCRM');
        }

        $paymentTypes[] = array(__("Не выбрано"), "", false);
        if ($response->isSuccessful()) {
            foreach ($response->paymentTypes as $code => $params) {
                $paymentTypes[] = array(Tools::iconvArray($params["name"], "UTF-8", "WINDOWS-1251"), $params["code"], false);
            }
        } else {
            Tools::logger(array('error' => 'Ошибка соединения с RetailCRM'), "connect", 'Ошибка соединения с RetailCRM');
        }

        $paymentOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['payment_systems']);
        $payment = $paymentOrm->select(array('*'));

        $field2 = "";
        foreach ($payment as $paymentValue) {
            $tmpPaymentTypes = $paymentTypes;
            if (isset($value["payment"][$paymentValue["id"]])) {
                foreach ($tmpPaymentTypes as $key => $val) {
                    if ($val[1] == $value["payment"][$paymentValue["id"]]) {
                        $tmpPaymentTypes[$key][2] = "selected";
                        break;
                    }
                }
            }

            $field2 .= $PHPShopGUI->setField($paymentValue["name"], $PHPShopGUI->setSelect('payment[' . $paymentValue["id"] . ']', $tmpPaymentTypes),1, null, null, 'control-label', false);
        }

        $tab2 .= $PHPShopGUI->setCollapse('Способы оплаты', $field2);

        try {
            $response = $helper->api->statusesList();
        } catch (CurlException $e) {
            Tools::logger(array('error' => 'Ошибка соединения с RetailCRM'), "connect", 'Ошибка соединения с RetailCRM');
        }

        $statuses[] = array(__("Не выбрано"), "", false);
        if ($response->isSuccessful()) {
            foreach ($response->statuses as $code => $params) {
                $statuses[] = array(Tools::iconvArray($params["name"], "UTF-8", "WINDOWS-1251"), $params["code"], false);
            }
        } else {
            Tools::logger(array('error' => 'Ошибка соединения с RetailCRM'), "connect", 'Ошибка соединения с RetailCRM');
        }

        $statusesOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['order_status']);
        $status = $statusesOrm->select(array('*'));

        $field3 = "";
        array_unshift($status, array("id" => "new", "name" => __("Новый заказ")));
        foreach ($status as $statusValue) {
            $tmpStatuses = $statuses;
            if (isset($value["status"][$statusValue["id"]])) {
                foreach ($tmpStatuses as $key => $val) {
                    if ($val[1] == $value["status"][$statusValue["id"]]) {
                        $tmpStatuses[$key][2] = "selected";
                        break;
                    }
                }
            }

            $field3 .= $PHPShopGUI->setField($statusValue["name"], $PHPShopGUI->setSelect('status[' . $statusValue["id"] . ']', $tmpStatuses),1, null, null, 'control-label', false);
        }


        $tab2 .= $PHPShopGUI->setCollapse('Статусы', $field3);

        if (isset($GLOBALS['SysValue']['base']['oneclick'])) {
            $field3 = "";
            $status = array(
                array("id" => 1, "name" => "Новая"),
                array("id" => 2, "name" => "Просили перезвонить"),
                array("id" => 3, "name" => "Недоступен"),
                array("id" => 4, "name" => "Выполнен"),
            );
            foreach ($status as $statusValue) {
                $tmpStatuses = $statuses;
                if (isset($value["status-oneclick"][$statusValue["id"]])) {
                    foreach ($tmpStatuses as $key => $val) {
                        if ($val[1] == $value["status-oneclick"][$statusValue["id"]]) {
                            $tmpStatuses[$key][2] = "selected";
                            break;
                        }
                    }
                }

                $field3 .= $PHPShopGUI->setField($statusValue["name"], $PHPShopGUI->setSelect('status-oneclick[' . $statusValue["id"] . ']', $tmpStatuses));
            }

            $tab2 .= $PHPShopGUI->setCollapse('Статусы (заказ в один клик)', $field3);
        }

        if (isset($GLOBALS['SysValue']['base']['returncall'])) {
            $field3 = "";
            $status = array(
                array("id" => 1, "name" => "Новая"),
                array("id" => 2, "name" => "Просили перезвонить"),
                array("id" => 3, "name" => "Недоступен"),
                array("id" => 4, "name" => "Выполнен"),
            );
            foreach ($status as $statusValue) {
                $tmpStatuses = $statuses;
                if (isset($value["status-oneclick"][$statusValue["id"]])) {
                    foreach ($tmpStatuses as $key => $val) {
                        if ($val[1] == $value["status-returncall"][$statusValue["id"]]) {
                            $tmpStatuses[$key][2] = "selected";
                            break;
                        }
                    }
                }

                $field3 .= $PHPShopGUI->setField($statusValue["name"], $PHPShopGUI->setSelect('status-returncall[' . $statusValue["id"] . ']', $tmpStatuses));
            }

            $tab2 .= $PHPShopGUI->setCollapse('Статусы (обратный звонок)', $field3);
        }


        $PHPShopGUI->setTab(
                array("Настройки", $tab1, true), array("Справочники", $tab2), array("Инструкция", $PHPShopGUI->setInfo($help)), array("О Модуле", $PHPShopGUI->setPay(false, false, $data['version'], true)));
    } else {
        $PHPShopGUI->setTab(array("Настройки", $tab1, true), array("Инструкция", $PHPShopGUI->setInfo($help)), array("О Модуле", $PHPShopGUI->setPay(false, false, $data['version'], true)));
    }

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>