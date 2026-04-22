<?php

/**
 *
 */
class Pozvonim
{
    const APPS_HOST = 'http://appspozvonim.com';
    const INSTALL_URL = '/phpshop/install';
    const RESTORE_URL = '/phpshop/restore';
    const LOGIN_URL = '/phpshop/login';

    public $errorMessage;
    public $useWin1251Encoding = true;

    /**
     * @return boolean
     */
    public function restoreTokenToEmail($email)
    {
        if ($email = $this->validEmail($email)) {
            if ($data = $this->restoreToken($email)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Сохраняет  опции плагина
     *
     * @param array $data
     * @return array
     */
    public function update(array $data)
    {
        if (!empty($data)) {

            if (isset($data['code']) && !empty($data['code'])) {
                if (!preg_match('/\/([a-z0-9]{32})\/connect/iu', $data['code'], $code)) {
                    $this->errorMessage = 'Код виджета имеет неверный формат';
                    return false;
                }
                $code = $code[1];
                $data['key'] = $code;
                $data['appId'] = 0;
                return $data;
            }

            if ($data = $this->valid($data)) {
                $data['code'] = '';
                if (!$data = $this->register($data)) {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
        return $data;
    }

    public function register($data)
    {

        $data['token'] = !empty($data['token']) ? $data['token'] : md5(uniqid('', true));
        $curl = new PCurl();
        $data['locale'] = 'ru';
        $result = '';
        try {
            $sendData = $data;
            if ($this->useWin1251Encoding) {
                foreach ($sendData as $name => $val) {
                    $sendData[$name] = iconv('windows-1251', 'UTF-8', $val);
                }
            }
            $curl->get(self::APPS_HOST . self::INSTALL_URL, $sendData);
            $result = trim($curl->response);
            if ($result) {
                unset($data['locale']);
                $result = @json_decode(trim($result), true);
                if (is_array($result)) {
                    if ($result['status'] != 1) {
                        $this->errorMessage = $this->useWin1251Encoding ? iconv('UTF-8', 'WINDOWS-1251', $result['message']) : $result['message'];
                        return false;
                    } else {
                        $data['key'] = $result['key'];
                        $data['appId'] = $result['id'];
                        return $data;
                    }
                }
            }
        } catch (Exception $e) {

        }

        $this->errorMessage = 'Ошибка на стороне сервера pozvonim.com: ' . print_r($result, true);
        return false;
    }

    /**
     * @param $email
     * @return bool
     */
    public function restoreToken($email)
    {
        $curl = new PCurl();
        try {
            $curl->get(self::APPS_HOST . self::RESTORE_URL, array('email' => $email, 'locale' => 'ru'));
            $result = trim($curl->response);
            if ($result) {
                $result = @json_decode(trim($result), true);
                if (is_array($result)) {
                    if ($result['status'] != 1) {
                        $this->errorMessage = $result['message'];
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        } catch (Exception $e) {

        }
        return false;
    }

    /**
     * @param $data
     * @return bool
     */
    public function valid($data)
    {
        foreach (array('phone' => 'телефон', 'email' => 'email', 'host' => 'домен') as $field => $title) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $this->errorMessage = 'Поле ' . $title . ' обязательно';
                return false;
            } else {
                if ($this->useWin1251Encoding) {
                    $data[$field] = trim(iconv('utf-8', 'windows-1251', $data[$field]));
                } else {
                    $data[$field] = trim($data[$field]);
                }
            }
        }

        if (isset($data['token']) && !empty($data['token'])) {
            if (strlen($data['token']) != 32) {
                $this->errorMessage = 'Секретный код имеет неверный формат';
                return false;
            }
        }

        $data['phone'] = '+' . trim($data['phone'], '+');
        if (!preg_match('/^\+[0-9]{9,20}$/', $data['phone'])) {
            $this->errorMessage = 'Телефон должен соотвестовать формату (+79998887766) ';
            return false;
        }

        $this->validEmail($data['email']);

        if (!preg_match('/^[a-zа-я0-9\-.]*[a-zа-я0-9\-]{2,}\.[a-zа-я]{2,6}$/im', $data['host'])
        ) {
            $this->errorMessage = 'Домен имеет неверный формат ';
            return false;
        }

        return $data;
    }

    /**
     * @param $email
     * @return bool|string
     */
    public function validEmail($email)
    {
        $email = trim($email);
        if (empty($email)) {
            $this->errorMessage = 'Необходимо указать корректный email';
            return false;
        }

        if (!preg_match('/^[a-z0-9._%+\-]+?@[a-z0-9а-я.\-]+\.[a-zрф]{2,6}$/i', $email)) {
            $this->errorMessage = 'Необходимо указать корректный email';
            return false;
        }

        return $email;
    }

}
