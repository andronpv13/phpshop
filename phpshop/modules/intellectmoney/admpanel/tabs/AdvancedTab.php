<?php

require_once __DIR__ . '/BaseTab.php';
require_once __DIR__ . '/../../class/IntellectMoneyCommon/IntegrationMethod.php';
require_once __DIR__ . '/../../class/IntellectMoneyCommon/LanguageHelper.php';

final class AdvancedTab extends BaseTab
{
    public function __construct($userSettings)
    {
        parent::__construct($userSettings);
        global $PHPShopGUI;
        $languageHelper = PaySystem\LanguageHelper::getInstance('ru', '1251');

        $this->generatetHeader('Дополнительные настройки');
        $this->generateInput($languageHelper->getTitle('preference'), 'preference', $userSettings->getPreference());
        $this->generateInput($languageHelper->getTitle('successUrl'), 'successUrl', $userSettings->getSuccessUrl());
        $this->generateInput($languageHelper->getTitle('expireDate'), 'expireDate', $userSettings->getExpireDate());
    }
    
    public function getTab()
    {
        return [
            'Дополнительные настройки', 
            $this->data, 
            true, 
        ];
    }

    public static function getParams()
    {
        $paramNames = ['preference', 'successUrl', 'expireDate'];
        $result = [];
        foreach($paramNames as $paramName)
        {
            if (isset($_POST[$paramName]))
            {
                $result[$paramName] = $_POST[$paramName];
            }
        }

        return $result;
    }
}