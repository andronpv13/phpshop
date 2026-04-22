<?php

$TitlePage = __("О программе PHPShop");

// Стартовый вид
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $version, $PHPShopBase, $TitlePage, $shop_type;

    $licFile = PHPShopFile::searchFile('../../license/', 'getLicense', true);
    @$License = parse_ini_file_true("../../license/" . $licFile, 1);

    $TechPodUntilUnixTime = $License['License']['SupportExpires'];
    $_SESSION['support'] = $TechPodUntilUnixTime;
    if (is_numeric($TechPodUntilUnixTime))
        $TechPodUntil = PHPShopDate::get($TechPodUntilUnixTime);
    else
        $TechPodUntil = " " . __("ознакомительный режим");

    $DomenLocked = $License['License']['DomenLocked'];
    if (empty($DomenLocked))
        $DomenLocked = $_SERVER['SERVER_NAME'];


    $LicenseUntilUnixTime = $License['License']['Expires'];
    if (is_numeric($LicenseUntilUnixTime))
        $LicenseUntil = PHPShopDate::get($LicenseUntilUnixTime);
    else
        $LicenseUntil = " " . __("без ограничений");

    if (getenv("COMSPEC"))
        $License['License']['Pro'] = 'Enabled';

    if ($License['License']['Pro'] == 'Start') {
        $product_name = 'Basic';
        $mod_limit = __('максимум 5 модулей') . ' <a href="https://www.phpshop.ru/order/?from=' . $_SERVER['SERVER_NAME'] . '" target="_blank">' . __('Снять ограничение') . ' Basic?</a>';
    } else {
        if ($License['License']['Pro'] == 'Enabled') {
            $product_name = 'Pro';
            $mod_limit = __('без ограничений');
        } else {
            $product_name = 'Enterprise';
            $mod_limit = __('без ограничений кроме <span class="label label-default">Pro</span> модулей');
        }
    }

    $YandexCloudUntilUnixTime = $License['License']['YandexCloud'];
    if (is_numeric($YandexCloudUntilUnixTime) and $YandexCloudUntilUnixTime > time())
        $YandexCloudUntil = PHPShopDate::get($YandexCloudUntilUnixTime);
    else
        $YandexCloudUntil = __("нет подписки");


    // Размер названия поля
    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->addJSFiles('./system/gui/system.gui.js');
    $PHPShopGUI->setActionPanel($TitlePage, false, false);

    if (empty($License['License']['Serial'])) {
        $loadLicClass = 'hide';
        $serialNumber = " " . __("ознакомительный режим");
    } else {
        $loadLicClass = null;
        $serialNumber = '<code>' . $License['License']['Serial'] . "</code>&nbsp;&nbsp;" . '<button id="loadLic" value="1" type="button" class="btn btn-sm btn-default  ' . $loadLicClass . '" target="_blank"><span class="glyphicon glyphicon-hdd"></span> ' . __('Синхронизировать') . '</button>';
    }

    if (!empty($licFile))
        $licFilepath = '/license/' . $licFile;
    else
        $licFilepath = __("ознакомительный режим");

    if (strstr($License['License']['HardwareLocked'], '-')) {
        $ShowcaseArray = explode("-", $License['License']['HardwareLocked']);
        if (is_array($ShowcaseArray))
            $ShowcaseLimit = $ShowcaseArray[1];
    }
    elseif ($License['License']['HardwareLocked'] == 'Showcase')
        $ShowcaseLimit = __('без ограничений');
    else
        $ShowcaseLimit = __('нет');

    $shop_type_value = array(__('интернет-магазин'), __('каталог продукции'), __('сайт компании'));

    // Содержание закладки 1
    $Tab1 = $PHPShopGUI->setCollapse('Информация', $PHPShopGUI->setField("Название программы", '<a class="btn btn-sm btn-default" href="https://www.phpshop.ru/page/compare.html?from=' . $_SERVER['SERVER_NAME'] . '" target="_blank"><span class="glyphicon glyphicon-info-sign"></span> PHPShop ' . $product_name . '</a>') .
            $PHPShopGUI->setField("Версия программы", '<a class="btn btn-sm btn-default" href="https://www.phpshop.ru/docs/update.html?from=' . $_SERVER['SERVER_NAME'] . '" target="_blank"><span class="glyphicon glyphicon-info-sign"></span> ' . substr($version, 0, strlen($version) - 1) . '</a>') .
            $PHPShopGUI->setField("Подключаемые модули", $mod_limit, false, false, false, 'text-right') .
            $PHPShopGUI->setField("Конфигурация", $shop_type_value[$shop_type], false, false, false, 'text-right') .
            $PHPShopGUI->setField("Дополнительные витрины", $ShowcaseLimit, false, 'Многосайтовость', false, 'text-right') .
            $PHPShopGUI->setField("Окончание поддержки", $TechPodUntil . '&nbsp;&nbsp; <a class="btn btn-sm btn-default  ' . $loadLicClass . '" href="?path=support"><span class="glyphicon glyphicon-user"></span> ' . __('Задать вопрос в поддержку') . '</a>', false, false, false, 'text-right') .
            $PHPShopGUI->setField("Окончание лицензии", $LicenseUntil, false, false, false, 'text-right') .
            $PHPShopGUI->setField("Окончание подписки <a href=\"?path=system.yandexcloud\">Yandex Cloud</a>", $YandexCloudUntil, false, false, false, 'text-right') .
            $PHPShopGUI->setField("Файл лицензии", $licFilepath, false, false, false, 'text-right') .
            $PHPShopGUI->setField("Серийный номер", $serialNumber, false, false, false, 'text-right') .
            $PHPShopGUI->setField("Версия PHP", phpversion(), false, false, false, 'text-right') .
            $PHPShopGUI->setField("Версия MySQL", @mysqli_get_server_info($PHPShopBase->link_db), false, false, false, 'text-right') .
            $PHPShopGUI->setField("Max execution time", @ini_get('max_execution_time') . ' sec.', false, 'Максимальное время работы', false, 'text-right') .
            $PHPShopGUI->setField("Memory limit", @ini_get('memory_limit'), false, 'Выделяемая память', false, 'text-right') .
            $PHPShopGUI->setField("Upload max filesize", @ini_get('upload_max_filesize'), false, 'Максимальный размер загружаемого файла для CML', false, 'text-right') .
            $PHPShopGUI->setField("Имя базы данных", $PHPShopBase->getParam('connect.dbase'), false, false, false, 'text-right') .
            $PHPShopGUI->setField("Кодировка", $PHPShopBase->codBase, false, false, false, 'text-right')
    );

    if (!empty($TechPodUntilUnixTime) and time() > $TechPodUntilUnixTime)
        $Tab1 .= $PHPShopGUI->setField(false, '</form><form method="post" target="_blank" enctype="multipart/form-data" action="https://www.phpshop.ru/order/" name="product_upgrade" id="product_support" style="display:none">
<input type="hidden" value="supportenterprise" name="addToCartFromPages" id="addToCartFromPages">             
<input type="hidden" value="' . $DomenLocked . '" name="addToCartFromPagesDomen" id="addToCartFromPagesDomen">
</form><form><a class="btn btn-sm btn-primary pay-support" href="#" target="_blank"><span class="glyphicon glyphicon-ruble"></span> ' . __('Приобрести техническую поддержку') . '</a>');

    $Tab2 = $PHPShopGUI->setCollapse('Лицензионное соглашение', $PHPShopGUI->loadLib('tab_license', false, './system/'));

    // Запрос модуля на закладку
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $License);

    // Вывод формы закладки
    $PHPShopGUI->setTab(array("Основное", $Tab1), array("Лицензионное соглашение", $Tab2, true));

    // Вывод кнопок сохранить и выход в футер
    $ContentFooter = $PHPShopGUI->setInput("submit", "loadLic", "Применить", "", "", "", "", "actionLoadLic.system.edit");

    // Футер
    $PHPShopGUI->Compile($ContentFooter);
    return true;
}

// Синхронизация лицензии
function actionLoadLic() {
    global $PHPShopBase;

    // Удаление лицензии
    $licFile = PHPShopFile::searchFile('../../license/', 'getLicense', true);
    $License = parse_ini_file_true("../../license/" . $licFile, 1);

    if (empty($License['License']['DomenLocked']))
        $License['License']['DomenLocked'] = $_SERVER['SERVER_NAME'];

    if (!empty($licFile)) {
        if (@unlink("../../license/" . $licFile)) {
            $action = true;

            $protocol = 'http://';
            if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])) {
                $protocol = 'https://';
            }
            
            // Получение новой лицензии
            $url = $protocol . $License['License']['DomenLocked'];
            $сurl = curl_init();
            curl_setopt_array($сurl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS=>['action' => __FUNCTION__,'s'=>md5($PHPShopBase->SysValue['connect']['host'].$PHPShopBase->SysValue['connect']['dbase'].$PHPShopBase->SysValue['connect']['user_db'].$PHPShopBase->SysValue['connect']['pass_db'])]
            ]);
            curl_exec($сurl);
            curl_close($сurl);
            return array("success" => $action);
        } else {
            //Ошибка обновления, нет прав изменения файла лицензии!
            $action = false;
        }
    }
}

// Обработка событий
$PHPShopGUI->getAction();

// Вывод формы при старте
$PHPShopGUI->setAction(null, 'actionStart', 'none');