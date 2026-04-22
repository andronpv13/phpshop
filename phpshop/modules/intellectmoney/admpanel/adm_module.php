<?php

require_once __DIR__ . '/tabs/CommonTab.php';
require_once __DIR__ . '/tabs/CashboxTab.php';
require_once __DIR__ . '/tabs/HoldTab.php';
require_once __DIR__ . '/tabs/AdvancedTab.php';
require_once __DIR__ . '/tabs/LogsTab.php';
require_once __DIR__ . '/../class/IntellectMoneyCommon/UserSettings.php';
require_once __DIR__ . '/../class/intellectmoney.database.helper.php';

/**
 * Функция обрабатывающая вывод настроек модуля.
 * 
 * @return void
 */
function actionStart() {
    global $PHPShopGUI;
    $databaseHelper = new DataBaseHelper('phpshop_modules_intellectmoney_settings');
    if (isset($_POST['saveID']))
    {
        $params = array_merge(CommonTab::getParams(), CashboxTab::getParams(), HoldTab::getParams(), AdvancedTab::getParams(), LogsTab::getParams());
        
        $userSettings = PaySystem\UserSettings::getInstance($params);
        foreach($userSettings->getParams() as $key => $val)
        {
            if ($val != null) {
                $databaseHelper->setSetting($key, $val);
            }
        }
    }
    else
    {
        $userSettings = $databaseHelper->loadUserSettings();
    }
    
    $PHPShopGUI->setTab((new CommonTab($userSettings))->getTab(), (new CashboxTab($userSettings))->getTab(), (new HoldTab($userSettings))->getTab(), (new AdvancedTab($userSettings))->getTab(), (new LogsTab($userSettings))->getTab());
    
    return true;
}


// Обработка событий.
$PHPShopGUI->getAction();

// Устанавливаем наш обработчик.
$PHPShopGUI->setLoader('', 'actionStart');