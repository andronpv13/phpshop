<?php

// ��������� ������
PHPShopObj::loadClass("array");

class PHPShopModule extends PHPShopArray
{
    private $domen_api;
    private $login_api;
    private $password_api;
    private $admin_phone;
    private $sender;
    private $order_template_sms;
    private $done_order_template_sms;
    private $order_template_admin_sms;
    private $change_status_order_template_sms;
    private $cascade_domen_api;
    private $cascade_sender;
    private $cascade_enabled;
    private $order_template_viber;
    private $order_template_viber_button_text;
    private $order_template_viber_button_url;
    private $order_template_viber_image_url;
    private $order_template_admin_viber;
    private $order_template_admin_viber_button_text;
    private $order_template_admin_viber_button_url;
    private $order_template_admin_viber_image_url;
    private $change_status_order_template_viber;
    private $change_status_order_template_viber_button_text;
    private $change_status_order_template_viber_button_url;
    private $change_status_order_template_viber_image_url;

    function __construct()
    {
        
        $this->option();

        $this->domen_api                                      = trim($this->option['domen_api']);
        $this->login_api                                      = trim($this->option['login_api']);
        $this->password_api                                   = trim($this->option['password_api']);
        $this->admin_phone                                    = trim($this->option['admin_phone']);
        $this->sender                                         = trim($this->option['sender']);
        $this->order_template_sms                             = trim($this->option['order_template_sms']);
        $this->done_order_template_sms                        = trim($this->option['done_order_template_sms']);
        $this->order_template_admin_sms                       = trim($this->option['order_template_admin_sms']);
        $this->change_status_order_template_sms               = trim($this->option['change_status_order_template_sms']);
        $this->cascade_domen_api                              = trim($this->option['cascade_domen_api']);
        $this->cascade_sender                                 = trim($this->option['cascade_sender']);
        $this->cascade_enabled                                = trim($this->option['cascade_enabled']);
        $this->order_template_viber                           = trim($this->option['order_template_viber']);
        $this->order_template_viber_button_text               = trim($this->option['order_template_viber_button_text']);
        $this->order_template_viber_button_url                = trim($this->option['order_template_viber_button_url']);
        $this->order_template_viber_image_url                 = trim($this->option['order_template_viber_image_url']);
        $this->order_template_admin_viber                     = trim($this->option['order_template_admin_viber']);
        $this->order_template_admin_viber_button_text         = trim($this->option['order_template_admin_viber_button_text']);
        $this->order_template_admin_viber_button_url          = trim($this->option['order_template_admin_viber_button_url']);
        $this->order_template_admin_viber_image_url           = trim($this->option['order_template_admin_viber_image_url']);
        $this->change_status_order_template_viber             = trim($this->option['change_status_order_template_viber']);
        $this->change_status_order_template_viber_button_text = trim($this->option['change_status_order_template_viber_button_text']);
        $this->change_status_order_template_viber_button_url  = trim($this->option['change_status_order_template_viber_button_url']);
        $this->change_status_order_template_viber_image_url   = trim($this->option['change_status_order_template_viber_image_url']);

    }

    /**
     * ��������� ������
     */
    function option() {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['smspircompany']['smspircompany_message']);
        $this->option = $PHPShopOrm->select();
    }

    /**
     * @return server api
     */
    function getServerApi()
    {
        return $this->domen_api;
    }


    /**
     * @return login
     */
    function getLoginApi()
    {
        return $this->login_api;
    }

    /**
     * @return password
     */
    function getPassApi()
    {
        return $this->password_api;
    }

    /**
     * @return phone
     */
    function getAdminPhone()
    {
        return $this->admin_phone;
    }

    /**
     * @return sender
     */
    function getSender()
    {
        return $this->sender;
    }

    /**
     * @return order template
     */
    function getTplOrder()
    {
        return $this->order_template_sms;
    }

    /**
     * @return done order template
     */
    function getTplDoneOrder()
    {
        return $this->done_order_template_sms;
    }

    /**
     * @return reject order template
     */
    function getTplAdminOrder()
    {
        return $this->order_template_admin_sms;
    }

    /**
     * @return status order template
     */
    function getTplStatusOrder()
    {
        return $this->change_status_order_template_sms;
    }

    /**
     * get server cascade api
     * @return string
     */
    function getCascadeDomenApi()
    {
        return $this->cascade_domen_api;
    }

    /**
     * get cascade_sender
     * @return string
     */
    function getCascadeSender()
    {
        return $this->cascade_sender;
    }

    /**
     * get cascade_enabled
     * @return int 0 || 1
     */
    function getCascadeEnabled()
    {
        return $this->cascade_enabled;
    }

    // TEMPLATES VIBER

    /**
     * @return order template viber
     */
    function getTplOrderViber()
    {
        return $this->order_template_viber;
    }

    /**
     * @return reject order template
     */
    function getTplAdminOrderViber()
    {
        return $this->order_template_admin_viber;
    }

    /**
     * @return status order template
     */
    function getTplStatusOrderViber()
    {
        return $this->change_status_order_template_viber;
    }

    /**
     * parser string
     */
    function parseString($string, $datainsert)
    {
      $str = strtr($string, $datainsert);
      return $str;
    }

    /**
     * validate phone
     */
    function true_num($phone)
    {
        $str = preg_replace("/[^0-9]/", "", $phone);

        return $str;
    }

    /**
     * host name
     */
    function getHostName()
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }

        return $protocol . $_SERVER['SERVER_NAME'];
    }


    /**
     * send sms
     */
    function sendSms($phone, $msg, $tpl = null)
    {
        // XML-��������
        $href = 'https://'.$this->domen_api.'/xml/'; // ����� �������

        $msg = mb_convert_encoding($msg, 'utf-8', 'windows-1251');
        $sender = mb_convert_encoding($this->sender, 'utf-8', 'windows-1251');

        $src = '<?xml version="1.0" encoding="utf-8"?>';
        $src .= '<request>';
        $src .=     '<security>';
        $src .=         '<login value="' . $this->login_api . '" />';
        $src .=         '<password value="' . $this->password_api . '" />';
        $src .=     '</security>';
        $src .=     '<message type="sms">';
        $src .=         '<sender>' . $sender . '</sender>';
        $src .=         '<text>' . $msg . '</text>';

        foreach ($phone as $k => $p) {
          $src .=       '<abonent phone="' . trim($p) . '" number_sms="' . $k . '" />';
        }

        $src .=     '</message>';
        $src .= '</request>';


        if ($this->cascade_enabled) {
            $href = 'https://'.$this->cascade_domen_api.'/xml/message.php'; // ����� �������
            $cascade_sender = mb_convert_encoding($this->cascade_sender, 'utf-8', 'windows-1251');

            $src  = '<?xml version="1.0" encoding="utf-8"?>';
            $src .= '<request>';
            $src .=     '<security>';
            $src .=         '<login value="' . $this->login_api . '" />';
            $src .=         '<password value="' . $this->password_api . '" />';
            $src .=     '</security>';
            $src .=     '<message>';
            $src .=         '<send_viber>1</send_viber>';
            $src .=         '<send_sms>2</send_sms>';
            $src .=         '<sender_viber>' . $cascade_sender . '</sender_viber>';
            $src .=         '<text_viber>' . $msg . '</text_viber>';

            switch ($tpl) {
                case 'order_template_viber':

                    if ($this->order_template_viber_button_text && $this->order_template_viber_button_url) {

                        $order_template_viber_button_text = mb_convert_encoding($this->order_template_viber_button_text, 'utf-8', 'windows-1251');

                        $src .= '<button_text>' . $order_template_viber_button_text . '</button_text>';
                        $src .= '<button_url>' . $this->order_template_viber_button_url . '</button_url>';

                        if ($this->order_template_viber_image_url) {
                            $src .= '<image_url>' . $this->getHostName() . $this->order_template_viber_image_url . '</image_url>';
                        }

                    }

                    break;

                case 'order_template_admin_viber':

                    if ($this->order_template_admin_viber_button_text && $this->order_template_admin_viber_button_url) {

                        $order_template_admin_viber_button_text = mb_convert_encoding($this->order_template_admin_viber_button_text, 'utf-8', 'windows-1251');

                        $src .= '<button_text>' . $order_template_admin_viber_button_text . '</button_text>';
                        $src .= '<button_url>' . $this->order_template_admin_viber_button_url . '</button_url>';

                        if ($this->order_template_admin_viber_image_url) {
                            $src .= '<image_url>' . $this->getHostName() . $this->order_template_admin_viber_image_url . '</image_url>';
                        }

                    }

                    break;

                case 'change_status_order_template_viber':

                    if ($this->change_status_order_template_viber_button_text && $this->change_status_order_template_viber_button_url) {

                        $change_status_order_template_viber_button_text = mb_convert_encoding($this->change_status_order_template_viber_button_text, 'utf-8', 'windows-1251');

                        $src .= '<button_text>' . $change_status_order_template_viber_button_text . '</button_text>';
                        $src .= '<button_url>' . $this->change_status_order_template_viber_button_url . '</button_url>';

                        if ($this->change_status_order_template_viber_image_url) {
                            $src .= '<image_url>' . $this->getHostName() . $this->change_status_order_template_viber_image_url . '</image_url>';
                        }

                    }

                    break;
            }

            $src .=         '<finale_viber_read>1</finale_viber_read>';
            $src .=         '<sender_sms>' . $sender . '</sender_sms>';
            $src .=         '<text_sms>' . $msg . '</text_sms>';

            foreach ($phone as $k => $p) {
                $src .=     '<abonent phone="' . trim($p) . '" client_id_message="' . $k . '" />';
            }

            $src .=     '</message>';
            $src .=  '</request>';
            
            
        }

        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_HTTPHEADER, array ('Content-type: text/xml','charset=utf-8','Expect:'));
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt ($ch, CURLOPT_POST, true);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, $src);
        curl_setopt ($ch, CURLOPT_URL, $href);
        curl_exec($ch);
        curl_close($ch);
        
    }

    /**
     * @param $msg
     * �������� ��� ��������������
     */
    function sendSmsAdmin($msg, $tpl = null)
    {
        $phone = $this->admin_phone;
        $phone = explode(',', $phone);
        $this->sendSms($phone, $msg, $tpl);
    }
}