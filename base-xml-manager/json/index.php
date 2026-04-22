<?php

/**
 * Синхронизация данных через JSON
 * @package PHPShopExchange
 * @author PHPShop Software
 * @version 1.3
 */
$_classPath = "../../phpshop/";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("basexml");
PHPShopObj::loadClass("string");
PHPShopObj::loadClass("system");

// Подключаем БД
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);

class PHPShopJSON extends PHPShopBaseXml {

    public $debug = false;

    function __construct() {
        global $PHPShopBase;

        $this->true_method = array('select', 'update', 'delete', 'insert', 'mail', 'get_order_num');
        $this->token = $_SERVER['HTTP_TOKEN'];

        if (empty($this->token))
            $this->error('No token');

        $this->PHPShopBase = $PHPShopBase;

        if ($this->admin()) {

            $this->sql = json_decode(file_get_contents("php://input"), true);

            // Многомерный
            if (is_array($this->sql[0])) {

                $sql_array = $this->sql;

                foreach ($sql_array as $sql) {
                    $this->sql = $sql;
                    $this->parser();

                    if (in_array($this->xml['method'], $this->true_method)) {
                        if (method_exists($this, $this->xml['method'])) {

                            // Проверка прав
                            if ($this->checkRules($this->xml['method']))
                                call_user_func(array($this, $this->xml['method']));
                            else
                                $this->error('No permission');

                            if (!empty($this->error)) {
                                $this->error($this->error);
                            }
                        } else
                            $this->error('Non method');
                    } else
                        $this->error('False method');

                    $result[] = $this->compile();
                }

                header("Content-Type: application/json");
                echo json_encode($result);
            }
            // Простой
            else {
                $this->parser();

                if (in_array($this->xml['method'], $this->true_method)) {
                    if (method_exists($this, $this->xml['method'])) {

                        // Проверка прав
                        if ($this->checkRules($this->xml['method']))
                            call_user_func(array($this, $this->xml['method']));
                        else
                            $this->error('No permission');

                        if (!empty($this->error)) {
                            $this->error($this->error);
                        }
                    } else
                        $this->error('Non method');
                } else
                    $this->error('False method');

                $result = $this->compile();
                header("Content-Type: application/json");
                echo json_encode($result);
            }
        } else {
            $this->error('Token not found');
        }
    }

    public function parser() {

        if (is_array($this->sql)) {
            $this->xml['method'] = $this->sql['method'];

            if (is_array($this->sql['vars']))
                foreach ($this->sql['vars'] as $k => $v)
                    $this->sql['vars'][$k] = PHPShopString::utf8_win1251($v);

            $this->xml['vars'] = array($this->sql['vars']);
            $this->xml['from'] = $this->sql['from'];

            if (!empty($this->sql['where']))
                $this->xml['where'] = $this->parseWhereString($this->sql['where']);
            if (!empty($this->sql['order']))
                $this->xml['order'] = array('order' => $this->sql['order']);
            if (!empty($this->sql['limit']))
                $this->xml['limit'] = array('limit' => $this->sql['limit']);
        } else
            $this->error('Non json');
    }

    public function admin() {
        $PHPShopOrm = new PHPShopOrm($this->PHPShopBase->getParam('base.users'));
        $PHPShopOrm->debug = $this->debug;
        $data = $PHPShopOrm->select(array('token,status'), array('enabled' => "='1'"), array('order' => 'id desc'), array('limit' => 100));
        if (is_array($data)) {
            foreach ($data as $v)
                if ($this->token and $this->token == $v['token']) {
                    $this->UserStatus = unserialize($v['status']);
                    return true;
                }
        }
    }

    public function checkRules($do = 'select') {
        $rules_array = array(
            'select' => 0,
            'update' => 1,
            'insert' => 2,
            'delete' => 1,
            'mail' => 1,
            'get_order_num' => 1
        );

        $array = explode("-", $this->UserStatus['api']);

        if (!empty($array[$rules_array[$do]]))
            return true;
    }

    public function is_serialize($str) {
        $array = unserialize($str);

        if (is_array($array)) {
            array_walk_recursive($array, 'array2iconvUTF');
            $result = $array;
        } else
            $result = PHPShopString::win_utf8($str);

        return $result;
    }

    public function compile() {

        if ($this->data)
            $result['status'] = 'succes';
        else
            $result['status'] = 'false';

        if (is_array($this->data)) {

            foreach ($this->data as $row) {
                if (is_array($row)) {
                    foreach ($row as $key => $val) {
                        $result['data'][$row['id']][$key] = $this->is_serialize($val);
                    }
                } else {
                    foreach ($this->data as $key => $val) {
                        $result['data'][$key] = $this->is_serialize($val);
                    }
                }
            }
        }

        return $result;
    }

    public function error($text) {
        if (!empty($text)) {
            header("Content-Type: application/json");
            exit(json_encode(array('status' => 'error', 'error' => $text)));
        }
    }

    /**
     * Номер заказа
     */
    public function get_order_num() {

        $PHPShopOrm = new PHPShopOrm();
        $res = $PHPShopOrm->query("select uid from " . $GLOBALS['SysValue']['base']['orders'] . " order by id desc LIMIT 0, 1");
        $row = mysqli_fetch_array($res);
        $last = $row['uid'];
        $all_num = explode("-", $last);
        $ferst_num = $all_num[0];
        $order_num = $ferst_num + 1;

        $ouid = $order_num . "-" . substr(abs(crc32(uniqid(session_id()))), 0, 2);
        $this->data = ['uid' => $ouid];
    }

    /**
     * Сообщение
     */
    public function mail() {
        PHPShopObj::loadClass(['mail', 'system']);
        $GLOBALS['PHPShopSystem'] = new PHPShopSystem();
        $this->data = new PHPShopMail($this->xml['vars']['mail'], $GLOBALS['PHPShopSystem']->getParam('adminmail2'), $this->xml['vars']['title'], $this->xml['vars']['content']);
    }

}

/*
 * Смена кодировки на UTF-8 в массиве
 */

function array2iconvUTF(&$value) {
    $value = iconv("CP1251", "UTF-8", $value);
}

new PHPShopJSON();