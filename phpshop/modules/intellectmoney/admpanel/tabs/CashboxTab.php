<?php

require_once __DIR__ . '/BaseTab.php';
require_once __DIR__ . '/../../class/IntellectMoneyCommon/IntegrationMethod.php';
require_once __DIR__ . '/../../class/IntellectMoneyCommon/LanguageHelper.php';
require_once __DIR__ . '/../../class/IntellectMoneyCommon/VATs.php';

final class CashboxTab extends BaseTab
{
    public function __construct($userSettings)
    {
        parent::__construct($userSettings);
        global $PHPShopGUI;
        $languageHelper = PaySystem\LanguageHelper::getInstance('ru', '1251');
        
        $vats = [];
        foreach(PaySystem\VATs::getList() as $vat)
        {
            $vats[] = [
                'title' => $vat['name'],
                'value' => $vat['id'],
                'selected' => $vat['id'] == $userSettings->getTax(), 
            ];
        }

        $this->generatetHeader('Настройки кассы');
        $this->generateInput($languageHelper->getTitle('group') , 'group'   , $userSettings->getGroup());
        $this->generateSelect($languageHelper->getTitle('tax')  , 'tax'     , $vats);
        $this->generateInput($languageHelper->getTitle('inn')   , 'inn'     , $userSettings->getInn());
    }
    
    public function getTab()
    {
        return [
            'Настройки кассы', 
            $this->data, 
            true, 
        ];
    }

    public static function getParams()
    {
        $paramNames = ['group', 'tax', 'inn'];
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