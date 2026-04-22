<?php

require_once "Autoloader.php";
Autoloader::register();

class Tools {

    public static function getDate($log) {
        if (file_exists($log)) {
            return file_get_contents($log);
        } else {
            return date('Y-m-d H:i:s', strtotime('-1 days', strtotime(date('Y-m-d H:i:s'))));
        }
    }

    /**
     * Запись лога
     * @param array $message содержание запроса в ту или иную сторону
     * @param string $order_id номер заказа
     * @param string $status статус операции
     * @param string $type request
     */
    public static function logger($message, $type, $status = null, $order_id = null) {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['retailcrm']['retailcrm_log']);
        $log = array(
            'message_new' => serialize($message),
            'order_id_new' => $order_id,
            'status_new' => $status,
            'type_new' => $type,
            'date_new' => time()
        );
        $PHPShopOrm->insert($log);
    }

    public static function iconvArray($arg, $in = "WINDOWS-1251", $out = "UTF-8") {

        if ($GLOBALS['PHPShopBase']->codBase == 'utf-8')
            return $arg;

        if (is_array($arg)) {
            foreach ($arg as $key => $val) {
                $arg[iconv($in, $out, $key)] = (is_array($val)) ? self::iconvArray($val, $in, $out) : iconv($in, $out, $val);
            }

            return $arg;
        } elseif (is_string($arg)) {

            return iconv($in, $out, $arg);
        }

        return $arg;
    }

    public static function clearArray($arr) {
        if (!is_array($arr)) {
            return $arr;
        }

        $result = array();
        foreach ($arr as $index => $node) {
            $result[$index] = (is_array($node)) ? self::clearArray($node) : trim($node);
            if ($result[$index] == '' || $index === "actionList" || $index === "edit" || count($result[$index]) < 1) {
                unset($result[$index]);
            }
        }

        return $result;
    }

    public static function explodeFio($fio) {
        $fio = (!$fio) ? false : explode(" ", $fio, 3);

        switch (count($fio)) {
            default:
            case 0:
                $newFio['firstName'] = 'ФИО  не указано';
                break;
            case 1:
                $newFio['firstName'] = $fio[0];
                break;
            case 2:
                $newFio = array(
                    'lastName' => $fio[0],
                    'firstName' => $fio[1]
                );
                break;
            case 3:
                $newFio = array(
                    'lastName' => $fio[0],
                    'firstName' => $fio[1],
                    'patronymic' => $fio[2]
                );
                break;
        }

        return $newFio;
    }

}

?>