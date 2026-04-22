<?php

/**
 * Библиотека Отправление почты
 * @version 2.7
 * @package PHPShopClass
 * <code>
 * // example:
 * $PHPShopMail= new PHPShopMail('user@localhost','admin@localhost','Test','Hi, user!');
 * </code>
 */
class PHPShopMail {

    /**
     * @var string кодировка письма
     */
    var $codepage = "windows-1251";

    /**
     * @var string Тип содержания
     */
    var $type = "text/plain";
    var $debug = false;

    /**
     * Конструктор
     * @param string $to куда
     * @param string $from от кого
     * @param string $subject заголовок письма
     * @param string $content содержание письма
     * @param boolean $type тип письма, текст или html, true ='text/html', false = text/plain
     * @param boolean $noSend true - не отсылать письмо при создании экземпляра класса. 
     * @param array $option дополнительные параметры SMTP 
     */
    function __construct($to, $from, $subject, $content, $type = false, $noSend = false, $option = false, $codepage = false) {
        global $PHPShopSystem, $_classPath;

        if (empty($_classPath))
            $_classPath = './phpshop/';

        if ($GLOBALS['PHPShopBase']->codBase == 'utf-8' and empty($codepage))
            $this->codepage = 'utf-8';

        // Загрузчик
        require_once $_classPath . 'lib/phpmailer/PHPMailerAutoload.php';
        $this->mail = new PHPMailer;
        $this->PHPShopSystem = $PHPShopSystem;
        $option = $this->PHPShopSystem->getMailOption($option);

        $GLOBALS['SysValue']['other']['serverPath'] = $_SERVER['SERVER_NAME'] . "/" . $GLOBALS['SysValue']['dir']['dir'];

        // Дополнительные настройки
        if (is_array($option)) {

            // Replyto
            if (!empty($option['replyto']))
                $this->mail->addReplyTo($option['replyto']);

            // SMTP
            if ($option['smtp'] == true) {
                $this->mail->isSMTP();
                $this->mail->SMTPDebug = $option['debug'];
                $this->mail->Debugoutput = 'html';
                $this->mail->Host = $option['host'];
                $this->mail->Port = $option['port'];
                $this->SMTPSecure = $option['tls'];
                $this->mail->SMTPAuth = 1;
                $this->mail->Username = $option['user'];
                $this->mail->Password = $option['password'];
                $this->mail->SMTPAutoTLS = $option['auth'];


                if (!empty($option['auth']))
                    $this->mail->SMTPAuth = true;


                // Debug
                if ($this->debug)
                    $this->mail->SMTPDebug = 2;
            }

            // SENDMAIL
            elseif ($option['sendmail'] == true) {
                $this->mail->isMail();
            }
        } else {
            $this->mail->isMail();
        }

        $this->mail->XMailer = 'PHPShop';
        $this->mail->CharSet = $this->codepage;

        if (!empty($type)) {
            $this->type = 'text/html';
            $this->mail->isHTML(true);
            $this->mail->msgHTML($content);
        } else {
            $this->mail->isHTML(false);
            $this->mail->Body = $content;
        }


        if (strstr($from, ',')) {
            $from_array = explode(",", $from);
            $this->from = trim($from_array[0]);
        } else
            $this->from = $from;

        $this->mail->setFrom($this->from, $PHPShopSystem->getName());


        if (strstr($to, ',')) {
            $to_array = explode(",", $from);

            // Несколько адресов
            if (is_array($to_array)) {
                foreach ($to_array as $k => $mail)
                    if ($k == 0)
                        $this->mail->addAddress($mail);
                    else
                        $this->mail->addBCC($mail);
            }
        } else
            $this->mail->addAddress($to);


        $this->mail->Subject = $subject;

        if (empty($noSend)) {
            $this->sendMail();
        } else {
            $this->setOrgData();
        }
    }

    /**
     * Устанавливаем метки данных магазина
     */
    function setOrgData() {

        if ($this->PHPShopSystem) {
            $GLOBALS['SysValue']['other']['adminMail'] = $this->PHPShopSystem->getParam('adminmail2');
            $GLOBALS['SysValue']['other']['telNum'] = $this->PHPShopSystem->getParam('tel');
            $GLOBALS['SysValue']['other']['org_name'] = $this->PHPShopSystem->getSerilizeParam('bank.org_name');
            $GLOBALS['SysValue']['other']['org_adres'] = $this->PHPShopSystem->getSerilizeParam('bank.org_adres');
            $GLOBALS['SysValue']['other']['logo'] = $this->PHPShopSystem->getLogo(true);
            $GLOBALS['SysValue']['other']['charset'] = $this->codepage;
            $GLOBALS['SysValue']['other']['shopName'] = $this->PHPShopSystem->getName();
            $GLOBALS['SysValue']['other']['serverPath'] = $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir'];
            $GLOBALS['SysValue']['other']['date'] = date("d-m-y H:i");

            // Адрес сайта
            PHPShopParser::set('serverName', PHPShopString::check_idna($_SERVER['SERVER_NAME']));
            PHPShopParser::set('serverShop', PHPShopString::check_idna($_SERVER['SERVER_NAME'])). $GLOBALS['SysValue']['dir']['dir'];

            // Социальные сети
            PHPShopParser::set('vk', $this->PHPShopSystem->getSerilizeParam('bank.vk'));
            PHPShopParser::set('telegram', $this->PHPShopSystem->getSerilizeParam('bank.telegram'));
            PHPShopParser::set('odnoklassniki', $this->PHPShopSystem->getSerilizeParam('bank.odnoklassniki'));
            PHPShopParser::set('whatsapp', $this->PHPShopSystem->getSerilizeParam('bank.whatsapp'));
            PHPShopParser::set('youtube', $this->PHPShopSystem->getSerilizeParam('bank.youtube'));
        }
    }

    /**
     * Отправление письма html
     * @param string $content содержание
     */
    function sendMailNow($content) {
        $this->mail->msgHTML($content);
        $result = $this->mail->send();
        if (!$result and $this->debug) {
            echo "Mailer Error: " . $this->mail->ErrorInfo;
        } elseif ($this->debug) {
            echo "Message sent!";
        }
        $GLOBALS['SysValue']['other']['logo'] = $this->PHPShopSystem->getLogo(false);
        return $result;
    }

    /**
     * Отправление письма text
     * @param string $content содержание
     * @param strong $header заголовок
     */
    function sendMail() {
        $result = $this->mail->send();
        if (!$result and $this->debug) {
            echo "Mailer Error: " . $this->mail->ErrorInfo;
        } elseif ($this->debug) {
            echo "Message sent!";
        }
        $GLOBALS['SysValue']['other']['logo'] = $this->PHPShopSystem->getLogo(false);
        return $result;
    }

}

/**
 *  Библиотека Отправление почты с вложением
 */
class PHPShopMailFile extends PHPShopMail {

    function __construct($to, $from, $subject, $content, $filename, $file, $option = false) {
        parent::__construct($to, $from, $subject, $content, true, true, $option);
        $this->mail->addAttachment($file, $filename);
        if (empty($content))
            $content = $filename;
        $this->sendMailNow($content);
    }

}

?>