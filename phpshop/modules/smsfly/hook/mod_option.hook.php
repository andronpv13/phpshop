<?php

// Настройки модуля
PHPShopObj::loadClass("array");

class PHPShopSmsfly extends PHPShopArray {

    function __construct() {
        $this->objType = 3;
        $this->objBase = $GLOBALS['SysValue']['base']['smsfly']['smsfly_system'];
        parent::__construct("merchant_user", "merchant_pwd", "phone", "sandbox", "alfaname");

        $this->option = parent::getArray();
    }

    function true_num($phone, $pre = 38) {
        $step1 = str_replace(array('+', '(', ')', '-', ' '), '', $phone);
        if (substr($step1, 0, 8) != '38')
            $step1 = $pre . $step1;
        return $step1;
    }

    function send($msg, $recipient = false) {

        // Настройки модуля
        $text = iconv('windows-1251', 'utf-8', htmlspecialchars($msg,ENT_QUOTES, 'windows-1251'));
        $description = iconv('windows-1251', 'utf-8', htmlspecialchars($msg,ENT_QUOTES, 'windows-1251'));
        $rate = 120;
        $livetime = 4;
        
        // Номер телефона
        if (empty($recipient))
            $recipient = $this->option['phone'];
        
        // Проверка номера
        $recipient = $this->true_num($recipient);

        $user = $this->option['merchant_user'];
        $password = $this->option['merchant_pwd'];
        $source = $this->option['alfaname'];

        if (empty($source)) {
            $server_name = explode(".", str_replace('www', '', $_SERVER['SERVER_NAME']));
            $source = $server_name[0]; // Alfaname
        }

        $myXML = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $myXML .= "<request>";
        $myXML .= "<operation>SENDSMS</operation>";
        $myXML .= '		<message start_time="AUTO" end_time="AUTO" livetime="' . $livetime . '" rate="' . $rate . '" desc="' . $description . '" source="' . $source . '">' . "\n";
        $myXML .= "		<body>" . $text . "</body>";
        $myXML .= "		<recipient>" . $recipient . "</recipient>";
        $myXML .= "</message>";
        $myXML .= "</request>";

        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_USERPWD, $user . ':' . $password);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_URL, 'http://sms-fly.com/api/api.php');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml", "Accept: text/xml"));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $myXML);
            $response = curl_exec($ch);
            curl_close($ch);

            if ($this->option['sandbox'] == 1) {
                echo '<textarea spellcheck="false" style="width:300px;height:300px">';
                echo 'Запрос: ' . iconv('utf-8', 'windows-1251', $myXML);
                echo '-----';
                echo 'Ответ:' . $response;
                echo '</textarea>';
            }
            return true;
        }
    }

}

?>
