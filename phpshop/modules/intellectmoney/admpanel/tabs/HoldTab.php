<?php

require_once __DIR__ . '/BaseTab.php';
require_once __DIR__ . '/../../class/IntellectMoneyCommon/IntegrationMethod.php';
require_once __DIR__ . '/../../class/IntellectMoneyCommon/LanguageHelper.php';

final class HoldTab extends BaseTab
{
    public function __construct($userSettings)
    {
        parent::__construct($userSettings);
        global $PHPShopGUI;
        $languageHelper = PaySystem\LanguageHelper::getInstance('ru', '1251');

        $this->generatetHeader('Холдирование');
        $this->generateCheckbox($languageHelper->getTitle('holdMode')   , 'holdMode', $userSettings->getHoldMode());
        $this->generateInput($languageHelper->getTitle('holdTime')      , 'holdTime', $userSettings->getHoldTime());
    }
    
    public function getTab()
    {
        return [
            'Холдирование', 
            $this->data, 
            true, 
        ];
    }

    public static function getParams()
    {
        $paramNames = ['holdTime'];
        $result = [];
        foreach($paramNames as $paramName)
        {
            if (isset($_POST[$paramName]))
            {
                $result[$paramName] = $_POST[$paramName];
            }
        }
        
        $result['holdMode'] = isset($_POST['holdMode']) && $_POST['holdMode'] == "" ? "on" : "off";

        return $result;
    }
}