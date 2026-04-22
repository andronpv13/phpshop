<?php

require_once __DIR__ . '/BaseTab.php';
require_once __DIR__ . '/../../class/IntellectMoneyCommon/IntegrationMethod.php';
require_once __DIR__ . '/../../class/IntellectMoneyCommon/LanguageHelper.php';

/**
 * Обработчик вкладки "Основное".
 */
final class CommonTab extends BaseTab
{
    /**
     * Статусы заказов в PHPShop.
     * @var array
     */
    private static $cms_order_states;

    public function __construct($userSettings)
    {
        parent::__construct($userSettings);
        global $PHPShopGUI;
        $languageHelper = PaySystem\LanguageHelper::getInstance('ru', '1251');
        
        $integrationMethods = [];
        foreach(PaySystem\IntegrationMethod::values() as $method) {
            $integrationMethods[] = [
                'title' => $languageHelper->getTitle('integrationMethod_' . $method),
                'value' => $method,
                'selected' => $method == $userSettings->getIntegrationMethod(), 
            ];
        }

        $this->generateSelect($languageHelper->getTitle('integrationMethod')    , 'integrationMethod'   , $integrationMethods);
        $this->generateInput($languageHelper->getTitle('eshopId')               , 'eshopId'             , $userSettings->getEshopId());
        $this->generateInput($languageHelper->getTitle('accountId')             , 'accountId'           , $userSettings->getAccountId());
        $this->generateInput($languageHelper->getTitle('formId')                , 'formId'              , $userSettings->getFormId());
        $this->generateInput($languageHelper->getTitle('secretKey')             , 'secretKey'           , $userSettings->getSecretKey());
        $this->generateCheckbox($languageHelper->getTitle('testMode')           , 'testMode'            , $userSettings->getTestMode());

        $this->generatetHeader('Статусы заказа');
        $this->generateSelect($languageHelper->getTitle('statusCreated')        , 'statusCreated'       , $this->generateArrayForInvoiceStatus($userSettings->getStatusCreated()));
        $this->generateSelect($languageHelper->getTitle('statusCancelled')      , 'statusCancelled'     , $this->generateArrayForInvoiceStatus($userSettings->getStatusCancelled()));
        $this->generateSelect($languageHelper->getTitle('statusPaid')           , 'statusPaid'          , $this->generateArrayForInvoiceStatus($userSettings->getStatusPaid()));
        $this->generateSelect($languageHelper->getTitle('statusHolded')         , 'statusHolded'        , $this->generateArrayForInvoiceStatus($userSettings->getStatusHolded()));
        $this->generateSelect($languageHelper->getTitle('statusPartiallyPaid')  , 'statusPartiallyPaid' , $this->generateArrayForInvoiceStatus($userSettings->getStatusPartiallyPaid()));
        $this->generateSelect($languageHelper->getTitle('statusRefunded')       , 'statusRefunded'      , $this->generateArrayForInvoiceStatus($userSettings->getStatusRefunded()));
        
    }

    public function getTab()
    {
        return [
            'Основное', 
            $this->data, 
            true, 
        ];
    }

    public static function getParams()
    {
        $paramNames = ['eshopId', 'integrationMethod', 'formId', 'accountId', 'secretKey', 'statusCreated', 'statusCancelled', 'statusPaid', 'statusHolded', 'statusPartiallyPaid', 'statusRefunded'];
        $result = [];
        foreach($paramNames as $paramName)
        {
            if (isset($_POST[$paramName]))
            {
                $result[$paramName] = $_POST[$paramName];
            }
        }
        
        $result['testMode'] = isset($_POST['testMode']) && $_POST['testMode'] == "" ? "on" : "off";
        return $result;
    }

    /**
     * Сгенерировать массив статусов из статусов CMS.
     * 
     * @param int $selectedId Идентификатор выбранного статуса.
     * 
     * @return array
     * Возвращает ассоциативный массив элементов, для отображение в UI.
     */
    private function generateArrayForInvoiceStatus($selectedId) {
        if (empty(self::$cms_order_states)) {
            $cms_states = (new DataBaseHelper('phpshop_order_status'))->selectAll();
            foreach($cms_states as $state) {
                self::$cms_order_states[] = [
                    'title' => $state['name'],
                    'value' => $state['id'],
                    'selected' => false, 
                ];
            }
        }

        $clone = [];
        foreach(self::$cms_order_states as $state) {
            $clone[] = [
                'title' => $state['title'], 
                'value' => $state['value'],
                'selected' => $state['value'] == $selectedId,
            ];
        }

        return $clone;
    }
}