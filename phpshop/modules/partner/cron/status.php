<?php

/**
 * Обновление статусов заказов из csv файла
 * Для включения поменяйте значение enabled на true
 */
// Включение
$enabled = false;
$csv_file = 'status.csv';
session_start();

$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("date");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("cart");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("text");
PHPShopObj::loadClass("lang");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("file");
PHPShopObj::loadClass("order");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopSystem = new PHPShopSystem();
$PHPShopOrderStatusArray = new PHPShopOrderStatusArray();

// Авторизация
if ($_GET['s'] == md5($PHPShopBase->SysValue['connect']['host'] . $PHPShopBase->SysValue['connect']['dbase'] . $PHPShopBase->SysValue['connect']['user_db'] . $PHPShopBase->SysValue['connect']['pass_db']))
    $enabled = true;

if (empty($enabled))
    exit("Ошибка авторизации!");


// Настройки модуля
PHPShopObj::loadClass("modules");
$PHPShopModules = new PHPShopModules($_classPath . "modules/");
$PHPShopModules->checkInstall('partner');

include_once($_classPath . 'modules/partner/class/partner.class.php');
$PHPShopPartnerOrder = new PHPShopPartnerOrder();
$PHPShopPartnerOrder->option = $PHPShopPartnerOrder->option();

// Статусы заказов
$GetOrderStatusArray = $PHPShopOrderStatusArray->getKey('name.id', true);
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
$csv_load_count = 0;

// Обработка строки CSV
function csv_update($row) {
    static $n;
    global $PHPShopPartnerOrder, $GetOrderStatusArray, $PHPShopOrm, $csv_load_count;

    if ($n > 0) {

        // Если заказ выполнен, заносим эти данные в лог партнера, начисляем % партнеру
        if ($row[1] == $PHPShopPartnerOrder->option['order_status']) {

            // Сумма заказа
            $data = $PHPShopOrm->getOne(['id', 'sum', 'statusi'], ['id' => '=' . (int) $row[0]]);

            // Начисляем бонус партнеру
            if (!empty($data['id'])) {
                $PHPShopPartnerOrder->addBonus($data['id'], $data['sum']);

                // Смена статуса заказа партнера
                $PHPShopPartnerOrder->updateLog($data['id']);
            }
        }

        // Смена статуса заказа
        if (!empty($row[1]))
            $PHPShopOrm->update(['statusi_new' => $row[1]], ['id' => '=' . (int) $row[0]]);

        $csv_load_count++;
    }
    $n++;
}

// Модуль включен
$result = PHPShopFile::readCsv($csv_file, 'csv_update');
if ($result)
    echo 'Обработано ' . $GLOBALS['csv_load_count'] . ' строк';
?>