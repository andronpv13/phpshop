<?php

require_once __DIR__ . '/BaseTab.php';
require_once __DIR__ . '/../../class/IntellectMoneyCommon/IntegrationMethod.php';
require_once __DIR__ . '/../../class/IntellectMoneyCommon/LanguageHelper.php';
require_once __DIR__ . '/../../class/intellectmoney.logger.class.php';

final class LogsTab extends BaseTab
{
    public function __construct($userSettings)
    {
        parent::__construct($userSettings);
        $logs = IMLogger::GetLogs();

        $this->generatetHeader('Логи');
        if ($logs != null) 
            foreach($logs as $log)
                $this->generateLog($log);
        else
            $this->generateLog(['message' => 'Логи отсутствуют', 'kind' => 'info']);
    }
    
    public function getTab()
    {
        return [
            'Логи', 
            $this->data, 
            true, 
        ];
    }

    public static function getParams()
    {
        return [];
    }

    protected function generateLog($log)
    {
        global $PHPShopGUI;
        $show_msg = '[' . date('Y-m-d H:i:s', $log['timestamp']) . '] ' . $log['message'];
        $show_msg .= $this->endsWith($show_msg, '.') || $this->endsWith($show_msg, '!') ? '' : '.';
        $this->data .= $PHPShopGUI->setAlert($show_msg, $log['kind']);
    }

    private function endsWith($haystack, $needle) {
        $length = strlen( $needle );
        if( !$length ) {
            return true;
        }
        return substr( $haystack, -$length ) === $needle;
    }
}